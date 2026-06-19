<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_absensi_schema.php';

requireAdmin();
ensureAbsensiSchema($conn);

$page_title = 'Foto Referensi Petugas';
$active_menu = 'daftar_hadir';

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content app-page">
    <header class="page-header page-header-blue">
        <div class="page-header-content">
            <div class="page-header-icon"><i class="bi bi-camera-fill"></i></div>
            <div>
                <h1 class="page-title">Foto Referensi Petugas</h1>
                <p class="page-subtitle">Unggah foto referensi petugas (bulk). Nama file harus sesuai `username`.</p>
            </div>
        </div>
    </header>

    <div class="container-fluid p-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Instruksi</h5>
            </div>
            <div class="card-body">
                <ul>
                    <li>Siapkan gambar JPEG/PNG. Nama file gunakan <code>username.jpg</code> sesuai kolom <code>users.username</code>.</li>
                    <li>Ukuran file sebaiknya &lt;= 2MB dan wajah terlihat jelas.</li>
                    <li>Setelah proses, sistem menyimpan foto ke <code>uploads/wajah_ref/</code> dan descriptor ke tabel <code>users.face_descriptor</code>.</li>
                </ul>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Bulk Upload Foto Referensi</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="fileInput" class="form-label">Pilih beberapa gambar</label>
                    <input type="file" id="fileInput" class="form-control" accept="image/*" multiple>
                </div>
                <div class="mb-3">
                    <button id="btnProcess" class="btn btn-primary">Proses dan Simpan</button>
                    <button id="btnClear" class="btn btn-outline-secondary">Bersihkan</button>
                </div>

                <div id="bulkStatus" class="mt-3"></div>

                <hr>
                <h6>Hasil Proses</h6>
                <div class="table-responsive">
                    <table class="table table-sm" id="resultTable">
                        <thead>
                            <tr><th>#</th><th>File</th><th>Username</th><th>Status</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.15/dist/face-api.min.js"></script>
<script src="assets/js/bulk-face.js"></script>
