<?php
session_start();
include "koneksi.php";  // pastikan $koneksi adalah PDO

$error   = "";
$success = "";

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $stmt = $koneksi->prepare("UPDATE testimoni SET name = ?, message = ? WHERE id = ?");
    if ($stmt->execute([ $_POST['name'], $_POST['message'], $_POST['id'] ])) {
        header("Location: ".$_SERVER['PHP_SELF']."?updated=1");
        exit;
    } else {
        $error = "Gagal memperbarui testimoni.";
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $stmt = $koneksi->prepare("DELETE FROM testimoni WHERE id = ?");
    if ($stmt->execute([ $_POST['id'] ])) {
        header("Location: ".$_SERVER['PHP_SELF']."?deleted=1");
        exit;
    } else {
        $error = "Gagal menghapus testimoni.";
    }
}

// Fetch all testimoni
$query = $koneksi->query("SELECT * FROM testimoni ORDER BY submitted_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin – Kelola Testimoni</title>
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo-container">
      <img src="../user/Gambar/LOGO.png" alt="Nova Trans Logo" style="width:32px;height:auto;margin-right:8px;">
      <h2>NOVA TRANS</h2>
    </div>
    <div class="menu-item"><a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></div>
    <div class="menu-item"><a href="data_regist.php"><i class="fas fa-user-cog"></i><span>Data Registrasi</span></a></div>
    <div class="menu-item"><a href="data_bus.php"><i class="fas fa-database"></i><span>Data Bus</span></a></div>
    <div class="menu-item"><a href="kelola_kendaraan.php"><i class="fas fa-bus"></i><span>Kelola Kendaraan</span></a></div>
    <div class="menu-item"><a href="booking.php"><i class="fas fa-ticket-alt"></i><span>Kelola Booking</span></a></div>
    <div class="menu-item"><a href="kontak.php"><i class="fas fa-address-book"></i><span>Kelola Kontak</span></a></div>
    <div class="menu-item"><a href="testimoni.php" class="active"><i class="fas fa-comments"></i><span>Kelola Testimoni</span></a></div>
    <div class="menu-item"><a href="laporan.php"><i class="fas fa-file-alt"></i><span>Laporan</span></a></div>
    <div class="menu-item" style="margin-top:auto;position:absolute;bottom:20px;width:100%;">
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="main">
    <div class="header">
      <h1>Kelola Testimoni</h1>
      <div class="user-info">
        <img src="https://i.pinimg.com/736x/18/f5/ba/18f5bab8f9181e8d7c371b16833a6849.jpg" alt="Admin">
        <div><span style="font-weight:600;">Admin</span></div>
      </div>
    </div>

    <div class="toolbar">
      <div class="search-wrapper">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari testimoni berdasarkan nama atau pesan...">
      </div>
      <div class="action-buttons">
        <button class="btn btn-outline" id="exportBtn"><i class="fas fa-file-export"></i> Export</button>
      </div>
    </div>

    <div class="table-container">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Testimoni berhasil dihapus.</div>
      <?php endif; ?>
      <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Testimoni berhasil diperbarui.</div>
      <?php endif; ?>

      <table id="testimoniTable">
        <thead>
          <tr>
            <th>Id</th>
            <th>Nama</th>
            <th>Pesan</th>
            <th>Dikirim pada</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)): ?>
          <tr data-id="<?= $row['id'] ?>">
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
            <td><?= htmlspecialchars($row['submitted_at']) ?></td>
            <td class="action-cell">
              <button class="btn-icon btn-edit" data-id="<?= $row['id'] ?>"><i class="fas fa-pen"></i></button>
              <button class="btn-icon btn-delete" data-id="<?= $row['id'] ?>"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Edit Testimoni -->
  <div class="modal" id="editModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Edit Testimoni</h3>
        <button class="modal-close">&times;</button>
      </div>
      <div class="modal-body">
        <form id="editForm" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" id="editId">
          <div class="form-group">
            <label for="editName">Nama</label>
            <input type="text" name="name" id="editName" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="editMessage">Pesan</label>
            <textarea name="message" id="editMessage" rows="4" class="form-control" required></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline modal-close">Batal</button>
        <button class="btn btn-primary" id="saveEditBtn">Simpan</button>
      </div>
    </div>
  </div>

  <!-- Modal Hapus Testimoni -->
  <div class="modal" id="deleteModal">
    <div class="modal-content" style="max-width:450px;">
      <div class="modal-header">
        <h3 class="modal-title">Konfirmasi Hapus</h3>
        <button class="modal-close">&times;</button>
      </div>
      <div class="modal-body" style="text-align:center;padding:30px 20px;">
        <i class="fas fa-exclamation-triangle" style="font-size:48px;color:#dc3545;margin-bottom:20px;"></i>
        <h4>Apakah Anda yakin ingin menghapus testimoni ini?</h4>
        <input type="hidden" id="deleteId">
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline modal-close">Batal</button>
        <button class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
      </div>
    </div>
  </div>

  <script>
    // helper
    const modals = document.querySelectorAll('.modal');
    function openModal(m){ m.style.display='block'; document.body.style.overflow='hidden'; }
    function closeModal(m){ m.style.display='none'; document.body.style.overflow='auto'; }
    document.querySelectorAll('.modal-close').forEach(b=>b.addEventListener('click', ()=>modals.forEach(closeModal)));

    // Edit
    document.querySelectorAll('.btn-edit').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const id = btn.dataset.id;
        const tr = document.querySelector(`tr[data-id="${id}"]`);
        document.getElementById('editId').value = id;
        document.getElementById('editName').value = tr.children[1].innerText.trim();
        document.getElementById('editMessage').value = tr.children[2].innerText.trim();
        openModal(document.getElementById('editModal'));
      });
    });
    document.getElementById('saveEditBtn').addEventListener('click', ()=>{
      document.getElementById('editForm').submit();
    });

    // Delete
    document.querySelectorAll('.btn-delete').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        document.getElementById('deleteId').value = btn.dataset.id;
        openModal(document.getElementById('deleteModal'));
      });
    });
    document.getElementById('confirmDeleteBtn').addEventListener('click', ()=>{
      const f = document.createElement('form');
      f.method = 'post'; f.action = '<?= $_SERVER['PHP_SELF'] ?>';
      f.innerHTML = `
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="${document.getElementById('deleteId').value}">
      `;
      document.body.appendChild(f);
      f.submit();
    });

    // Search filter
    document.getElementById('searchInput').addEventListener('input', e=>{
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('#testimoniTable tbody tr').forEach(tr=>{
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  </script>
</body>
</html>
