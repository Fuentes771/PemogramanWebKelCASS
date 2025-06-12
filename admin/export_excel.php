<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

$conn = new mysqli("localhost", "root", "", "toko_kopi");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$start_date = $_POST['start_date'] ?? null;
$end_date   = $_POST['end_date'] ?? null;

if (!$start_date || !$end_date) {
    die("Tanggal mulai dan tanggal akhir harus diisi.");
}

// Siapkan spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Penjualan');

// Header kolom
$headers = ['ID Order', 'Nama Customer', 'Tanggal Order', 'Produk (Qty & Note)', 'Total Pembayaran (Rp)', 'Metode Pembayaran', 'Status Pembayaran'];
$sheet->fromArray($headers, null, 'A1');

// Ambil data orders
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
    // Ambil produk dari kolom items (JSON)
    $produkList = [];
    $items = json_decode($row['items'], true);

    if (is_array($items)) {
        foreach ($items as $item) {
            $qty = $item['quantity'] ?? 0;
            $name = $item['name'] ?? '-';
            $price = $item['price'] ?? 0;
            $note = $item['note'] ?? '';

            $produkList[] = "{$qty}x {$name} (Rp " . number_format($price, 0, ',', '.') . ")"
                . ($note ? "\nCatatan: " . $note : '');
        }
    } else {
        $produkList[] = 'Data produk tidak valid';
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

// Tambah baris total keseluruhan
$sheet->setCellValue("D$rowNum", 'TOTAL');
$sheet->setCellValue("E$rowNum", $totalKeseluruhan);

// Format tampilan
$sheet->getStyle("A1:G1")->getFont()->setBold(true);
$sheet->getStyle("A1:G$rowNum")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
$sheet->getStyle("E2:E$rowNum")->getNumberFormat()->setFormatCode('"Rp" #,##0');
$sheet->getStyle("A1:G1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');
$sheet->getStyle("D2:D$rowNum")->getAlignment()->setWrapText(true);

foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output file
$filename = "Laporan_Penjualan_{$start_date}_sampai_{$end_date}.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
