<?php
require_once 'config.php';
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi dan sanitasi masukan
    $nama = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $penilaian = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, 
        ['options' => ['min_range' => 1, 'max_range' => 5]]);
    $ulasan = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_STRING);

    if ($nama && $email && $penilaian !== false && $ulasan) {
        try {
            $stmt = $pdo->prepare("INSERT INTO customer_reviews 
                                   (customer_name, email, review_text, rating) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $email, $ulasan, $penilaian]);
            
            // Buat kode kupon unik
            $kodeKupon = buatKodeKupon();
            
            // Kirim email terima kasih menggunakan PHPMailer
            kirimEmailTerimaKasih($nama, $email, $kodeKupon);
            
            // Kembali dengan pesan sukses
            header('Location: ../aboutus.php?review=success');
            exit();
        } catch(PDOException $e) {
            // Catat kesalahan dan kembali dengan pesan error
            error_log("Gagal menyimpan ulasan: " . $e->getMessage());
            header('Location: ../aboutus.php?review=error');
            exit();
        }
    } else {
        // Masukan tidak valid
        header('Location: ../aboutus.php?review=invalid');
        exit();
    }
} else {
    // Bukan permintaan POST
    header('Location: ../aboutus.php');
    exit();
}

function buatKodeKupon() {
    $acak = strtoupper(substr(md5(uniqid()), 0, 6));
    return "TERIMAKASIH10-{$acak}";
}

function kirimEmailTerimaKasih($nama, $email, $kodeKupon) {
    $mail = new PHPMailer(true);
    
    try {
        // Pengaturan server
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kopikukicass@gmail.com'; // Ganti dengan email Anda
        $mail->Password = 'xvkj cerh lxxk vivk';    // Gunakan App Password Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        // Penerima
        $mail->setFrom('kopikukicass@gmail.com', 'Kupi & Kuki');
        $mail->addAddress($email, $nama);
        
        // Isi Email
        $mail->isHTML(true);
        $mail->Subject = "Terima Kasih atas Ulasan Anda - Kupi & Kuki";
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 30px;'>
              <div style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.05);'>
                <div style='background-color: #6F4E37; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
                  <h2 style='margin: 0;'>Kupi & Kuki</h2>
                </div>
                
                <div style='padding: 20px;'>
                  <h3 style='color: #6F4E37;'>Halo $nama,</h3>
                  
                  <p style='font-size: 16px; color: #555;'>
                    Terima kasih telah meluangkan waktu untuk memberikan ulasan kepada kami. 
                    Ulasan Anda sangat berharga untuk membantu kami meningkatkan pelayanan.
                  </p>
                  
                  <p style='font-size: 16px; color: #555;'>
                    Sebagai bentuk apresiasi, kami ingin memberikan Anda kode promo khusus:
                  </p>
                  
                  <div style='text-align: center; margin: 30px 0;'>
                    <span style='display: inline-block; background-color: #6F4E37; color: #fff; font-size: 20px; font-weight: bold; padding: 12px 24px; border-radius: 6px;'>
                      {$kodeKupon}
                    </span>
                  </div>
                  
                  <p style='font-size: 16px; color: #555;'>
                    Gunakan kode ini untuk mendapatkan diskon 10% pada pembelian berikutnya di Kupi & Kuki.
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
                  <p>&copy; " . date('Y') . " Kupi & Kuki. Semua hak dilindungi.</p>
                  <p>Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.</p>
                </div>
              </div>
            </div>
        ";
        
        $mail->AltBody = "Halo $nama,\n\nTerima kasih telah memberikan ulasan kepada Kupi & Kuki.\n\nSebagai apresiasi, berikut kode promo Anda: $kodeKupon\n\nGunakan kode ini untuk mendapatkan diskon 10% pada pembelian berikutnya.\n\nKode berlaku hingga " . date('d M Y', strtotime('+30 days')) . ".\n\nSalam hangat,\nTim Kupi & Kuki";
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Pengiriman email gagal: " . $mail->ErrorInfo);
    }
}
?>
