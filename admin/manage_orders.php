<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

require '../php/config.php';

// Proses perubahan status pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $_SESSION['admin_message'] = "Status pesanan berhasil diperbarui!";
        
        // Jika status selesai atau dibatalkan, atur ulang antrian
        if ($new_status === 'completed' || $new_status === 'cancelled') {
            $stmt = $pdo->prepare("UPDATE orders SET queue_position = NULL WHERE id = ?");
            $stmt->execute([$order_id]);

            // Update posisi antrian untuk pesanan yang masih pending
            $stmt = $pdo->query("SELECT id FROM orders WHERE status = 'pending' ORDER BY order_date ASC");
            $pending_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $position = 1;
            foreach ($pending_orders as $order) {
                $stmt = $pdo->prepare("UPDATE orders SET queue_position = ? WHERE id = ?");
                $stmt->execute([$position, $order['id']]);
                $position++;
            }
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['admin_error'] = "Terjadi kesalahan saat memperbarui status pesanan.";
    }

    header("Location: manage_orders.php");
    exit();
}

// Ambil semua data pesanan
$stmt = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan</title>
    <link rel="stylesheet" href="../css/manage_orders_style.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">Kupi & Kuki Admin</div>
        <nav>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="add_menu.php">Tambah Menu</a>
            <a href="manage_orders.php">Kelola Pesanan</a>
            <a href="view_subscribers.php">Pelanggan</a>
            <a href="ulasan.php">Ulasan</a>
            <a href="../php/logout.php">Keluar</a>
        </nav>
    </header>

    <section class="order-management">
        <h2>Daftar Pesanan</h2>
        
        <?php if (isset($_SESSION['admin_message'])): ?>
            <div class="admin-message success">
                <?php echo $_SESSION['admin_message']; ?>
                <?php unset($_SESSION['admin_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['admin_error'])): ?>
            <div class="admin-message error">
                <?php echo $_SESSION['admin_error']; ?>
                <?php unset($_SESSION['admin_error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="orders-container">
            <?php if (empty($orders)): ?>
                <p>Tidak ada pesanan yang ditemukan.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): 
                    $items = json_decode($order['items'], true);
                    $order_date = new DateTime($order['order_date']);
                ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <span class="order-date"><?php echo $order_date->format('d M Y H:i'); ?></span>
                        </div>
                        
                        <div class="customer-info">
                            <p><strong>Pelanggan:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p><strong>Total:</strong> Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></p>
                            <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                        </div>
                        
                        <div class="order-items">
                            <h4>Daftar Item:</h4>
                            <ul>
                                <?php foreach ($items as $item): ?>
                                    <li>
                                        <?php echo $item['quantity']; ?>x <?php echo htmlspecialchars($item['name']); ?>
                                        <span>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <?php if (!empty($order['notes'])): ?>
                            <div class="order-notes">
                                <h4>Catatan:</h4>
                                <p><?php echo htmlspecialchars($order['notes']); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="order-footer">
                            <form method="post" class="status-form">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="status-select">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Diproses</option>
                                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Selesai</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Dibatalkan</option>
                                </select>
                                <button type="submit" name="update_status" class="update-btn">
                                    <i class="fas fa-sync-alt"></i> Perbarui
                                </button>
                            </form>
                            
                            <span class="status-badge <?php echo $order['status']; ?>">
                                <?php 
                                // Konversi label status
                                switch ($order['status']) {
                                    case 'pending': echo 'Menunggu'; break;
                                    case 'processing': echo 'Diproses'; break;
                                    case 'completed': echo 'Selesai'; break;
                                    case 'cancelled': echo 'Dibatalkan'; break;
                                    default: echo ucfirst($order['status']);
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>