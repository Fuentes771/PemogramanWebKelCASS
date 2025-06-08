<?php
require '../php/config.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Jakarta'); // Pastikan timezone benar

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Cek apakah email terdaftar sebagai admin
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND role = 'admin'");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", time() + 3600); // 1 jam ke depan

        // Simpan token dan expiry ke DB
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE email = :email");
        $stmt->execute([
            'token' => $token,
            'expiry' => $expiry,
            'email' => $email
        ]);

        // Kirim email reset
        $reset_link = "http://localhost/PemogramanWebKelCASS/admin/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kopikukibdl@gmail.com'; // GANTI
            $mail->Password = 'etwn rpbf pzof bmjh';     // GANTI
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('kopikukibdl@gmail.com', 'Admin Toko Kopi&Kuki');
            $mail->addAddress($email);
            $mail->Subject = 'Reset Password Admin';
            $mail->Body = "Klik link berikut untuk reset password Anda:\n\n$reset_link\n\nLink berlaku selama 1 jam.";

            $mail->send();
            $success = "Link reset telah dikirim ke email Anda.";
        } catch (Exception $e) {
            $error = "Gagal mengirim email. Error: " . $mail->ErrorInfo;
        }
    } else {
        $error = "Email tidak ditemukan atau bukan admin.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Lupa Password</title></head>
<body>
    <h2>Lupa Password Admin</h2>
    <?php
    if (isset($error)) echo "<p style='color:red;'>$error</p>";
    if (isset($success)) echo "<p style='color:green;'>$success</p>";
    ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Masukkan email admin" required>
        <button type="submit">Kirim Link Reset</button>
    </form>
</body>
</html>