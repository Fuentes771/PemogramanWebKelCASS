<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

require '../php/config.php';

// Menangani penambahan menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_menu'])) {
    $nama_menu = $_POST['nama_menu'];
    $harga_menu = $_POST['harga_menu'];

    // Proses unggah gambar
    $gambar = $_FILES['gambar_menu']['name'];
    $folder_tujuan = "../uploads/"; // Pastikan folder ini ada dan dapat ditulis
    $file_tujuan = $folder_tujuan . basename($gambar);
    $unggah_ok = 1;
    $tipe_file = strtolower(pathinfo($file_tujuan, PATHINFO_EXTENSION));

    // Cek apakah file gambar adalah gambar asli
    $cek = getimagesize($_FILES['gambar_menu']['tmp_name']);
    if ($cek === false) {
        echo "File bukan gambar yang valid.";
        $unggah_ok = 0;
    }

    // Cek ukuran file
    if ($_FILES['gambar_menu']['size'] > 500000) { // 500KB
        echo "Maaf, file terlalu besar (maksimal 500KB).";
        $unggah_ok = 0;
    }

    // Cek format file
    if (!in_array($tipe_file, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        $unggah_ok = 0;
    }

    // Jika semua cek lolos, unggah file
    if ($unggah_ok == 1) {
        if (move_uploaded_file($_FILES['gambar_menu']['tmp_name'], $file_tujuan)) {
            // Simpan data menu ke database
            $stmt = $pdo->prepare("INSERT INTO menu (name, price, image) VALUES (:nama, :harga, :gambar)");
            $stmt->bindParam(':nama', $nama_menu);
            $stmt->bindParam(':harga', $harga_menu);
            $stmt->bindParam(':gambar', $gambar);
            $stmt->execute();

            $berhasil = "Menu berhasil ditambahkan!";
        } else {
            echo "Maaf, terjadi kesalahan saat mengunggah file.";
        }
    }
}

// Menangani penghapusan menu
if (isset($_GET['hapus'])) {
    $id_menu = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM menu WHERE id = :id");
    $stmt->bindParam(':id', $id_menu);
    $stmt->execute();
    header("Location: add_menu.php"); // Alihkan setelah menghapus
    exit();
}

// Ambil data menu dari database
$stmt = $pdo->query("SELECT * FROM menu ORDER BY id ASC");
$daftar_menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu</title>
    <link rel="stylesheet" href="../css/add_menu_style.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
   <header class="navbar">
        <div class="logo">Kupi & Kuki Admin</div>
        <nav>
             <a href="admin_dashboard.php">Dasbor</a>
            <a href="add_menu.php">Tambah Menu</a>
            <a href="manage_orders.php">Kelola Pesanan</a>
            <a href="view_subscribers.php">Pelanggan</a>
            <a href="ulasan.php">Ulasan</a>
            <a href="../php/logout.php">Keluar</a>
        </nav>
    </header>

    <div class="auth-container">
        <h2>Tambah Menu</h2>
        <?php if (isset($berhasil)): ?>
            <p class="success"><?php echo $berhasil; ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="nama_menu" placeholder="Nama Menu" required>
            <input type="number" name="harga_menu" placeholder="Harga Menu" required>
            <input type="file" name="gambar_menu" accept="image/*" required>
            <button type="submit" name="tambah_menu">Tambah Menu</button>
        </form>
    </div>

    <div class="menu-list">
        <h2>Daftar Menu</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Menu</th>
                <th>Harga</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
            <?php foreach ($daftar_menu as $menu): ?>
            <tr>
                <td><?php echo $menu['id']; ?></td>
                <td><?php echo htmlspecialchars($menu['name']); ?></td>
                <td>Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?></td>
                <td>
                    <?php if ($menu['image']): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($menu['image']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>" width="100">
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?hapus=<?php echo $menu['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
