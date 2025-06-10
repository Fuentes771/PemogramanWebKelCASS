<?php
session_start();
require '../php/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_username = trim($_POST['admin_username']);
    $admin_password = trim($_POST['admin_password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND role = 'admin'");
    $stmt->bindParam(':username', $admin_username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($admin_password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        header('Location: admin_dashboard.php');
        exit();
    } else {
        $pesan_error = "Nama pengguna atau kata sandi admin salah";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Masuk Sebagai Admin</h2>
        <?php if (isset($pesan_error)): ?>
            <p class="error"><?php echo $pesan_error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="admin_username" placeholder="Nama Pengguna Admin" required>
            <input type="password" name="admin_password" placeholder="Kata Sandi Admin" required>
            <button type="submit">Masuk</button>
        </form>
        <p style="text-align: center; margin-top: 10px;">
            <a href="forgot_password.php">Lupa kata sandi?</a>
        </p>
    </div>
</body>
</html>
