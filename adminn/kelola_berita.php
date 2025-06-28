<?php
// kelola_berita.php – Kelola Berita NOVA TRANS
require_once __DIR__ . '/../koneksi.php';

// 1. Cegah browser cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 2. Session & Auth
session_start();
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../masuk.php');
    exit;
}

$adminName = htmlspecialchars($_SESSION['email']);

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Query berita dengan pencarian
$sql = "SELECT * FROM berita WHERE judul LIKE :search ORDER BY tanggal DESC";
$stmt = $koneksi->prepare($sql);
$stmt->bindValue(':search', "%$search%");
$stmt->execute();
$berita = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pesan sukses atau error
$success = isset($_GET['berhasil']) && $_GET['berhasil'] == '1' ? "Berita berhasil ditambahkan!" : null;
$success_edit = isset($_GET['berhasil_edit']) && $_GET['berhasil_edit'] == '1' ? "Berita berhasil diperbarui!" : null;
$success_hapus = isset($_GET['berhasil_hapus']) && $_GET['berhasil_hapus'] == '1' ? "Berita berhasil dihapus!" : null;
$error_hapus = isset($_GET['error_hapus']) && $_GET['error_hapus'] == '1' ? "Gagal menghapus berita. Silakan coba lagi." : null;

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_POST['action'] === 'add') {
            $judul = trim($_POST['judul']);
            $deskripsi = trim($_POST['deskripsi']);
            $teks_lengkap = trim($_POST['teks_lengkap']);
            $gambar = trim($_POST['gambar']);
            if ($judul && $deskripsi && $teks_lengkap && $gambar) {
                $stmt = $koneksi->prepare("INSERT INTO berita (judul, deskripsi, teks_lengkap, gambar, tanggal) VALUES (:judul, :deskripsi, :teks_lengkap, :gambar, NOW())");
                $stmt->bindParam(':judul', $judul);
                $stmt->bindParam(':deskripsi', $deskripsi);
                $stmt->bindParam(':teks_lengkap', $teks_lengkap);
                $stmt->bindParam(':gambar', $gambar);
                $stmt->execute();
                echo json_encode(['status' => 'success', 'message' => 'Berita berhasil ditambahkan!', 'id' => $koneksi->lastInsertId()]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi.']);
            }
        } elseif ($_POST['action'] === 'edit') {
            $id = trim($_POST['id']);
            $judul = trim($_POST['judul']);
            $deskripsi = trim($_POST['deskripsi']);
            $teks_lengkap = trim($_POST['teks_lengkap']);
            $gambar = trim($_POST['gambar']);
            if ($id && $judul && $deskripsi && $teks_lengkap && $gambar) {
                $stmt = $koneksi->prepare("UPDATE berita SET judul = :judul, deskripsi = :deskripsi, teks_lengkap = :teks_lengkap, gambar = :gambar, tanggal = NOW() WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':judul', $judul);
                $stmt->bindParam(':deskripsi', $deskripsi);
                $stmt->bindParam(':teks_lengkap', $teks_lengkap);
                $stmt->bindParam(':gambar', $gambar);
                $stmt->execute();
                echo json_encode(['status' => 'success', 'message' => 'Berita berhasil diperbarui!', 'id' => $id]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi.']);
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = trim($_POST['id']);
            if ($id) {
                $stmt = $koneksi->prepare("DELETE FROM berita WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                echo json_encode(['status' => 'success', 'message' => 'Berita berhasil dihapus!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Berita – NOVA TRANS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .success {
            color: #2e7d32;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
        }
        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
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
        <div class="menu-item"><a href="testimoni.php"><i class="fas fa-comments"></i><span>Kelola Testimoni</span></a></div>
        <div class="menu-item"><a href="kelola_berita.php" class="active"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></div>
        <div class="menu-item"><a href="laporan.php"><i class="fas fa-file-alt"></i><span>Laporan</span></a></div>
        <div class="menu-item" style="margin-top:auto;position:absolute;bottom:20px;">
            <a href="../user/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>

    <div class="main">
        <div class="header">
            <h1>Kelola Berita</h1>
            <div class="actions">
                <a href="export_berita.php" class="btn btn-outline">Export</a>
                <button class="btn btn-primary" id="addNewsBtn">Tambah Berita</button>
            </div>
        </div>

        <div class="table-container">
            <?php if ($success): ?>
                <div class="status-badge status-tersedia" style="margin-bottom: 15px;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            <?php if ($success_edit): ?>
                <div class="status-badge status-tersedia" style="margin-bottom: 15px;">
                    <?php echo $success_edit; ?>
                </div>
            <?php endif; ?>
            <?php if ($success_hapus): ?>
                <div class="status-badge status-tersedia" style="margin-bottom: 15px;">
                    <?php echo $success_hapus; ?>
                </div>
            <?php endif; ?>
            <?php if ($error_hapus): ?>
                <div class="status-badge" style="background-color: #fee2e2; color: #991b1b; margin-bottom: 15px;">
                    <?php echo $error_hapus; ?>
                </div>
            <?php endif; ?>
            <table id="newsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Teks Lengkap</th>
                        <th>Gambar</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($berita as $b): ?>
                        <tr data-id="<?php echo htmlspecialchars($b['id']); ?>">
                            <td><?php echo htmlspecialchars($b['id']); ?></td>
                            <td><?php echo htmlspecialchars($b['judul']); ?></td>
                            <td><?php echo htmlspecialchars(substr($b['deskripsi'], 0, 50)) . (strlen($b['deskripsi']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo htmlspecialchars(substr($b['teks_lengkap'], 0, 50)) . (strlen($b['teks_lengkap']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo htmlspecialchars($b['gambar']); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($b['tanggal'])); ?></td>
                            <td class="actions-col">
                                <button class="btn-icon btn-edit" 
                                        data-id="<?php echo htmlspecialchars($b['id']); ?>" 
                                        data-judul="<?php echo htmlspecialchars($b['judul'], ENT_QUOTES, 'UTF-8'); ?>" 
                                        data-deskripsi="<?php echo htmlspecialchars($b['deskripsi'], ENT_QUOTES, 'UTF-8'); ?>" 
                                        data-teks_lengkap="<?php echo htmlspecialchars($b['teks_lengkap'], ENT_QUOTES, 'UTF-8'); ?>" 
                                        data-gambar="<?php echo htmlspecialchars($b['gambar'], ENT_QUOTES, 'UTF-8'); ?>" 
                                        onclick="showEditModal(this)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon btn-delete" onclick="deleteNews(<?php echo htmlspecialchars($b['id']); ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal untuk Tambah Berita -->
        <div id="addNewsModal" class="modal">
            <div class="modal-content">
                <button class="close-modal" id="closeAddModal">×</button>
                <h3>Tambah Berita Baru</h3>
                <form id="addNewsForm">
                    <div class="form-group">
                        <label for="add_judul">Judul</label>
                        <input type="text" name="judul" id="add_judul" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="add_deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" id="add_deskripsi" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="add_teks_lengkap">Teks Lengkap</label>
                        <textarea name="teks_lengkap" id="add_teks_lengkap" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="add_gambar">Nama File Gambar (contoh: berita1.png)</label>
                        <input type="text" name="gambar" id="add_gambar" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="saveAddBtn">Simpan</button>
                        <button type="button" class="btn btn-cancel" id="cancelAddModal">Batal</button>
                    </div>
                </form>
                <div id="addModalMessage" style="margin-top: 10px; display: none;"></div>
            </div>
        </div>

        <!-- Modal untuk Edit Berita -->
        <div id="editNewsModal" class="modal">
            <div class="modal-content">
                <button class="close-modal" id="closeEditModal">×</button>
                <h3>Edit Berita</h3>
                <form id="editNewsForm">
                    <input type="hidden" name="id" id="edit_newsId">
                    <div class="form-group">
                        <label for="edit_judul">Judul</label>
                        <input type="text" name="judul" id="edit_judul" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_teks_lengkap">Teks Lengkap</label>
                        <textarea name="teks_lengkap" id="edit_teks_lengkap" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_gambar">Nama File Gambar (contoh: berita1.png)</label>
                        <input type="text" name="gambar" id="edit_gambar" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="saveEditBtn">Simpan</button>
                        <button type="button" class="btn btn-cancel" id="cancelEditModal">Batal</button>
                    </div>
                </form>
                <div id="editModalMessage" style="margin-top: 10px; display: none;"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Buka modal tambah
            $('#addNewsBtn').click(function() {
                $('#addNewsModal').css('display', 'flex');
                $('#addNewsForm')[0].reset();
                $('#addModalMessage').hide();
            });

            // Tutup modal tambah
            $('#closeAddModal, #cancelAddModal').click(function() {
                $('#addNewsModal').hide();
                $('#addModalMessage').hide().text('');
            });

            // Tutup modal edit
            $('#closeEditModal, #cancelEditModal').click(function() {
                $('#editNewsModal').hide();
                $('#editModalMessage').hide().text('');
            });

            // Submit form tambah
            $('#addNewsForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'kelola_berita.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=add',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#addModalMessage').text(response.message).removeClass('error').addClass('success').show();
                            setTimeout(function() {
                                $('#addNewsModal').hide();
                                $('#addModalMessage').hide();
                                $.post('kelola_berita.php', { search: '<?php echo htmlspecialchars($search); ?>' }, function(data) {
                                    let parser = new DOMParser();
                                    let doc = parser.parseFromString(data, 'text/html');
                                    let newRow = doc.querySelector('tbody tr:last-child').outerHTML;
                                    $('#newsTable tbody').append(newRow);
                                });
                            }, 1500);
                        } else {
                            $('#addModalMessage').text(response.message).removeClass('success').addClass('error').show();
                        }
                    },
                    error: function() {
                        $('#addModalMessage').text('Terjadi error saat menyimpan berita.').removeClass('success').addClass('error').show();
                    }
                });
            });

            // Submit form edit
            $('#editNewsForm').submit(function(e) {
                e.preventDefault();
                const id = $('#edit_newsId').val();
                $.ajax({
                    url: 'kelola_berita.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=edit',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#editModalMessage').text(response.message).removeClass('error').addClass('success').show();
                            setTimeout(function() {
                                $('#editNewsModal').hide();
                                $('#editModalMessage').hide();
                                $.post('kelola_berita.php', { search: '<?php echo htmlspecialchars($search); ?>' }, function(data) {
                                    let parser = new DOMParser();
                                    let doc = parser.parseFromString(data, 'text/html');
                                    let updatedRow = doc.querySelector(`tr[data-id="${id}"]`);
                                    if (updatedRow) {
                                        $(`tr[data-id="${id}"]`).replaceWith(updatedRow.outerHTML);
                                    }
                                });
                            }, 1500);
                        } else {
                            $('#editModalMessage').text(response.message).removeClass('success').addClass('error').show();
                        }
                    },
                    error: function() {
                        $('#editModalMessage').text('Terjadi error saat menyimpan berita.').removeClass('success').addClass('error').show();
                    }
                });
            });

            // Buka modal edit
            window.showEditModal = function(button) {
                const id = $(button).data('id');
                const judul = $(button).data('judul');
                const deskripsi = $(button).data('deskripsi');
                const teks_lengkap = $(button).data('teks_lengkap');
                const gambar = $(button).data('gambar');

                $('#editNewsModal').css('display', 'flex');
                $('#edit_newsId').val(id);
                $('#edit_judul').val(judul);
                $('#edit_deskripsi').val(deskripsi);
                $('#edit_teks_lengkap').val(teks_lengkap);
                $('#edit_gambar').val(gambar);
                $('#editModalMessage').hide();
            };

            // Hapus berita
            window.deleteNews = function(id) {
                if (confirm('Yakin ingin menghapus berita ini?')) {
                    $.ajax({
                        url: 'kelola_berita.php',
                        type: 'POST',
                        data: { action: 'delete', id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                $(`tr[data-id="${id}"]`).remove();
                                alert(response.message);
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function() {
                            alert('Terjadi error saat menghapus berita.');
                        }
                    });
                }
            };

            // Tutup modal jika klik di luar
            $(window).click(function(e) {
                if (e.target.id === 'addNewsModal') {
                    $('#addNewsModal').hide();
                    $('#addModalMessage').hide().text('');
                } else if (e.target.id === 'editNewsModal') {
                    $('#editNewsModal').hide();
                    $('#editModalMessage').hide().text('');
                }
            });
        });
    </script>
</body>
</html>