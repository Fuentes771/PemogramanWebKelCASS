<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

require '../php/config.php';

$stmt = $pdo->query("SELECT * FROM subscribers");
$subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subscribers</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/view_subscribers_style.css">
</head>
<body>
    <header>
        <div class="logo">Bean Scene Admin</div>
        <nav>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="add_menu.php">Penambahan Menu</a>
            <a href="manage_orders.php">Manajemen Order</a>
            <a href="view_subscribers.php">View Subscribers</a>
            <a href="../php/logout.php">Logout</a>
        </nav>
    </header>

    <section>
        <h1>Subscribers List</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Subscribed At</th>
            </tr>
            <?php foreach ($subscribers as $subscriber): ?>
            <tr>
                <td><?php echo $subscriber['id']; ?></td>
                <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
                <td><?php echo $subscriber['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </section>
    <form action="export_subscribers.php" method="post">
    <center>
        <h2>Export Subscribers</h2>
        <p>Click the button below to export the subscribers list as a CSV file.</p>    
    <button type="submit" class="export-button">Export Subscribers</button>
</form>

</body>
</html>
