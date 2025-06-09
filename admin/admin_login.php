<?php
session_start();
require '../php/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pengguna_admin = trim($_POST['nama_pengguna_admin']);
    $kata_sandi_admin = trim($_POST['kata_sandi_admin']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND role = 'admin'");
    $stmt->bindParam(':username', $nama_pengguna_admin);
    $stmt->execute();
    $pengguna = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pengguna && password_verify($kata_sandi_admin, $pengguna['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $pengguna['username'];
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
            <input type="text" name="nama_pengguna_admin" placeholder="Nama Pengguna Admin" required>
            <input type="password" name="kata_sandi_admin" placeholder="Kata Sandi Admin" required>
            <button type="submit">Masuk</button>
        </form>
        <p style="text-align: center; margin-top: 10px;">
            <a href="forgot_password.php">Lupa kata sandi?</a>
        </p>
    </div>
</body>
</html>
