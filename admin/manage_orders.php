<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

require '../php/config.php';

// Proses perubahan status pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_pesanan = (int)$_POST['order_id'];
    $status_baru = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status_baru, $id_pesanan]);
        $_SESSION['admin_message'] = "Status pesanan berhasil diperbarui!";
        
        // Jika status selesai atau dibatalkan, atur ulang antrian
        if ($status_baru === 'completed' || $status_baru === 'cancelled') {
            $stmt = $pdo->prepare("UPDATE orders SET queue_position = NULL WHERE id = ?");
            $stmt->execute([$id_pesanan]);

            // Update posisi antrian untuk pesanan yang masih pending
            $stmt = $pdo->query("SELECT id FROM orders WHERE status = 'pending' ORDER BY order_date ASC");
            $pesanan_pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $posisi = 1;
            foreach ($pesanan_pending as $pesanan) {
                $stmt = $pdo->prepare("UPDATE orders SET queue_position = ? WHERE id = ?");
                $stmt->execute([$posisi, $pesanan['id']]);
                $posisi++;
            }
        }
    } catch (PDOException $e) {
        error_log("Kesalahan database: " . $e->getMessage());
        $_SESSION['admin_error'] = "Terjadi kesalahan saat memperbarui status pesanan.";
    }

    header("Location: manage_orders.php");
    exit();
}

// Ambil semua data pesanan
$stmt = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC");
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <a href="admin_dashboard.php">Dasbor</a>
            <a href="add_menu.php">Tambah Menu</a>
            <a href="manage_orders.php">Manajemen Pesanan</a>
            <a href="view_subscribers.php">Pelanggan Terdaftar</a>
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
            <?php if (empty($pesanan)): ?>
                <p>Tidak ada pesanan yang ditemukan.</p>
            <?php else: ?>
                <?php foreach ($pesanan as $pesanan_item): 
                    $daftar_item = json_decode($pesanan_item['items'], true);
                    $tanggal_pesanan = new DateTime($pesanan_item['order_date']);
                ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Pesanan #<?php echo $pesanan_item['id']; ?></h3>
                            <span class="order-date"><?php echo $tanggal_pesanan->format('d M Y H:i'); ?></span>
                        </div>
                        
                        <div class="customer-info">
                            <p><strong>Pelanggan:</strong> <?php echo htmlspecialchars($pesanan_item['customer_name']); ?></p>
                            <p><strong>Total:</strong> Rp <?php echo number_format($pesanan_item['total_amount'], 0, ',', '.'); ?></p>
                            <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($pesanan_item['payment_method']); ?></p>
                        </div>
                        
                        <div class="order-items">
                            <h4>Daftar Item:</h4>
                            <ul>
                                <?php foreach ($daftar_item as $item): ?>
                                    <li>
                                        <?php echo $item['quantity']; ?>x <?php echo htmlspecialchars($item['name']); ?>
                                        <span>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <?php if (!empty($pesanan_item['notes'])): ?>
                            <div class="order-notes">
                                <h4>Catatan:</h4>
                                <p><?php echo htmlspecialchars($pesanan_item['notes']); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="order-footer">
                            <form method="post" class="status-form">
                                <input type="hidden" name="order_id" value="<?php echo $pesanan_item['id']; ?>">
                                <select name="status" class="status-select">
                                    <option value="pending" <?php echo $pesanan_item['status'] === 'pending' ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="processing" <?php echo $pesanan_item['status'] === 'processing' ? 'selected' : ''; ?>>Diproses</option>
                                    <option value="completed" <?php echo $pesanan_item['status'] === 'completed' ? 'selected' : ''; ?>>Selesai</option>
                                    <option value="cancelled" <?php echo $pesanan_item['status'] === 'cancelled' ? 'selected' : ''; ?>>Dibatalkan</option>
                                </select>
                                <button type="submit" name="update_status" class="update-btn">
                                    <i class="fas fa-sync-alt"></i> Perbarui
                                </button>
                            </form>
                            
                            <span class="status-badge <?php echo $pesanan_item['status']; ?>">
                                <?php 
                                // Konversi label status
                                switch ($pesanan_item['status']) {
                                    case 'pending': echo 'Menunggu'; break;
                                    case 'processing': echo 'Diproses'; break;
                                    case 'completed': echo 'Selesai'; break;
                                    case 'cancelled': echo 'Dibatalkan'; break;
                                    default: echo ucfirst($pesanan_item['status']);
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
