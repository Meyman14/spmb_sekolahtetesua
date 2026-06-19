<?php
/**
 * Banner judul halaman menu.
 * Variabel: $page_heading (wajib), $page_subtitle (opsional, boleh HTML), $page_header_theme, $page_header_icon
 */
$theme = $page_header_theme ?? 'red';
$icon = $page_header_icon ?? 'bi-mortarboard-fill';
$allowedThemes = ['red', 'green', 'blue', 'orange', 'purple'];
if (!in_array($theme, $allowedThemes, true)) {
    $theme = 'red';
}
?>
<div class="page-header page-header-banner page-header-<?php echo $theme; ?>">
    <div class="page-header-icon"><i class="bi <?php echo htmlspecialchars($icon); ?>"></i></div>
    <div>
        <h1><?php echo htmlspecialchars($page_heading); ?></h1>
        <?php if (!empty($page_subtitle)): ?>
        <p><?php echo $page_subtitle; ?></p>
        <?php endif; ?>
    </div>
</div>
