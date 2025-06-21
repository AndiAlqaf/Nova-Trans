<?php
// laporan.php – Laporan Transaksi NOVA TRANS
session_start();
require 'koneksi.php';  // di sini $koneksi = PDO

// Jika dipanggil sebagai API, keluarkan JSON saja
if (isset($_GET['api']) && $_GET['api'] === 'laporan') {
    header('Content-Type: application/json; charset=utf-8');

    $tipe        = $_GET['tipe']   ?? 'daily';
    $statusParam = $_GET['status'] ?? 'all';
    $where       = [];
    $params      = [];

    if ($tipe === 'daily') {
        $where[]  = 'DATE(tanggal_pemesanan) = ?';
        $params[] = $_GET['tanggal'] ?? date('Y-m-d');
    } else {
        $where[]  = 'MONTH(tanggal_pemesanan) = ?';
        $params[] = intval($_GET['bulan'] ?? date('n'));
        $where[]  = 'YEAR(tanggal_pemesanan)  = ?';
        $params[] = intval($_GET['tahun'] ?? date('Y'));
    }
    if ($statusParam !== 'all') {
        $where[]  = 'status = ?';
        $params[] = $statusParam;
    }

    $sql = "
      SELECT
        b.id_booking                                      AS id,
        DATE_FORMAT(b.tanggal_pemesanan, '%d/%m/%Y')     AS tanggal,
        b.nama_pemesan                                   AS pelanggan,
        CONCAT(db.kota_asal, ' → ', db.kota_tujuan)       AS rute,
        db.nama_kelas                                    AS bus,
        (CHAR_LENGTH(b.kursi) - CHAR_LENGTH(REPLACE(b.kursi, ',', '')) + 1)
                                                        AS jumlahTiket,
        CONCAT('Rp ', FORMAT(b.total_harga,0,'de_DE'))   AS total,
        b.status                                         AS status
      FROM booking b
      JOIN data_bus db ON b.id_bus = db.id_bus
    ";
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY b.tanggal_pemesanan ASC';

    $stmt = $koneksi->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laporan Transaksi – NOVA TRANS</title>
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="laporan.css">
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
    <div class="menu-item"><a href="laporan.php" class="active"><i class="fas fa-file-alt"></i><span>Laporan</span></a></div>
    <div class="menu-item" style="margin-top:auto;position:absolute;bottom:20px;width:100%;">
      <a href="login.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </div>

  <div class="main">
    <div class="header">
      <h1>Laporan Transaksi</h1>
      <div class="user-info">
        <img src="https://i.pinimg.com/736x/18/f5/ba/18f5bab8f9181e8d7c371b16833a6849.jpg" alt="Admin"><div><span style="font-weight:600;">Admin</span></div>
      </div>
    </div>
<!-- Filter Laporan -->
<div class="card">
  <div class="card-header">
    <h2>Filter Laporan</h2>
  </div>
  <div class="filter-container">
    <div class="filter-group">
      <label for="report-type">Tipe Laporan</label>
      <select id="report-type" class="filter-select">
        <option value="daily">Transaksi Harian</option>
        <option value="monthly">Transaksi Bulanan</option>
      </select>
    </div>

    <div class="filter-group" id="daily-filter">
      <label for="date-filter">Tanggal</label>
      <input
        type="date"
        id="date-filter"
        class="filter-input"
        value="<?= date('Y-m-d') ?>"
      >
    </div>

    <div class="filter-group" id="monthly-filter" style="display:none">
      <label for="month-filter">Bulan</label>
      <select id="month-filter" class="filter-select">
        <?php
        $bulan = ['Januari','Februari','Maret','April','Mei','Juni',
                  'Juli','Agustus','September','Oktober','November','Desember'];
        foreach ($bulan as $i => $n) {
          $val = $i+1;
          $sel = ($val == date('n')) ? ' selected' : '';
          echo "<option value=\"{$val}\"{$sel}>{$n}</option>";
        }
        ?>
      </select>
    </div>

    <div class="filter-group">
      <label for="year-select">Tahun</label>
      <select id="year-select" class="filter-select">
        <?php
        $currentYear = date('Y');
        for ($y = $currentYear - 2; $y <= $currentYear + 1; $y++) {
          $sel = ($y == $currentYear) ? ' selected' : '';
          echo "<option value=\"{$y}\"{$sel}>{$y}</option>";
        }
        ?>
      </select>
    </div>

    <div class="filter-group">
      <label for="payment-status">Status</label>
      <select id="payment-status" class="filter-select">
        <option value="all">Semua</option>
        <option value="Paid">Lunas</option>
        <option value="Pending">Pending</option>
        <option value="Canceled">Dibatalkan</option>
      </select>
    </div>

    <div class="filter-group action-group">
      <button id="show-report" class="btn">
        <i class="fas fa-search"></i> Tampilkan
      </button>
    </div>
  </div>
</div>


    <!-- Statistik Transaksi & Pendapatan -->
<div class="stats-wrapper">
  <div class="stat-card">
    <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
    <div class="stat-info">
      <h3 id="total-transactions">0</h3>
      <p>Total Transaksi</p>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
    <div class="stat-info">
      <h3 id="total-income">Rp 0</h3>
      <p>Total Pendapatan</p>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
    <div class="stat-info">
      <h3 id="paid-transactions">0</h3>
      <p>Transaksi Lunas</p>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
    <div class="stat-info">
      <h3 id="pending-transactions">0</h3>
      <p>Transaksi Pending</p>
    </div>
  </div>
</div>


    <!-- Tabel Laporan -->
    <div class="card">
      <div class="card-header">
        <h2 id="report-title">Laporan Transaksi Harian</h2>
      </div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Rute</th>
              <th>Bus</th><th>Jumlah</th><th>Total</th><th>Status</th>
            </tr>
          </thead>
          <tbody id="transaction-data">
            <!-- JS akan render data di sini -->
          </tbody>
        </table>
      </div>
    </div>

  </div>

<script>
// Helper & state
const bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni',
                   'Juli','Agustus','September','Oktober','November','Desember'];
let dataCache = [];

// Update judul
function updateTitle(){
  const tipe = document.getElementById('report-type').value;
  const el   = document.getElementById('report-title');
  if (tipe === 'daily') {
    const d = new Date(document.getElementById('date-filter').value);
    el.textContent = `Laporan Transaksi Harian: ${d.getDate()} ${bulanIndo[d.getMonth()]} ${d.getFullYear()}`;
  } else {
    const m = document.getElementById('month-filter').value,
          y = document.getElementById('year-select').value;
    el.textContent = `Laporan Transaksi Bulanan: ${bulanIndo[m-1]} ${y}`;
  }
}

// Fetch & parse JSON
async function fetchData(){
  const tipe   = document.getElementById('report-type').value;
  const status = document.getElementById('payment-status').value;
  const qs     = new URLSearchParams({ api:'laporan', tipe, status });
  if (tipe==='daily') {
    qs.append('tanggal', document.getElementById('date-filter').value);
  } else {
    qs.append('bulan', document.getElementById('month-filter').value);
    qs.append('tahun', document.getElementById('year-select').value);
  }
  const res = await fetch('laporan.php?' + qs.toString());
  try {
    return await res.json();
  } catch {
    return [];
  }
}

// Render tabel & pendapatan
async function updateTable(){
  dataCache = await fetchData();

  // Tabel
  const tbody = document.getElementById('transaction-data');
  tbody.innerHTML = '';
  if (!dataCache.length) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center">Tidak ada data</td></tr>';
  } else {
    dataCache.forEach(item => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${item.id}</td>
        <td>${item.tanggal}</td>
        <td>${item.pelanggan}</td>
        <td>${item.rute}</td>
        <td>${item.bus}</td>
        <td>${item.jumlahTiket}</td>
        <td>${item.total}</td>
        <td>${item.status}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  // HITUNG STATISTIK
  const totalCount = dataCache.length;
  const paidCount  = dataCache.filter(x=> x.status==='Paid').length;
  const pendingCount = dataCache.filter(x=> x.status==='Pending').length;
  const income = dataCache
    .reduce((sum, it) => sum + parseInt(it.total.replace(/[^0-9]/g,''),10), 0);

  document.getElementById('total-transactions').textContent   = totalCount;
  document.getElementById('paid-transactions').textContent    = paidCount;
  document.getElementById('pending-transactions').textContent = pendingCount;
  document.getElementById('total-income').textContent         =
    new Intl.NumberFormat('id-ID',{
      style:'currency',currency:'IDR',minimumFractionDigits:0
    }).format(income).replace('IDR','Rp');
}

// Event bindings
document.getElementById('show-report')
        .addEventListener('click', ()=>{
  updateTitle();
  updateTable();
});
['report-type','date-filter','month-filter','year-select','payment-status']
  .forEach(id => document.getElementById(id)
  .addEventListener('change', updateTitle));

// Init default = hari ini
window.addEventListener('DOMContentLoaded', ()=>{
  document.getElementById('date-filter').value =
    new Date().toISOString().substr(0,10);
  updateTitle();
  updateTable();
});
</script>
</body>
</html>
