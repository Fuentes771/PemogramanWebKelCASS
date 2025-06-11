<?php
// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "toko_kopi";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek session login admin
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// Statistik
$menu_query = mysqli_query($conn, "SELECT COUNT(*) AS total_menu FROM menu");
$total_menu = mysqli_fetch_assoc($menu_query)['total_menu'];

$order_query = mysqli_query($conn, "SELECT COUNT(*) AS total_order FROM orders");
$total_order = mysqli_fetch_assoc($order_query)['total_order'];

$today_query = mysqli_query($conn, "SELECT COUNT(*) AS today_order FROM orders WHERE DATE(order_date) = CURDATE()");
$today_order = mysqli_fetch_assoc($today_query)['today_order'];

$subs_query = mysqli_query($conn, "SELECT COUNT(*) AS total_subscriber FROM subscribers");
$total_subscriber = mysqli_fetch_assoc($subs_query)['total_subscriber'];

// Grafik mingguan
$weekly_orders = [];
$labels = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('D', strtotime($date));
    $result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM orders WHERE DATE(order_date) = '$date'");
    $weekly_orders[] = mysqli_fetch_assoc($result)['count'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin - Kupi & Kuki</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/admin_style.css" />
    <link rel="stylesheet" href="../css/navbar.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<header class="navbar">
    <div class="logo">Kupi & Kuki Admin</div>
    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="add_menu.php">Tambah Menu</a>
        <a href="manage_orders.php">Kelola Pesanan</a>
        <a href="view_subscribers.php">Lihat Pelanggan</a>
        <a href="ulasan.php">Ulasan</a>
        <a href="../php/logout.php">Keluar</a>
    </nav>
</header>

<section>
    <h2>Selamat datang di Dashboard Admin</h2>
    <p>Pilih opsi di atas untuk mengelola menu dan pesanan.</p>

    <div class="stats-wrapper">
        <div class="card"><h3><?= $total_menu ?></h3><p>Total Menu</p></div>
        <div class="card"><h3><?= $total_order ?></h3><p>Total Pesanan</p></div>
        <div class="card"><h3><?= $today_order ?></h3><p>Pesanan Hari Ini</p></div>
        <div class="card"><h3><?= $total_subscriber ?></h3><p>Pelanggan</p></div>
    </div>

    <h3>Statistik Pesanan</h3>
    <div style="max-width: 400px; margin: auto;">
        <canvas id="orderChart"></canvas>
    </div>

    <h3>Grafik Penjualan Mingguan</h3>
    <div style="max-width: 600px; margin: auto;">
        <canvas id="weeklyChart"></canvas>
    </div>

    <h3>Export Laporan Penjualan (.xlsx)</h3>
    <form method="post" action="export_excel.php" style="margin-bottom: 20px;">
        <label>Dari Tanggal:
            <input type="date" name="start_date" required />
        </label>
        <label>Sampai Tanggal:
            <input type="date" name="end_date" required />
        </label>
        <button type="submit">Export ke Excel (.xlsx)</button>
    </form>

    <h3>Pesanan Terbaru</h3>
    <table class="tabel-pesanan" border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $recent_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
            while ($row = mysqli_fetch_assoc($recent_orders)) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['customer_name']}</td>
                        <td>" . date('d-m-Y H:i', strtotime($row['order_date'])) . "</td>
                        <td>Rp " . number_format($row['total_amount'], 0, ',', '.') . "</td>
                        <td>" . ucfirst($row['status']) . "</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</section>

<script>
    const ctx = document.getElementById('orderChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Order Hari ini', 'Sisa Order'],
            datasets: [{
                data: [<?= $today_order ?>, <?= $total_order - $today_order ?>],
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
                    text: 'Perbandingan Order Hari Ini & Total Pesanan',
                    color: '#f9f9f9',
                    font: {
                        family: 'Georgia, serif',
                        size: 16
                    }
                }
            }
        }
    });

    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Jumlah Order',
                data: <?= json_encode($weekly_orders) ?>,
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
                    text: 'Order 7 Hari Terakhir',
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
