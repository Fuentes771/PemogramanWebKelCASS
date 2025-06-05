<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ContactUs</title>
  <link rel="stylesheet" href="css/contactUs.css" />
    <link rel="stylesheet" href="css/navbar.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Clicker+Script&display=swap" rel="stylesheet">
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

 <!-- Cangkir kopi kiri dan kanan -->

 <!-- Cangkir kopi kiri dan kanan -->
  <img src="img/Coffee_Left.png" class="coffee-cup left" alt="Coffee Cup Left" />
  <img src="img/Coffee_Right.png" class="coffee-cup right" alt="Coffee Cup Right" />

  <!-- Dekorasi bagian atas -->
  <!-- Dekorasi bagian atas -->
  <div class="top-decoration">
    <!-- tidak perlu img di sini -->
  </div>


 

  <!-- Form subscribe -->
  <section class="subscribe-section">
    <div class="content-wrapper">
    <h1>Berlangganan untuk Penawaran dari Kami</h1>
    <p>Jangan lewatkan berita terkini, pembaruan, tips menarik, dan penawaran spesial dari kami. </p>

  <?php
      session_start();
      require 'php/config.php'; // Pastikan Anda sudah mengatur koneksi database di config.php

      if ($_SERVER["REQUEST_METHOD"] === "POST") {
          $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

          if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
              // Simpan email ke database
              $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (:email)");
              $stmt->bindParam(':email', $email);
              
              try {
                  $stmt->execute();
                  echo '<div class="success-message">Thanks for subscribing!</div>';
              } catch (PDOException $e) {
                  if ($e->getCode() == 23000) {
                      echo '<div class="error-message">Email already subscribed.</div>';
                  } else {
                      echo '<div class="error-message">Error: ' . $e->getMessage() . '</div>';
                  }
              }
          } else {
              echo '<div class="error-message">Invalid email address.</div>';
          }
      }
      ?>


    <form method="POST" action="">
      <input type="email" name="email" placeholder="Enter your mail" required />
      <button type="submit">Berlangganan</button>
    </form>
  </section>

  <!-- Bagian footer dengan gambar sebagai background -->
  <footer class="footer">
    <div class="footer-overlay">
      <div class="footer-container">
        <div class="footer-column">
          <h2>Kopi & Kuki</h2>
          <p>Awali harimu dengan kopi dan kue terbaik pilihan kami. Nikmati rasa, nikmati hidup — hanya di Kopi & Kuki.</p>
          <div class="social-icons">
            <a href="#"><img src="img/faceb.png" alt="Facebook"/></a>
            <a href="#"><img src="img/Inst.png" alt="Instagram"/></a>
            <a href="#"><img src="img/twit.png" alt="Twitter"/></a>
            <a href="#"><img src="img/you.png" alt="YouTube"/></a>
          </div>
        </div>

        <div class="footer-column">
          <h3>About</h3>
          <ul>
            <li>Menu</li>
            <li>Features</li>
            <li>News & Blogs</li>
            <li>Help & Supports</li>
          </ul>
        </div>

        <div class="footer-column">
          <h3>Company</h3>
          <ul>
            <li>How we work</li>
            <li>Terms of service</li>
            <li>Pricing</li>
            <li>FAQ</li>
          </ul>
        </div>

        <div class="footer-column">
          <h3>Contact Us</h3>
          <p>Jl. Prof. Dr. Sumantri Brojonegoro No. 1 Bandar Lampung, 35145, INDONESIA. </p>
          <p>+62 85712345678</p>
          <p>KopiKuki@mail.com</p>
          <p>www.Kopikuki.com</p>
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
