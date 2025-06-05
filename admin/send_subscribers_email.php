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

// Koneksi database
$koneksi = new mysqli("localhost", "root", "", "toko_kopi");
if ($koneksi->connect_error) {
    $_SESSION['success_message'] = "Koneksi database gagal: " . $koneksi->connect_error;
    header("Location: view_subscribers.php");
    exit();
}

$query = $koneksi->query("SELECT email FROM subscribers");
if (!$query) {
    $_SESSION['success_message'] = "Gagal mengambil data email: " . $koneksi->error;
    header("Location: view_subscribers.php");
    exit();
}

// Kirim email menggunakan PHPMailer
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

    // Kirim ke semua subscriber
    while ($row = $query->fetch_assoc()) {
        $mail->clearAddresses();
        $mail->addAddress($row['email']);

        $mail->isHTML(true);
        $mail->Subject = "Nikmati Diskon {$diskon}% dari KopiKuki - Kode Promo di Dalam!";
        $mail->Body = "
            <h2>Halo Pelanggan Setia KopiKuki ☕</h2>
            <p>Terima kasih telah menjadi bagian dari komunitas kami.</p>
            <p>Untuk menyambut momen spesial ini, kami memberikan Anda <strong>diskon sebesar {$diskon}%</strong>!</p>
            <p>Gunakan kode promo berikut saat checkout:</p>
            <h3 style='color: #6f4e37;'>📌 {$kupon}</h3>
            <p><em>Jangan lewatkan kesempatan ini. Berlaku untuk pembelian online di website resmi KopiKuki.</em></p>
            <br>
            <p>Salam hangat,</p>
            <p><strong>Tim KopiKuki</strong></p>
        ";
        $mail->AltBody = "Halo pelanggan setia KopiKuki,\n\nKami memberikan Anda diskon {$diskon}%!\nGunakan kode kupon: {$kupon}\n\nSalam hangat,\nTim KopiKuki";

        $mail->send();
    }

    $_SESSION['success_message'] = "Kupon berhasil terkirim: <strong>{$kupon}</strong>";
    header("Location: view_subscribers.php");
    exit();
} catch (Exception $e) {
    $_SESSION['success_message'] = "Kupon gagal dikirim. Error: {$mail->ErrorInfo}";
    header("Location: view_subscribers.php");
    exit();
}
