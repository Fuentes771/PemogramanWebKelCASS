<!DOCTYPE html> 
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Hubungi Kami</title>
  <link rel="stylesheet" href="css/contactUs.css" />
  <link rel="stylesheet" href="css/navbar.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Clicker+Script&display=swap" rel="stylesheet">
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

 <!-- Cangkir kopi kiri dan kanan -->
  <img src="img/Coffee_Left.png" class="coffee-cup left" alt="Cangkir Kopi Kiri" />
  <img src="img/Coffee_Right.png" class="coffee-cup right" alt="Cangkir Kopi Kanan" />

  <!-- Form berlangganan -->
  <section class="subscribe-section">
    <div class="content-wrapper">
      <h1>Berlangganan untuk Penawaran dari Kami</h1>
      <p>Jangan lewatkan berita terkini, pembaruan, tips menarik, dan penawaran spesial dari kami.</p>

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
                  echo '<div class="success-message">Terima kasih sudah berlangganan!</div>';
              } catch (PDOException $e) {
                  if ($e->getCode() == 23000) {
                      echo '<div class="error-message">Email sudah terdaftar.</div>';
                  } else {
                      echo '<div class="error-message">Kesalahan: ' . $e->getMessage() . '</div>';
                  }
              }
          } else {
              echo '<div class="error-message">Alamat email tidak valid.</div>';
          }
      }
  ?>

    <form method="POST" action="">
      <input type="email" name="email" placeholder="Masukkan email Anda" required />
      <button type="submit">Berlangganan</button>
    </form>
  </section>

  <!-- Bagian footer dengan gambar sebagai latar belakang -->
  <footer class="footer">
    <div class="footer-overlay">
      <div class="footer-container">
        <div class="footer-column">
          <h2>Kupi & Kuki</h2>
          <p>Awali harimu dengan kopi dan kue terbaik pilihan kami. Nikmati rasa, nikmati hidup — hanya di Kupi & Kuki.</p>
          <div class="social-icons">
            <a href="#"><img src="img/faceb.png" alt="Facebook"/></a>
            <a href="#"><img src="img/Inst.png" alt="Instagram"/></a>
            <a href="#"><img src="img/twit.png" alt="Twitter"/></a>
            <a href="#"><img src="img/you.png" alt="YouTube"/></a>
          </div>
        </div>

        <div class="footer-column">
          <h3>Tentang</h3>
          <ul>
            <li>Menu</li>
            <li>Fitur</li>
            <li>Berita & Blog</li>
            <li>Bantuan & Dukungan</li>
          </ul>
        </div>

        <div class="footer-column">
          <h3>Perusahaan</h3>
          <ul>
            <li>Cara Kerja Kami</li>
            <li>Syarat Layanan</li>
            <li>Harga</li>
            <li>FAQ</li>
          </ul>
        </div>

        <div class="footer-column">
          <h3>Hubungi Kami</h3>
          <p>Jl. Prof. Dr. Sumantri Brojonegoro No. 1 Bandar Lampung, 35145, INDONESIA.</p>
          <p>+62 85712345678</p>
          <p>kopikukicass@gmail.com</p>
          <p>www.Kopikuki.com</p>
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
