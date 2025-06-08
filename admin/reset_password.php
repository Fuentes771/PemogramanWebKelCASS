<?php
require '../php/config.php';
date_default_timezone_set('Asia/Jakarta');

$token = $_GET['token'] ?? '';
$token = trim($token);
$valid = false;

if ($token) {
    $now = date("Y-m-d H:i:s");

    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token AND token_expiry > :now");
    $stmt->execute(['token' => $token, 'now' => $now]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $valid = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = trim($_POST['new_password']);

            if (strlen($new_password) < 6) {
                echo "<p>Password minimal 6 karakter</p>";
            } else {
                $new_pass_hash = password_hash($new_password, PASSWORD_DEFAULT);

                $update = $pdo->prepare("UPDATE users SET password = :pass, reset_token = NULL, token_expiry = NULL WHERE id = :id");
                $update->execute([
                    ':pass' => $new_pass_hash,
                    ':id' => $user['id']
                ]);

                echo "<p>Password berhasil direset. <a href='admin_login.php'>Login sekarang</a></p>";
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
    <?php if ($valid): ?>
        <h2>Reset Password Baru</h2>
        <form method="POST">
            <input type="password" name="new_password" placeholder="Password Baru" required>
            <button type="submit">Reset</button>
        </form>
    <?php else: ?>
        <p>Token tidak valid atau sudah kedaluwarsa.</p>
    <?php endif; ?>
</body>
</html>