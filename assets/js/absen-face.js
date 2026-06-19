/**
 * Absensi petugas dengan face-api.js (@vladmandic/face-api)
 */
(function () {
    'use strict';

    var video = document.getElementById('absenVideo');
    var canvas = document.getElementById('absenCanvas');
    var statusEl = document.getElementById('absenFaceStatus');
    var btnVerifikasi = document.getElementById('btnVerifikasiWajah');
    var stream = null;

    function setStatus(msg, type) {
        if (!statusEl) return;
        statusEl.className = 'absen-face-status alert alert-' + (type || 'info');
        statusEl.textContent = msg;
        statusEl.classList.remove('d-none');
    }

    async function startCamera() {
        if (!video) return;
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
            audio: false,
        });
        video.srcObject = stream;
        return new Promise(function (resolve) {
            video.onloadedmetadata = function () {
                video.play();
                resolve();
            };
        });
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(function (t) { t.stop(); });
            stream = null;
        }
    }

    function captureFrameBase64() {
        if (!canvas || !video) return '';
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        var ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        return canvas.toDataURL('image/jpeg', 0.85);
    }

    async function postJson(url, body) {
        var res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
            credentials: 'same-origin',
        });

        if (res.status === 401) {
            // session expired or unauthorized
            try {
                var j = await res.json();
                throw new Error(j.pesan || 'Unauthorized');
            } catch (e) {
                throw new Error('Sesi anda berakhir. Silakan login kembali.');
            }
        }

        // try parse JSON, if fails, throw to be caught by caller
        var text = await res.text();
        try {
            return JSON.parse(text);
        } catch (e) {
            throw new Error('Response bukan JSON: ' + text.substring(0, 200));
        }
    }

    // no registration function — feature removed

    async function onVerifikasiWajah() {
        if (!btnVerifikasi) return;
        btnVerifikasi.disabled = true;
        setStatus('Memindai wajah...', 'info');

        try {
            var foto = captureFrameBase64();
            if (!foto) {
                setStatus('Gagal menangkap foto. Periksa kamera Anda.', 'danger');
                btnVerifikasi.disabled = false;
                return;
            }
            var data = await postJson('proses_absen.php', { foto: foto });

            if (data.ok) {
                setStatus(data.pesan + (data.jarak != null ? ' (cocok)' : ''), 'success');
                // show modal if exists (without requiring Bootstrap JS)
                var modalEl = document.getElementById('modalAbsenSukses');
                if (modalEl) {
                    // create backdrop
                    var backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show custom-backdrop';
                    document.body.appendChild(backdrop);
                    modalEl.style.display = 'block';
                    modalEl.classList.add('show');
                    modalEl.setAttribute('aria-modal', 'true');
                    modalEl.setAttribute('role', 'dialog');
                    modalEl.style.zIndex = 1100;
                    document.body.classList.add('modal-open');
                    var btn = document.getElementById('btnLanjutKerja');
                    if (btn) {
                        btn.onclick = function () {
                            window.location.href = data.redirect || 'dashboard.php';
                        };
                    }
                    return;
                }
                setTimeout(function () {
                    window.location.href = data.redirect || 'dashboard.php';
                }, 1000);
            } else {
                setStatus(data.pesan || 'Gagal menyimpan absen.', 'danger');
                btnVerifikasi.disabled = false;
            }
        } catch (e) {
            // show friendly message for auth/session issues
            setStatus('Error: ' + (e.message || e), 'danger');
            btnVerifikasi.disabled = false;
        }
    }

    async function init() {
        if (!video) {
            setStatus('Elemen video tidak ditemukan.', 'danger');
            return;
        }

        try {
            await startCamera();
            setStatus('Kamera siap. Arahkan kamera dan tekan tombol untuk absen.', 'success');

            if (btnVerifikasi) {
                btnVerifikasi.addEventListener('click', onVerifikasiWajah);
            }
        } catch (e) {
            setStatus('Tidak dapat mengakses kamera: ' + (e.message || e) + '. Izinkan akses kamera di browser.', 'danger');
        }
    }

    window.addEventListener('beforeunload', stopCamera);
    document.addEventListener('DOMContentLoaded', init);
})();
