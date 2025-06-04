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
            <a href="#" id="openCartBtn">🛒 Keranjang (<span id="cartCount">0</span>)</a>
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

    <!-- Cart Sidebar -->
    <div id="cartSidebar" class="cart-sidebar">
        <div class="cart-sidebar-content">
            <span class="close" onclick="closeCart()">&times;</span>
            <h2>Keranjang Belanja</h2>
            <div id="cartItems"></div>
            <div id="cartTotal"></div>
            <form id="checkoutForm" onsubmit="return handleCheckout(event)">
                <input type="text" id="buyerName" placeholder="Nama Anda" required>
                <input type="text" id="buyerPhone" placeholder="Nomor HP" required>
                <button type="submit" class="btn-order">Bayar</button>
            </form>
            <div id="checkoutSuccess" style="display:none;">
                Pembayaran berhasil! Terima kasih sudah memesan.
            </div>
        </div>
    </div>

    <script>
    // Cart Management
    const CART_KEY = 'kupiKukiCart';
    let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
    
    // Initialize cart count on page load
    updateCartCount();
    
    function addToCart(id) {
        const menuItems = <?php echo json_encode($menus); ?>;
        const item = menuItems.find(m => m.id === id);
        
        if (!item) {
            console.error('Item not found');
            return;
        }
        
        const existingItem = cart.find(c => c.id === id);
        
        if (existingItem) {
            existingItem.qty += 1;
        } else {
            cart.push({...item, qty: 1});
        }
        
        saveCart();
        updateCartCount();
        showCart();
    }
    
    function removeCartItem(id) {
        cart = cart.filter(item => item.id !== id);
        saveCart();
        updateCartCount();
        renderCartItems();
    }
    
    function updateCartItem(id, newQty) {
        const item = cart.find(item => item.id === id);
        if (item) {
            item.qty = Math.max(1, newQty);
            saveCart();
            renderCartItems();
        }
    }
    
    function saveCart() {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
    }
    
    function updateCartCount() {
        const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
        document.getElementById('cartCount').textContent = totalQty;
    }
    
    // Cart UI Functions
    function showCart() {
        renderCartItems();
        document.getElementById('cartSidebar').classList.add('open');
    }
    
    function closeCart() {
        document.getElementById('cartSidebar').classList.remove('open');
    }
    
    function renderCartItems() {
        const cartItemsDiv = document.getElementById('cartItems');
        
        if (cart.length === 0) {
            cartItemsDiv.innerHTML = '<p>Keranjang kosong</p>';
            document.getElementById('cartTotal').textContent = '';
            document.getElementById('checkoutForm').style.display = 'none';
            return;
        }
        
        let html = '';
        cart.forEach(item => {
            html += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h4>${item.name}</h4>
                        <p>Rp ${item.price.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="cart-item-controls">
                        <button onclick="updateCartItem(${item.id}, ${item.qty - 1})">-</button>
                        <span>${item.qty}</span>
                        <button onclick="updateCartItem(${item.id}, ${item.qty + 1})">+</button>
                        <button onclick="removeCartItem(${item.id})">Hapus</button>
                    </div>
                    <div class="cart-item-total">
                        Rp ${(item.price * item.qty).toLocaleString('id-ID')}
                    </div>
                </div>
            `;
        });
        
        cartItemsDiv.innerHTML = html;
        
        const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        document.getElementById('cartTotal').textContent = `Total: Rp ${total.toLocaleString('id-ID')}`;
        document.getElementById('checkoutForm').style.display = 'block';
        document.getElementById('checkoutSuccess').style.display = 'none';
    }
    
    // Checkout Handling
    function handleCheckout(e) {
        e.preventDefault();
        
        const name = document.getElementById('buyerName').value.trim();
        const phone = document.getElementById('buyerPhone').value.trim();
        
        // Simple validation
        if (!name || !phone) {
            alert('Harap isi nama dan nomor HP');
            return false;
        }
        
        // In a real app, you would send this to your server
        console.log('Checkout data:', { name, phone, cart });
        
        // Show success message
        document.getElementById('checkoutForm').style.display = 'none';
        document.getElementById('checkoutSuccess').style.display = 'block';
        
        // Clear cart after checkout
        cart = [];
        saveCart();
        updateCartCount();
        
        // Close cart after 3 seconds
        setTimeout(() => {
            closeCart();
            // Reset form
            document.getElementById('checkoutForm').reset();
            document.getElementById('checkoutForm').style.display = 'block';
            document.getElementById('checkoutSuccess').style.display = 'none';
        }, 3000);
        
        return false;
    }
    
    // Event listeners
    document.getElementById('openCartBtn').addEventListener('click', function(e) {
        e.preventDefault();
        showCart();
    });
    </script>
</body>
</html>