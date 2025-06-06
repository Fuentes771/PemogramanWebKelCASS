<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM customer_reviews WHERE id = ?");
        $stmt->execute([$id]);
    }
}

header('Location: ../admin/ulasan.php');
exit();
?>