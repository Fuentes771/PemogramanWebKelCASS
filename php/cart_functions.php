<?php
// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fungsi tambah ke keranjang
if (isset($_POST['add_to_cart'])) {
    $id_menu = (int)$_POST['menu_id'];
    $jumlah = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
        $stmt->execute([$id_menu]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($item) {
            if (isset($_SESSION['cart'][$id_menu])) {
                $_SESSION['cart'][$id_menu]['jumlah'] += $jumlah;
            } else {
                $_SESSION['cart'][$id_menu] = [
                    'id' => $item['id'],
                    'nama' => $item['name'],
                    'harga' => $item['price'],
                    'gambar' => $item['image'],
                    'jumlah' => $jumlah
                ];
            }
            $_SESSION['pesan_keranjang'] = "Item berhasil ditambahkan ke keranjang!";
        }
    } catch (PDOException $e) {
        error_log("Kesalahan database: " . $e->getMessage());
    }
    header("Location: menu.php");
    exit();
}

// Fungsi hapus dari keranjang
if (isset($_GET['remove_item'])) {
    $id_menu = (int)$_GET['remove_item'];
    if (isset($_SESSION['cart'][$id_menu])) {
        unset($_SESSION['cart'][$id_menu]);
        $_SESSION['pesan_keranjang'] = "Item berhasil dihapus dari keranjang!";
    }
    header("Location: menu.php");
    exit();
}

// Fungsi perbarui jumlah item di keranjang
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $id_menu => $jumlah) {
        $id_menu = (int)$id_menu;
        $jumlah = max(1, (int)$jumlah);
        
        if (isset($_SESSION['cart'][$id_menu])) {
            $_SESSION['cart'][$id_menu]['jumlah'] = $jumlah;
        }
    }
    $_SESSION['pesan_keranjang'] = "Keranjang berhasil diperbarui!";
    header("Location: menu.php");
    exit();
}
?>
