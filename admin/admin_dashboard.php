<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "toko_kopi";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

$query_menu = mysqli_query($conn, "SELECT COUNT(*) AS total_menu FROM menu");
$total_menu = mysqli_fetch_assoc($query_menu)['total_menu'];

$query_order = mysqli_query($conn, "SELECT COUNT(*) AS total_order FROM orders");
$total_order = mysqli_fetch_assoc($query_order)['total_order'];

$query_hari_ini = mysqli_query($conn, "SELECT COUNT(*) AS order_hari_ini FROM orders WHERE DATE(order_date) = CURDATE()");
$order_hari_ini = mysqli_fetch_assoc($query_hari_ini)['order_hari_ini'];

$query_pelanggan = mysqli_query($conn, "SELECT COUNT(*) AS total_pelanggan FROM subscribers");
$total_pelanggan = mysqli_fetch_assoc($query_pelanggan)['total_pelanggan'];

$pesanan_mingguan = [];
$label_hari = [];

for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $label_hari[] = date('D', strtotime($tanggal));
    $hasil = mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM orders WHERE DATE(order_date) = '$tanggal'");
    $pesanan_mingguan[] = mysqli_fetch_assoc($hasil)['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dasbor Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="navbar">
        <div class="logo">Kupi & Kuki Admin</div>
        <nav>
             <a href="admin_dashboard.php">Dasbor</a>
            <a href="add_menu.php">Tambah Menu</a>
            <a href="manage_orders.php">Kelola Pesanan</a>
            <a href="view_subscribers.php">Lihat Pelanggan</a>
            <a href="ulasan.php">Ulasan</a>
            <a href="../php/logout.php">Keluar</a>
        </nav>
    </header>

    <section>
        <h2>Selamat datang di Dasboard Admin</h2>
        <p>Pilih opsi di atas untuk mengelola menu dan pesanan.</p>

        <div class="stats-wrapper">
            <div class="card"><h3><?= $total_menu ?></h3><p>Total Menu</p></div>
            <div class="card"><h3><?= $total_order ?></h3><p>Total Pesanan</p></div>
            <div class="card"><h3><?= $order_hari_ini ?></h3><p>Pesanan Hari Ini</p></div>
            <div class="card"><h3><?= $total_pelanggan ?></h3><p>Pelanggan</p></div>
        </div>

        <h3>Statistik Pesanan</h3>
        <div style="max-width: 400px; margin: auto;">
            <canvas id="grafikPesanan"></canvas>
        </div>

        <h3>Grafik Penjualan Mingguan</h3>
        <div style="max-width: 600px; margin: auto;">
            <canvas id="grafikMingguan"></canvas>
        </div>

        <h3>Export Laporan Penjualan (CSV)</h3>
        <form method="post" action="export_csv.php" style="margin-bottom: 20px;">
        <label>Dari Tanggal:
            <input type="date" name="start_date" required>
        </label>
        <label>Sampai Tanggal:
            <input type="date" name="end_date" required>
        </label>
        <button type="submit">Export ke Excel (CSV)</button>
        </form>


        <h3>Pesanan Terbaru</h3>
        <table class="tabel-pesanan">
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
            <?php
            $pesanan_terbaru = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
            while ($baris = mysqli_fetch_assoc($pesanan_terbaru)) {
                echo "<tr>
                        <td>{$baris['id']}</td>
                        <td>{$baris['customer_name']}</td>
                        <td>{$baris['order_date']}</td>
                        <td>Rp " . number_format($baris['total_amount'], 0, ',', '.') . "</td>
                        <td>{$baris['status']}</td>
                      </tr>";
            }
            ?>
        </table>
    </section>

    <script>
        const ctx = document.getElementById('grafikPesanan').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Pesanan Hari Ini', 'Sisa Pesanan'],
                datasets: [{
                    data: [<?= $order_hari_ini ?>, <?= $total_order - $order_hari_ini ?>],
                    backgroundColor: ['#d49e42', 'rgba(255, 255, 255, 0.6)'],
                    borderColor: ['#b3862a', '#ccc'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#f9f9f9',
                            font: {
                                family: 'Georgia, serif',
                                size: 14
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Perbandingan Pesanan Hari Ini & Total Pesanan',
                        color: '#f9f9f9',
                        font: {
                            family: 'Georgia, serif',
                            size: 16
                        }
                    }
                }
            }
        });

        const weeklyCtx = document.getElementById('grafikMingguan').getContext('2d');
        new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($label_hari) ?>,
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: <?= json_encode($pesanan_mingguan) ?>,
                    backgroundColor: '#d49e42',
                    borderColor: '#b3862a',
                    borderWidth: 2,
                    borderRadius: 4
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#f9f9f9',
                            font: {
                                family: 'Georgia, serif'
                            }
                        },
                        grid: {
                            display: true,
                            color: 'rgba(111, 78, 55, 0.2)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#f9f9f9',
                            font: {
                                family: 'Georgia, serif'
                            }
                        },
                        grid: {
                            display: true,
                            color: 'rgba(111, 78, 55, 0.2)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Pesanan 7 Hari Terakhir',
                        color: '#f9f9f9',
                        font: {
                            family: 'Georgia, serif',
                            size: 16
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
