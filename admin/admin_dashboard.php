<?php
$host = "localhost";
$user = "root"; // ganti jika username DB Anda berbeda
$pass = "";     // ganti jika ada password
$db   = "toko_kopi"; // nama database Anda

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>

<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// Ambil data total
$menu_query = mysqli_query($conn, "SELECT COUNT(*) AS total_menu FROM menu");
$menu_data = mysqli_fetch_assoc($menu_query);
$total_menu = $menu_data['total_menu'];

$order_query = mysqli_query($conn, "SELECT COUNT(*) AS total_order FROM orders");
$order_data = mysqli_fetch_assoc($order_query);
$total_order = $order_data['total_order'];

$today_query = mysqli_query($conn, "SELECT COUNT(*) AS today_order FROM orders WHERE DATE(order_date) = CURDATE()");
$today_data = mysqli_fetch_assoc($today_query);
$today_order = $today_data['today_order'];

$subs_query = mysqli_query($conn, "SELECT COUNT(*) AS total_subscriber FROM subscribers");
$subs_data = mysqli_fetch_assoc($subs_query);
$total_subscriber = $subs_data['total_subscriber'];
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
            <a href="ulasan.php">Ulasan</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>


    <section>
        <h2>Selamat datang di Dashboard Admin</h2>
        <p>Pilih opsi di atas untuk mengelola menu dan order.</p>
        <div class="card">
        <h3><?= $total_menu ?></h3>
        <p>Total Menu</p>
        </div>
        <div class="card">
        <h3><?= $total_order ?></h3>
        <p>Total Order</p>
        </div>
        <div class="card">
        <h3><?= $today_order ?></h3>
        <p>Order Hari Ini</p>
        </div>
        <div class="card">
        <h3><?= $total_subscriber ?></h3>
        <p>Subscriber</p>
        </div>

    <h3>Order Terbaru</h3>
<table class="order-table">
    <tr>
        <th>ID</th>
        <th>Nama Customer</th>
        <th>Tanggal</th>
        <th>Total</th>
        <th>Status</th>
    </tr>
    <?php
    $recent_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
    while ($row = mysqli_fetch_assoc($recent_orders)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['customer_name']}</td>
                <td>{$row['order_date']}</td>
                <td>Rp " . number_format($row['total_amount'], 0, ',', '.') . "</td>
                <td>{$row['status']}</td>
              </tr>";
    }
    ?>
</table>

    </div>
</div>

    </section>
</body>
</html>
