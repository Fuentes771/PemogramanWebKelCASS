<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

$conn = new mysqli("localhost", "root", "", "toko_kopi");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$start_date = $_POST['start_date'] ?? null;
$end_date   = $_POST['end_date'] ?? null;

// Validasi input tanggal sederhana
if (!$start_date || !$end_date) {
    die("Tanggal mulai dan tanggal akhir harus diisi.");
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Penjualan');

// Header kolom
$headers = ['ID Order', 'Nama Customer', 'Tanggal Order', 'Produk (Qty & Note)', 'Total Pembayaran (Rp)', 'Metode Pembayaran', 'Status Pembayaran'];
$sheet->fromArray($headers, null, 'A1');

// Ambil data orders dalam rentang tanggal
$stmt = $conn->prepare("
    SELECT * FROM orders 
    WHERE DATE(order_date) BETWEEN ? AND ? 
    ORDER BY order_date DESC
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$rowNum = 2;
$totalKeseluruhan = 0;

while ($row = $result->fetch_assoc()) {
    // Ambil produk per order
    $produkList = [];
    $itemStmt = $conn->prepare("SELECT product_name, quantity, price, note FROM order_items WHERE order_id = ?");
    $itemStmt->bind_param("i", $row['id']);
    $itemStmt->execute();
    $itemResult = $itemStmt->get_result();

    while ($item = $itemResult->fetch_assoc()) {
        $produkList[] = $item['quantity'] . 'x ' . $item['product_name'] 
                        . ' (Rp ' . number_format($item['price'], 0, ',', '.') . ')' 
                        . ($item['note'] ? "\nCatatan: " . $item['note'] : '');
    }
    $produkStr = implode("\n", $produkList);

    $totalKeseluruhan += $row['total_amount'];

    $sheet->fromArray([
        $row['id'],
        $row['customer_name'],
        date('d-m-Y H:i', strtotime($row['order_date'])),
        $produkStr,
        $row['total_amount'],
        $row['payment_method'],
        ucfirst($row['status'])
    ], null, 'A' . $rowNum);

    $rowNum++;
}

// Tambah baris total
$sheet->setCellValue("D$rowNum", 'TOTAL');
$sheet->setCellValue("E$rowNum", $totalKeseluruhan);

// Format kolom angka dan header
$sheet->getStyle("A1:G1")->getFont()->setBold(true);
$sheet->getStyle("A1:G$rowNum")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Format kolom total pembayaran jadi format mata uang Rupiah
$sheet->getStyle("E2:E$rowNum")->getNumberFormat()->setFormatCode('"Rp" #,##0');

// Background warna header
$sheet->getStyle("A1:G1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');

// Auto width kolom
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Wrap text khusus di kolom Produk (D)
$sheet->getStyle("D2:D$rowNum")->getAlignment()->setWrapText(true);

// Output file dengan nama dinamis
$filename = "Laporan_Penjualan_{$start_date}_sampai_{$end_date}.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
