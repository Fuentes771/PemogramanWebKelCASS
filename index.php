<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Clicker+Script&display=swap" rel="stylesheet">
  <title>Beranda</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/navbar.css" />  
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

  <section class="hero">
    <div class="hero-text">
      <h1 class="title">Kupi & Kuki</h1>
      <p class="description">
        Kupi & Kuki adalah tempat di mana aroma kopi segar berpadu sempurna dengan manisnya kuki hangat. Temukan kenyamanan dalam setiap tegukan dan gigitan.
      </p>
      <a href="menu.php" class="order-button">Pesan Sekarang</a>
    </div>
  </section>
</body>
</html>
