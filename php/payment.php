<?php

// Simulasi data QRIS (Anda bisa mengganti ini dengan QRIS yang sebenarnya)
$qrisImage = '../img/csan-qr-a.jpg'; // Ganti dengan path ke gambar QRIS yang sesuai
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <link rel="stylesheet" href="../css/payment.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Pembayaran</h1>
    </header>

    <section>
        <h2>Silakan lakukan pembayaran menggunakan QRIS di bawah ini:</h2>
        <img src="<?php echo htmlspecialchars($qrisImage); ?>" alt="QRIS" style="width: 200px; height: 200px;">
        <p>Setelah melakukan pembayaran, silakan kembali ke halaman utama.</p>
        <a href="../index.php" class="btn-back">Kembali ke Home</a>
    </section>
</body>
</html>
