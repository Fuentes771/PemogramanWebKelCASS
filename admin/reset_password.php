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
    <title>Reset Password</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f7f7f7; font-family: Arial, sans-serif;">

<div style="max-width: 600px; margin: 50px auto; background-color: #ffffff; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
    <?php if ($valid): ?>
        <h2 style="color: #333;">Reset Password</h2>
        <p style="color: #666; font-size: 16px;">Masukkan password baru Anda di bawah ini.</p>
        <?php if (isset($message)) echo $message; ?>
        <form method="POST" style="margin-top: 20px;">
            <input type="password" name="new_password" placeholder="Password Baru" required
                   style="width: 80%; padding: 12px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px;">
            <br><br>
            <button type="submit"
                    style="padding: 12px 24px; font-size: 16px; background-color: #00c896; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
                Reset Password
            </button>
        </form>
    <?php else: ?>
        <h2 style="color: #c00;">Token Tidak Valid</h2>
        <p style="font-size: 16px; color: #666;">Token tidak valid atau sudah kedaluwarsa. Silakan minta reset ulang.</p>
    <?php endif; ?>
</div>

</body>
</html>
