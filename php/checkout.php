<?php
session_start();
require 'config.php';
require 'cart_functions.php';

// Redirect to menu if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: ../menu.php");
    exit();
}

// Calculate cart total
$cart_total = 0;
$order_items = [];
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $order_items[] = [
        'menu_id' => $item['id'],
        'name' => $item['name'],
        'price' => $item['price'],
        'quantity' => $item['quantity'],
        'image' => $item['image']
    ];
}

// Process checkout if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_order'])) {
    $customer_name = trim($_POST['name']);
    $notes = trim($_POST['notes'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'QRIS'; // Get selected payment method
    
    if (empty($customer_name)) {
        $_SESSION['checkout_error'] = "Please enter your name";
        header("Location: checkout.php");
        exit();
    }

    try {
        // Prepare order data
        $items_json = json_encode($order_items);
        
        // Insert order into database
        $stmt = $pdo->prepare("INSERT INTO orders 
                              (customer_name, total_amount, payment_method, notes, items) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $customer_name,
            $cart_total,
            $payment_method,
            $notes,
            $items_json
        ]);
        
        // Store order success data in session
        $_SESSION['order_success'] = [
            'message' => "Thank you for your order! Your coffee will be ready soon.",
            'payment_method' => $payment_method,
            'total_amount' => $cart_total
        ];
        
        // Clear the cart
        $_SESSION['cart'] = [];
        
        // Redirect to success page
        header("Location: order_success.php");
        exit();
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['checkout_error'] = "Error processing your order. Please try again.";
        header("Location: checkout.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupi & Kuki - Checkout</title>
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/checkout.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    
  <header class="navbar">
    <div class="logo">Kupi & Kui - Checkout</div>
    <nav>
      <a href="../index.php">Home</a>
      <a href="../menu.php">Menu</a>
      <a href="../aboutus.php">About Us</a>
      <a href="../ContactUs.php">Contact Us</a>
    </nav>
  </header>

  <main class="checkout-container">
    <section class="order-summary">
        <h2>Your Order Summary</h2>
        <div class="order-items">
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="order-item">
                    <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div class="item-quantity">x<?php echo $item['quantity']; ?></div>
                    <div class="item-price">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="order-total">
            <span>Total:</span>
            <span>Rp <?php echo number_format($cart_total, 0, ',', '.'); ?></span>
        </div>
    </section>

    <section class="payment-section">
        <h2>Payment Method</h2>
        <div class="payment-methods">
            <div class="payment-method active" data-method="qris">
                <i class="fas fa-qrcode"></i>
                <span>QRIS</span>
            </div>
            <div class="payment-method" data-method="transfer">
                <i class="fas fa-exchange-alt"></i>
                <span>Transfer</span>
            </div>
            <div class="payment-method" data-method="cash">
                <i class="fas fa-money-bill-wave"></i>
                <span>Tunai</span>
            </div>
        </div>

        <div class="payment-details active" id="qris-payment">
            <h3>Scan QR Code Below to Pay</h3>
            <div class="qris-code">
                <!-- Replace with your actual QRIS image -->
                <img src="images/qris_kupi_kuki.png" alt="QRIS Payment Code">
                <p>Scan this QR code using your mobile banking app</p>
            </div>
            <div class="payment-instructions">
                <p><strong>Payment Instructions:</strong></p>
                <ol>
                    <li>Open your mobile banking or e-wallet app</li>
                    <li>Select QRIS payment option</li>
                    <li>Scan the QR code above</li>
                    <li>Confirm the amount (Rp <?php echo number_format($cart_total, 0, ',', '.'); ?>)</li>
                    <li>Complete the payment</li>
                </ol>
            </div>
        </div>

        <div class="payment-details" id="transfer-payment">
            <h3>Bank Transfer Information</h3>
            <div class="bank-details">
                <p><strong>Bank Name:</strong> BCA (Bank Central Asia)</p>
                <p><strong>Account Number:</strong> 1234567890</p>
                <p><strong>Account Name:</strong> Kupi & Kuki</p>
                <p><strong>Amount to Transfer:</strong> Rp <?php echo number_format($cart_total, 0, ',', '.'); ?></p>
            </div>
            <div class="payment-instructions">
                <p><strong>Payment Instructions:</strong></p>
                <ol>
                    <li>Transfer the exact amount to the account above</li>
                    <li>Use your name as the transfer reference</li>
                    <li>Keep the transaction receipt as proof of payment</li>
                    <li>Show the receipt to our staff when collecting your order</li>
                </ol>
            </div>
        </div>

        <div class="payment-details" id="cash-payment">
            <h3>Cash Payment</h3>
            <div class="cash-instructions">
                <p>Please prepare exact change for:</p>
                <p class="cash-amount">Rp <?php echo number_format($cart_total, 0, ',', '.'); ?></p>
                <p>Payment will be completed when you receive your order at the counter.</p>
            </div>
        </div>

        <form method="post" class="order-form">
            <input type="hidden" name="payment_method" id="selected_payment_method" value="QRIS">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="notes">Order Notes (Optional)</label>
                <textarea id="notes" name="notes" placeholder="Special requests, allergies, etc."></textarea>
            </div>
            <button type="submit" name="complete_order" class="complete-order-btn">
                Complete Order
            </button>
        </form>
    </section>
  </main>

  <script src="../js/checkout.js"></script>
  <script>
    // Handle payment method selection
    document.querySelectorAll('.payment-method').forEach(method => {
        method.addEventListener('click', function() {
            // Remove active class from all methods
            document.querySelectorAll('.payment-method').forEach(m => {
                m.classList.remove('active');
            });
            
            // Add active class to clicked method
            this.classList.add('active');
            
            // Get the method type
            const methodType = this.getAttribute('data-method');
            
            // Hide all payment details
            document.querySelectorAll('.payment-details').forEach(detail => {
                detail.classList.remove('active');
            });
            
            // Show selected payment details
            document.getElementById(methodType + '-payment').classList.add('active');
            
            // Update hidden input value
            document.getElementById('selected_payment_method').value = methodType;
        });
    });
  </script>
</body>
</html>