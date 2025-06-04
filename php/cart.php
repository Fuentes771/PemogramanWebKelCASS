<?php
session_start();
require 'config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="stylesheet" href="../css/cart.css"> <!-- Tambahkan CSS untuk keranjang -->
</head>
<body>
    <header>
        <h1>Keranjang Anda</h1>
    </header>

    <section>
        <div id="cartItemsContainer"></div>
        <div id="totalPriceContainer"></div>
        <button id="checkoutBtn">Checkout</button>
    </section>

    <script src="../js/cart.js"></script>
</body>
</html>
