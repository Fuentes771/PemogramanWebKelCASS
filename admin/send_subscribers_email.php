<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ambil nilai diskon dari POST
$diskon = isset($_POST['discount']) ? intval($_POST['discount']) : 0;
if ($diskon <= 0 || $diskon > 100) {
    $_SESSION['success_message'] = "Diskon tidak valid!";
    header("Location: view_subscribers.php");
    exit();
}

// Generate kode kupon unik
function generateCoupon($diskon) {
    $random = strtoupper(substr(md5(uniqid()), 0, 5));
    return "KOPIKUKI-{$diskon}-{$random}";
}
$kupon = generateCoupon($diskon);

// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "toko_kopi");
if ($koneksi->connect_error) {
    $_SESSION['success_message'] = "Koneksi database gagal: " . $koneksi->connect_error;
    header("Location: view_subscribers.php");
    exit();
}

// Ambil semua email subscriber
$query = $koneksi->query("SELECT email FROM subscribers");
if (!$query || $query->num_rows === 0) {
    $_SESSION['success_message'] = "Tidak ada email subscriber yang ditemukan.";
    header("Location: view_subscribers.php");
    exit();
}

// Kirim email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'kopikukicass@gmail.com'; // Ganti dengan email kamu
    $mail->Password = 'xvkj cerh lxxk vivk';    // Gunakan App Password Gmail
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('kopikukicass@gmail.com', 'KopiKuki');

    // Kirim ke setiap subscriber
    while ($row = $query->fetch_assoc()) {
        $email = $row['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

        $mail->clearAddresses();
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Nikmati Diskon {$diskon}% dari KopiKuki - Gunakan Kode Promo Saat Checkout!";
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 30px;'>
              <div style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.05);'>
                <h2 style='color: #6f4e37; text-align: center;'>KopiKuki</h2>
                <p style='font-size: 16px; color: #333;'>Hi Pelanggan Setia,</p>

                <p style='font-size: 16px; color: #555;'>
                  Terima kasih telah menjadi bagian dari komunitas <strong>KopiKuki</strong>. Untuk menyambut momen spesial ini, kami memberikan Anda
                  <strong>diskon sebesar {$diskon}%</strong>!
                </p>

                <p style='font-size: 16px; color: #555;'>Gunakan kode promo berikut saat checkout:</p>

                <div style='text-align: center; margin: 30px 0;'>
                  <span style='display: inline-block; background-color: #6f4e37; color: #fff; font-size: 20px; font-weight: bold; padding: 12px 24px; border-radius: 6px;'>
                    📌 {$kupon}
                  </span>
                </div>

                <div style='display: flex; justify-content: space-between; font-size: 14px; color: #555; margin-top: 20px;'>
                  <div><strong>Total Diskon:</strong><br>{$diskon}%</div>
                  <div><strong>Berlaku Sampai:</strong><br>30 Juni 2025</div>
                </div>

                <p style='font-size: 14px; color: #777; font-style: italic; margin-top: 20px;'>
                  *Berlaku untuk pembelian online di website resmi KopiKuki. Jangan lewatkan kesempatan ini!
                </p>

                <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>

                <p style='font-size: 16px; color: #333;'>Salam hangat,</p>
                <p style='font-size: 16px; font-weight: bold; color: #6f4e37;'>Tim KopiKuki</p>
              </div>
            </div>
        ";
        $mail->AltBody = "Halo pelanggan setia KopiKuki,\n\nKami memberikan Anda diskon {$diskon}%!\nGunakan kode kupon: {$kupon}\n\nSalam hangat,\nTim KopiKuki";

        $mail->send();
    }

    $_SESSION['success_message'] = "Kupon berhasil dikirim ke semua pelanggan: <strong>{$kupon}</strong>";
    header("Location: view_subscribers.php");
    exit();
} catch (Exception $e) {
    $_SESSION['success_message'] = "Kupon gagal dikirim. Error: {$mail->ErrorInfo}";
    header("Location: view_subscribers.php");
    exit();
}
