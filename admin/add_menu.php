<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

require '../php/config.php';

// Menangani penambahan menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_menu'])) {
    $menu_name = $_POST['menu_name'];
    $menu_price = $_POST['menu_price'];

    // Proses upload gambar
    $image = $_FILES['menu_image']['name'];
    $target_dir = "../uploads/"; // Pastikan folder ini ada dan dapat ditulis
    $target_file = $target_dir . basename($image);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Cek apakah file gambar adalah gambar sebenarnya
    $check = getimagesize($_FILES['menu_image']['tmp_name']);
    if ($check === false) {
        echo "File bukan gambar yang valid.";
        $uploadOk = 0;
    }

    // Cek ukuran file
    if ($_FILES['menu_image']['size'] > 500000) { // 500KB
        echo "Maaf, file terlalu besar (maksimal 500KB).";
        $uploadOk = 0;
    }

    // Cek format file
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        $uploadOk = 0;
    }

    // Jika semua cek lolos, upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES['menu_image']['tmp_name'], $target_file)) {
            // Simpan data menu ke database
            $stmt = $pdo->prepare("INSERT INTO menu (name, price, image) VALUES (:name, :price, :image)");
            $stmt->bindParam(':name', $menu_name);
            $stmt->bindParam(':price', $menu_price);
            $stmt->bindParam(':image', $image);
            $stmt->execute();

            $success = "Menu berhasil ditambahkan!";
        } else {
            echo "Maaf, terjadi kesalahan saat mengunggah file.";
        }
    }
}

// Menangani penghapusan menu
if (isset($_GET['delete'])) {
    $menu_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM menu WHERE id = :id");
    $stmt->bindParam(':id', $menu_id);
    $stmt->execute();
    header("Location: add_menu.php"); // Alihkan setelah menghapus
    exit();
}

// Ambil data menu dari database
$stmt = $pdo->query("SELECT * FROM menu ORDER BY id ASC");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu</title>
    <link rel="stylesheet" href="../css/add_menu_style.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    <header>
        <div class="logo">Bean Scene Admin</div>
        <nav>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="add_menu.php">Penambahan Menu</a>
            <a href="manage_orders.php">Manajemen Order</a>
            <a href="view_subscribers.php">Pelanggan</a>
            <a href="ulasan.php">Ulasan</a>
            <a href="../php/logout.php">Keluar</a>
        </nav>
    </header>

    <div class="auth-container">
        <h2>Tambah Menu</h2>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="menu_name" placeholder="Nama Menu" required>
            <input type="number" name="menu_price" placeholder="Harga Menu" required>
            <input type="file" name="menu_image" accept="image/*" required>
            <button type="submit" name="add_menu">Tambah Menu</button>
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
            <?php foreach ($menus as $menu): ?>
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
                    <a href="?delete=<?php echo $menu['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>