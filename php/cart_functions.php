<?php
// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart functionality
if (isset($_POST['add_to_cart'])) {
    $menu_id = (int)$_POST['menu_id'];
    $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
        $stmt->execute([$menu_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($item) {
            if (isset($_SESSION['cart'][$menu_id])) {
                $_SESSION['cart'][$menu_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$menu_id] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'quantity' => $quantity
                ];
            }
            $_SESSION['cart_message'] = "Item added to cart!";
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
    }
    header("Location: menu.php");
    exit();
}

// Remove from cart functionality
if (isset($_GET['remove_item'])) {
    $menu_id = (int)$_GET['remove_item'];
    if (isset($_SESSION['cart'][$menu_id])) {
        unset($_SESSION['cart'][$menu_id]);
        $_SESSION['cart_message'] = "Item removed from cart!";
    }
    header("Location: menu.php");
    exit();
}

// Update cart quantities
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $menu_id => $quantity) {
        $menu_id = (int)$menu_id;
        $quantity = max(1, (int)$quantity);
        
        if (isset($_SESSION['cart'][$menu_id])) {
            $_SESSION['cart'][$menu_id]['quantity'] = $quantity;
        }
    }
    $_SESSION['cart_message'] = "Cart updated!";
    header("Location: menu.php");
    exit();
}
?>