<?php
require '../php/config.php';

// Ambil 5 pesanan terbaru yang sedang diproses
$stmt = $pdo->query("SELECT * FROM orders 
                    WHERE status IN ('pending', 'processing') 
                    ORDER BY status DESC, order_date ASC 
                    LIMIT 5");
$active_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung jumlah pesanan yang selesai hari ini
$stmt = $pdo->query("SELECT COUNT(*) as completed_count FROM orders 
                    WHERE status = 'completed' 
                    AND DATE(order_date) = CURDATE()");
$completed_data = $stmt->fetch(PDO::FETCH_ASSOC);
$completed_count = $completed_data['completed_count'];

// Cek apakah ada pesanan yang baru selesai sejak terakhir diperiksa
session_start();
$last_completed_count = $_SESSION['last_completed_count'] ?? 0;
$new_completed = ($completed_count > $last_completed_count);
$_SESSION['last_completed_count'] = $completed_count;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Kupi & Kuki</title>
    <link rel="stylesheet" href="../css/antrian.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="queue-container">
        <div class="shop-header">
            <h1><i class="fas fa-mug-hot"></i> Antrian Kupi & Kuki</h1>
            <div class="shop-info">
                <div class="current-time">
                    <i class="fas fa-clock"></i>
                    <span id="live-clock"><?php echo date('H:i:s'); ?></span>
                </div>
                <div class="completed-orders">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $completed_count; ?> pesanan selesai hari ini</span>
                </div>
            </div>
        </div>
        
        <div class="queue-status">
            <div class="status-indicator">
                <span class="indicator processing"></span>
                <span>Sedang Diproses</span>
            </div>
            <div class="status-indicator">
                <span class="indicator pending"></span>
                <span>Menunggu</span>
            </div>
        </div>
        
        <div class="orders-list">
            <?php if (empty($active_orders)): ?>
                <div class="empty-queue">
                    <i class="fas fa-check-circle"></i>
                    <p>Tidak ada antrian saat ini</p>
                    <small>Silakan melakukan pemesanan di kasir</small>
                </div>
            <?php else: ?>
                <?php foreach ($active_orders as $order): 
                    $order_date = new DateTime($order['order_date']);
                ?>
                    <div class="queue-item <?php echo $order['status']; ?>">
                        <div class="queue-header">
                            <span class="order-number">#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></span>
                            <span class="customer-name"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                        </div>
                        <div class="queue-details">
                            <span class="order-time"><i class="fas fa-clock"></i> <?php echo $order_date->format('H:i'); ?></span>
                            <span class="order-status">
                                <?php if ($order['status'] == 'processing'): ?>
                                    <i class="fas fa-cog fa-spin"></i> Sedang Diproses
                                <?php else: ?>
                                    <i class="fas fa-hourglass-half"></i> Menunggu
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if ($order['status'] == 'processing'): ?>
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="queue-footer">
            <p>Terima kasih telah memesan di Kupi & Kuki</p>
            <small>Antrian diperbarui setiap 30 detik</small>
        </div>
    </div>

        <!-- Tambahkan elemen audio untuk notifikasi -->
    <audio id="notification-sound" preload="auto">
        <source src="../sounds/antrian.mp3" type="audio/mpeg">
        <source src="../sounds/antrian.ogg" type="audio/ogg">
    </audio>
    
    <script>
        // Perbarui jam secara real-time
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {hour12: false});
            document.getElementById('live-clock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        
        // Animasi progress bar
        document.querySelectorAll('.progress-fill').forEach(bar => {
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 5;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                }
                bar.style.width = `${progress}%`;
            }, 1000);
        });
        
        // Muat ulang otomatis setiap 30 detik
        setTimeout(function(){
            window.location.reload();
        }, 5000);

        // Periksa apakah ada pesanan baru yang selesai
        <?php if ($new_completed): ?>
            window.onload = function() {
                // Mainkan suara notifikasi
                const notificationSound = document.getElementById('notification-sound');
                notificationSound.play().catch(e => console.log("Autoplay prevented: ", e));
                
                // Tampilkan alert visual (opsional)
                const alertBox = document.createElement('div');
                alertBox.className = 'completed-alert';
                alertBox.innerHTML = '<i class="fas fa-check-circle"></i> Pesanan telah selesai!';
                document.body.appendChild(alertBox);
                
                // Hilangkan alert setelah 3 detik
                setTimeout(() => {
                    alertBox.style.opacity = '0';
                    setTimeout(() => alertBox.remove(), 500);
                }, 3000);
            };
        <?php endif; ?>

    </script>
</body>
</html>
