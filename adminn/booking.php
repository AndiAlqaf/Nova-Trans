<?php
session_start();
include "koneksi.php";

// Ambil opsi enum untuk kolom status di tabel booking
$statusOptions = [];
try {
    $enumStmt = $koneksi->query("SHOW COLUMNS FROM booking LIKE 'status'");
    $enumRow  = $enumStmt->fetch(PDO::FETCH_ASSOC);
    if (preg_match("/^enum\((.*)\)$/", $enumRow['Type'], $m)) {
        $vals = explode(',', $m[1]);
        $statusOptions = array_map(fn($v)=> trim($v, "' \""), $vals);
    }
} catch (PDOException $e) {
    $statusOptions = ['Tersedia','Sudah Dipilih','Temporary','Pending','Paid','Cancelled'];
}

// ========== HANDLE CREATE BOOKING ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $nama      = $_POST['nama_pemesan'];
    $telp      = $_POST['no_telepon'];
    $email     = $_POST['email'];
    $tgl       = $_POST['tanggal_pemesanan'];
    $status    = $_POST['status'];
    $harga     = intval($_POST['total_harga']);
    $bus       = $_POST['id_bus'];
    $metode    = $_POST['metode_pembayaran'];
    $kursi     = $_POST['kursi'];

    // 1) Insert ke booking
    $ins1 = $koneksi->prepare(
        "INSERT INTO booking
         (nama_pemesan,no_telepon,email,tanggal_pemesanan,status,total_harga,
          id_bus,metode_pembayaran,kursi,created_at,updated_at)
         VALUES(?,?,?,?,?,?,?,?,?,NOW(),NOW())"
    );
    $ins1->execute([$nama,$telp,$email,$tgl,$status,$harga,$bus,$metode,$kursi]);

    // 2) Ambil ID baru
    $idBooking = $koneksi->lastInsertId();

    // 3) Sinkron ke laporan
    $idTrans   = sprintf('TRX-%s-%03d', date('Ymd'), $idBooking);
    $waktu     = date('Y-m-d H:i:s');
    // Anda bisa menyimpan rute lengkap jika ada tabel rute; di sini kita kirim ID bus
    $rute      = $bus;
    // Hitung jumlah tiket dari string kursi (dipisah koma)
    $jmlTiket  = substr_count($kursi, ',') + 1;

    $ins2 = $koneksi->prepare(
        "INSERT INTO laporan
         (id_transaksi,waktu_transaksi,nama_pelanggan,rute,kode_bus,
          jumlah_tiket,total,status)
         VALUES(?,?,?,?,?,?,?,?)"
    );
    $ins2->execute([
        $idTrans,
        $waktu,
        $nama,
        $rute,
        $bus,
        $jmlTiket,
        $harga,
        $status
    ]);

    header('Location: booking.php');
    exit;
}

// ========== HANDLE EDIT BOOKING ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id        = $_POST['id_booking'];
    $nama      = $_POST['nama_pemesan'];
    $telp      = $_POST['no_telepon'];
    $email     = $_POST['email'];
    $tgl       = $_POST['tanggal_pemesanan'];
    $status    = $_POST['status'];
    $harga     = intval($_POST['total_harga']);
    $bus       = $_POST['id_bus'];
    $metode    = $_POST['metode_pembayaran'];
    $kursi     = $_POST['kursi'];

    $upd = $koneksi->prepare(
      "UPDATE booking SET
         nama_pemesan=?,no_telepon=?,email=?,tanggal_pemesanan=?,
         status=?,total_harga=?,id_bus=?,metode_pembayaran=?,kursi=?,updated_at=NOW()
       WHERE id_booking=?"
    );
    $upd->execute([$nama,$telp,$email,$tgl,$status,$harga,$bus,$metode,$kursi,$id]);
    header('Location: booking.php');
    exit;
}

// ========== HANDLE DELETE BOOKING ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = $_POST['id_booking'];
    $del = $koneksi->prepare("DELETE FROM booking WHERE id_booking=?");
    $del->execute([$id]);
    header('Location: booking.php');
    exit;
}

// ========== FETCH SEMUA BOOKING ==========
$stmt = $koneksi->prepare("SELECT * FROM booking ORDER BY id_booking DESC");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Booking - NOVA TRANS</title>
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- SIDEBAR -->
  <div class="sidebar">
    <div class="logo-container">
      <img src="../user/Gambar/LOGO.png" alt="Nova Trans Logo" style="width:32px;height:auto;margin-right:8px;">
      <h2>NOVA TRANS</h2>
    </div>
    <div class="menu-item"><a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></div>
    <div class="menu-item"><a href="data_regist.php"><i class="fas fa-user-cog"></i><span>Data Registrasi</span></a></div>
    <div class="menu-item"><a href="data_bus.php"><i class="fas fa-database"></i><span>Data Bus</span></a></div>
     <div class="menu-item"><a href="kelola_kendaraan.php"><i class="fas fa-bus"></i><span>Kelola Kendaraan</span></a></div>
    <div class="menu-item"><a href="booking.php" class="active"><i class="fas fa-ticket-alt"></i><span>Kelola Booking</span></a></div>
    <div class="menu-item"><a href="kontak.php"><i class="fas fa-address-book"></i><span>Kelola Kontak</span></a></div>
    <div class="menu-item"><a href="testimoni.php"><i class="fas fa-comments"></i><span>Kelola Testimoni</span></a></div>
    <div class="menu-item"><a href="laporan.php"><i class="fas fa-file-alt"></i><span>Laporan</span></a></div>
   <div class="menu-item" style="margin-top:auto;position:absolute;bottom:20px;">
      <a href="../user/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <!-- MAIN -->
  <div class="main">
    <div class="header">
      <h1>Kelola Booking</h1>
      <div class="user-info">
        <img src="https://i.pinimg.com/736x/18/f5/ba/18f5bab8f9181e8d7c371b16833a6849.jpg" alt="Admin">
        <span style="font-weight:600;">Admin</span>
      </div>
    </div>

    <!-- Statistik -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-ticket-alt"></i></div>
        <div class="stat-content"><h3><?= count($bookings) ?></h3><p>Total Booking</p></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-content">
          <h3><?= count(array_filter($bookings, fn($b)=> $b['status']==='Tersedia')) ?></h3>
          <p>Booking Tersedia</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
        <div class="stat-content">
          <h3><?= count(array_filter($bookings, fn($b)=> $b['status']==='Temporary')) ?></h3>
          <p>Temporary</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
        <div class="stat-content">
          <h3>
            <?= count(array_filter($bookings, fn($b)=> in_array($b['status'], ['Sudah Dipilih','Penuh']))) ?>
          </h3>
          <p>Sudah Dipilih/Penuh</p>
        </div>
      </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
      <button class="btn btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Tambah Booking</button>
    </div>

    <!-- Tabel Booking -->
    <div class="booking-table table-responsive">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Nama Pemesan</th><th>No. Telepon</th><th>Email</th>
            <th>Tgl Pemesanan</th><th>Status</th><th>Total Harga</th>
            <th>ID Bus</th><th>Metode</th><th>Kursi</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($bookings as $b): ?>
            <tr data-id="<?= $b['id_booking'] ?>">
              <td><?= $b['id_booking'] ?></td>
              <td><?= htmlspecialchars($b['nama_pemesan']) ?></td>
              <td><?= htmlspecialchars($b['no_telepon']) ?></td>
              <td><?= htmlspecialchars($b['email']) ?></td>
              <td><?= $b['tanggal_pemesanan'] ?></td>
              <td>
                <span class="status-badge status-<?= strtolower(str_replace(' ','-',$b['status'])) ?>">
                  <?= htmlspecialchars($b['status']) ?>
                </span>
              </td>
              <td>Rp <?= number_format($b['total_harga'],0,',','.') ?></td>
              <td><?= htmlspecialchars($b['id_bus']) ?></td>
              <td><?= htmlspecialchars($b['metode_pembayaran']) ?></td>
              <td><?= htmlspecialchars($b['kursi']) ?></td>
              <td>
                <button class="btn-icon btn-edit" onclick="openModal(<?= $b['id_booking'] ?>)"><i class="fas fa-pen"></i></button>
                <form method="post" style="display:inline" onsubmit="return confirm('Hapus booking ini?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id_booking" value="<?= $b['id_booking'] ?>">
                  <button type="submit" class="btn-icon btn-delete"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div><!-- /main -->

  <!-- Modal Add/Edit Booking -->
  <div class="modal" id="bookingModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle">Tambah Booking</h3>
        <span class="close" onclick="closeModal()">&times;</span>
      </div>
      <form id="bookingForm" method="post" action="booking.php">
        <input type="hidden" name="action" id="formAction" value="">
        <input type="hidden" name="id_booking" id="formId" value="">
        <div class="form-group"><label>Nama Pemesan</label><input type="text" name="nama_pemesan" id="fNama" required></div>
        <div class="form-group"><label>No. Telepon</label><input type="tel" name="no_telepon" id="fTelepon" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" id="fEmail" required></div>
        <div class="form-group"><label>Tanggal Pemesanan</label><input type="date" name="tanggal_pemesanan" id="fTanggal" required></div>
        <div class="form-group"><label>Status</label>
          <select name="status" id="fStatus" required>
            <?php foreach($statusOptions as $opt): ?>
              <option><?= $opt ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Total Harga (Rp)</label><input type="number" name="total_harga" id="fHarga" required></div>
        <div class="form-group"><label>ID Bus</label><input type="text" name="id_bus" id="fBus" required></div>
        <div class="form-group"><label>Metode Pembayaran</label>
          <select name="metode_pembayaran" id="fMetode" required>
            <option>Cash</option><option>Transfer</option>
          </select>
        </div>
        <div class="form-group"><label>Kursi (pisahkan koma)</label><input type="text" name="kursi" id="fKursi" required></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Script Modal & Form -->
  <script>
    function openModal(id=null) {
      document.getElementById('bookingModal').style.display = 'block';
      document.body.style.overflow = 'hidden';
      if (id) {
        document.getElementById('modalTitle').innerText = 'Edit Booking';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formId').value     = id;
        const row = document.querySelector(`tr[data-id="${id}"]`);
        document.getElementById('fNama').value      = row.cells[1].innerText;
        document.getElementById('fTelepon').value   = row.cells[2].innerText;
        document.getElementById('fEmail').value     = row.cells[3].innerText;
        document.getElementById('fTanggal').value   = row.cells[4].innerText;
        document.getElementById('fStatus').value    = row.cells[5].innerText.trim();
        document.getElementById('fHarga').value     = parseInt(row.cells[6].innerText.replace(/[^0-9]/g,''));
        document.getElementById('fBus').value       = row.cells[7].innerText;
        document.getElementById('fMetode').value    = row.cells[8].innerText;
        document.getElementById('fKursi').value     = row.cells[9].innerText;
      } else {
        document.getElementById('modalTitle').innerText = 'Tambah Booking';
        document.getElementById('formAction').value     = 'add';
        document.getElementById('bookingForm').reset();
      }
    }
    function closeModal() {
      document.getElementById('bookingModal').style.display = 'none';
      document.body.style.overflow = 'auto';
    }
    window.onclick = e => { if (e.target.id==='bookingModal') closeModal(); };
  </script>
</body>
</html>
