<?php
include 'koneksi.php';

$kota_asal = $_GET['kota_asal'] ?? '';
$kota_tujuan = $_GET['kota_tujuan'] ?? '';
$tanggal_berangkat = $_GET['tanggal_berangkat'] ?? '';
$jumlah_penumpang = $_GET['jumlah_penumpang'] ?? 1;
$tanggal_pulang = $_GET['tanggal_pulang'] ?? '';

// Query pencarian
$query = "SELECT * FROM data_bus WHERE kota_asal LIKE :asal AND kota_tujuan LIKE :tujuan";
$stmt = $conn->prepare($query);
$stmt->execute([
    ':asal' => "%$kota_asal%",
    ':tujuan' => "%$kota_tujuan%"
]);
$hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Pilih Bus - Nova Trans</title>
    <link rel="stylesheet" href="keberangkatan.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link rel="stylesheet" href="keberangkatan.css"/>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
      <div class="logo-mobile-wrap">
        <a href="pesantiket.php" class="logo">
          <img src="Gambar/LOGO.png" alt="Madinah Trans" />
        </a>
      </div>
      <div class="nav-links">
        <a href="pesantiket.php"><i class="fas fa-ticket-alt"></i> Pesan Tiket</a>
        <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
        <a href="outlet.php"><i class="fas fa-store"></i> Outlet</a>
        <a href="kontak.php"><i class="fas fa-phone"></i> Kontak</a>
        <a href="blog.php"><i class="fas fa-newspaper"></i> Blog</a>
      </div>
      <div class="auth-buttons">
        <span class="user-email"><i class="fas fa-user"></i> <?= $userEmail ?></span>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
    </nav>
  
        <div class="booking-progress">
        <div class="step completed">
            <div class="step-icon"><i class="fas fa-search"></i></div>
            <div class="step-label">Pilih Bus</div>
        </div>
        <div class="step">
            <div class="step-icon"><i class="fas fa-chair"></i></div>
            <div class="step-label">Pilih Kursi</div>
        </div>
        <div class="step">
            <div class="step-icon"><i class="fas fa-user"></i></div>
            <div class="step-label">Detail Penumpang</div>
        </div>
        <div class="step">
            <div class="step-icon"><i class="fas fa-credit-card"></i></div>
            <div class="step-label">Pembayaran</div>
        </div>
    </div>

    <main>
        <div class="container">
            <div class="search-details">
                <div class="route-info">
                    <div class="city-pair">
                        <h2><?= htmlspecialchars($kota_asal) ?> <i class="fas fa-arrow-right"></i> <?= htmlspecialchars($kota_tujuan) ?></h2>
                    </div>
                    <div class="journey-meta">
                        <span><i class="far fa-calendar"></i> <?= htmlspecialchars($tanggal_berangkat) ?></span>
                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($jumlah_penumpang) ?> Penumpang</span>
                    </div>
                </div>
            </div>

            <div class="sort-section">
                <div class="sort-text"><?= count($hasil) ?> Bus Tersedia</div>
            </div>

            <div class="bus-list">
                <?php if (count($hasil) > 0): ?>
                    <?php foreach ($hasil as $bus): ?>
                        <div class="bus-item">
                            <div class="bus-info">
                                <div class="bus-operator">
                                    <div class="operator-details">
                                        <h3><?= htmlspecialchars($bus['nama_kelas']) ?></h3>
                                        <span class="bus-type">Kelas (2-2)</span>
                                    </div>
                                </div>
                                <div class="bus-schedule">
                                    <div class="departure">
                                        <span class="time"><?= date('H:i', strtotime($bus['waktu_berangkat'])) ?></span>
                                        <span class="city"><?= htmlspecialchars($bus['kota_asal']) ?></span>
                                        <span class="terminal">Terminal Daya</span>
                                    </div>
                                    <div class="journey-time">
                                        <div class="line"></div>
                                        <span><?= substr($bus['estimasi_waktu'], 0, 5) ?> jam</span>
                                        <div class="line"></div>
                                    </div>
                                    <div class="arrival">
                                        <span class="time">--:--</span>
                                        <span class="city"><?= htmlspecialchars($bus['kota_tujuan']) ?></span>
                                        <span class="terminal">Terminal Tujuan</span>
                                    </div>
                                </div>
                                <div class="bus-amenities">
                                    <?php foreach (explode(',', $bus['fasilitas']) as $fasilitas): ?>
                                        <div class="amenity"><i class="fas fa-check"></i> <?= htmlspecialchars(trim($fasilitas)) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="bus-action">
                                <div class="price">
                                    <span class="amount">Rp <?= number_format($bus['harga_tiket'], 0, ',', '.') ?></span>
                                    <span class="per-seat">per kursi</span>
                                </div>
                                <a href="pilihkursi.php?id_bus=<?= $bus['id_bus'] ?>&tanggal=<?= urlencode($tanggal_berangkat) ?>&jumlah=<?= (int)$jumlah_penumpang ?>" class="btn btn-primary">Pilih</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada bus ditemukan untuk rute tersebut.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
<!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <div class="footer-section footer-logo">
         <img src="Gambar/LOGO2.png" alt="Logo Nova Trans" />
          <p>Solusi Transportasi Terbaik dan Terpercaya Rute Makassar - Luwu Timur</p>
          <div class="footer-social">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
        <div class="footer-section">
          <h3>Call Center</h3>
          <a href="tel:0822-9215-9543"><i class="fas fa-phone"></i> 0822-9215-9543</a>
          <a href="mailto:info@madinahtrans.co.id"><i class="fas fa-envelope"></i> info@novatrans.co.id</a>
          <a href="#"><i class="fab fa-whatsapp"></i> WhatsApp CS</a>
        </div>
        <div class="footer-section">
          <h3>Informasi</h3>
          <a href="pesantiket.php"><i class="fas fa-home"></i> Beranda</a>
          <a href="jadwal.php"><i class="fas fa-calendar-alt"></i> Jadwal</a>
          <a href="pesan-tiket.php"><i class="fas fa-ticket-alt"></i> Pesan Tiket</a>
          <a href="kontak.php"><i class="fas fa-address-book"></i> Kontak</a>
          <a href="blog.php"><i class="fas fa-newspaper"></i> Blog</a>
        </div>
        <div class="footer-section">
          <h3>Rute Populer</h3>
          <a href="#">Makassar - Sorowako</a>
          <a href="#">Makassar - Malili</a>
          <a href="#">Makassar - Mangkutana</a>
          <a href="#">Makassar - Palopo</a>
        </div>
      </div>
      <div class="footer-bottom">
        &copy; 2025 Nova Trans. All rights reserved.
      </div>
    </footer>
    

    <script src="keberangkatan.js"></script>
</body>
</html>