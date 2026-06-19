<?php

function getStatistikTerdaftar(mysqli $conn): array
{
    $status = 'terdaftar';

    $total = (int) mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) AS jml FROM siswa WHERE status = '$status'"
    ))['jml'];

    $laki = (int) mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) AS jml FROM siswa WHERE status = '$status' AND jenis_kelamin = 'Laki-laki'"
    ))['jml'];

    $perempuan = (int) mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) AS jml FROM siswa WHERE status = '$status' AND jenis_kelamin = 'Perempuan'"
    ))['jml'];

    $kristen = (int) mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) AS jml FROM siswa WHERE status = '$status' AND agama = 'Kristen'"
    ))['jml'];

    $katolik = (int) mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) AS jml FROM siswa WHERE status = '$status' AND agama = 'Katolik'"
    ))['jml'];

    $islam = (int) mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) AS jml FROM siswa WHERE status = '$status' AND agama = 'Islam'"
    ))['jml'];

    return compact('total', 'laki', 'perempuan', 'kristen', 'katolik', 'islam');
}

function getStatistikAdmin(mysqli $conn): array
{
    $perluVerifikasi = (int) mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) AS jml FROM siswa WHERE status = 'final'"
    ))['jml'];

    $draft = (int) mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) AS jml FROM siswa WHERE status = 'draft'"
    ))['jml'];

    $terdaftar = (int) mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT COUNT(*) AS jml FROM siswa WHERE status = 'terdaftar'"
    ))['jml'];

    return compact('perluVerifikasi', 'draft', 'terdaftar');
}
