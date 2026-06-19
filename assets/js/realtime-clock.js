/**
 * Real-time Clock Display
 * Sinkronisasi waktu dengan server untuk memastikan akurasi di semua halaman
 * 
 * Penggunaan:
 * <span id="realtime-hari"></span>
 * <span id="realtime-tanggal"></span>
 * <span id="realtime-jam"></span>
 * 
 * <script src="assets/js/realtime-clock.js"></script>
 * <script>
 *   RealtimeClock.init({
 *       hariSelector: '#realtime-hari',
 *       tanggalSelector: '#realtime-tanggal',
 *       jamSelector: '#realtime-jam',
 *       formatJam: 'HH:MM:SS'
 *   });
 * </script>
 */

const RealtimeClock = (function() {
    let serverTime = null;
    let lastServerCheck = 0;
    const checkInterval = 60000; // Sinkronisasi ulang setiap 60 detik
    let updateIntervalId = null;
    let syncIntervalId = null;
    
    let config = {
        hariSelector: null,
        tanggalSelector: null,
        jamSelector: null,
        formatJam: 'HH:MM:SS',
        apiUrl: 'api_get_time.php'
    };
    
    // Fetch waktu dari server
    function fetchServerTime() {
        fetch(config.apiUrl)
            .then(response => response.json())
            .then(data => {
                serverTime = {
                    hari: data.hari,
                    tanggal: data.tanggal,
                    jam: data.jam,
                    timestamp: data.timestamp * 1000
                };
                lastServerCheck = Date.now();
                updateDisplay();
            })
            .catch(error => console.log('Gagal mendapatkan waktu server:', error));
    }
    
    // Format jam sesuai konfigurasi
    function formatTime(hours, minutes, seconds) {
        const h = String(hours).padStart(2, '0');
        const m = String(minutes).padStart(2, '0');
        const s = String(seconds).padStart(2, '0');
        
        switch(config.formatJam.toUpperCase()) {
            case 'HH:MM:SS':
                return `${h}:${m}:${s}`;
            case 'HH:MM':
                return `${h}:${m}`;
            case 'H:MM:SS':
                return `${hours}:${m}:${s}`;
            case 'H:MM':
                return `${hours}:${m}`;
            default:
                return `${h}:${m}:${s}`;
        }
    }
    
    // Update tampilan waktu
    function updateDisplay() {
        if (!serverTime) {
            fetchServerTime();
            return;
        }
        
        // Hitung waktu lokal berdasarkan waktu server
        const elapsedMs = Date.now() - lastServerCheck;
        const currentTimestamp = serverTime.timestamp + elapsedMs;
        const now = new Date(currentTimestamp);
        
        // Update hari
        if (config.hariSelector) {
            const elem = document.querySelector(config.hariSelector);
            if (elem) elem.textContent = serverTime.hari;
        }
        
        // Update tanggal
        if (config.tanggalSelector) {
            const elem = document.querySelector(config.tanggalSelector);
            if (elem) elem.textContent = serverTime.tanggal;
        }
        
        // Update jam
        if (config.jamSelector) {
            const elem = document.querySelector(config.jamSelector);
            if (elem) {
                const jam = formatTime(now.getHours(), now.getMinutes(), now.getSeconds());
                elem.textContent = jam;
            }
        }
    }
    
    return {
        init: function(options = {}) {
            config = Object.assign(config, options);
            
            // Initial fetch
            fetchServerTime();
            
            // Update setiap detik
            if (updateIntervalId) clearInterval(updateIntervalId);
            updateIntervalId = setInterval(updateDisplay, 1000);
            
            // Sinkronisasi ulang dengan server
            if (syncIntervalId) clearInterval(syncIntervalId);
            syncIntervalId = setInterval(fetchServerTime, checkInterval);
        },
        
        destroy: function() {
            if (updateIntervalId) clearInterval(updateIntervalId);
            if (syncIntervalId) clearInterval(syncIntervalId);
        },
        
        getServerTime: function() {
            return serverTime;
        }
    };
})();
