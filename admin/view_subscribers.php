<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

require '../php/config.php';

$stmt = $pdo->query("SELECT * FROM subscribers");
$subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil pesan sukses (jika ada) dari session
$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Ambil history pengiriman kupon
$stmt = $pdo->query("SELECT * FROM coupon_sends ORDER BY sent_at DESC");
$coupon_sends = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subscribers</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/view_subscribers_style.css?v=1.1">
</head>
<body>
    <header class="navbar">
        <div class="logo">Kupi & Kuki Admin</div>
        <nav>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="add_menu.php">Tambah Menu</a>
            <a href="manage_orders.php">Kelola Pesanan</a>
            <a href="view_subscribers.php">Pelanggan</a>
            <a href="ulasan.php">Ulasan</a>
            <a href="../php/logout.php">Keluar</a>
        </nav>
    </header>

   <section>
  <h1>List Berlangganan</h1>
  <table>
      <thead>
          <tr>
              <th>ID Pelanggan</th>
              <th>Email</th>
              <th>Berlangganan saat</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach ($subscribers as $subscriber): ?>
          <tr>
              <td>
                <?php echo 'CUST-' . str_pad($subscriber['id'], 4, '0', STR_PAD_LEFT); ?>
              </td>
              <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
              <td><?php echo $subscriber['created_at']; ?></td>
          </tr>
          <?php endforeach; ?>
      </tbody>
      <tfoot>
          <tr>
             <td colspan="3" style="text-align: right; font-weight: bold; color: #f4c06f;">
                Total Berlangganan : <?php echo count($subscribers); ?>
            </td>
          </tr>
      </tfoot>
  </table>

  <!-- Tombol Send Coupon dan Lihat History, di dalam section Subscribers List -->
  <div style="text-align: center; margin-top: 20px;">
      <div style="display: inline-flex; gap: 10px;">
         <div class="button-container">
            <button class="send-button" onclick="openCouponModal()">Kirim kupon</button>
            <button class="history-button" onclick="openHistoryModal()">Riwayat Pengiriman Kupon</button>
         </div>
      </div>
  </div>
</section>

    <!-- Modal Send Coupon -->
    <div id="couponModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCouponModal()">&times;</span>
            <h2>Kirim Kupon</h2>

            <?php if (!empty($successMessage)): ?>
                <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>

            <form action="send_subscribers_email.php" method="post">

             <div class="form-row">
        <div class="form-group">
            <label for="discount">Diskon Kupon (%)</label>
            <input type="number" id="discount" name="discount"
                   min="1" max="100" required
                   placeholder="Masukkan diskon kupon (%)">
        </div>

        <span class="input-separator">s/d</span>

        <div class="form-group">
            <input class="input-no-label" type="number" id="max_discount" name="max_discount" min="1000" step="1000" required placeholder="Masukkan diskon maksimal (Rp)">
        </div>
    </div>

                <div class="form-group">
                    <label for="recipient_count">Jumlah Penerima</label>
                    <input type="number" id="recipient_count" name="recipient_count"
                           min="1" max="<?php echo count($subscribers); ?>" required
                           placeholder="Masukkan jumlah penerima">
                </div>

                <div class="form-group">
                    <label for="expiry_date">Berlaku Sampai Tanggal:</label>
                    <input type="date" id="expiry_date" name="expiry_date" required>
                </div>

                <button type="submit" class="send-gmail">Kirim ke gmail</button>
            </form>
        </div>
    </div>

    <!-- Modal History -->
   <div id="historyModal" class="modal">
    <div class="modal-content" style="max-width: 900px;">
        <span class="close" onclick="closeHistoryModal()">&times;</span>
        <h2>Riwayat Pengiriman Kupon</h2>

        <div class="modal-controls">
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Cari kupon atau email..." onkeyup="searchHistory()">
                <img src="../img/Search.png" alt="Search">
            </div>
        </div>

        <div class="table-wrapper">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>    
                            <th class="checkbox-cell"></th>
                            <th>Tanggal Kirim</th>
                            <th>Email Penerima</th>
                            <th>Kode Kupon</th>
                            <th>Diskon (%)</th>
                            <th>Max Diskon (Rp)</th>
                            <th>Kadaluwarsa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($coupon_sends)): ?>
                            <tr>
                                <td colspan="7">Belum ada kupon yang dikirim.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($coupon_sends as $send): ?>
                            <tr data-id="<?php echo $send['id']; ?>">
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="row-checkbox">
                                </td>
                                <td><?php echo $send['sent_at']; ?></td>
                                <td><?php echo htmlspecialchars($send['recipient_email']); ?></td>
                                <td><?php echo htmlspecialchars($send['coupon_code']); ?></td>
                                <td><?php echo $send['discount']; ?>%</td>
                                <td>Rp <?php echo number_format($send['max_discount']); ?></td>
                                <td><?php echo $send['expiry_date']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>

                    <?php if (!empty($coupon_sends)): ?>
                    <tfoot>
                        <tr>
                            <td colspan="7" style="text-align: right; font-weight: bold; color: #f4c06f;">
                                Total Penerima Kupon: 
                                <span id="totalPenerima"><?php echo count($coupon_sends); ?></span> orang
                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>

        <div class="action-buttons" style="margin-top: 20px; text-align: right;">
            <button class="custom-button btn-select-all" id="selectAllButton" onclick="toggleSelectAllRows()">Pilih Semua</button>
            <button class="custom-button btn-delete" onclick="deleteSelected()">Hapus Terpilih</button>
        </div>

            </div>
        </div>
    </div>
</div>

    <!-- Script Modal -->
    <script>
// Modal Functions
function openCouponModal() {
    document.getElementById('couponModal').classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent scrolling

    // Reset form supaya input kosong
    document.querySelector('#couponModal form').reset();
}

function closeCouponModal() {
    document.getElementById('couponModal').classList.remove('active');
    document.body.style.overflow = 'auto'; // Re-enable scrolling
}

function openHistoryModal() {
    document.getElementById('historyModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeHistoryModal() {
    document.getElementById('historyModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeCouponModal();
        closeHistoryModal();
    }
}

// Close with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeCouponModal();
        closeHistoryModal();
    }
});

// Set default expiry date to tomorrow
document.addEventListener('DOMContentLoaded', function() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const formattedDate = tomorrow.toISOString().split('T')[0];
    document.getElementById('expiry_date').value = formattedDate;
    
    // Auto-fill recipient count with total subscribers
    document.getElementById('recipient_count').max = <?php echo count($subscribers); ?>;
    document.getElementById('recipient_count').value = <?php echo count($subscribers); ?>;
});


   let allSelected = false;

function toggleSelectAllRows() {
    const visibleRows = document.querySelectorAll('tbody tr');
    allSelected = !allSelected;

    visibleRows.forEach(row => {
        // Hanya apply ke baris yang visible
        if (row.style.display !== 'none') {
            const checkbox = row.querySelector('.row-checkbox');
            if (checkbox) {
                checkbox.checked = allSelected;
            }
        }
    });

    // Update tombol text
    document.getElementById('selectAllButton').textContent = allSelected ? 'Batal' : 'Pilih Semua';
}


// Fungsi untuk delete selected
function deleteSelected() {
    const selectedRows = document.querySelectorAll('.row-checkbox:checked');
    if (selectedRows.length === 0) {
        alert('Please select at least one item to delete');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedRows.length} selected item(s)?`)) {
        const idsToDelete = [];
        
        selectedRows.forEach(checkbox => {
            const row = checkbox.closest('tr');
            idsToDelete.push(row.dataset.id);
            row.style.opacity = '0.5';
            row.style.backgroundColor = '#ff000033';
        });
        
        // Kirim permintaan AJAX ke server untuk menghapus
        fetch('delete_coupons.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ids: idsToDelete })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hapus baris dari tampilan setelah sukses
                selectedRows.forEach(checkbox => {
                    checkbox.closest('tr').remove();
                });
                
                // Periksa jika tabel kosong
                if (document.querySelectorAll('tbody tr').length === 0) {
                    const tbody = document.querySelector('tbody');
                    tbody.innerHTML = '<tr><td colspan="7">Belum ada kupon yang dikirim.</td></tr>';
                }
                
                alert(`${data.deleted_count} item(s) deleted successfully`);
            } else {
                alert('Error deleting items: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting items');
        });
    }
}

// Event listener untuk checkbox individual
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('tbody').addEventListener('click', function(e) {
        if (e.target.classList.contains('row-checkbox')) {
            updateSelectAllButton();
        }
    });
    
    function updateSelectAllButton() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const allChecked = checkboxes.length > 0 && Array.from(checkboxes).every(cb => cb.checked);
        allSelected = allChecked;
        document.getElementById('selectAllButton').textContent = allChecked ? 'Deselect All' : 'Select All';
    }
});

function searchHistory() {
    var input, filter, table, tr, td, i, j, txtValue, visibleCount;
    input = document.getElementById("searchInput");
    filter = input.value.toLowerCase();
    table = document.querySelector(".table-container table");
    tr = table.getElementsByTagName("tr");
    visibleCount = 0;

    // Loop semua baris kecuali header
    for (i = 1; i < tr.length; i++) {
        // Skip TFOOT (tfoot biasanya di akhir table)
        if (tr[i].parentNode.tagName.toLowerCase() === 'tfoot') continue;

        tr[i].style.display = "none"; // default disembunyikan

        // Loop semua kolom dalam baris
        td = tr[i].getElementsByTagName("td");
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // tampilkan baris
                    visibleCount++;
                    break; // tidak perlu cek kolom lain
                }
            }
        }
    }

    // Update total penerima kupon
    document.getElementById("totalPenerima").textContent = visibleCount;
}

</script>

</body>
</html>
