<?php
session_start();
require 'php/config.php';

// Get menu data from database
try {
    $stmt = $pdo->query("SELECT * FROM menu ORDER BY id ASC");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $menus = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopi & Kuki - Menu</title>
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    
</head>
<body>

    <header class="navbar">
        <div class="logo">Kopi & Kuki</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="aboutus.php">About Us</a>
            <a href="ContactUs.php">Contact Us</a>
        </nav>
    </header>

    <header class="hero">
        <h1>Discover the best coffee</h1>
        <p>Rasakan sensasi kopi yang nikmat dan berkualitas tinggi di Kupi Kuki.</p>
    </header>

    <section class="menu-section" id="menu">
        <h2>Enjoy a new blend of coffee style</h2>
        <p>Temukan berbagai rasa kopi khas kami yang menggugah selera.</p>
        <div class="menu-grid">
            <?php foreach ($menus as $menu): ?>
                <div class="menu-card">
                    <?php if ($menu['image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($menu['image']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($menu['name']); ?></h3>
                    <p>Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?></p>
                    <button class="btn-order" onclick="addToCart(<?php echo $menu['id']; ?>)">Order Now</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</body>
</html>