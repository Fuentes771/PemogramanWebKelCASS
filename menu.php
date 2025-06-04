<?php
session_start();
require 'php/config.php';

// Ambil data menu dari database
$stmt = $pdo->query("SELECT * FROM menu ORDER BY id ASC");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupi & Kuki - Menu</title>
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
</head>
<body>

    <header class="navbar">
        <div class="logo">Kupi & Kuki</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="aboutus.php">About Us</a>
            <a href="ContactUs.php">Contact Us</a>
            <a href="php/cart.php" id="openCartBtn">🛒 Keranjang (<span id="cartCount">0</span>)</a>
        </nav>
    </header>

    <header class="hero">
        <h1>Discover the best coffee</h1>
        <p>Rasakan sensasi kopi yang nikmat dan berkualitas tinggi di Bean Scene.</p>
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

    <script>
    let cart = JSON.parse(localStorage.getItem('beanSceneCart')) || [];

    function addToCart(id) {
        const item = <?php echo json_encode($menus); ?>.find(m => m.id === id);
        if (!item) return;

        const found = cart.find(c => c.id === id);
        if (found) {
            found.qty++;
        } else {
            cart.push({...item, qty: 1});
        }
        localStorage.setItem('beanSceneCart', JSON.stringify(cart));
        alert(`Berhasil menambah ${item.name} ke keranjang!`);
        updateCartCount();
    }

    function updateCartCount() {
        const totalQty = cart.reduce((acc, item) => acc + item.qty, 0);
        document.getElementById('cartCount').textContent = totalQty;
    }

    // Inisialisasi hitung keranjang saat halaman dimuat
    updateCartCount();
    </script>
</body>
</html>
