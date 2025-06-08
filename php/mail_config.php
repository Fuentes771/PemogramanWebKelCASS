<?php
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendThankYouEmail($to, $customerName) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'kopikukicass@gmail.com'; // Ganti dengan email kamu
    $mail->Password = 'xvkj cerh lxxk vivk';    // Gunakan App Password Gmail
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('kopikukicass@gmail.com', 'KopiKuki');
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Terima Kasih atas Ulasan Anda - Kopi & Kuki';
        
        $mail->Body = '
        <html>
    <head>
        <style>
            body { font-family: "Playfair Display", serif; color: #5c4a37; }
            .header { background-color: #f4c06f; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .footer { background-color: #f8f1e6; padding: 10px; text-align: center; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Kopi & Kuki</h1>
        </div>
        
        <div class="content">
            <h2>Halo '.$customerName.',</h2>
            <p>Terima kasih telah meluangkan waktu untuk memberikan ulasan kepada kami!</p>
            <p>Ulasan Anda sangat berarti untuk membantu kami terus meningkatkan pelayanan dan kualitas produk kami.</p>
            
            <p>Sebagai bentuk apresiasi, berikut adalah kode promo khusus untuk Anda:</p>
            <div style="background: #f8f1e6; padding: 15px; text-align: center; margin: 20px 0;">
                <h3>KODE PROMO: THANKYOU10</h3>
                <p>Dapatkan diskon 10% untuk pembelian berikutnya!</p>
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
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email tidak terkirim: {$mail->ErrorInfo}");
        return false;
    }
}
?>