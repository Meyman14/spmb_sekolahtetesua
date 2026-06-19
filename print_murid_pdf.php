<?php
/**
 * PDF Generator - Formulir Pendaftaran Siswa Baru
 * Layout: Fixed 3-column table (60mm Label + 5mm Colon + 120mm Value)
 * Auto Page Break dengan penuh kontrol atas halaman
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/helpers_date.php';
require_once __DIR__ . '/includes/role.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'ID murid tidak valid.';
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM siswa WHERE id = ? AND status = 'terdaftar' LIMIT 1");
if (!$stmt) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Gagal menyiapkan query database.';
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$murid = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$murid) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Data murid tidak ditemukan.';
    exit;
}

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

function safeText(string $text): string {
    return utf8_decode(trim($text));
}

function safeValue($value): string {
    $value = trim((string) $value);
    return $value !== '' ? $value : '-';
}

function findBlankoPath(): ?string {
    $dir = __DIR__ . '/uploads';
    if (!is_dir($dir)) return null;
    
    $files = glob($dir . '/blanko-pendaftaran.*');
    if (empty($files)) return null;
    
    usort($files, function ($a, $b) {
        return filemtime($b) <=> filemtime($a);
    });
    
    $newest = $files[0];
    foreach ($files as $i => $f) {
        if ($i > 0) @unlink($f);
    }
    
    return $newest;
}

function getImageType(string $path): ?string {
    $info = @getimagesize($path);
    if (!$info || empty($info[2])) return null;
    
    switch ($info[2]) {
        case IMAGETYPE_PNG: return 'PNG';
        case IMAGETYPE_JPEG: return 'JPG';
        case IMAGETYPE_GIF: return 'GIF';
        default: return null;
    }
}

function resolvePhotoPath(?string $path): ?string {
    if (empty($path)) return null;
    
    $candidates = [
        $path,
        __DIR__ . '/' . ltrim($path, '/\\'),
        __DIR__ . '/uploads/' . basename($path),
        __DIR__ . '/uploads/foto/' . basename($path),
        __DIR__ . '/uploads/foto_siswa/' . basename($path),
    ];
    
    foreach ($candidates as $c) {
        if (is_file($c)) return $c;
    }
    
    return null;
}

function maybe_resample_image(string $path): ?string {
    if (!function_exists('getimagesize')) return null;
    $info = @getimagesize($path);
    if (!$info) return null;
    
    $mime = $info['mime'] ?? '';
    if (!in_array($mime, ['image/jpeg','image/png','image/gif'])) return null;
    if (!function_exists('imagecreatetruecolor')) return null;
    
    switch ($mime) {
        case 'image/jpeg': $src = @imagecreatefromjpeg($path); break;
        case 'image/png': $src = @imagecreatefrompng($path); break;
        case 'image/gif': $src = @imagecreatefromgif($path); break;
        default: return null;
    }
    
    if (!$src) return null;
    $w = imagesx($src);
    $h = imagesy($src);
    
    if ($w <= 0 || $h <= 0) {
        imagedestroy($src);
        return null;
    }
    
    $dst = imagecreatetruecolor($w, $h);
    if ($mime === 'image/png') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $w, $h, $transparent);
    }
    
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $w, $h);
    $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'pdf_' . uniqid() . '.jpg';
    $ok = @imagejpeg($dst, $tmp, 92);
    imagedestroy($src);
    imagedestroy($dst);
    
    if ($ok && is_file($tmp)) {
        $GLOBALS['temp_files'][] = $tmp;
        return $tmp;
    }
    
    return null;
}

// ============================================================================
// LAYOUT & RENDERING FUNCTIONS
// ============================================================================

// Layout constants (in mm)
const COL1_X = 15;      // Label column X
const COL1_W = 60;      // Label column width
const COL2_X = 75;      // Colon column X
const COL2_W = 5;       // Colon column width
const COL3_X = 80;      // Value column X
const COL3_W = 120;     // Value column width

const ROW_HEIGHT = 8.0; // Row height in mm
const TITLE_FONT_SIZE = 12;
const DATA_FONT_SIZE = 11;
const PAGE_HEIGHT = 297;
const MARGIN_BOTTOM = 20;
const TOP_MARGIN = 15;

// Calculate section height in mm
function calcSectionHeight(int $rowCount): float {
    return 7 + ($rowCount * ROW_HEIGHT);
}

// Check page break for section - if section doesn't fit, start new page
function checkPageBreakForSection(\Fpdf\Fpdf $pdf, float $sectionHeight): void {
    $currentY = $pdf->GetY();
    $availableSpace = PAGE_HEIGHT - MARGIN_BOTTOM - $currentY;
    
    if ($sectionHeight > $availableSpace) {
        $pdf->AddPage();
        $pdf->SetY(TOP_MARGIN);
    }
}

// Render single data row with fixed 3-column layout
function renderRow(\Fpdf\Fpdf $pdf, string $label, string $value): void {
    $y = $pdf->GetY();
    
    // Column 1: Label (60mm, left-aligned)
    $pdf->SetFont('Arial', 'B', DATA_FONT_SIZE);
    $pdf->SetXY(COL1_X, $y);
    $displayLabel = safeText($label);
    if (strlen($displayLabel) > 45) $displayLabel = substr($displayLabel, 0, 42) . '...';
    $pdf->Cell(COL1_W, ROW_HEIGHT, $displayLabel, 0, 0, 'L');
    
    // Column 2: Colon (5mm, centered)
    $pdf->SetFont('Arial', 'B', DATA_FONT_SIZE);
    $pdf->SetXY(COL2_X, $y);
    $pdf->Cell(COL2_W, ROW_HEIGHT, ':', 0, 0, 'C');
    
    // Column 3: Value (120mm, left-aligned, NO WRAP)
    $pdf->SetFont('Arial', '', DATA_FONT_SIZE);
    $pdf->SetXY(COL3_X, $y);
    $displayValue = safeText(safeValue($value));
    if (strlen($displayValue) > 100) $displayValue = substr($displayValue, 0, 97) . '...';
    $pdf->Cell(COL3_W, ROW_HEIGHT, $displayValue, 0, 1, 'L');
}

// Render section: title + rows with page break control
function renderSection(\Fpdf\Fpdf $pdf, string $title, array $rows): void {
    $sectionHeight = calcSectionHeight(count($rows));
    checkPageBreakForSection($pdf, $sectionHeight);
    
    // Title
    $y = $pdf->GetY();
    $pdf->SetFont('Arial', 'B', TITLE_FONT_SIZE);
    $pdf->SetTextColor(0, 51, 102);
    $pdf->SetXY(COL1_X, $y);
    $pdf->Cell(0, 7, safeText($title), 0, 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(1);
    
    // Data rows
    foreach ($rows as $row) {
        renderRow($pdf, $row[0], $row[1]);
    }
    
    $pdf->Ln(6);
}

$GLOBALS['temp_files'] = [];




// ============================================================================
// MAIN EXECUTION
// ============================================================================

$blankoPath = findBlankoPath();
if (!$blankoPath) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'File header (blanko-pendaftaran.png) tidak ditemukan di folder uploads.';
    exit;
}

// Initialize PDF with auto page break enabled
$pdf = new \Fpdf\Fpdf('P', 'mm', 'A4');
$pdf->SetAutoPageBreak(true, MARGIN_BOTTOM);
$pdf->SetMargins(0, 0, 0);
$pdf->SetLineWidth(0.4);
$pdf->AddPage();

// ============================================================================
// PAGE 1: HEADER + ADMIN BLOCK
// ============================================================================

// 1. Header image (210x50mm at X=0, Y=0)
$imgType = getImageType($blankoPath);
if ($imgType) {
    $pdf->Image($blankoPath, 0, 0, 210, 50, $imgType);
}

// 2. Student photo (X=15, Y=60, 30x40mm)
$photoPath = resolvePhotoPath($murid['file_foto'] ?? null);
if ($photoPath) {
    $photoResized = maybe_resample_image($photoPath);
    $photoToUse = $photoResized ?? $photoPath;
    $photoImgType = getImageType($photoToUse) ?? 'JPG';
    $pdf->Image($photoToUse, 15, 60, 30, 40, $photoImgType);
}

// Photo border
$pdf->SetDrawColor(80, 80, 80);
$pdf->SetLineWidth(0.5);
$pdf->Rect(15, 60, 30, 40);

// 3. F-PD box and date (X=150, Y=60)
// F-PD box
$pdf->SetFillColor(220, 220, 220);
$pdf->SetDrawColor(100, 100, 100);
$pdf->Rect(150, 60, 50, 12, 'DF');

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetXY(150, 62);
$pdf->Cell(50, 8, 'F-PD', 0, 0, 'C');

// Date parsing
$regDate = $murid['tanggal_pendaftaran'] ?? date('Y-m-d');
$dt = date_parse($regDate);
$day = str_pad($dt['day'] ?? 0, 2, '0', STR_PAD_LEFT);
$month = str_pad($dt['month'] ?? 0, 2, '0', STR_PAD_LEFT);
$year = str_pad($dt['year'] ?? 0, 4, '0', STR_PAD_LEFT);

// Tanggal label
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(150, 74);
$pdf->Cell(50, 4, 'Tanggal:', 0, 1, 'R');

// Date boxes
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.3);

$boxH = 6;
$boxW = 12;

// Day
$pdf->Rect(150, 80, $boxW, $boxH);
$pdf->SetXY(150, 80);
$pdf->Cell($boxW, $boxH, $day, 0, 0, 'C');

// Month
$pdf->Rect(150 + $boxW + 2, 80, $boxW, $boxH);
$pdf->SetXY(150 + $boxW + 2, 80);
$pdf->Cell($boxW, $boxH, $month, 0, 0, 'C');

// Year
$pdf->Rect(150 + 2*($boxW + 2), 80, 20, $boxH);
$pdf->SetXY(150 + 2*($boxW + 2), 80);
$pdf->Cell(20, $boxH, $year, 0, 0, 'C');

// ============================================================================
// DATA SECTIONS (with auto page break)
// ============================================================================

$pdf->SetY(105);

renderSection($pdf, 'A. Data Pribadi', [
    ['Nama Lengkap', $murid['nama_lengkap'] ?? ''],
    ['NISN', $murid['nisn'] ?? ''],
    ['Jenis Kelamin', $murid['jenis_kelamin'] ?? ''],
    ['Agama', $murid['agama'] ?? ''],
    ['NIK', $murid['nik'] ?? ''],
    ['No. KK', $murid['no_kk'] ?? ''],
    ['Tempat, Tgl Lahir', trim(($murid['tempat_lahir'] ?? '') . ', ' . ($murid['tanggal_lahir'] ? format_date_indonesia($murid['tanggal_lahir']) : ''))],
    ['Pernah TK', $murid['pernah_tk'] ?? ''],
    ['Nama TK', ($murid['pernah_tk'] ?? '') === 'Ya' ? $murid['nama_tk'] ?? '' : '-'],
    ['Berkebutuhan Khusus', $murid['berkebutuhan_khusus'] ?? ''],
]);

renderSection($pdf, 'B. Alamat', [
    ['Alamat', $murid['alamat'] ?? ''],
    ['Dusun / Kelurahan', trim(($murid['dusun'] ?? '') . ($murid['kelurahan'] ? ', ' . $murid['kelurahan'] : ''))],
    ['Kecamatan / Kode Pos', trim(($murid['kecamatan'] ?? '') . ($murid['kode_pos'] ? ' — ' . $murid['kode_pos'] : ''))],
    ['Moda Transportasi', $murid['moda_transportasi'] ?? ''],
    ['Anak Ke-', $murid['anak_keberapa'] ?? ''],
]);

renderSection($pdf, 'C. Data Orang Tua / Wali', [
    ['Nama Ayah', $murid['nama_ayah'] ?? ''],
    ['NIK / Th. Lahir Ayah', trim(($murid['nik_ayah'] ?? '') . ' / ' . ($murid['tahun_lahir_ayah'] ?? ''))],
    ['Pendidikan / Pekerjaan Ayah', trim(($murid['pendidikan_ayah'] ?? '') . ' / ' . ($murid['pekerjaan_ayah'] ?? ''))],
    ['Nama Ibu', $murid['nama_ibu'] ?? ''],
    ['NIK / Th. Lahir Ibu', trim(($murid['nik_ibu'] ?? '') . ' / ' . ($murid['tahun_lahir_ibu'] ?? ''))],
    ['Pendidikan / Pekerjaan Ibu', trim(($murid['pendidikan_ibu'] ?? '') . ' / ' . ($murid['pekerjaan_ibu'] ?? ''))],
    ['Nama Wali', $murid['nama_wali'] ?? ''],
    ['No. HP / Email', trim(($murid['no_hp'] ?? '') . ' / ' . ($murid['email'] ?? ''))],
]);

renderSection($pdf, 'D. Data Periodik', [
    ['Tinggi / Berat', trim(($murid['tinggi_badan'] !== null ? $murid['tinggi_badan'] . ' cm' : '') . ' / ' . ($murid['berat_badan'] !== null ? $murid['berat_badan'] . ' kg' : ''))],
    ['Lingkar Kepala', $murid['lingkar_kepala'] ? $murid['lingkar_kepala'] . ' cm' : ''],
    ['Jarak / Waktu', trim(($murid['jarak_sekolah'] ?? '') . ' km / ' . ($murid['waktu_tempuh'] ?? ''))],
    ['Jumlah Saudara', $murid['jumlah_saudara'] ?? ''],
    ['Hobi', $murid['hobi'] ?? ''],
    ['Cita-cita', $murid['cita_cita'] ?? ''],
]);

renderSection($pdf, 'E. Informasi Administrasi', [
    ['Tanggal Pendaftaran', $murid['tanggal_pendaftaran'] ? format_date_indonesia($murid['tanggal_pendaftaran']) : format_date_indonesia(date('Y-m-d'))],
    ['Petugas Pendaftar', namaPetugasPendaftar($murid)],
    ['Status Pendaftaran', $murid['status'] ?? ''],
]);

// ============================================================================
// OUTPUT
// ============================================================================

$pdf->SetDisplayMode('real', 'default');
$pdf->Output('I', 'formulir-pendaftaran-' . $id . '.pdf');

// Cleanup temp files
foreach ($GLOBALS['temp_files'] as $file) {
    if (is_file($file)) @unlink($file);
}
