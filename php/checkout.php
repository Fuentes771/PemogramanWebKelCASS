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

// Process coupon code if submitted
$discount_amount = 0;
$coupon_error = '';
$coupon_success = '';
$coupon_code = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_coupon'])) {
    $coupon_code = trim($_POST['coupon_code']);
    
    if (!empty($coupon_code)) {
        try {
            // Check if coupon is valid (tanpa pengecekan email)
            $stmt = $pdo->prepare("SELECT * FROM coupon_sends 
                                  WHERE coupon_code = ? 
                                  AND expiry_date >= CURDATE() 
                                  AND is_used = FALSE");
            $stmt->execute([$coupon_code]);
            $coupon = $stmt->fetch();
            
            if ($coupon) {
                // Calculate discount
                $discount_percentage = $coupon['discount'] / 100;
                $discount_amount = $cart_total * $discount_percentage;
                
                // Apply maximum discount limit
                if ($discount_amount > $coupon['max_discount']) {
                    $discount_amount = $coupon['max_discount'];
                }
                
                // Store coupon info in session
                $_SESSION['applied_coupon'] = [
                    'code' => $coupon_code,
                    'discount_amount' => $discount_amount,
                    'coupon_id' => $coupon['id'],
                    'original_discount' => $coupon['discount'],
                    'max_discount' => $coupon['max_discount']
                ];
                
                $coupon_success = "Kupon berhasil diterapkan! Diskon: " . $coupon['discount'] . 
                                 "% (maks: Rp " . number_format($coupon['max_discount'], 0, ',', '.') . ")";
            } else {
                // Pesan error lebih informatif
                $stmt = $pdo->prepare("SELECT expiry_date < CURDATE() as is_expired, is_used 
                                      FROM coupon_sends 
                                      WHERE coupon_code = ?");
                $stmt->execute([$coupon_code]);
                $status = $stmt->fetch();
                
                if (!$status) {
                    $coupon_error = "Kode kupon tidak ditemukan.";
                } elseif ($status['is_expired']) {
                    $coupon_error = "Kupon sudah kadaluarsa.";
                } elseif ($status['is_used']) {
                    $coupon_error = "Kupon sudah digunakan sebelumnya.";
                } else {
                    $coupon_error = "Kupon tidak valid.";
                }
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $coupon_error = "Terjadi kesalahan sistem. Silakan coba lagi.";
        }
    } else {
        $coupon_error = "Silakan masukkan kode kupon.";
    }
}

// Calculate final total after discount
$final_total = $cart_total - ($_SESSION['applied_coupon']['discount_amount'] ?? 0);
if ($final_total < 0) $final_total = 0;

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
                              (customer_name, total_amount, payment_method, notes, items, discount_amount, coupon_code) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $customer_name,
            $final_total, // Gunakan final_total yang sudah didiskon
            $payment_method,
            $notes,
            $items_json,
            $_SESSION['applied_coupon']['discount_amount'] ?? 0,
            $_SESSION['applied_coupon']['code'] ?? null
        ]);
        
        // Mark coupon as used if applied
        if (isset($_SESSION['applied_coupon'])) {
            $stmt = $pdo->prepare("UPDATE coupon_sends 
                                  SET is_used = TRUE, used_at = NOW() 
                                  WHERE id = ?");
            $stmt->execute([$_SESSION['applied_coupon']['coupon_id']]);
        }
        
        // Di bagian proses checkout (setelah insert order ke database)
        $_SESSION['order_success'] = [
            'message' => "Terima kasih telah memesan!",
            'payment_method' => $payment_method,
            'total_amount' => $cart_total,
            'discount_amount' => $_SESSION['applied_coupon']['discount_amount'] ?? 0,
            'final_amount' => $final_total
        ];

        // Hapus data yang tidak perlu
        unset($_SESSION['cart']);

        header("Location: order_success.php");
        exit();
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['checkout_error'] = "Error processing your order. Please try again.";
        header("Location: checkout.php");
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
    <div class="logo">Kupi & Kuki - Checkout</div>
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
    
    <!-- Coupon Form -->
    <div class="coupon-section">
        <form method="post" class="coupon-form">
            <label for="coupon_code">Kode Kupon:</label>
            <input type="text" id="coupon_code" name="coupon_code" 
                   value="<?php echo htmlspecialchars($coupon_code); ?>" 
                   placeholder="Masukkan kode kupon">
            <button type="submit" name="apply_coupon" class="apply-coupon-btn">Gunakan</button>
        </form>
        
        <?php if (!empty($coupon_error)): ?>
            <div class="coupon-error"><?php echo $coupon_error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($coupon_success)): ?>
            <div class="coupon-success"><?php echo $coupon_success; ?></div>
        <?php endif; ?>
    </div>
    
    <div class="order-subtotal">
        <span>Subtotal:</span>
        <span>Rp <?php echo number_format($cart_total, 0, ',', '.'); ?></span>
    </div>
    
    <?php if ($discount_amount > 0): ?>
        <div class="order-discount">
            <span>Diskon:</span>
            <span>- Rp <?php echo number_format($discount_amount, 0, ',', '.'); ?></span>
        </div>
    <?php endif; ?>
    
    <div class="order-total">
        <span>Total:</span>
        <span>Rp <?php echo number_format($final_total, 0, ',', '.'); ?></span>
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

        <!-- In the QRIS payment section -->
        <div class="payment-details active" id="qris-payment">
            <h3>Scan QR Code Below to Pay</h3>
            <div class="qris-code">
                <img src="../img/qris_kupi_kuki.jpg" alt="QRIS Payment Code">
                <p>Scan this QR code using your mobile banking app</p>
            </div>
            <div class="payment-instructions" data-method="qris">
                <p><strong>Payment Instructions:</strong></p>
                <ol>
                    <li>Open your mobile banking or e-wallet app</li>
                    <li>Select QRIS payment option</li>
                    <li>Scan the QR code above</li>
                    <li>Confirm the amount (Rp <?php echo number_format($final_total, 0, ',', '.'); ?>)</li>
                    <li>Complete the payment</li>
                </ol>
            </div>
        </div>

        <!-- In the Transfer payment section -->
        <div class="payment-details" id="transfer-payment">
            <h3>Bank Transfer Information</h3>
            <div class="bank-details">
                <p><strong>Bank Name:</strong> BNI (Bank Central Asia)</p>
                <p><strong>Account Number:</strong> 1792018463</p>
                <p><strong>Account Name:</strong> M Sulthon Alfarizky</p>
                <p><strong>Amount to Transfer:</strong> Rp <?php echo number_format($final_total, 0, ',', '.'); ?></p>
            </div>
            <div class="payment-instructions" data-method="transfer">
                <p><strong>Payment Instructions:</strong></p>
                <ol>
                    <li>Transfer the exact amount to the account above</li>
                    <li>Use your name as the transfer reference</li>
                    <li>Keep the transaction receipt as proof of payment</li>
                    <li>Show the receipt to our staff when collecting your order</li>
                </ol>
            </div>
        </div>

        <!-- In the Cash payment section -->
        <div class="payment-details" id="cash-payment">
            <h3>Cash Payment</h3>
            <div class="cash-instructions">
                <p>Please prepare exact change for:</p>
                <p class="cash-amount">Rp <?php echo number_format($final_total, 0, ',', '.'); ?></p>
                <p>Payment will be completed when you receive your order at the counter.</p>
            </div>
            <div class="payment-instructions" data-method="cash">
                <p><strong>Payment Instructions:</strong></p>
                <ol>
                    <li>Prepare exact cash amount</li>
                    <li>Inform staff when collecting your order</li>
                    <li>Complete payment at the counter</li>
                </ol>
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

</body>
</html>