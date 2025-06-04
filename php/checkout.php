<?php
session_start();
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in']);
    exit();
}

// Simpan order ke database
foreach ($data as $item) {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, menu_name, quantity, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $item['name'], $item['qty'], 'Pending']);
}

echo json_encode(['success' => true]);
