<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ambil data dari form
$diskon = isset($_POST['discount']) ? intval($_POST['discount']) : 0;
$maxDiskon = isset($_POST['max_discount']) ? intval($_POST['max_discount']) : 0;
$recipientCount = isset($_POST['recipient_count']) ? intval($_POST['recipient_count']) : 0;
$expiryDate = isset($_POST['expiry_date']) ? $_POST['expiry_date'] : '';

if (
    $diskon <= 0 || $diskon > 100 ||
    $maxDiskon <= 0 || $maxDiskon > 100000000 ||
    $recipientCount <= 0 ||
    empty($expiryDate)
) {
    $_SESSION['success_message'] = "Input tidak valid!";
    header("Location: view_subscribers.php");
    exit();
}

// Format diskon maksimal (misalnya Rp 20.000)
$maxDiskonFormatted = 'Rp ' . number_format($maxDiskon, 0, ',', '.');

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

// Ambil email subscriber secara acak
$query = $koneksi->query("SELECT email FROM subscribers ORDER BY RAND() LIMIT {$recipientCount}");
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
    $mail->Username = 'kopikukicass@gmail.com';
    $mail->Password = 'xvkj cerh lxxk vivk'; // ganti ke env var kalau produksi
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('kopikukicass@gmail.com', 'KopiKuki');

    $sentCount = 0;

    while ($row = $query->fetch_assoc()) {
        $email = $row['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

        $mail->clearAddresses();
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Nikmati Diskon {$diskon}% Spesial dari KopiKuki - Gunakan Kode Promo saat Pembayaran!";

        $formattedDate = date('d F Y', strtotime($expiryDate));

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;'>
            <div style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);'>

                <h2 style='color: #6f4e37; text-align: center;'>Kupi & Kuki</h2>

                <p style='font-size: 16px; color: #333;'>Halo Pelanggan Setia,</p>

                <p style='font-size: 16px; color: #555; line-height: 1.6;'>
                    Terima kasih telah menjadi bagian dari keluarga <strong>KopiKuki</strong>. 
                    Sebagai bentuk apresiasi, kami memberikan <strong>diskon sebesar {$diskon}%</strong> hingga maksimal potongan <strong>{$maxDiskonFormatted}</strong> untuk Anda.
                </p>

                <p style='font-size: 16px; color: #555;'>Gunakan kode promo berikut saat checkout:</p>

                <div style='text-align: center; margin: 30px 0;'>
                    <span style='display: inline-block; background-color: #6f4e37; color: #fff; font-size: 24px; font-weight: bold; padding: 14px 28px; border-radius: 8px; letter-spacing: 1px;'>
                        {$kupon}
                    </span>
                </div>

                <div style='font-size: 14px; color: #555; margin-top: 20px;'>
                    <p><strong>Berlaku Sampai:</strong> {$formattedDate}</p>
                    <p><strong>Total Diskon:</strong> {$diskon}% (maks. {$maxDiskonFormatted})</p>
                </div>

                <p style='font-size: 14px; color: #777; font-style: italic; margin-top: 30px;'>
                    *Penawaran ini mengikuti syarat dan ketentuan yang ditetapkan. KopiKuki dapat sewaktu-waktu menyesuaikan promo yang berlaku.

                <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>

                <p style='font-size: 16px; color: #333;'>Salam hangat,</p>
                <br><br>
                <p style='font-size: 16px; font-weight: bold; color: #6f4e37;'>Tim KopiKuki</p>

            </div>
        </div>";

        $mail->AltBody = "Halo pelanggan setia KopiKuki,\n\nKami memberikan Anda diskon {$diskon}% hingga maksimal potongan {$maxDiskonFormatted}!\nGunakan kode kupon: {$kupon}\nBerlaku sampai: {$formattedDate}\n\nBelanja sekarang di http://localhost/PemogramanWebKelCASS/\n\nSalam hangat,\nTim KopiKuki";

        $mail->send();

        // >>> Tambahkan ke tabel coupon_sends setelah email berhasil dikirim <<<
        $stmt = $koneksi->prepare("INSERT INTO coupon_sends (recipient_email, coupon_code, discount, max_discount, expiry_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiis", $email, $kupon, $diskon, $maxDiskon, $expiryDate);
        $stmt->execute();
        $stmt->close();

        $sentCount++;
    }

    $_SESSION['success_message'] = "Kupon berhasil dikirim ke {$sentCount} pelanggan. Kode kupon: <strong>{$kupon}</strong>";
    header("Location: view_subscribers.php");
    exit();
} catch (Exception $e) {
    $_SESSION['success_message'] = "Kupon gagal dikirim. Error: " . htmlspecialchars($mail->ErrorInfo);
    header("Location: view_subscribers.php");
    exit();
}
