<?php
session_start();
require '../php/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Akses tidak diizinkan']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$ids = $data['ids'] ?? [];

// Filter hanya id yang numeric (integer positif)
$ids = array_filter($ids, fn($id) => ctype_digit(strval($id)));

if (empty($ids)) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada item yang valid dipilih']);
    exit;
}

try {
    $pdo->beginTransaction();

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("DELETE FROM coupon_sends WHERE id IN ($placeholders)");

    $stmt->execute(array_values($ids));

    $deletedCount = $stmt->rowCount();

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'deleted_count' => $deletedCount,
        'message' => "$deletedCount item berhasil dihapus"
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
