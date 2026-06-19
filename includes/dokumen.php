<?php

function daftarDokumenSiswa(): array
{
    return [
        'file_kk' => [
            'label' => 'Foto KK',
            'verif_col' => 'verif_file_kk',
        ],
        'file_akte' => [
            'label' => 'Akte Kelahiran',
            'verif_col' => 'verif_file_akte',
        ],
        'file_ijazah' => [
            'label' => 'Ijazah TK',
            'verif_col' => 'verif_file_ijazah',
        ],
        'file_kks' => [
            'label' => 'KKS/PKH',
            'verif_col' => 'verif_file_kks',
        ],
        'file_foto' => [
            'label' => 'Foto Siswa',
            'verif_col' => 'verif_file_foto',
        ],
        'file_surat_orangtua' => [
            'label' => 'Surat Pernyataan Orang Tua',
            'verif_col' => 'verif_file_surat_orangtua',
        ],
    ];
}

function resetVerifikasiDokumen(mysqli $conn, int $siswaId): void
{
    $set = [];
    foreach (daftarDokumenSiswa() as $info) {
        $set[] = $info['verif_col'] . " = 'belum'";
    }
    $sql = 'UPDATE siswa SET ' . implode(', ', $set) . ' WHERE id = ' . (int) $siswaId;
    mysqli_query($conn, $sql);
}
