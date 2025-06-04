<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Kupi & Kuki - Menu</title>
    <link rel="stylesheet" href="css/aboutus.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>

  <header class="navbar">
    <div class="logo">Kupi & Kuki - Menu</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="menu.php">Menu</a>
      <a href="aboutus.php">About Us</a>
      <a href="ContactUs.php">Contact Us</a>
    </nav>
  </header>

    <header class="hero">
        <h1>About Bodrin Coffee</h1>
        <p>Kami tidak hanya membuat kopi, kami membuat harimu lebih baik!</p>
        <a href="menu.php" class="btn">View Menu</a>
    </header>

    <section class="why-section">
        <h2>Why are we different?</h2>
        <p>Kami tidak hanya membuat kopi, kami membuat harimu lebih baik!</p>
        <div class="why-grid">
            <div class="why-box"><h3>Supreme Beans</h3><p>Biji kopi terbaik pilihan.</p></div>
            <div class="why-box"><h3>High Quality</h3><p>Kualitas rasa terjamin.</p></div>
            <div class="why-box"><h3>Extraordinary</h3><p>Kopi yang belum pernah Anda rasakan.</p></div>
            <div class="why-box"><h3>Affordable Price</h3><p>Harga bersahabat di kantong.</p></div>
        </div>
        <p class="call">Ayo mulai hari ini bersama kami!</p>
        <a href="ContactUs.php" class="btn">Contact Us</a>
    </section>

    <section class="testimonial">
        <h2>Ulasan Pelanggan</h2>
        <p class="subtitle">Pelanggan kami mengatakan hal luar biasa tentang kami</p>
        <div class="testimonial-box">
            <img src="img/splash-left.png" class="splash-left" alt="">
            <img src="img/splash-right.png" class="splash-right" alt="">

            <div class="testimonial-content">
                <span class="quote">❝</span>
                <p class="testimonial-text">
                    Bodrin Coffee adalah tempat terbaik untuk menikmati kopi berkualitas. Pelayanannya ramah dan suasananya nyaman. Sungguh pengalaman yang luar biasa!
                </p>

                <div class="testimonial-nav">
                    <button class="nav-btn">←</button>
                    <button class="nav-btn">→</button>
                </div>

                <div class="author-list">
                    <?php
                    $authors = [
                        ["Cindy Puji Lestari", "Project Manager", "img/ouner4.jpg"],
                        ["M.Sulthon Alfarizky", "Project Manager", "img/ouner3.jpg"],
                        ["Puan Akeyla Maharani", "Project Manager", "img/ouner2.jpg"],
                        ["Nabila Salwa Alghaida", "Project Manager", "img/ouner1.jpg"],
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

            </div>
        </div>
    </section>

</body>
</html>
