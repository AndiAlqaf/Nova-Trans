<?php
session_start();
include "koneksi.php";

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if ($_POST['password'] !== $_POST['confirmPassword']) {
        $_SESSION['error'] = 'Password dan konfirmasi password tidak cocok';
        header('Location: data_regist.php');
        exit;
    }

    $nama  = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role  = $_POST['role'];
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $koneksi->prepare(
        "INSERT INTO user (nama_pengguna, email, nomor_telepon, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())"
    );
    $stmt->execute([$nama, $email, $phone, $pass, $role]);
    header('Location: data_regist.php');
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id    = $_POST['user_id'];
    $nama  = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role  = $_POST['role'];

    $stmt = $koneksi->prepare(
        "UPDATE user SET nama_pengguna = ?, email = ?, nomor_telepon = ?, role = ? WHERE id_pengguna = ?"
    );
    $stmt->execute([$nama, $email, $phone, $role, $id]);
    header('Location: data_regist.php');
    exit;
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['user_id'];
    $stmt = $koneksi->prepare("DELETE FROM user WHERE id_pengguna = ?");
    $stmt->execute([$id]);
    header('Location: data_regist.php');
    exit;
}

// Fetch all users
$query = $koneksi->query("SELECT * FROM user ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Registrasi - NOVA TRANS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo-container">
      <i class="fas fa-bus"></i>
      <h2>NOVA TRANS</h2>
    </div>
    <div class="menu-item"><a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></div>
    <div class="menu-item"><a href="data_regist.php" class="active"><i class="fas fa-user-cog"></i><span>Data Registrasi</span></a></div>
    <div class="menu-item"><a href="data_bus.php"><i class="fas fa-database"></i><span>Data Bus</span></a></div>
     <div class="menu-item"><a href="kelola_kendaraan.php"><i class="fas fa-bus"></i><span>Kelola Kendaraan</span></a></div>
    <div class="menu-item"><a href="booking.php" ><i class="fas fa-ticket-alt"></i><span>Kelola Booking</span></a></div>   
     <div class="menu-item"><a href="kontak.php"><i class="fas fa-address-book"></i><span>Kelola Kontak</span></a></div>
    <div class="menu-item"><a href="laporan.php"><i class="fas fa-file-alt"></i><span>Laporan</span></a></div>
    <div class="menu-item" style="margin-top:auto;position:absolute;bottom:20px;width:100%;"><a href="login.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></div>
  </div>

  <div class="main">
    <div class="header">
      <h1>Kelola Pengguna</h1>
      <div class="user-info">
        <img src="https://i.pinimg.com/736x/18/f5/ba/18f5bab8f9181e8d7c371b16833a6849.jpg" alt="Admin">
        <div><span style="font-weight:600;">Admin</span></div>
      </div>
    </div>
    <div class="toolbar">
      <div class="search-wrapper"><i class="fas fa-search"></i><input type="text" id="searchInput" placeholder="Cari pengguna berdasarkan nama, email, atau nomor HP..."></div>
      <div class="action-buttons">
        <button class="btn btn-outline" id="exportBtn"><i class="fas fa-file-export"></i> Export</button>
        <button class="btn btn-primary" id="addUserBtn"><i class="fas fa-plus"></i> Tambah Pengguna</button>
      </div>
    </div>
    <div class="table-container">
      <table id="usersTable">
        <thead>
          <tr>
            <th>Pengguna</th><th>Tipe Akun</th><th>Nomor HP</th><th>Status</th><th>Terdaftar</th><th>Login Terakhir</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)): ?>
            <?php $status = ($row['login_terakhir'] === date('Y-m-d H:i:s') ? 'online' : 'offline'); ?>
            <tr data-user-id="<?= $row['id_pengguna'] ?>" data-status="<?= $status ?>" data-type="<?= htmlspecialchars($row['role']) ?>" data-date="<?= date('Y-m-d', strtotime($row['created_at'])) ?>">
              <td><div class="user-cell"><div class="user-info-cell"><div class="user-name"><?= htmlspecialchars($row['nama_pengguna']) ?></div><div class="user-email"><?= htmlspecialchars($row['email']) ?></div></div></div></td>
              <td><?= htmlspecialchars($row['role']) ?></td>
              <td><?= htmlspecialchars($row['nomor_telepon']) ?></td>
              <td><span class="status status-<?= $status ?>"><?= ucfirst($status) ?></span></td>
              <td><?= $row['created_at'] ?></td>
              <td><?= $row['login_terakhir'] ?></td>
              <td class="action-cell">
                <button class="btn-icon btn-view"    data-id="<?= $row['id_pengguna'] ?>"><i class="fas fa-eye"></i></button>
                <button class="btn-icon btn-edit"    data-id="<?= $row['id_pengguna'] ?>"><i class="fas fa-pen"></i></button>
                <button class="btn-icon btn-delete"  data-id="<?= $row['id_pengguna'] ?>"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Tambah/Edit Pengguna -->
  <div class="modal" id="userModal">
    <div class="modal-content">
      <div class="modal-header"><h3 class="modal-title" id="userModalTitle">Tambah Pengguna Baru</h3><button class="modal-close">&times;</button></div>
      <div class="modal-body">
        <form id="userForm" method="post" action="data_regist.php">
          <input type="hidden" name="action" id="formAction" value="">
          <input type="hidden" name="user_id" id="formUserId" value="">
          <div class="form-group"><label for="username">Nama Lengkap</label><input type="text" name="username" id="username" class="form-control" placeholder="Masukkan nama lengkap" required></div>
          <div class="form-group"><label for="email">Email</label><input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email" required></div>
          <div class="form-group"><label for="phone">Nomor HP</label><input type="tel" name="phone" id="phone" class="form-control" placeholder="Masukkan nomor HP" required></div>
          <div class="form-group"><label for="role">Tipe Akun</label><select name="role" id="role" class="form-control" required><option value="admin">Admin</option><option value="user">User</option></select></div>
          <div class="form-group password-group"><label for="password">Password</label><input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password"></div>
          <div class="form-group password-group"><label for="confirmPassword">Konfirmasi Password</label><input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Konfirmasi password"></div>
        </form>
      </div>
      <div class="modal-footer"><button class="btn btn-outline" id="cancelBtn">Batal</button><button class="btn btn-primary" id="saveBtn">Simpan</button></div>
    </div>
  </div>

  <!-- Modal Hapus Pengguna -->
  <div class="modal" id="deleteModal">
    <div class="modal-content" style="max-width:450px;">
      <div class="modal-header"><h3 class="modal-title">Konfirmasi Hapus</h3><button class="modal-close">&times;</button></div>
      <div class="modal-body" style="text-align:center;padding:30px 20px;"><i class="fas fa-exclamation-triangle" style="font-size:48px;color:var(--danger-color);margin-bottom:20px;"></i><h4>Apakah Anda yakin ingin menghapus pengguna ini?</h4><input type="hidden" id="deleteUserId" value=""></div>
      <div class="modal-footer"><button class="btn btn-outline closeModalBtn">Batal</button><button class="btn btn-danger" id="confirmDeleteBtn">Hapus</button></div>
    </div>
  </div>

  <script>
    // References
    const userModal = document.getElementById('userModal');
    const deleteModal = document.getElementById('deleteModal');
    const addUserBtn = document.getElementById('addUserBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveBtn = document.getElementById('saveBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const form = document.getElementById('userForm');
    const formAction = document.getElementById('formAction');
    const formUserId = document.getElementById('formUserId');

    function openModal(modal){ modal.style.display='block'; document.body.style.overflow='hidden'; }
    function closeModal(modal){ modal.style.display='none'; document.body.style.overflow='auto'; }
    function closeAll(){ document.querySelectorAll('.modal').forEach(m=>closeModal(m)); }

    // Tambah
    addUserBtn.addEventListener('click', ()=>{
      form.reset(); formUserId.value=''; formAction.value='add';
      document.querySelectorAll('.password-group').forEach(el=>el.style.display='block');
      document.getElementById('userModalTitle').textContent='Tambah Pengguna Baru';
      openModal(userModal);
    });

    // Edit
    document.querySelectorAll('.btn-edit').forEach(btn=>btn.addEventListener('click', ()=>{
      const id=btn.dataset.id;
      const row=document.querySelector(`tr[data-user-id="${id}"]`);
      document.getElementById('username').value=row.querySelector('.user-name').textContent;
      document.getElementById('email').value=row.querySelector('.user-email').textContent;
      document.getElementById('phone').value=row.cells[2].textContent;
      document.getElementById('role').value=row.cells[1].textContent;
      formUserId.value=id; formAction.value='edit';
      document.querySelectorAll('.password-group').forEach(el=>el.style.display='none');
      document.getElementById('userModalTitle').textContent='Edit Pengguna'; openModal(userModal);
    }));

    // Simpan
    saveBtn.addEventListener('click', e=>{
      e.preventDefault();
      if(formAction.value==='add'){
        const pw=document.getElementById('password').value;
        const cp=document.getElementById('confirmPassword').value;
        if(pw!==cp){ alert('Password dan konfirmasi password tidak cocok'); return; }
      }
      form.submit();
    });

    // Hapus
    document.querySelectorAll('.btn-delete').forEach(btn=>btn.addEventListener('click', ()=>{
      document.getElementById('deleteUserId').value=btn.dataset.id; openModal(deleteModal);
    }));
    confirmDeleteBtn.addEventListener('click', ()=>{
      const id=document.getElementById('deleteUserId').value;
      const f=document.createElement('form'); f.method='post'; f.action='data_regist.php';
      f.innerHTML=`<input type="hidden" name="action" value="delete"><input type="hidden" name="user_id" value="${id}">`;
      document.body.appendChild(f); f.submit();
    });

    // Close modals
    document.querySelectorAll('.modal-close, .closeModalBtn').forEach(b=>b.addEventListener('click', closeAll));
    window.addEventListener('click', e=>{ if(e.target.classList.contains('modal')) closeModal(e.target); });
  </script>
</body>
</html>
