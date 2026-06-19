<?php
/**
 * Halaman diagnostik untuk cek timezone sistem
 * Hapus file ini setelah selesai
 */

require_once 'koneksi.php';

// Get PHP timezone info
$php_timezone = date_default_timezone_get();
$php_time = date('Y-m-d H:i:s');
$php_date_ini = ini_get('date.timezone');

// Get MySQL timezone info
$stmt = mysqli_prepare($conn, "SELECT NOW() as db_time, @@global.time_zone, @@session.time_zone");
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$db_time = $row['db_time'] ?? 'N/A';
$db_tz_global = $row['@@global.time_zone'] ?? 'N/A';
$db_tz_session = $row['@@session.time_zone'] ?? 'N/A';

// System timezone from environment
$system_tz = shell_exec('tzutil /g') ?: shell_exec('date +%Z') ?: 'Unknown';

?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostik Timezone</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ddd; }
        h2 { margin-top: 0; color: #333; }
        pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Diagnostik Timezone Sistem</h1>
    
    <div class="box">
        <h2>PHP Configuration</h2>
        <pre>
Default timezone: <?php echo $php_timezone; ?>
php.ini setting: <?php echo $php_date_ini ?: '(not set)'; ?>
Current time: <?php echo $php_time; ?>
        </pre>
    </div>
    
    <div class="box">
        <h2>MySQL / MariaDB</h2>
        <pre>
Current time: <?php echo $db_time; ?>
Global timezone: <?php echo $db_tz_global; ?>
Session timezone: <?php echo $db_tz_session; ?>
        </pre>
    </div>
    
    <div class="box">
        <h2>Sistem Operasi</h2>
        <pre>
Timezone: <?php echo $system_tz; ?>
        </pre>
    </div>
    
    <div class="box">
        <h2>CLIENT Browser</h2>
        <pre id="client_tz"></pre>
    </div>

    <script>
    var now = new Date();
    document.getElementById('client_tz').textContent = 
        'Client timezone offset: ' + (-now.getTimezoneOffset()) + ' minutes\n' +
        'Client local time: ' + now.toString();
    </script>
</body>
</html>
