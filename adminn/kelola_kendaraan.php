<?php
session_start();
include "koneksi.php"; // $koneksi = PDO

// --- 1) Proses Tambah / Edit / Delete ---
$errorMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act       = $_POST['action'] ?? '';
    $id         = trim($_POST['id_kendaraan'] ?? '');
    $nopol      = trim($_POST['nomor_polisi'] ?? '');
    $jenis      = trim($_POST['jenis'] ?? '');
    $kapasitas  = trim($_POST['kapasitas'] ?? '');
    $status     = trim($_POST['status'] ?? '');
    $lastMaint  = trim($_POST['perawatan_terakhir'] ?? '');
    $nextMaint  = trim($_POST['perawatan_berikutnya'] ?? '');

    try {
        if ($act === 'add') {
            $sql = "INSERT INTO kendaraan
                      (id_kendaraan, nomor_polisi, jenis, kapasitas, status, tanggal_perawatan_terakhir, tanggal_perawatan_berikutnya)
                    VALUES (?,?,?,?,?,?,?)";
            $stmt = $koneksi->prepare($sql);
            $stmt->execute([$id,$nopol,$jenis,$kapasitas,$status,$lastMaint,$nextMaint]);
        }
        elseif ($act === 'edit') {
            $sql = "UPDATE kendaraan SET
                      nomor_polisi=?, jenis=?, kapasitas=?, status=?, tanggal_perawatan_terakhir=?, tanggal_perawatan_berikutnya=?
                    WHERE id_kendaraan=?";
            $stmt = $koneksi->prepare($sql);
            $stmt->execute([$nopol,$jenis,$kapasitas,$status,$lastMaint,$nextMaint,$id]);
        }
        elseif ($act === 'delete') {
            $sql = "DELETE FROM kendaraan WHERE id_kendaraan=?";
            $koneksi->prepare($sql)->execute([$id]);
        }
        header('Location: kelola_kendaraan.php');
        exit;
    } catch(PDOException $e) {
        $errorMsg = $e->getMessage();
    }
}

// --- 2) Ambil semua kendaraan ---
$query = $koneksi->query("SELECT * FROM kendaraan ORDER BY id_kendaraan");
$rows  = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Kendaraan – NOVA TRANS</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
    <div class="logo-container">
      <i class="fas fa-bus"></i>
      <h2>NOVA TRANS</h2>
    </div>
    
    <div class="menu-item">
      <a href="dashboard.php">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
      </a>
    </div>
    
    <div class="menu-item">
      <a href="data_regist.php">
        <i class="fas fa-user-cog"></i>
        <span>Data Registrasi</span>
      </a>
    </div>
    
    
    
    <div class="menu-item">
      <a href="data_bus.php">
        <i class="fas fa-database"></i>
        <span>Data Bus</span>
      </a>
    </div>

<div class="menu-item">
      <a href="kelola_kendaraan.php" class="active">
        <i class="fas fa-bus"></i>
        <span>Kelola Kendaraan</span>
      </a>
    </div>

    <div class="menu-item">
            <a href="booking.php">
                <i class="fas fa-ticket-alt"></i>
                <span>Kelola Booking</span>
            </a>
        </div>
        
        <div class="menu-item">
          <a href="kontak.php">
            <i class="fas fa-address-book">
            </i><span>Kelola Kontak</span>
          </a>
        </div>
    
    <div class="menu-item">
      <a href="laporan.php">
        <i class="fas fa-file-alt"></i>
        <span>Laporan</span>
      </a>
    </div>
    
    <div class="menu-item" style="margin-top: auto; position: absolute; bottom: 20px; width: 100%;">
      <a href="login.php">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>

  <div class="main">
    <div class="header">
      <h1>Kelola Kendaraan</h1>
      <div class="user-info">
        <img src="https://i.pinimg.com/736x/18/f5/ba/18f5bab8f9181e8d7c371b16833a6849.jpg" alt="Admin">
        <div><span style="font-weight:600;">Admin</span></div>
      </div>
    </div>

    <?php if($errorMsg): ?>
      <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <div class="page-title"><i class="fas fa-bus"></i> Daftar Kendaraan</div>

    <div class="control-panel">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari nomor polisi, jenis, dll...">
      </div>
      <button class="btn btn-primary" id="btnTambahKendaraan"><i class="fas fa-plus"></i> Tambah Kendaraan</button>
      <div class="filter-options">
        <select id="filterStatus">
          <option value="">Semua Status</option>
          <option value="tersedia">Tersedia</option>
          <option value="perawatan">Perawatan</option>
          <option value="digunakan">Digunakan</option>
        </select>
        <select id="filterJenis">
          <option value="">Semua Jenis</option>
          <option value="Bus Besar">Bus Besar</option>
          <option value="Bus Sedang">Bus Sedang</option>
          <option value="Mini Bus">Mini Bus</option>
        </select>
      </div>
    </div>

    <div class="data-card">
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nomor Polisi</th>
              <th>Jenis</th>
              <th>Kapasitas</th>
              <th>Status</th>
              <th>Perawatan Terakhir</th>
              <th>Perawatan Berikutnya</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="kendaraanTableBody">
            <?php foreach($rows as $r): ?>
            <tr data-id="<?= $r['id_kendaraan']?>"
                data-nopol="<?= htmlspecialchars($r['nomor_polisi'],ENT_QUOTES)?>"
                data-jenis="<?= htmlspecialchars($r['jenis'],ENT_QUOTES)?>"
                data-kapasitas="<?= $r['kapasitas']?>"
                data-status="<?= htmlspecialchars($r['status'],ENT_QUOTES)?>"
                data-last-maintenance="<?= $r['tanggal_perawatan_terakhir']?>"
                data-next-maintenance="<?= $r['tanggal_perawatan_berikutnya']?>">
              <td><?= $r['id_kendaraan'] ?></td>
              <td><?= htmlspecialchars($r['nomor_polisi']) ?></td>
              <td><?= htmlspecialchars($r['jenis']) ?></td>
              <td><?= $r['kapasitas'] ?></td>
              <td><span class="status-badge status-<?= $r['status']?>"><?= ucfirst($r['status'])?></span></td>
              <td><?= $r['tanggal_perawatan_terakhir'] ?></td>
              <td><?= $r['tanggal_perawatan_berikutnya'] ?></td>
              <td>
                <div class="action-buttons">
                  <button class="btn-icon btn-view"   onclick="showDetailModal('<?= $r['id_kendaraan']?>')">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="btn-icon btn-edit"   onclick="showEditModal('<?= $r['id_kendaraan']?>')">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn-icon btn-delete" onclick="showDeleteModal('<?= $r['id_kendaraan']?>')">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Tambah -->
  <div id="modalTambahKendaraan" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2><i class="fas fa-plus-circle"></i> Tambah Kendaraan</h2>
        <span class="close" data-modal="modalTambahKendaraan">&times;</span>
      </div>
      <form id="formTambahKendaraan" method="post" action="kelola_kendaraan.php">
        <input type="hidden" name="action" value="add">
        <div class="form-group"><label>ID Kendaraan</label><input name="id_kendaraan" required></div>
        <div class="form-group"><label>Nomor Polisi</label><input name="nomor_polisi" required></div>
        <div class="form-group">
          <label>Jenis</label>
          <select name="jenis" required>
            <option>Bus Besar</option>
            <option>Bus Sedang</option>
            <option>Mini Bus</option>
          </select>
        </div>
        <div class="form-group"><label>Kapasitas</label><input type="number" name="kapasitas" required></div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" required>
            <option value="tersedia">Tersedia</option>
            <option value="perawatan">Perawatan</option>
            <option value="digunakan">Digunakan</option>
          </select>
        </div>
        <div class="form-group"><label>Perawatan Terakhir</label><input type="date" name="perawatan_terakhir" required></div>
        <div class="form-group"><label>Perawatan Berikutnya</label><input type="date" name="perawatan_berikutnya" required></div>
        <div class="form-footer">
          <button type="button" class="btn btn-cancel" onclick="closeModal('modalTambahKendaraan')">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Edit -->
  <div id="modalEditKendaraan" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2><i class="fas fa-edit"></i> Edit Kendaraan</h2>
        <span class="close" data-modal="modalEditKendaraan">&times;</span>
      </div>
      <form id="formEditKendaraan" method="post" action="kelola_kendaraan.php">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id_kendaraan" id="edit_id">
        <div class="form-group"><label>Nomor Polisi</label><input name="nomor_polisi" id="edit_nopol" required></div>
        <div class="form-group">
          <label>Jenis</label>
          <select name="jenis" id="edit_jenis" required>
            <option>Bus Besar</option>
            <option>Bus Sedang</option>
            <option>Mini Bus</option>
          </select>
        </div>
        <div class="form-group"><label>Kapasitas</label><input type="number" name="kapasitas" id="edit_kapasitas" required></div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" id="edit_status" required>
            <option value="tersedia">Tersedia</option>
            <option value="perawatan">Perawatan</option>
            <option value="digunakan">Digunakan</option>
          </select>
        </div>
        <div class="form-group"><label>Perawatan Terakhir</label><input type="date" name="perawatan_terakhir" id="edit_perawatan_terakhir" required></div>
        <div class="form-group"><label>Perawatan Berikutnya</label><input type="date" name="perawatan_berikutnya" id="edit_perawatan_berikutnya" required></div>
        <div class="form-footer">
          <button type="button" class="btn btn-cancel" onclick="closeModal('modalEditKendaraan')">Batal</button>
          <button type="submit" class="btn btn-primary">Perbarui</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Detail -->
  <div id="modalDetailKendaraan" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2><i class="fas fa-info-circle"></i> Detail Kendaraan</h2>
        <span class="close" data-modal="modalDetailKendaraan">&times;</span>
      </div>
      <div class="detail-container">
        <div class="detail-row"><div class="detail-label">ID</div><div class="detail-value" id="detail_id"></div></div>
        <div class="detail-row"><div class="detail-label">Nopol</div><div class="detail-value" id="detail_nopol"></div></div>
        <div class="detail-row"><div class="detail-label">Jenis</div><div class="detail-value" id="detail_jenis"></div></div>
        <div class="detail-row"><div class="detail-label">Kapasitas</div><div class="detail-value" id="detail_kapasitas"></div></div>
        <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value" id="detail_status"></div></div>
        <div class="detail-row"><div class="detail-label">Perawatan Terakhir</div><div class="detail-value" id="detail_last"></div></div>
        <div class="detail-row"><div class="detail-label">Perawatan Berikutnya</div><div class="detail-value" id="detail_next"></div></div>
      </div>
      <div class="form-footer">
        <button class="btn btn-cancel" onclick="closeModal('modalDetailKendaraan')">Tutup</button>
        <button class="btn btn-primary" id="btnEditFromDetail">Edit</button>
      </div>
    </div>
  </div>

  <!-- Modal Delete -->
  <div id="modalDeleteKendaraan" class="modal">
    <div class="modal-content" style="max-width:450px;">
      <div class="modal-header">
        <h2><i class="fas fa-exclamation-triangle" style="color:red"></i> Konfirmasi Hapus</h2>
        <span class="close" data-modal="modalDeleteKendaraan">&times;</span>
      </div>
      <div style="padding:20px;text-align:center">
        <p>Hapus kendaraan:</p>
        <p id="delete_info" style="font-weight:600"></p>
      </div>
      <div class="form-footer">
        <button class="btn btn-cancel" onclick="closeModal('modalDeleteKendaraan')">Batal</button>
        <button class="btn btn-danger" id="btnConfirmDelete">Hapus</button>
      </div>
    </div>
  </div>

  <!-- Notifikasi -->
  <div id="notificationContainer"></div>

  <script>
    let currentId = null;
    document.addEventListener('DOMContentLoaded',()=>{
      document.getElementById('btnTambahKendaraan').onclick = ()=>openModal('modalTambahKendaraan');
      document.querySelectorAll('.close').forEach(btn=>btn.onclick=()=>closeModal(btn.dataset.modal));
      window.onclick = e=>{ if(e.target.classList.contains('modal')) closeModal(e.target.id); };
      document.getElementById('searchInput').oninput = filterTable;
      document.getElementById('filterStatus').onchange = filterTable;
      document.getElementById('filterJenis').onchange = filterTable;
      document.getElementById('btnEditFromDetail').onclick = ()=>{ closeModal('modalDetailKendaraan'); showEditModal(currentId); };
      document.getElementById('btnConfirmDelete').onclick = deleteKendaraan;
    });
    function openModal(id){document.getElementById(id).style.display='block'}
    function closeModal(id){document.getElementById(id).style.display='none'}
    function filterTable(){
      const s=document.getElementById('searchInput').value.toLowerCase(),
            fs=document.getElementById('filterStatus').value,
            fj=document.getElementById('filterJenis').value;
      document.querySelectorAll('#kendaraanTableBody tr').forEach(tr=>{
        const id=tr.dataset.id.toLowerCase(),
              nopol=tr.dataset.nopol.toLowerCase(),
              jenis=tr.dataset.jenis.toLowerCase(),
              status=tr.dataset.status.toLowerCase();
        const okS = id.includes(s)||nopol.includes(s)||jenis.includes(s),
              okF = !fs||status===fs,
              okJ = !fj||jenis===fj.toLowerCase();
        tr.style.display = okS&&okF&&okJ?'':'none';
      });
    }
    function showDetailModal(id){
      currentId=id;
      const r=document.querySelector(`tr[data-id="${id}"]`);
      document.getElementById('detail_id').textContent=id;
      document.getElementById('detail_nopol').textContent=r.dataset.nopol;
      document.getElementById('detail_jenis').textContent=r.dataset.jenis;
      document.getElementById('detail_kapasitas').textContent=r.dataset.kapasitas;
      document.getElementById('detail_status').innerHTML=`<span class="status-badge status-${r.dataset.status}">${r.dataset.status}</span>`;
      document.getElementById('detail_last').textContent=r.dataset['lastMaintenance']||r.dataset['last-maintenance'];
      document.getElementById('detail_next').textContent=r.dataset['nextMaintenance']||r.dataset['next-maintenance'];
      openModal('modalDetailKendaraan');
    }
    function showEditModal(id){
      currentId=id;
      const r=document.querySelector(`tr[data-id="${id}"]`);
      document.getElementById('edit_id').value=id;
      document.getElementById('edit_nopol').value=r.dataset.nopol;
      document.getElementById('edit_jenis').value=r.dataset.jenis;
      document.getElementById('edit_kapasitas').value=r.dataset.kapasitas;
      document.getElementById('edit_status').value=r.dataset.status;
      document.getElementById('edit_perawatan_terakhir').value=r.dataset['last-maintenance'];
      document.getElementById('edit_perawatan_berikutnya').value=r.dataset['next-maintenance'];
      openModal('modalEditKendaraan');
    }
    function showDeleteModal(id){
      currentId=id;
      const r=document.querySelector(`tr[data-id="${id}"]`);
      document.getElementById('delete_info').textContent=`${id} – ${r.dataset.nopol} (${r.dataset.jenis})`;
      openModal('modalDeleteKendaraan');
    }
    function deleteKendaraan(){
      const f=document.createElement('form');
      f.method='post'; f.action='kelola_kendaraan.php';
      f.innerHTML=`<input name="action" value="delete"><input name="id_kendaraan" value="${currentId}">`;
      document.body.appendChild(f); f.submit();
    }
    function showNotification(msg,type='success'){
      const n=document.createElement('div');
      n.className=`notification ${type}`;
      n.innerHTML=`<span>${msg}</span><button>&times;</button>`;
      document.getElementById('notificationContainer').append(n);
      n.querySelector('button').onclick=()=>n.remove();
      setTimeout(()=>n.remove(),5000);
    }
  </script>
</body>
</html>
