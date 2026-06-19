<?php

const LEVEL_ADMIN = 'admin';
const LEVEL_PETUGAS = 'petugas';

function userLevel(): string
{
    $level = $_SESSION['level'] ?? '';
    return in_array($level, [LEVEL_ADMIN, LEVEL_PETUGAS], true) ? $level : LEVEL_PETUGAS;
}

function isAdmin(): bool
{
    return userLevel() === LEVEL_ADMIN;
}

function isPetugas(): bool
{
    return userLevel() === LEVEL_PETUGAS;
}

function requireAdmin(string $redirect = 'dashboard.php'): void
{
    if (!isAdmin()) {
        $_SESSION['access_error'] = 'Halaman ini hanya untuk Admin.';
        header('Location: ' . $redirect);
        exit;
    }
}

function requirePetugas(string $redirect = 'dashboard.php'): void
{
    if (!isPetugas()) {
        $_SESSION['access_error'] = 'Halaman ini hanya untuk Petugas.';
        header('Location: ' . $redirect);
        exit;
    }
}

function requirePetugasOrAdmin(string $redirect = 'dashboard.php'): void
{
    if (!isPetugas() && !isAdmin()) {
        $_SESSION['access_error'] = 'Halaman ini hanya untuk Petugas atau Admin.';
        header('Location: ' . $redirect);
        exit;
    }
}

function roleLabel(): string
{
    return isAdmin() ? 'Administrator' : 'Petugas';
}

/** Data petugas yang sedang login (saat input pendaftaran). */
function petugasSaatIni(): array
{
    return [
        'username' => $_SESSION['username'] ?? '',
        'nama' => $_SESSION['nama'] ?? ($_SESSION['username'] ?? ''),
    ];
}

/** Tampilan nama petugas pendaftar dari baris tabel siswa. */
function namaPetugasPendaftar(array $siswa): string
{
    if (!empty($siswa['petugas_nama'])) {
        return $siswa['petugas_nama'];
    }
    if (!empty($siswa['petugas_username'])) {
        return $siswa['petugas_username'];
    }

    return '-';
}
