<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bean Scene</title>
  <link rel="stylesheet" href="css/contactUs.css" />
    <link rel="stylesheet" href="css/navbar.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
</head>
<body>

<header class="navbar">
    <div class="logo">Bean Scene</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="menu.php">Menu</a>
      <a href="aboutus.php">About Us</a>
      <a href="ContactUs.php">Contact Us</a>
    </nav>
  </header>

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
    <h1>Subscribe to get the Latest News</h1>
    <p>Don't miss out on our latest news, updates, tips and special offers</p>

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
      <button type="submit">Subscribe</button>
    </form>
  </section>

  <!-- Bagian footer dengan gambar sebagai background -->
  <footer class="footer">
    <div class="footer-overlay">
      <div class="footer-container">
        <div class="footer-column">
          <h2>Bean Scene</h2>
          <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry...</p>
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
          <p>Akshya Nagar 1st Block 1st Cross,<br>Rammurthy nagar, Bangalore-560016</p>
          <p>+1 202-918-2132</p>
          <p>beanscene@mail.com</p>
          <p>www.beanscene.com</p>
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
