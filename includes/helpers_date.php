<?php
/**
 * Date helper utilities for application-wide formatting/parsing.
 * Stored format in DB remains YYYY-MM-DD; display format is "DD MMMM YYYY" (e.g. "07 Juni 2026").
 */

function format_date_indonesia($date): string
{
    if ($date === null || $date === '') return '';
    if ($date instanceof DateTime) $iso = $date->format('Y-m-d');
    else $iso = (string) $date;
    // normalize possible datetime
    $ts = strtotime($iso);
    if ($ts === false) return (string) $date;
    $hari = date('d', $ts);
    $bulanIndex = (int)date('n', $ts);
    $tahun = date('Y', $ts);
    $bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $bulanNama = $bulanIndo[$bulanIndex - 1] ?? '';
    return sprintf('%02d %s %04d', (int)$hari, $bulanNama, (int)$tahun);
}

/**
 * Parse user-supplied Indonesian-formatted date into ISO YYYY-MM-DD.
 * Accepts: "07 Juni 2026", "07-06-2026", "07/06/2026", and ISO strings.
 */
function parse_date_indonesia(?string $s): ?string
{
    if ($s === null) return null;
    $s = trim($s);
    if ($s === '') return null;
    // If already ISO-like
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return $s;
    // dd-mm-yyyy or dd/mm/yyyy or dd.mm.yyyy
    if (preg_match('#^(\d{1,2})[\-/\.](\d{1,2})[\-/\.](\d{4})$#', $s, $m)) {
        $d = (int)$m[1]; $mo = (int)$m[2]; $y = (int)$m[3];
        if (checkdate($mo, $d, $y)) return sprintf('%04d-%02d-%02d', $y, $mo, $d);
    }
    // dd MonthName yyyy (MonthName in Indonesian)
    if (preg_match('/^(\d{1,2})\s+([A-Za-zÀ-ž]+)\s+(\d{4})$/u', $s, $m)) {
        $d = (int)$m[1]; $bulanNama = mb_strtolower($m[2], 'UTF-8'); $y = (int)$m[3];
        $map = [
            'januari'=>1,'februari'=>2,'maret'=>3,'april'=>4,'mei'=>5,'juni'=>6,'juli'=>7,'agustus'=>8,'september'=>9,'oktober'=>10,'november'=>11,'desember'=>12
        ];
        $mo = $map[$bulanNama] ?? 0;
        if ($mo && checkdate($mo, $d, $y)) return sprintf('%04d-%02d-%02d', $y, $mo, $d);
    }
    // fallback to strtotime
    $ts = strtotime($s);
    if ($ts !== false) return date('Y-m-d', $ts);
    return null;
}

?>
