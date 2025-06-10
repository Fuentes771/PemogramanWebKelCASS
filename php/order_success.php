<?php
session_start();

// Debugging
error_log("Session contents: " . print_r($_SESSION, true));

// Redirect jika tidak ada data order
if (!isset($_SESSION['order_success'])) {
    $_SESSION['checkout_error'] = "Silakan selesaikan proses checkout terlebih dahulu";
    header("Location: checkout.php");
    exit();
}

// Ambil data dari session
$order_data = $_SESSION['order_success'];
$total_amount = $order_data['total_amount'] ?? 0;
$discount_amount = $order_data['discount_amount'] ?? 0;
$payment_method = $order_data['payment_method'] ?? 'Tidak diketahui';
$final_amount = $total_amount - $discount_amount;

// Hapus data session setelah digunakan
unset($_SESSION['order_success']);
unset($_SESSION['applied_coupon']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - Kupi & Kuki</title>
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/order_success.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">Kupi & Kuki</div>
        <nav>
            <a href="../index.php">Home</a>
            <a href="../menu.php">Menu</a>
            <a href="../aboutus.php">About Us</a>
            <a href="../ContactUs.php">Contact Us</a>
        </nav>
    </header>

    <main class="success-container">
        <div class="success-card">
            <h1>Order Berhasil!</h1>
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <div class="order-details <?php echo $discount_amount > 0 ? 'has-discount' : ''; ?>">
                <div class="detail-row">
                    <span>Total Pembayaran:</span>
                    <span>Rp <?php echo number_format($total_amount, 0, ',', '.'); ?></span>
                </div>
                
                <?php if ($discount_amount > 0): ?>
                <div class="detail-row discount-row">
                    <span>Diskon:</span>
                    <span>- Rp <?php echo number_format($discount_amount, 0, ',', '.'); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="detail-row final-amount">
                    <span>Total Akhir:</span>
                    <span>Rp <?php echo number_format($final_amount, 0, ',', '.'); ?></span>
                </div>
                
                <div class="detail-row">
                    <span>Metode Pembayaran:</span>
                    <span><?php echo htmlspecialchars($payment_method); ?></span>
                </div>
            </div>
            
            <p class="success-message"><?php echo $order_data['message'] ?? 'Terima kasih telah memesan di Kupi & Kuki.'; ?></p>
            
            <a href="../menu.php" class="back-to-menu">Kembali ke Menu</a>
        </div>
    </main>

    <audio autoplay>
        <source src="../sounds/notification.mp3" type="audio/mpeg">
        <source src="../sounds/suara.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

</body>
</html>