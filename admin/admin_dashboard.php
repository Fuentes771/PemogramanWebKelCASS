<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        <h2>Selamat datang di Dashboard Admin</h2>
        <p>Pilih opsi di atas untuk mengelola menu dan order.</p>
    </section>
</body>
</html>
