<?php
session_start();

// Check if there's a successful order
if (!isset($_SESSION['order_success'])) {
    header("Location: ../menu.php");
    exit();
}

// Get order details from session
$order_message = $_SESSION['order_success']['message'];
$payment_method = $_SESSION['order_success']['payment_method'];
$total_amount = $_SESSION['order_success']['total_amount'];

// Clear the success message from session
unset($_SESSION['order_success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupi & Kuki - Order Success</title>
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 5rem;
            color: #4CAF50;
            margin-bottom: 1rem;
        }
        
        .success-message {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .order-details {
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            text-align: left;
        }
        
        .back-to-menu {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.8rem 1.5rem;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .back-to-menu:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    
  <header class="navbar">
    <div class="logo">Kupi & Kui</div>
    <nav>
      <a href="../index.php">Home</a>
      <a href="../menu.php">Menu</a>
      <a href="../aboutus.php">About Us</a>
      <a href="../ContactUs.php">Contact Us</a>
    </nav>
  </header>

  <main class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="success-message"><?php echo htmlspecialchars($order_message); ?></div>
        
        <div class="order-details">
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment_method); ?></p>
            <p><strong>Total Amount:</strong> Rp <?php echo number_format($total_amount, 0, ',', '.'); ?></p>
        </div>
        
        <p>Your order has been received and is being prepared.</p>
        <a href="../menu.php" class="back-to-menu">Back to Menu</a>
  </main>

  <audio autoplay>
    <source src="../sounds/notification.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
  </audio>

</body>
</html>