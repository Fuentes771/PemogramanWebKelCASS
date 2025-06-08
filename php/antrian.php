<?php
require '../php/config.php';

// Ambil 5 pesanan terbaru yang sedang diproses
$stmt = $pdo->query("SELECT * FROM orders 
                    WHERE status IN ('pending', 'processing') 
                    ORDER BY status DESC, order_date ASC 
                    LIMIT 5");
$active_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get completed orders count for today
$stmt = $pdo->query("SELECT COUNT(*) as completed_count FROM orders 
                    WHERE status = 'completed' 
                    AND DATE(order_date) = CURDATE()");
$completed_data = $stmt->fetch(PDO::FETCH_ASSOC);
$completed_count = $completed_data['completed_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Coffee Shop</title>
    <link rel="stylesheet" href="../css/antrian.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="queue-container">
        <div class="shop-header">
            <h1><i class="fas fa-mug-hot"></i> Coffee Shop Queue</h1>
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
                    <small>Silahkan melakukan pemesanan di kasir</small>
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
                                    <i class="fas fa-cog fa-spin"></i>
                                <?php else: ?>
                                    <i class="fas fa-hourglass-half"></i>
                                <?php endif; ?>
                                <?php echo ucfirst($order['status']); ?>
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
            <p>Terima kasih telah memesan di Coffee Shop kami</p>
            <small>Antrian diperbarui setiap 30 detik</small>
        </div>
    </div>
    
    <script>
        // Update clock in real-time
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {hour12: false});
            document.getElementById('live-clock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        
        // Animate progress bars
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
        
        // Auto-refresh every 30 seconds
        setTimeout(function(){
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>