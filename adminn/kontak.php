<?php
session_start();
require 'koneksi.php';

// ========== HANDLE CREATE CONTACT ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $nama   = $_POST['nama_lengkap'];
    $email  = $_POST['email'];
    $subjek = $_POST['subjek'];
    $pesan  = $_POST['pesan'];

    $ins = $koneksi->prepare("
      INSERT INTO kontak (nama_lengkap, email, subjek, pesan, dikirim_pada)
      VALUES (?, ?, ?, ?, NOW())
    ");
    $ins->execute([$nama, $email, $subjek, $pesan]);
    header('Location: kontak.php');
    exit;
}

// ========== HANDLE EDIT CONTACT ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id     = $_POST['id'];
    $nama   = $_POST['nama_lengkap'];
    $email  = $_POST['email'];
    $subjek = $_POST['subjek'];
    $pesan  = $_POST['pesan'];

    $upd = $koneksi->prepare("
      UPDATE kontak
      SET nama_lengkap = ?, email = ?, subjek = ?, pesan = ?
      WHERE id = ?
    ");
    $upd->execute([$nama, $email, $subjek, $pesan, $id]);
    header('Location: kontak.php');
    exit;
}

// ========== HANDLE DELETE CONTACT ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = $_POST['id'];
    $del = $koneksi->prepare("DELETE FROM kontak WHERE id = ?");
    $del->execute([$id]);
    header('Location: kontak.php');
    exit;
}

// ========== FETCH ALL CONTACTS ==========
$stmt = $koneksi->query("SELECT * FROM kontak ORDER BY dikirim_pada DESC");
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total    = count($contacts);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Kontak – NOVA TRANS</title>
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo-container">
      <i class="fas fa-bus"></i><h2>NOVA TRANS</h2>
    </div>
    <div class="menu-item"><a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></div>
    <div class="menu-item"><a href="data_regist.php"><i class="fas fa-user-cog"></i><span>Data Registrasi</span></a></div>
    <div class="menu-item"><a href="data_bus.php"><i class="fas fa-database"></i><span>Data Bus</span></a></div>
    <div class="menu-item"><a href="kelola_kendaraan.php"><i class="fas fa-bus"></i><span>Kelola Kendaraan</span></a></div>
    <div class="menu-item"><a href="booking.php"><i class="fas fa-ticket-alt"></i><span>Kelola Booking</span></a></div>
    <div class="menu-item"><a href="kontak.php" class="active"><i class="fas fa-address-book"></i><span>Kelola Kontak</span></a></div>
    <div class="menu-item"><a href="laporan.php"><i class="fas fa-file-alt"></i><span>Laporan</span></a></div>
    <div class="menu-item" style="margin-top:auto;position:absolute;bottom:20px;width:100%;">
      <a href="login.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="main">
    <div class="header">
      <h1>Kelola Kontak</h1>
      <div class="user-info">
        <img src="https://i.pinimg.com/736x/18/f5/ba/18f5bab8f9181e8d7c371b16833a6849.jpg" alt="Admin">
        <span style="font-weight:600;">Admin</span>
      </div>
    </div>

    <!-- Statistik -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon total"><i class="fas fa-address-book"></i></div>
        <div class="stat-number"><?= $total ?></div>
        <div class="stat-label">Total Kontak</div>
      </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
      <button class="btn btn-primary" onclick="openModal()">
        <i class="fas fa-plus"></i> Tambah Kontak
      </button>
    </div>

    <!-- Tabel Kontak -->
    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Lengkap</th>
            <th>Email</th>
            <th>Subjek</th>
            <th>Pesan</th>
            <th>Dikirim Pada</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($contacts): foreach ($contacts as $c): ?>
            <tr data-id="<?= $c['id'] ?>">
              <td><?= $c['id'] ?></td>
              <td><?= htmlspecialchars($c['nama_lengkap']) ?></td>
              <td><?= htmlspecialchars($c['email']) ?></td>
              <td><?= htmlspecialchars($c['subjek']) ?></td>
              <td><?= nl2br(htmlspecialchars(substr($c['pesan'],0,50))) ?>…</td>
              <td><?= $c['dikirim_pada'] ?></td>
              <td>
                <button class="btn-icon btn-edit" onclick="openModal(<?= $c['id'] ?>)" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <form method="post" style="display:inline" onsubmit="return confirm('Hapus kontak ini?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
                  <button type="submit" class="btn-icon btn-delete" title="Hapus">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="7" style="text-align:center">Belum ada kontak</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Add/Edit -->
  <div class="modal" id="contactModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle">Tambah Kontak</h3>
        <span class="close" onclick="closeModal()">&times;</span>
      </div>
      <form id="contactForm" method="post" action="kontak.php">
        <input type="hidden" name="action" id="formAction" value="">
        <input type="hidden" name="id" id="formId" value="">
        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" name="nama_lengkap" id="fNama" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" id="fEmail" required>
        </div>
        <div class="form-group">
          <label>Subjek</label>
          <input type="text" name="subjek" id="fSubjek" required>
        </div>
        <div class="form-group">
          <label>Pesan</label>
          <textarea name="pesan" id="fPesan" rows="4" required></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal(id = null) {
      document.getElementById('contactModal').style.display = 'block';
      document.body.style.overflow = 'hidden';
      if (id) {
        document.getElementById('modalTitle').innerText = 'Edit Kontak';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formId').value     = id;
        const row = document.querySelector(`tr[data-id="${id}"]`);
        document.getElementById('fNama').value   = row.cells[1].innerText;
        document.getElementById('fEmail').value  = row.cells[2].innerText;
        document.getElementById('fSubjek').value = row.cells[3].innerText;
        // Ambil pesan lengkap via AJAX atau letakkan data-pesan di <tr> jika perlu
        document.getElementById('fPesan').value  = '';
      } else {
        document.getElementById('modalTitle').innerText = 'Tambah Kontak';
        document.getElementById('formAction').value     = 'add';
        document.getElementById('contactForm').reset();
      }
    }
    function closeModal() {
      document.getElementById('contactModal').style.display = 'none';
      document.body.style.overflow = 'auto';
    }
    window.onclick = e => { if (e.target.id === 'contactModal') closeModal(); };
  </script>
</body>
</html>
