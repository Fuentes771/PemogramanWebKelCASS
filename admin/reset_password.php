<?php
require '../php/config.php';
date_default_timezone_set('Asia/Jakarta');

$token = $_GET['token'] ?? '';
$token = trim($token);
$valid = false;

if ($token) {
    $now = date("Y-m-d H:i:s");

    // Cek token dan expiry
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token AND token_expiry > :now");
    $stmt->execute(['token' => $token, 'now' => $now]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $valid = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['new_password'])) {
                $new_password = trim($_POST['new_password']);

                if (strlen($new_password) < 1) {
                    $message = "<p style='color:red;'>Password tidak boleh kosong.</p>";
                } else {
                    $new_pass_hash = password_hash($new_password, PASSWORD_DEFAULT);

                    $update = $pdo->prepare("UPDATE users SET password = :pass, reset_token = NULL, token_expiry = NULL WHERE id = :id");
                    $update->execute([
                        ':pass' => $new_pass_hash,
                        ':id' => $user['id']
                    ]);

                    echo "<div style='max-width: 600px; margin: 50px auto; font-family: Arial, sans-serif; text-align: center; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>
                            <h2 style='color: #00c896;'>Password Berhasil Direset</h2>
                            <p style='font-size: 16px;'>Silakan login kembali menggunakan password baru Anda.</p>
                            <a href='admin_login.php' style='display: inline-block; margin-top: 20px; padding: 12px 24px; background-color: #00c896; color: white; text-decoration: none; border-radius: 5px;'>Login Sekarang</a>
                          </div>";
                    exit();
                }
            } else {
                $message = "<p style='color:red;'>Kolom password belum diisi.</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password | Kuki Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
<body style="margin: 0; padding: 0; background-color: #FFF8F0; font-family: 'Poppins', Arial, sans-serif;">

<div style="max-width: 600px; margin: 60px auto; background-color: #ffffff; padding: 40px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center;">

    <?php if ($valid): ?>
        <h2 style="color: #4E342E; margin-bottom: 10px;">Reset Password</h2>
        <p style="color: #7B6F65; font-size: 16px;">Silakan masukkan password baru Anda.</p>
        <?php if (isset($message)) echo $message; ?>

        <form method="POST" style="margin-top: 25px;">
            <input type="password" name="new_password" placeholder="Password Baru" required
                   style="width: 80%; padding: 14px; font-size: 16px; border: 1px solid #D7CCC8; border-radius: 6px;">
            <br><br>
            <button type="submit"
                    style="padding: 12px 26px; font-size: 16px; background-color:rgb(126, 68, 6); color: #fff; border: none; border-radius: 6px; cursor: pointer;">
                Reset Password
            </button>
        </form>

    <?php else: ?>
        <h2 style="color: #B71C1C;">Token Tidak Valid</h2>
        <p style="font-size: 16px; color: #5D4037;">Token Anda tidak valid atau sudah kedaluwarsa. Silakan minta reset ulang.</p>
    <?php endif; ?>
</div>

</body>
</html>

