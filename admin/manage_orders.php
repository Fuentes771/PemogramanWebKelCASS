<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

require '../php/config.php';

$stmt = $pdo->query("SELECT * FROM orders");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Order</title>
    <link rel="stylesheet" href="../css/manage_orders_style.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    <header>
        <div class="logo">Bean Scene Admin</div>
        <nav>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="add_menu.php">Penambahan Menu</a>
            <a href="manage_orders.php">Manajemen Order</a>
            <a href="view_subscribers.php">View Subscribers</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <section>
        <h2>Daftar Order</h2>
        <table>
            <tr>
                <th>ID Order</th>
                <th>Nama Menu</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo $order['menu_name']; ?></td>
                <td><?php echo $order['quantity']; ?></td>
                <td><?php echo $order['status']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </section>
</body>
</html>
