<?php
// Start session and check admin login
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

require_once '../php/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kopi & Kuki</title>
    <link rel="stylesheet" href="../css/ulasan.css">
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
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="admin-container">
        <h2>Ulasan Pelanggan</h2>
        
        <div class="review-filters">
            <a href="?filter=all" class="btn">Semua</a>
            <a href="?filter=approved" class="btn">Disetujui</a>
            <a href="?filter=pending" class="btn">Menunggu</a>
        </div>

        <div class="reviews-list">
            <?php
            // Determine filter
            $filter = $_GET['filter'] ?? 'pending';
            $query = "SELECT * FROM customer_reviews ";
            
            if ($filter === 'approved') {
                $query .= "WHERE approved = 1 ";
            } elseif ($filter === 'pending') {
                $query .= "WHERE approved = 0 ";
            }
            
            $query .= "ORDER BY review_date DESC";
            
            $stmt = $pdo->query($query);
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($reviews):
                foreach ($reviews as $review):
            ?>
            <div class="review-item <?php echo $review['approved'] ? 'approved' : 'pending'; ?>">
                <div class="review-header">
                    <h3><?php echo htmlspecialchars($review['customer_name']); ?></h3>
                    <div class="review-meta">
                        <span class="rating"><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></span>
                        <span class="date"><?php echo date('d M Y', strtotime($review['review_date'])); ?></span>
                    </div>
                </div>
                <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                <div class="review-actions">
                    <?php if (!$review['approved']): ?>
                        <a href="../php/approve_review.php?id=<?php echo $review['id']; ?>" class="btn">Setujui</a>
                    <?php endif; ?>
                    <a href="../php/delete_review.php?id=<?php echo $review['id']; ?>" class="btn btn-danger">Hapus</a>
                </div>
            </div>
            <?php
                endforeach;
            else:
            ?>
            <p>Tidak ada ulasan yang ditemukan.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>