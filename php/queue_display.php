<?php
require '../php/config.php';

// Ambil 5 pesanan terbaru yang sedang diproses
$stmt = $pdo->query("SELECT * FROM orders 
                    WHERE status IN ('pending', 'processing') 
                    ORDER BY status DESC, order_date ASC 
                    LIMIT 5");
$active_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Coffee Shop</title>
    <link rel="stylesheet" href="../css/queue_display.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="queue-container">
        <h1>Antrian Pesanan</h1>
        <div class="current-time"><?php echo date('H:i:s'); ?></div>
        
        <div class="orders-list">
            <?php if (empty($active_orders)): ?>
                <div class="empty-queue">
                    <i class="fas fa-check-circle"></i>
                    <p>Tidak ada antrian saat ini</p>
                </div>
            <?php else: ?>
                <?php foreach ($active_orders as $order): 
                    $order_date = new DateTime($order['order_date']);
                ?>
                    <div class="queue-item <?php echo $order['status']; ?>">
                        <div class="queue-header">
                            <span class="order-number">#<?php echo $order['id']; ?></span>
                            <span class="customer-name"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                        </div>
                        <div class="queue-details">
                            <span class="order-time"><?php echo $order_date->format('H:i'); ?></span>
                            <span class="order-status"><?php echo ucfirst($order['status']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-refresh setiap 30 detik
        setTimeout(function(){
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>