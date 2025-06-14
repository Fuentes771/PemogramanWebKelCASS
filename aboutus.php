<?php if (isset($_GET['review'])): ?>
    <div class="notification <?php echo $_GET['review']; ?>">
        <?php
        switch ($_GET['review']) {
            case 'success':
                echo 'Terima kasih! Ulasan Anda telah berhasil dikirim.';
                break;
            case 'error':
                echo 'Maaf, terjadi kesalahan saat mengirim ulasan.';
                break;
            case 'invalid':
                echo 'Data yang Anda masukkan tidak valid.';
                break;
        }
        ?>
    </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Clicker+Script&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    <title>Tentang Kami - Kupi & Kuki - Menu</title>
    <link rel="stylesheet" href="css/aboutus.css">
    <link rel="stylesheet" href="css/navbar.css">
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

<header class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Temukan kopi terbaik</h1>
                <div class="hero-description">
                    <p>Di setiap cangkir kopi kami, ada kehangatan yang menenangkan. Di setiap kukis yang kami panggang, tersimpan rasa manis yang memanjakan hati.</p>
                    <p>Kami tidak sekadar menjual kopi dan kukis — kami menghadirkan momen istimewa yang mengubah hari biasa jadi luar biasa. Karena bagi kami, kebahagiaan dimulai dari hal sederhana: aroma kopi yang membangkitkan semangat, dan gigitan kukis yang membuatmu tersenyum tanpa sadar."</p>
                </div>
                <div class="hero-button">
                    <a href="menu.php" class="btn">Lihat Menu</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="img/hero-atas.jpg" alt="Kopi Premium">
            </div>
        </div>
    </div>
</header>

<section class="why-section">
    <h2>Mengapa Kami Berbeda?</h2>
    <p>Kami tidak hanya membuat kopi & kukis, kami membuat harimu lebih baik 
        dan menjadi bahagia karena telah membuat makanan yang menarik!</p>
    <div class="why-grid">
       
    <div class="why-box">
        <img src="img/img beans.jpg" alt="Kukis Unggulan" class="why-icon">
        <h3>Kukis Unggulan</h3>
        <p>Kukis terbaik dengan rasa yang meleleh di setiap gigitan.</p>
    </div>

    <div class="why-box">
        <img src="img/img quality.jpg" alt="Kualitas Tinggi" class="why-icon">
        <h3>Kualitas Tinggi</h3>
        <p>Kualitas rasa terjamin.</p>
    </div>

    <div class="why-box">
        <img src="img/img extraordinary.jpg" alt="Luar Biasa" class="why-icon">
        <h3>Luar Biasa</h3>
        <p>Kopi yang belum pernah Anda rasakan sensasinya.</p>
    </div>

    <div class="why-box">
        <img src="img/img price.jpg" alt="Harga Terjangkau" class="why-icon">
        <h3>Harga Terjangkau</h3>
        <p>Harga bersahabat di kantong.</p>
    </div>

    </div>
    <p class="call">Waktunya manjakan dirimu, karena kamu pantas untuk rasa terbaik!</p>
    <a href="ContactUs.php" class="btn">Hubungi Kami</a>
</section>

<section class="testimonial">
    <h2>Ulasan Pelanggan</h2>
    <p class="subtitle">Pelanggan kami mengatakan hal luar biasa tentang kami</p>
    
    <div class="testimonial-box">
      <div class="testimonial-content">
        <?php
        // Koneksi ke database
        require_once 'php/config.php';
        
        // Ambil ulasan terbaru yang disetujui dari database
        $stmt = $pdo->query("SELECT * FROM customer_reviews WHERE approved = 1 ORDER BY review_date DESC LIMIT 1");
        $latest_review = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($latest_review):
        ?>
        <span class="quote">❝</span>
        <p class="testimonial-text">
          "<?php echo htmlspecialchars($latest_review['review_text']); ?>"
        </p>
        <p class="review-author">— <?php echo htmlspecialchars($latest_review['customer_name']); ?></p>
        <?php else: ?>
        <p class="testimonial-text">Belum ada ulasan. Jadilah yang pertama memberikan ulasan!</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="review-form-container">
      <h3>Berikan Ulasan Anda</h3>
      <form action="php/submit_review.php" method="POST" class="review-form">
        <div class="form-group">
          <label for="name">Nama:</label>
          <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email (untuk menerima promo):</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
          <label for="rating">Penilaian:</label>
          <select id="rating" name="rating" required>
            <option value="5">★★★★★</option>
            <option value="4">★★★★☆</option>
            <option value="3">★★★☆☆</option>
            <option value="2">★★☆☆☆</option>
            <option value="1">★☆☆☆☆</option>
          </select>
        </div>
        <div class="form-group">
          <label for="review">Ulasan:</label>
          <textarea id="review" name="review" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn">Kirim Ulasan</button>
      </form>
    </div>

    <div class="author-list">
      <?php
      $authors = [
          ["Cindy Puji Lestari", "Manajer Proyek", "img/ouner4.jpg"],
          ["M.Sulthon Alfarizky", "Manajer Proyek", "img/ouner3.jpg"],
          ["Puan Akeyla Maharani", "Manajer Proyek", "img/ouner2.jpg"],
          ["Nabila Salwa Alghaida", "Manajer Proyek", "img/ouner1.jpg"],
      ];

      foreach ($authors as $author) {
          echo "
          <div class='author-item'>
              <img src='{$author[2]}' alt='{$author[0]}' width='200'>
              <div class='author-info'>
                  <h3>{$author[0]}</h3>
                  <p>{$author[1]}</p>
              </div>
          </div>
          ";
      }
      ?>
    </div>
</section>

</body>
</html>
