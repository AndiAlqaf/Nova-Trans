<?php
require_once __DIR__ . '/../koneksi.php';

// Ambil ID Booking dari URL
$booking_id = $_GET['booking_id'] ?? '';
if (!$booking_id) {
    die("ID Booking tidak ditemukan.");
}

// Ambil data booking
$stmt = $koneksi->prepare("SELECT * FROM booking WHERE id_booking = :id");
$stmt->execute([':id' => $booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$booking) {
    die("Data booking tidak ditemukan.");
}

// Ambil data bus
$stmt = $koneksi->prepare("SELECT * FROM data_bus WHERE id_bus = :id_bus");
$stmt->execute([':id_bus' => $booking['id_bus']]);
$bus = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$bus) {
    die("Data bus tidak ditemukan.");
}

// Hitung waktu tiba
$waktu_berangkat = new DateTime($bus['waktu_berangkat']);
$durasi = explode(':', $bus['estimasi_waktu']);
$waktu_berangkat->add(new DateInterval("PT{$durasi[0]}H{$durasi[1]}M"));
$waktu_tiba = $waktu_berangkat->format('H:i');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Tiket - Nova Trans</title>
    <link rel="stylesheet" href="cetaktikett.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
<body>
<div class="ticket-container">
  <div class="ticket">
    <div class="ticket-top">
      <div class="route-info">
        <div class="location">
          <h3><?= htmlspecialchars($bus['kota_asal']) ?></h3>
          <p><?= htmlspecialchars($bus['terminal_asal']) ?></p>
        </div>
        <div class="route-arrow">→</div>
        <div class="location">
          <h3><?= htmlspecialchars($bus['kota_tujuan']) ?></h3>
          <p><?= htmlspecialchars($bus['terminal_tujuan']) ?></p>
        </div>
      </div>
      <div class="ticket-number">
        ID Booking: <?= htmlspecialchars($booking['id_booking']) ?>
      </div>
    </div>

    <div class="ticket-bottom">
      <div class="ticket-details">
        <div class="detail-item">
          <label>Nama Pemesan</label>
          <div class="value"><?= htmlspecialchars($booking['nama_pemesan']) ?></div>
        </div>
        <div class="detail-item">
          <label>No. Telepon</label>
          <div class="value"><?= htmlspecialchars($booking['no_telepon']) ?></div>
        </div>
        <div class="detail-item">
          <label>Email</label>
          <div class="value"><?= htmlspecialchars($booking['email']) ?></div>
        </div>
        <div class="detail-item">
          <label>Kursi</label>
          <div class="value"><?= htmlspecialchars($booking['kursi']) ?></div>
        </div>
        <div class="detail-item">
          <label>Kelas</label>
          <div class="value"><?= htmlspecialchars($bus['nama_kelas']) ?></div>
        </div>
        <div class="detail-item">
          <label>Harga</label>
          <div class="value">Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></div>
        </div>
        <div class="detail-item">
          <label>Status</label>
          <div class="value"><?= htmlspecialchars($booking['status']) ?></div>
        </div>
        <div class="detail-item">
          <label>Berangkat</label>
          <div class="value"><?= date('d-m-Y', strtotime($bus['tanggal'])) ?>, <?= date('H:i', strtotime($bus['waktu_berangkat'])) ?> WITA</div>
        </div>
        <div class="detail-item">
          <label>Tiba</label>
          <div class="value"><?= $waktu_tiba ?> WITA</div>
        </div>
      </div>

      <div class="actions">
        <button class="btn btn-primary" onclick="window.print()">🖨 Cetak Tiket</button>
        <a class="btn btn-secondary" href="pesantiket.php">⬅ Kembali ke Pemesanan</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
