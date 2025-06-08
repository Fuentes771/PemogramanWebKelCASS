<?php
require_once 'config.php';
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, 
        ['options' => ['min_range' => 1, 'max_range' => 5]]);
    $review = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_STRING);

    if ($name && $email && $rating !== false && $review) {
        try {
            $stmt = $pdo->prepare("INSERT INTO customer_reviews 
                                   (customer_name, email, review_text, rating) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $review, $rating]);
            
            // Generate kode promo unik
            $couponCode = generateCouponCode();
            
            // Send thank you email with PHPMailer
            sendThankYouEmail($name, $email, $couponCode);
            
            // Redirect back with success message
            header('Location: ../aboutus.php?review=success');
            exit();
        } catch(PDOException $e) {
            // Log error and redirect with error message
            error_log("Error saving review: " . $e->getMessage());
            header('Location: ../aboutus.php?review=error');
            exit();
        }
    } else {
        // Invalid input
        header('Location: ../aboutus.php?review=invalid');
        exit();
    }
} else {
    // Not a POST request
    header('Location: ../aboutus.php');
    exit();
}

function generateCouponCode() {
    $random = strtoupper(substr(md5(uniqid()), 0, 6));
    return "THANKYOU10-{$random}";
}

function sendThankYouEmail($name, $email, $couponCode) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kopikukicass@gmail.com'; // Ganti dengan email Anda
        $mail->Password = 'xvkj cerh lxxk vivk';    // Gunakan App Password Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('kopikukicass@gmail.com', 'Kopi & Kuki');
        $mail->addAddress($email, $name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "Terima Kasih atas Ulasan Anda - Kopi & Kuki";
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 30px;'>
              <div style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.05);'>
                <div style='background-color: #6F4E37; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
                  <h2 style='margin: 0;'>Kopi & Kuki</h2>
                </div>
                
                <div style='padding: 20px;'>
                  <h3 style='color: #6F4E37;'>Halo $name,</h3>
                  
                  <p style='font-size: 16px; color: #555;'>
                    Terima kasih telah meluangkan waktu untuk memberikan ulasan kepada kami. 
                    Ulasan Anda sangat berharga untuk membantu kami meningkatkan pelayanan.
                  </p>
                  
                  <p style='font-size: 16px; color: #555;'>
                    Sebagai bentuk apresiasi, kami ingin memberikan Anda kode promo khusus:
                  </p>
                  
                  <div style='text-align: center; margin: 30px 0;'>
                    <span style='display: inline-block; background-color: #6F4E37; color: #fff; font-size: 20px; font-weight: bold; padding: 12px 24px; border-radius: 6px;'>
                      {$couponCode}
                    </span>
                  </div>
                  
                  <p style='font-size: 16px; color: #555;'>
                    Gunakan kode ini untuk mendapatkan diskon 10% pada pembelian berikutnya di Kopi & Kuki.
                  </p>
                  
                  <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/PemogramanWebKelCASS/index.php' style='display: inline-block; padding: 12px 24px; background-color: #6F4E37; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;'>
                      Lihat Menu Kami
                    </a>
                  </div>
                  
                  <p style='font-size: 16px; color: #555;'>
                    Kode promo berlaku hingga " . date('d M Y', strtotime('+30 days')) . ".
                  </p>
                  
                  <p style='font-size: 16px; color: #555;'>
                    Kami berharap dapat melayani Anda kembali segera!
                  </p>
                </div>
                
                <div style='text-align: center; font-size: 12px; color: #777; padding-top: 20px; border-top: 1px solid #eee;'>
                  <p>&copy; " . date('Y') . " Kopi & Kuki. Semua hak dilindungi.</p>
                  <p>Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.</p>
                </div>
              </div>
            </div>
        ";
        
        $mail->AltBody = "Halo $name,\n\nTerima kasih telah memberikan ulasan kepada Kopi & Kuki.\n\nSebagai apresiasi, berikut kode promo Anda: $couponCode\n\nGunakan kode ini untuk mendapatkan diskon 10% pada pembelian berikutnya.\n\nKode berlaku hingga " . date('d M Y', strtotime('+30 days')) . ".\n\nSalam hangat,\nTim Kopi & Kuki";
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
    }
}
?>