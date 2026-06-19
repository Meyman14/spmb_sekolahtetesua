<?php
$page_title = $page_title ?? 'SPMB';
$active_menu = $active_menu ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - SPMB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/app-pages.css">
    <link rel="stylesheet" href="assets/css/app-credit.css">
    <?php if (!empty($extra_css)): ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($extra_css); ?>">
    <?php endif; ?>
</head>
<body>
<div class="app-wrapper">
