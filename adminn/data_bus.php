<?php
session_start();
require_once __DIR__ . '/../koneksi.php';// $koneksi = PDO

// 1) Ambil daftar kendaraan untuk dropdown
try {
    $kendStmt = $koneksi->prepare("SELECT * FROM kendaraan ORDER BY id_kendaraan");
    $kendStmt->execute();
    $kendaraans = $kendStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error loading kendaraan: " . $e->getMessage());
}

// 2) Handle Create Bus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $id_kendaraan    = $_POST['id_kendaraan'];
    $nama_kelas      = $_POST['nama_kelas'];
    $kota_asal       = $_POST['kota_asal'];
    $terminal_asal   = $_POST['terminal_asal'];
    $kota_tujuan     = $_POST['kota_tujuan'];
    $terminal_tujuan = $_POST['terminal_tujuan'];
    $waktu_berangkat = $_POST['waktu_berangkat'];
    $estimasi_waktu  = $_POST['estimasi_waktu'];
    $tanggal         = $_POST['tanggal'];
    $fasilitas       = !empty($_POST['fasilitas']) && is_array($_POST['fasilitas'])
                       ? implode(', ', $_POST['fasilitas'])
                       : '';
    $harga_tiket     = $_POST['harga_tiket'];
    $status          = $_POST['status'];

    $ins = $koneksi->prepare("
        INSERT INTO data_bus
          (id_kendaraan, nama_kelas, kota_asal, terminal_asal, kota_tujuan, terminal_tujuan,
           waktu_berangkat, estimasi_waktu, tanggal, fasilitas, harga_tiket, status)
        VALUES
          (:kend, :kelas, :asal, :t_asal, :tujuan, :t_tujuan,
           :ber, :est, :tgl, :fas, :harga, :stat)
    ");
    $ins->execute([
        ':kend'   => $id_kendaraan,
        ':kelas'  => $nama_kelas,
        ':asal'   => $kota_asal,
        ':t_asal' => $terminal_asal,
        ':tujuan' => $kota_tujuan,
        ':t_tujuan'=> $terminal_tujuan,
        ':ber'    => $waktu_berangkat,
        ':est'    => $estimasi_waktu,
        ':tgl'    => $tanggal,
        ':fas'    => $fasilitas,
        ':harga'  => $harga_tiket,
        ':stat'   => $status,
    ]);
    header('Location: data_bus.php');
    exit;
}

// 3) Handle Edit Bus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id              = $_POST['id_bus'];
    $id_kendaraan    = $_POST['id_kendaraan'];
    $nama_kelas      = $_POST['nama_kelas'];
    $kota_asal       = $_POST['kota_asal'];
    $terminal_asal   = $_POST['terminal_asal'];
    $kota_tujuan     = $_POST['kota_tujuan'];
    $terminal_tujuan = $_POST['terminal_tujuan'];
    $waktu_berangkat = $_POST['waktu_berangkat'];
    $estimasi_waktu  = $_POST['estimasi_waktu'];
    $tanggal         = $_POST['tanggal'];
    $fasilitas       = !empty($_POST['fasilitas']) && is_array($_POST['fasilitas'])
                       ? implode(', ', $_POST['fasilitas'])
                       : '';
    $harga_tiket     = $_POST['harga_tiket'];
    $status          = $_POST['status'];

    $upd = $koneksi->prepare("
        UPDATE data_bus SET
          id_kendaraan    = :kend,
          nama_kelas      = :kelas,
          kota_asal       = :asal,
          terminal_asal   = :t_asal,
          kota_tujuan     = :tujuan,
          terminal_tujuan = :t_tujuan,
          waktu_berangkat = :ber,
          estimasi_waktu  = :est,
          tanggal         = :tgl,
          fasilitas       = :fas,
          harga_tiket     = :harga,
          status          = :stat
        WHERE id_bus = :id
    ");
    $upd->execute([
        ':kend'   => $id_kendaraan,
        ':kelas'  => $nama_kelas,
        ':asal'   => $kota_asal,
        ':t_asal' => $terminal_asal,
        ':tujuan' => $kota_tujuan,
        ':t_tujuan'=> $terminal_tujuan,
        ':ber'    => $waktu_berangkat,
        ':est'    => $estimasi_waktu,
        ':tgl'    => $tanggal,
        ':fas'    => $fasilitas,
        ':harga'  => $harga_tiket,
        ':stat'   => $status,
        ':id'     => $id,
    ]);
    header('Location: data_bus.php');
    exit;
}

// 4) Handle Delete Bus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = $_POST['id_bus'];
    $del = $koneksi->prepare("DELETE FROM data_bus WHERE id_bus=?");
    $del->execute([$id]);
    header('Location: data_bus.php');
    exit;
}

// 5) Fetch semua bus
try {
    $stmt = $koneksi->prepare("SELECT * FROM data_bus ORDER BY id_bus");
    $stmt->execute();
    $buses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data_bus: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Bus – NOVA TRANS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <div class="menu-item"><a href="data_bus.php" class="active"><i class="fas fa-database"></i><span>Data Bus</span></a></div>
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
      <h1>Data Bus</h1>
      <div class="user-info">
        <img src="https://i.pinimg.com/736x/18/f5/ba/18f5bab8f9181e8d7c371b16833a6849.jpg" alt="Admin">
        <span style="font-weight:600;">Admin</span>
      </div>
    </div>

    <div class="toolbar">
      <button id="addBusBtn" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Bus
      </button>
    </div>

    <div class="table-container">
      <table id="busTable">
        <thead>
          <tr>
            <th>ID Bus</th>
            <th>Kendaraan</th>
            <th>Kelas</th>
            <th>Kota Asal</th>
            <th>Terminal Asal</th>
            <th>Kota Tujuan</th>
            <th>Terminal Tujuan</th>
            <th>Berangkat</th>
            <th>Estimasi</th>
            <th>Tanggal</th>
            <th>Fasilitas</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($buses): foreach ($buses as $row): ?>
          <tr data-id="<?= $row['id_bus'] ?>"
              data-id_kendaraan="<?= $row['id_kendaraan'] ?>"
              data-fasilitas="<?= htmlspecialchars($row['fasilitas']) ?>">
            <td><?= $row['id_bus'] ?></td>
            <td>
              <?php
                foreach ($kendaraans as $k) {
                  if ($k['id_kendaraan']==$row['id_kendaraan']) {
                    echo htmlspecialchars($k['nomor_polisi']);
                    break;
                  }
                }
              ?>
            </td>
            <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
            <td><?= htmlspecialchars($row['kota_asal']) ?></td>
            <td><?= htmlspecialchars($row['terminal_asal']) ?></td>
            <td><?= htmlspecialchars($row['kota_tujuan']) ?></td>
            <td><?= htmlspecialchars($row['terminal_tujuan']) ?></td>
            <td><?= htmlspecialchars($row['waktu_berangkat']) ?></td>
            <td><?= htmlspecialchars($row['estimasi_waktu']) ?></td>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td>
              <?= implode(' ',
                array_map(fn($f)=>"<span class=\"facility-tag\">".htmlspecialchars(trim($f))."</span>",
                          explode(',',$row['fasilitas']))
              ) ?>
            </td>
            <td>Rp <?= number_format($row['harga_tiket'],0,',','.') ?></td>
            <td>
              <span class="status <?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
                <?= htmlspecialchars($row['status']) ?>
              </span>
            </td>
            <td>
              <button class="btn-icon btn-edit"><i class="fas fa-pen"></i></button>
              <button class="btn-icon btn-delete"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="14" style="text-align:center">Tidak ada data bus</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Tambah/Edit -->
  <div class="modal" id="busModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="busModalTitle">Tambah Bus</h3>
        <span class="close" id="closeBusModal">&times;</span>
      </div>
      <div class="modal-body">
        <form id="busForm" method="post" action="data_bus.php">
          <input type="hidden" name="action" id="busAction" value="">
          <input type="hidden" name="id_bus" id="busId" value="">

          <!-- **Dropdown Kendaraan** -->
          <div class="form-group">
            <label for="inputKendaraan">Kendaraan</label>
            <select name="id_kendaraan" id="inputKendaraan" required>
              <option value="">-- Pilih Kendaraan --</option>
              <?php foreach($kendaraans as $k): ?>
                <option value="<?= $k['id_kendaraan'] ?>">
                  <?= htmlspecialchars($k['nomor_polisi']) ?> &mdash; <?= $k['kapasitas'] ?> org
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="inputKelas">Kelas</label>
            <input type="text" name="nama_kelas" id="inputKelas" required>
          </div>
          <div class="form-group">
            <label for="inputKotaAsal">Kota Asal</label>
            <input type="text" name="kota_asal" id="inputKotaAsal" required>
          </div>
          <div class="form-group">
            <label for="inputTerminalAsal">Terminal Asal</label>
            <input type="text" name="terminal_asal" id="inputTerminalAsal">
          </div>
          <div class="form-group">
            <label for="inputKotaTujuan">Kota Tujuan</label>
            <input type="text" name="kota_tujuan" id="inputKotaTujuan" required>
          </div>
          <div class="form-group">
            <label for="inputTerminalTujuan">Terminal Tujuan</label>
            <input type="text" name="terminal_tujuan" id="inputTerminalTujuan">
          </div>
          <div class="form-group">
            <label for="inputBerangkat">Waktu Berangkat</label>
            <input type="datetime-local" name="waktu_berangkat" id="inputBerangkat" required>
          </div>
          <div class="form-group">
            <label for="inputEstimasi">Estimasi Waktu</label>
            <input type="time" name="estimasi_waktu" id="inputEstimasi" required>
          </div>
          <div class="form-group">
            <label for="inputTanggal">Tanggal</label>
            <input type="date" name="tanggal" id="inputTanggal" required>
          </div>
          <div class="form-group">
            <label for="inputFasilitas">Fasilitas (CTRL+klik pilih banyak)</label>
            <select name="fasilitas[]" id="inputFasilitas" multiple>
              <option value="AC">AC</option>
              <option value="Toilet">Toilet</option>
              <option value="WiFi">WiFi</option>
              <option value="Snack">Snack</option>
            </select>
          </div>
          <div class="form-group">
            <label for="inputHarga">Harga Tiket</label>
            <input type="number" name="harga_tiket" id="inputHarga" required>
          </div>
          <div class="form-group">
            <label for="inputStatus">Status</label>
            <select name="status" id="inputStatus">
              <option value="Tersedia">Tersedia</option>
              <option value="Tidak Tersedia">Tidak Tersedia</option>
              <option value="Maintenance">Maintenance</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" id="cancelBusBtn">Batal</button>
        <button class="btn btn-primary" id="saveBusBtn">Simpan</button>
      </div>
    </div>
  </div>

  <script>
    const busModal  = document.getElementById('busModal');
    const busForm   = document.getElementById('busForm');
    const busAction = document.getElementById('busAction');
    const busId     = document.getElementById('busId');
    const saveBtn   = document.getElementById('saveBusBtn');
    const addBtn    = document.getElementById('addBusBtn');
    const closeBtn  = document.getElementById('closeBusModal');
    const cancelBtn = document.getElementById('cancelBusBtn');

    function openModal(mode, row=null) {
      busForm.reset();
      if (mode==='add') {
        document.getElementById('busModalTitle').innerText='Tambah Bus';
        busAction.value='add';
      } else {
        document.getElementById('busModalTitle').innerText='Edit Bus';
        busAction.value='edit';
        const cells = row.cells;
        busId.value = row.dataset.id;
        document.getElementById('inputKendaraan').value    = row.dataset.id_kendaraan;
        document.getElementById('inputKelas').value         = cells[2].innerText;
        document.getElementById('inputKotaAsal').value      = cells[3].innerText;
        document.getElementById('inputTerminalAsal').value  = cells[4].innerText;
        document.getElementById('inputKotaTujuan').value    = cells[5].innerText;
        document.getElementById('inputTerminalTujuan').value= cells[6].innerText;
        document.getElementById('inputBerangkat').value     = cells[7].innerText;
        document.getElementById('inputEstimasi').value      = cells[8].innerText;
        document.getElementById('inputTanggal').value       = cells[9].innerText;
        const fats = row.dataset.fasilitas.split(',').map(x=>x.trim());
        Array.from(document.getElementById('inputFasilitas').options)
             .forEach(o=> o.selected = fats.includes(o.value));
        document.getElementById('inputHarga').value         = cells[11].innerText.replace(/\D/g,'');
        document.getElementById('inputStatus').value        = cells[12].innerText;
      }
      busModal.style.display='block';
    }

    addBtn.addEventListener('click', () => openModal('add'));
    document.querySelectorAll('.btn-edit')
            .forEach(b=>b.addEventListener('click', e=> openModal('edit', e.currentTarget.closest('tr'))));
    document.querySelectorAll('.btn-delete')
            .forEach(b=>b.addEventListener('click', e=>{
              if (confirm('Hapus bus ini?')) {
                const id = e.currentTarget.closest('tr').dataset.id;
                const f = document.createElement('form');
                f.method='post'; f.action='data_bus.php';
                f.innerHTML=`
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id_bus" value="${id}">
                `;
                document.body.appendChild(f);
                f.submit();
              }
            }));

    saveBtn.addEventListener('click', e=>{ e.preventDefault(); busForm.submit(); });
    closeBtn.addEventListener('click', ()=> busModal.style.display='none');
    cancelBtn.addEventListener('click', ()=> busModal.style.display='none');
    window.addEventListener('click', e=> { if(e.target===busModal) busModal.style.display='none'; });
  </script>
</body>
</html>
