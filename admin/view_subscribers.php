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
</head>
<body>
    <header>
        <div class="logo">Kopi & Kuki Admin</div>
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

    <!-- Tombol Send Coupon dan Lihat History, di dalam section Subscribers List -->
    <div style="text-align: center; margin-top: 20px;">
        <div style="display: inline-flex; gap: 10px;">
           <div class="button-container">
    <button class="send-button" onclick="openCouponModal()">Kirim kupon</button>
    <button class="history-button" onclick="openHistoryModal()">Lihat History Pengiriman Kupon</button>
    </div>
        </div>
    </div>
</section>


    <!-- Modal Send Coupon -->
    <div id="couponModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCouponModal()">&times;</span>
            <h2>Kirim Kupon</h2>

            <?php if (!empty($successMessage)): ?>
                <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
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

                <button type="submit" class="send-button">Kirim ke gmail</button>
            </form>
        </div>
    </div>

    <!-- Modal History -->
    <div id="historyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeHistoryModal()">&times;</span>
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
                            <td><?php echo htmlspecialchars($send['coupon_code']); ?></td>
                            <td><?php echo $send['discount']; ?>%</td>
                            <td>Rp <?php echo number_format($send['max_discount']); ?></td>
                            <td><?php echo $send['expiry_date']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script Modal -->
    <script>
// Modal Functions
function openCouponModal() {
    document.getElementById('couponModal').classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent scrolling
}

function closeCouponModal() {
    document.getElementById('couponModal').classList.remove('active');
    document.body.style.overflow = 'auto'; // Re-enable scrolling
}

function openHistoryModal() {
    document.getElementById('historyModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeHistoryModal() {
    document.getElementById('historyModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeCouponModal();
        closeHistoryModal();
    }
}

// Close with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeCouponModal();
        closeHistoryModal();
    }
});

// Set default expiry date to tomorrow
document.addEventListener('DOMContentLoaded', function() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const formattedDate = tomorrow.toISOString().split('T')[0];
    document.getElementById('expiry_date').value = formattedDate;
    
    // Auto-fill recipient count with total subscribers
    document.getElementById('recipient_count').max = <?php echo count($subscribers); ?>;
    document.getElementById('recipient_count').value = <?php echo count($subscribers); ?>;
});
</script>

</body>
</html>
