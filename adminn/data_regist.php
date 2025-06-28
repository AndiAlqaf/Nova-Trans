<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

// Function to set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        // Validate inputs
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['role'])) {
            throw new Exception('Semua field wajib diisi!');
        }

        if ($_POST['password'] !== $_POST['confirmPassword']) {
            throw new Exception('Password dan konfirmasi password tidak cocok');
        }

        // Check if email already exists
        $stmt = $koneksi->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Email sudah terdaftar!');
        }

        $nama  = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
        $role  = filter_var($_POST['role'], FILTER_SANITIZE_STRING);
        $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $koneksi->prepare(
            "INSERT INTO user (nama_pengguna, email, nomor_telepon, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$nama, $email, $phone, $pass, $role]);
        setFlashMessage('success', 'Pengguna berhasil ditambahkan!');
    } catch (Exception $e) {
        setFlashMessage('error', $e->getMessage());
    }
    header('Location: data_regist.php');
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    try {
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['role'])) {
            throw new Exception('Semua field wajib diisi!');
        }

        $id    = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
        $nama  = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
        $role  = filter_var($_POST['role'], FILTER_SANITIZE_STRING);

        // Check if email already exists for other users
        $stmt = $koneksi->prepare("SELECT COUNT(*) FROM user WHERE email = ? AND id_pengguna != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Email sudah terdaftar untuk pengguna lain!');
        }

        $stmt = $koneksi->prepare(
            "UPDATE user SET nama_pengguna = ?, email = ?, nomor_telepon = ?, role = ? WHERE id_pengguna = ?"
        );
        $stmt->execute([$nama, $email, $phone, $role, $id]);
        setFlashMessage('success', 'Pengguna berhasil diperbarui!');
    } catch (Exception $e) {
        setFlashMessage('error', $e->getMessage());
    }
    header('Location: data_regist.php');
    exit;
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
        $stmt = $koneksi->prepare("DELETE FROM user WHERE id_pengguna = ?");
        $stmt->execute([$id]);
        setFlashMessage('success', 'Pengguna berhasil dihapus!');
    } catch (Exception $e) {
        setFlashMessage('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
    }
    header('Location: data_regist.php');
    exit;
}

// Fetch all users
try {
    $query = $koneksi->query("SELECT * FROM user ORDER BY created_at DESC");
} catch (Exception $e) {
    setFlashMessage('error', 'Gagal mengambil data pengguna: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Registrasi - NOVA TRANS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .flash-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: bold;
        }
        .flash-success { background-color: #d4edda; color: #155724; }
        .flash-error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="logo-container">
        <img src="../user/Gambar/LOGO.png" alt="Nova Trans Logo" style="width:32px;height:auto;margin-right:8px;">
        <h2>NOVA TRANS</h2>
    </div>
    <div class="menu-item"><a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></div>
    <div class="menu-item"><a href="data_regist.php" class="active"><i class="fas fa-user-cog"></i><span>Data Registrasi</span></a></div>
    <div class="menu-item"><a href="data_bus.php"><i class="fas fa-database"></i><span>Data Bus</span></a></div>
    <div class="menu-item"><a href="kelola_kendaraan.php"><i class="fas fa-bus"></i><span>Kelola Kendaraan</span></a></div>
    <div class="menu-item"><a href="booking.php"><i class="fas fa-ticket-alt"></i><span>Kelola Booking</span></a></div>
    <div class="menu-item"><a href="kontak.php"><i class="fas fa-address-book"></i><span>Kelola Kontak</span></a></div>
    <div class="menu-item"><a href="testimoni.php"><i class="fas fa-comments"></i><span>Kelola Testimoni</span></a></div>
    <div class="menu-item"><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></div>
    <div class="menu-item"><a href="laporan.php"><i class="fas fa-file-alt"></i><span>Laporan</span></a></div>
    <div class="menu-item" style="margin-top:auto;position:absolute;bottom:20px;">
        <a href="../user/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<div class="main">
    <div class="header">
        <h1>Kelola Pengguna</h1>
        <div class="user-info">
            <img src="https://i.pinimg.com/736x/18/f5/ba/18f5bab8f9181e8d7c371b16833a6849.jpg" alt="Admin">
            <div><span style="font-weight:600;">Admin</span></div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <?php $flash = $_SESSION['flash']; ?>
        <div class="flash-message flash-<?php echo $flash['type']; ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="toolbar">
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari pengguna berdasarkan nama, email, atau nomor HP...">
        </div>
        <div class="action-buttons">
            <button class="btn btn-outline" id="exportBtn"><i class="fas fa-file-export"></i> Export</button>
            <button class="btn btn-primary" id="addUserBtn"><i class="fas fa-plus"></i> Tambah Pengguna</button>
        </div>
    </div>

    <div class="table-container">
        <table id="usersTable">
            <thead>
            <tr>
                <th>Pengguna</th>
                <th>Tipe Akun</th>
                <th>Nomor HP</th>
                <th>Status</th>
                <th>Terdaftar</th>
                <th>Login Terakhir</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)): ?>
                <?php $status = ($row['login_terakhir'] && $row['login_terakhir'] === date('Y-m-d H:i:s') ? 'online' : 'offline'); ?>
                <tr data-user-id="<?= htmlspecialchars($row['id_pengguna']) ?>" data-status="<?= htmlspecialchars($status) ?>" data-type="<?= htmlspecialchars($row['role']) ?>" data-date="<?= htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))) ?>">
                    <td>
                        <div class="user-cell">
                            <div class="user-info-cell">
                                <div class="user-name"><?= htmlspecialchars($row['nama_pengguna']) ?></div>
                                <div class="user-email"><?= htmlspecialchars($row['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td><?= htmlspecialchars($row['nomor_telepon']) ?></td>
                    <td><span class="status status-<?= htmlspecialchars($status) ?>"><?= ucfirst($status) ?></span></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td><?= htmlspecialchars($row['login_terakhir'] ?? '-') ?></td>
                    <td class="action-cell">
                        <button class="btn-icon btn-edit" data-id="<?= htmlspecialchars($row['id_pengguna']) ?>"><i class="fas fa-pen"></i></button>
                        <button class="btn-icon btn-delete" data-id="<?= htmlspecialchars($row['id_pengguna']) ?>"><i class="fas fa-trash"></i></button>
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
        <div class="modal-header">
            <h3 class="modal-title" id="userModalTitle">Tambah Pengguna Baru</h3>
            <button class="modal-close">×</button>
        </div>
        <div class="modal-body">
            <form id="userForm" method="post" action="data_regist.php">
                <input type="hidden" name="action" id="formAction" value="">
                <input type="hidden" name="user_id" id="formUserId" value="">
                <div class="form-group">
                    <label for="username">Nama Lengkap</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Nomor HP</label>
                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="Masukkan nomor HP" required pattern="[0-9]{10,13}">
                </div>
                <div class="form-group">
                    <label for="role">Tipe Akun</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="form-group password-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" minlength="6">
                </div>
                <div class="form-group password-group">
                    <label for="confirmPassword">Konfirmasi Password</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Konfirmasi password" minlength="6">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" id="cancelBtn">Batal</button>
            <button class="btn btn-primary" id="saveBtn">Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Hapus Pengguna -->
<div class="modal" id="deleteModal">
    <div class="modal-content" style="max-width:450px;">
        <div class="modal-header">
            <h3 class="modal-title">Konfirmasi Hapus</h3>
            <button class="modal-close">×</button>
        </div>
        <div class="modal-body" style="text-align:center;padding:30px 20px;">
            <i class="fas fa-exclamation-triangle" style="font-size:48px;color:var(--danger-color);margin-bottom:20px;"></i>
            <h4>Apakah Anda yakin ingin menghapus pengguna ini?</h4>
            <input type="hidden" id="deleteUserId" value="">
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline closeModalBtn">Batal</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
        </div>
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
    const searchInput = document.getElementById('searchInput');

    function openModal(modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function closeAll() {
        document.querySelectorAll('.modal').forEach(m => closeModal(m));
    }

    // Tambah
    addUserBtn.addEventListener('click', () => {
        form.reset();
        formUserId.value = '';
        formAction.value = 'add';
        document.querySelectorAll('.password-group').forEach(el => el.style.display = 'block');
        document.getElementById('userModalTitle').textContent = 'Tambah Pengguna Baru';
        openModal(userModal);
    });

    // Edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const row = document.querySelector(`tr[data-user-id="${id}"]`);
            document.getElementById('username').value = row.querySelector('.user-name').textContent;
            document.getElementById('email').value = row.querySelector('.user-email').textContent;
            document.getElementById('phone').value = row.cells[2].textContent;
            document.getElementById('role').value = row.cells[1].textContent;
            formUserId.value = id;
            formAction.value = 'edit';
            document.querySelectorAll('.password-group').forEach(el => el.style.display = 'none');
            document.getElementById('userModalTitle').textContent = 'Edit Pengguna';
            openModal(userModal);
        });
    });

    // Simpan
    saveBtn.addEventListener('click', e => {
        e.preventDefault();
        if (formAction.value === 'add') {
            const pw = document.getElementById('password').value;
            const cp = document.getElementById('confirmPassword').value;
            if (!pw || !cp) {
                alert('Password wajib diisi!');
                return;
            }
            if (pw !== cp) {
                alert('Password dan konfirmasi password tidak cocok');
                return;
            }
        }
        form.submit();
    });

    // Hapus
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('deleteUserId').value = btn.dataset.id;
            openModal(deleteModal);
        });
    });

    confirmDeleteBtn.addEventListener('click', () => {
        const id = document.getElementById('deleteUserId').value;
        const f = document.createElement('form');
        f.method = 'post';
        f.action = 'data_regist.php';
        f.innerHTML = `<input type="hidden" name="action" value="delete"><input type="hidden" name="user_id" value="${id}">`;
        document.body.appendChild(f);
        f.submit();
    });

    // Search functionality
    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('#usersTable tbody tr');
        rows.forEach(row => {
            const name = row.querySelector('.user-name').textContent.toLowerCase();
            const email = row.querySelector('.user-email').textContent.toLowerCase();
            const phone = row.cells[2].textContent.toLowerCase();
            row.style.display = (name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm)) ? '' : 'none';
        });
    });

    // Close modals
    document.querySelectorAll('.modal-close, .closeModalBtn').forEach(b => {
        b.addEventListener('click', closeAll);
    });

    window.addEventListener('click', e => {
        if (e.target.classList.contains('modal')) {
            closeModal(e.target);
        }
    });

    // Export functionality (CSV example)
    document.getElementById('exportBtn').addEventListener('click', () => {
        const rows = document.querySelectorAll('#usersTable tbody tr');
        let csv = 'Nama,Email,Nomor HP,Tipe Akun,Status,Terdaftar,Login Terakhir\n';
        rows.forEach(row => {
            const cells = row.cells;
            csv += `"${cells[0].querySelector('.user-name').textContent}",` +
                `"${cells[0].querySelector('.user-email').textContent}",` +
                `"${cells[2].textContent}",` +
                `"${cells[1].textContent}",` +
                `"${cells[3].textContent}",` +
                `"${cells[4].textContent}",` +
                `"${cells[5].textContent}"\n`;
        });
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('href', url);
        a.setAttribute('download', 'users_export.csv');
        a.click();
    });
</script>
</body>
</html>