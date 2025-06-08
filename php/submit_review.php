<?php
require_once 'config.php';

// Fungsi untuk mengirim email terima kasih
function sendThankYouEmail($to, $customerName) {
    $subject = "Terima Kasih atas Ulasan Anda - Kopi & Kuki";
    
    $message = '
    <html>
    <head>
        <style>
            body { font-family: "Arial", sans-serif; color: #333; }
            .header { background-color: #f4c06f; padding: 20px; text-align: center; }
            .content { padding: 20px; line-height: 1.6; }
            .promo-box { background: #f8f1e6; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px; }
            .footer { background-color: #f4f4f4; padding: 10px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>Kopi & Kuki</h2>
        </div>
        
        <div class="content">
            <h3>Halo '.htmlspecialchars($customerName).',</h3>
            <p>Terima kasih telah meluangkan waktu untuk memberikan ulasan kepada kami!</p>
            <p>Ulasan Anda sangat berarti untuk membantu kami terus meningkatkan kualitas pelayanan.</p>
            
            <div class="promo-box">
                <h3>KODE PROMO: THANKYOU10</h3>
                <p>Dapatkan diskon 10% untuk pembelian berikutnya!</p>
                <p>Berlaku hingga '.date('d M Y', strtotime('+1 month')).'</p>
            </div>
            
            <p>Kami berharap dapat menyambut Anda kembali di kedai Kopi & Kuki.</p>
        </div>
        
        <div class="footer">
            <p>Kopi & Kuki &copy; '.date('Y').'</p>
            <p>Jl. Aroma Kopi No. 123, Kota Aromatik</p>
        </div>
    </body>
    </html>
    ';
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= 'From: Kopi & Kuki <no-reply@kopikuki.com>' . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi dan sanitasi input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, 
        ['options' => ['min_range' => 1, 'max_range' => 5]]);
    $review = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if ($name && $rating !== false && $review) {
        try {
            // Mulai transaksi
            $pdo->beginTransaction();
            
            // Simpan ke database
            $stmt = $pdo->prepare("INSERT INTO customer_reviews 
                                  (customer_name, review_text, rating, email) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $review, $rating, $email]);
            
            // Kirim email jika email valid
            if ($email) {
                sendThankYouEmail($email, $name);
            }
            
            // Commit transaksi
            $pdo->commit();
            
            // Redirect dengan pesan sukses
            header('Location: ../aboutus.php?review=success');
            exit();
        } catch(PDOException $e) {
            // Rollback jika ada error
            $pdo->rollBack();
            
            // Log error
            error_log("Error saving review: " . $e->getMessage());
            header('Location: ../aboutus.php?review=error');
            exit();
        }
    } else {
        // Input tidak valid
        header('Location: ../aboutus.php?review=invalid');
        exit();
    }
} else {
    // Bukan request POST
    header('Location: ../aboutus.php');
    exit();
}
?>