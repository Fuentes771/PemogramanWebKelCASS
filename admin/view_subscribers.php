<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

require '../php/config.php';

$stmt = $pdo->query("SELECT * FROM subscribers");
$subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil pesan sukses (jika ada) dari session
$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subscribers</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/view_subscribers_style.css">
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
            <a href="../php/logout.php">Logout</a>
        </nav>
    </header>

    <section>
        <h1>Subscribers List</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Subscribed At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscribers as $subscriber): ?>
                <tr>
                    <td><?php echo $subscriber['id']; ?></td>
                    <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
                    <td><?php echo $subscriber['created_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="coupon-form">
    <h2>Send Coupon</h2>

    <?php if (!empty($successMessage)): ?>
        <div class="success-message"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form action="send_subscribers_email.php" method="post">
        <div class="form-group">
            <input type="number" id="discount" name="discount" min="1" max="100" required placeholder="Masukkan diskon kupon">
        </div>
        <button type="submit" class="send-button">Send to gmail</button>
    </form>
</section>
</body>
</html>
