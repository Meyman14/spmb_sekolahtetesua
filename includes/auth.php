<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function request_wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xrw = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($accept, 'application/json') !== false) return true;
    if (strtolower($xrw) === 'xmlhttprequest') return true;
    if (stripos($contentType, 'application/json') !== false) return true;
    return false;
}

if (!isset($_SESSION['username'])) {
    if (request_wants_json()) {
        header('Content-Type: application/json; charset=utf-8', true, 401);
        echo json_encode(['ok' => false, 'pesan' => 'Sesi login tidak ditemukan. Silakan login kembali.']);
        exit;
    }

    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/role.php';

if (!isset($_SESSION['level']) || !in_array($_SESSION['level'], ['admin', 'petugas'], true)) {
    $_SESSION['level'] = 'petugas';
}

// Petugas wajib absen hadir hari ini sebelum mengakses menu lain
require_once __DIR__ . '/absensi.php';
wajibAbsensiPetugas();