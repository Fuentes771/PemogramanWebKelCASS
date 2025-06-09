<?php
$conn = new mysqli("localhost", "root", "", "toko_kopi");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $end_date   = $_POST['end_date'];

    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=Laporan_Penjualan_{$start_date}_sampai_{$end_date}.csv");

    $output = fopen('php://output', 'w');

    // UTF-8 BOM agar Excel membaca karakter dengan benar
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, ['ID Order', 'Nama Customer', 'Tanggal Order', 'Total Pembayaran (Rp)', 'Status Pembayaran']);

    $stmt = $conn->prepare("SELECT * FROM orders WHERE DATE(order_date) BETWEEN ? AND ? ORDER BY order_date DESC");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['customer_name'],
            date('d-m-Y H:i', strtotime($row['order_date'])),
            'Rp ' . number_format($row['total_amount'], 0, ',', '.'),
            ucfirst($row['status'])
        ]);
    }

    fclose($output);
    exit;
}
?>
