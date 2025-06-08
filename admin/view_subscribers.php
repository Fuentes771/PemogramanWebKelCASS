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

// Ambil history pengiriman kupon
$stmt = $pdo->query("SELECT * FROM coupon_sends ORDER BY sent_at DESC");
$coupon_sends = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subscribers</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/view_subscribers_style.css">
    <style>
        /* Styling tambahan untuk tabel history */
        .coupon-history table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .coupon-history th, .coupon-history td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .coupon-history th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
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

       <div class="form-row">
        <div class="form-group">
            <label for="discount">Diskon Kupon (%)</label>
            <input type="number" id="discount" name="discount"
                   min="1" max="100" required
                   placeholder="Masukkan diskon kupon (%)">
        </div>

        <span class="input-separator">s/d</span>

        <div class="form-group">
            <input class="input-no-label" type="number" id="max_discount" name="max_discount" min="1000" step="1000" required placeholder="Masukkan diskon maksimal (Rp)">
        </div>
    </div>

    <div class="form-group">
        <label for="recipient_count">Jumlah Penerima</label>
        <input type="number" id="recipient_count" name="recipient_count"
               min="1" max="<?php echo count($subscribers); ?>" required
               placeholder="Masukkan jumlah penerima">
    </div>

    <div class="form-group">
        <label for="expiry_date">Berlaku Sampai Tanggal:</label>
        <input type="date" id="expiry_date" name="expiry_date" required>
    </div>

        <button type="submit" class="send-button">Send to gmail</button>
    </form>
    </section>

    <!-- Tambahan: History Pengiriman Kupon -->
    <section class="coupon-history">
        <h2>History Pengiriman Kupon</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal Kirim</th>
                    <th>Email Penerima</th>
                    <th>Kode Kupon</th>
                    <th>Diskon (%)</th>
                    <th>Max Diskon (Rp)</th>
                    <th>Expired Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($coupon_sends)): ?>
                    <tr><td colspan="6">Belum ada kupon yang dikirim.</td></tr>
                <?php else: ?>
                    <?php foreach ($coupon_sends as $send): ?>
                    <tr>
                        <td><?php echo $send['sent_at']; ?></td>
                        <td><?php echo htmlspecialchars($send['recipient_email']); ?></td>
                        <td><?php echo $send['coupon_code']; ?></td>
                        <td><?php echo $send['discount']; ?>%</td>
                        <td>Rp <?php echo number_format($send['max_discount']); ?></td>
                        <td><?php echo $send['expiry_date']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

</body>
</html>
