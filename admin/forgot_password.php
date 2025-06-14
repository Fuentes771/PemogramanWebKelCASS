<?php
require '../php/config.php';
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND role = 'admin'");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", time() + 3600); // 1 jam ke depan

        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE email = :email");
        $stmt->execute([
            'token' => $token,
            'expiry' => $expiry,
            'email' => $email
        ]);

        $reset_link = "http://localhost/PemogramanWebKelCASS/admin/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kopikukicass@gmail.com'; // GANTI
            $mail->Password = 'xvkj cerh lxxk vivk';     // GANTI
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('kopikukicass@gmail.com', 'Kupi & Kuki');
            $mail->addAddress($email);
            $mail->Subject = 'Pengaturan Ulang Kata Sandi Admin';
            $mail->isHTML(true);
            $mail->Body = "
                <div style='font-family: Poppins, Arial, sans-serif; background-color: #fff8f0; padding: 20px; color: #333;'>
                    <h2 style='color: #4E342E;'>Pengaturan Ulang Kata Sandi Admin</h2>
                    <p>Halo <strong>Admin</strong>,</p>
                    <p>Kami menerima permintaan untuk mengatur ulang kata sandi Anda sebagai admin <strong>Kupi & Kuki </strong>.</p>
                    <p>Silakan klik tombol di bawah ini untuk melanjutkan pengaturan ulang kata sandi Anda:</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='$reset_link' style='background-color: #7e4406; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px;'>Atur Ulang Kata Sandi</a>
                    </p>
                    <p>Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.</p>
                    <p style='margin-top: 30px;'>Salam hangat,<br><strong>Kupi & Kuki </strong></p>
                </div>
            ";
            $mail->send();
            $success = "Tautan pengaturan ulang telah dikirim ke email Anda.";
        } catch (Exception $e) {
            $error = "Gagal mengirim email. Pesan kesalahan: " . $mail->ErrorInfo;
        }
    } else {
        $error = "Email tidak ditemukan atau bukan akun admin.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Kata Sandi Admin | Kupi & Kuki</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>

<body style="margin: 0; padding: 0; background-color: #FFF8F0; font-family: 'Poppins', Arial, sans-serif;">
<div style="max-width: 500px; margin: 60px auto; background-color: #ffffff; padding: 40px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center;">
    <img src='https://example.com/logo_kuki.png' alt='Logo Kupi & Kuki' style='max-width: 120px; margin-bottom: 20px;'>
    <h2 style="color: #4E342E; margin-bottom: 15px;">Lupa Kata Sandi Admin</h2>
    <p style="color: #7B6F65; font-size: 16px;">Masukkan email admin Anda untuk menerima tautan pengaturan ulang kata sandi.</p>

    <?php if (isset($error)) echo "<p style='color:red; margin-top: 20px;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:green; margin-top: 20px;'>$success</p>"; ?>

    <form method="POST" style="margin-top: 25px;">
        <input type="email" name="email" placeholder="Masukkan email admin Anda" required
               style="width: 85%; padding: 14px; font-size: 16px; border: 1px solid #D7CCC8; border-radius: 6px;">
        <br><br>
        <button type="submit"
                style="padding: 12px 26px; font-size: 16px; background-color: rgb(126, 68, 6); color: #fff; border: none; border-radius: 6px; cursor: pointer;">
            Kirim Link Reset
        </button>
    </form>
</div>
</body>
</html>
