<?php
// Minimal server info helper for debugging DocumentRoot and asset timestamps
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Server Info - SPMB</title>
  <style>body{font-family:Segoe UI,Arial;padding:20px;background:#f7fbff}pre{background:#fff;border:1px solid #ddd;padding:12px}</style>
</head>
<body>
  <h1>Server Info</h1>
  <p><strong>Script file:</strong> <?php echo htmlspecialchars(__FILE__); ?></p>
  <p><strong>Realpath (cwd):</strong> <?php echo htmlspecialchars(realpath('.')); ?></p>
  <p><strong>Document root:</strong> <?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? ''); ?></p>
  <p><strong>Requested URI:</strong> <?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? ''); ?></p>

  <h2>Asset timestamps</h2>
  <ul>
    <?php
    $files = [
      'assets/js/login-slideshow.js',
      'assets/css/login-posters.css',
      'test_slideshow.html'
    ];
    foreach ($files as $f) {
        $path = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f);
        if (is_file($path)) {
            echo '<li><strong>' . htmlspecialchars($f) . '</strong>: exists — last modified ' . date('c', filemtime($path)) . '</li>';
        } else {
            echo '<li><strong>' . htmlspecialchars($f) . '</strong>: <em>not found</em></li>';
        }
    }
    ?>
  </ul>

  <h2>Quick links</h2>
  <ul>
    <li><a href="/spmb_sekolah/assets/js/login-slideshow.js" target="_blank">View login-slideshow.js</a></li>
    <li><a href="/spmb_sekolah/assets/css/login-posters.css" target="_blank">View login-posters.css</a></li>
    <li><a href="/spmb_sekolah/test_slideshow.html" target="_blank">Open test_slideshow.html</a></li>
  </ul>
</body>
</html>
