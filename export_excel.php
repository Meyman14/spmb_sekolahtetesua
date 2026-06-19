<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_siswa_schema.php';
require_once 'includes/export_excel_helper.php';
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

requireAdmin();
ensureSiswaSchema($conn);

$result = mysqli_query(
    $conn,
    "SELECT * FROM siswa WHERE status = 'terdaftar' ORDER BY nama_lengkap ASC"
);

if (!$result) {
    http_response_code(500);
    exit('Gagal mengambil data siswa.');
}

$columns = exportExcelColumns();
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Data Terdaftar');

$colCount = count($columns);
$lastCol = Coordinate::stringFromColumnIndex($colCount);

foreach ($columns as $index => $col) {
    $colLetter = Coordinate::stringFromColumnIndex($index + 1);
    $sheet->setCellValue($colLetter . '1', $col['label']);
}

$rowNum = 2;
while ($row = mysqli_fetch_assoc($result)) {
    foreach ($columns as $index => $col) {
        $colLetter = Coordinate::stringFromColumnIndex($index + 1);
        $cellRef = $colLetter . $rowNum;
        $value = exportExcelCellValue($row, $col);

        if (exportExcelIsTextColumn($col)) {
            $sheet->setCellValueExplicit($cellRef, $value, DataType::TYPE_STRING);
        } else {
            $sheet->setCellValue($cellRef, $value);
        }
    }
    $rowNum++;
}

mysqli_free_result($result);

$headerRange = 'A1:' . $lastCol . '1';
$sheet->getStyle($headerRange)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'E0E0E0'],
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ],
]);

if ($rowNum > 2) {
    $dataRange = 'A2:' . $lastCol . ($rowNum - 1);
    $sheet->getStyle($dataRange)->applyFromArray([
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_TOP,
            'wrapText' => true,
        ],
    ]);
}

for ($i = 1; $i <= $colCount; $i++) {
    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
}

$sheet->freezePane('A2');
$sheet->setAutoFilter($headerRange);

$filename = 'Data_Terdaftar_SPMB_' . date('Y-m-d_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
