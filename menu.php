<?php
session_start();
require 'php/config.php';
require 'php/cart_functions.php';

// Ambil data menu dari database
try {
    $stmt = $pdo->query("SELECT * FROM menu ORDER BY id ASC");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Kesalahan database: " . $e->getMessage());
    $menus = [];
}

// Hitung total belanja
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Menu</title>
    <link rel="stylesheet" href="css/style2.css" />
    <link rel="stylesheet" href="css/navbar.css" />
    <link rel="stylesheet" href="css/menu.css" />
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
    
<header class="navbar">
    <div class="logo">Kupi & Kuki</div>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="menu.php">Menu</a>
        <a href="aboutus.php">Tentang Kami</a>
        <a href="ContactUs.php">Hubungi Kami</a>
    </nav>
</header>

<header class="hero">
    <h1>Temukan kopi dan kudapan terbaik</h1>
    <p>Rasakan sensasi kopi dan makanan penutup yang nikmat dan berkualitas tinggi di Kupi & Kuki.</p>
</header>

<section class="menu-section" id="menu">
    <h2>Nikmati perpaduan kopi dan kue yang memanjakan.</h2>
    <p>Temukan berbagai varian rasa khas kami yang menggugah selera.</p>
    <div class="menu-grid">
        <?php foreach ($menus as $menu): ?>
            <div class="menu-card">
                <?php if ($menu['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($menu['image']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>" />
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($menu['name']); ?></h3>
                <p>Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?></p>
                <form method="post" class="quantity-selector">
                    <input type="hidden" name="menu_id" value="<?php echo $menu['id']; ?>" />
                    <button type="button" class="quantity-btn minus">-</button>
                    <input type="number" name="quantity" value="1" min="1" class="quantity-input" />
                    <button type="button" class="quantity-btn plus">+</button>
                    <button type="submit" name="add_to_cart" class="btn-order">Tambah ke Keranjang</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Tombol Toggle Keranjang -->
<div class="cart-toggle">
    <i class="fas fa-shopping-cart"></i>
    <?php if (count($_SESSION['cart']) > 0): ?>
        <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
    <?php endif; ?>
</div>

<!-- Sidebar Keranjang -->
<div class="cart-container" id="cartContainer">
    <div class="cart-header">
        <h3>Keranjang Anda</h3>
        <button class="close-cart">&times;</button>
    </div>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <p>Keranjang Anda kosong</p>
    <?php else: ?>
        <form method="post" action="menu.php">
            <div class="cart-items">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="cart-item">
                        <?php if ($item['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" />
                        <?php endif; ?>
                        <div class="cart-item-details">
                            <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="cart-item-price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></div>
                            <div class="cart-item-quantity">
                                <button type="button" class="qty-minus">-</button>
                                <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="qty-input" />
                                <button type="button" class="qty-plus">+</button>
                            </div>
                            <a href="menu.php?remove_item=<?php echo $item['id']; ?>" class="remove-item">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="cart-total">
                    <span>Total:</span>
                    <span>Rp <?php echo number_format($cart_total, 0, ',', '.'); ?></span>
                </div>
                <button type="submit" name="update_cart" class="checkout-btn">Perbarui Keranjang</button>
                <button type="button" class="checkout-btn" style="margin-top: 0.5rem;">Lanjut ke Pembayaran</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<!-- Pesan Notifikasi Keranjang -->
<?php if (isset($_SESSION['cart_message'])): ?>
    <div class="cart-message" id="cartMessage">
        <?php echo $_SESSION['cart_message']; ?>
    </div>
    <?php unset($_SESSION['cart_message']); ?>
<?php endif; ?>

<script src="js/menu.js"></script>
</body>
</html>
