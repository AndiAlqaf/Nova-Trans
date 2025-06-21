<?php
// datapemesan.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'koneksi.php';  // Asumsikan koneksi PDO $conn

// 1. Baca parameter dari GET atau fallback ke SESSION
$id_bus     = $_GET['id_bus']      ?? ($_SESSION['last_booking']['id_bus']     ?? null);
$tanggal    = $_GET['tanggal']     ?? ($_SESSION['last_booking']['tanggal']    ?? null);
$kursi      = $_GET['kursi']       ?? ($_SESSION['last_booking']['kursi']      ?? null);
$total      = $_GET['total']       ?? ($_SESSION['last_booking']['total']      ?? null);

// 2. Simpan parameter sebagai backup
$_SESSION['last_booking'] = [
    'id_bus'  => $id_bus,
    'tanggal' => $tanggal,
    'kursi'   => $kursi,
    'total'   => $total,
];

// 3. Validasi parameter
$missing = [];
if (empty($id_bus))  $missing[] = 'id_bus';
if (empty($tanggal)) $missing[] = 'tanggal';
if (empty($kursi))   $missing[] = 'kursi';
if ($total === null) $missing[] = 'total';
if ($missing) {
    die("Error: Data pemesanan tidak lengkap. Parameter hilang: "
      . implode(', ', $missing)
      . ". <a href='pesantiket.php'>Kembali ke Pemesanan</a>");
}

// 4. Validasi format tanggal
$dt = DateTime::createFromFormat('Y-m-d', $tanggal);
if (!$dt || $dt->format('Y-m-d') !== $tanggal) {
    die("Format tanggal tidak valid. Gunakan format YYYY-MM-DD. <a href='pesantiket.php'>Kembali</a>");
}

// 5. Split kursi untuk display
$selectedSeats = array_filter(array_map('trim', explode(',', $kursi)), fn($v)=>$v!=='');

// 6. Ambil data bus
try {
    $stmt = $conn->prepare("SELECT * FROM data_bus WHERE id_bus = :bus");
    $stmt->execute([':bus'=>$id_bus]);
    $bus = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$bus) die("Data bus tidak ditemukan.");
} catch(PDOException $e) {
    die("Error mengambil data bus: " . $e->getMessage());
}

// 7. Hitung waktu tiba
try {
    $t = new DateTime($bus['waktu_berangkat']);
    [$h,$m] = explode(':', substr($bus['estimasi_waktu'],0,5));
    $t->add(new DateInterval("PT{$h}H{$m}M"));
    $waktu_tiba = $t->format('H:i');
} catch(Exception) {
    $waktu_tiba = '';
}

// 8. Format tanggal untuk display
try {
    $d = new DateTime($tanggal);
    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $tanggal_display = $hari[$d->format('w')] . ', ' . $d->format('d M Y');
} catch(Exception) {
    $tanggal_display = $tanggal;
}

// 9. Jika form disubmit, INSERT booking baru
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama_pemesan'] ?? '');
    $telp  = trim($_POST['no_telepon']   ?? '');
    $email = trim($_POST['email']        ?? '');

    if (!$nama || !$telp || !$email) {
        $error = 'Semua field harus diisi!';
    } else {
        try {
            // 9.a INSERT booking baru dengan status Pending
            $sql = "INSERT INTO booking
                       (id_bus, tanggal_pemesanan, kursi, total_harga,
                        nama_pemesan, no_telepon, email, status, created_at)
                    VALUES
                       (:bus, :tgl, :kursi, :total,
                        :nama, :telp, :email, 'Pending', NOW())";
            $ins = $conn->prepare($sql);
            $ins->execute([
                ':bus'   => $id_bus,
                ':tgl'   => $tanggal,
                ':kursi' => $kursi,
                ':total' => $total,
                ':nama'  => $nama,
                ':telp'  => $telp,
                ':email' => $email
            ]);

            // 9.b Ambil booking_id yang baru saja di INSERT
            $booking_id = $conn->lastInsertId();

            // 9.c Hapus semua ghost Temporary entries untuk kursi ini
            // Persiapkan parameter untuk IN(...)
            $seats = array_filter(array_map('trim', explode(',', $kursi)), fn($v)=>$v!=='');
            $inParams = [];
            $inPlaceholders = [];
            foreach ($seats as $i => $s) {
                $ph = ":seat{$i}";
                $inParams[$ph] = $s;
                $inPlaceholders[] = $ph;
            }
            $deleteSql = "
                DELETE FROM booking
                 WHERE status = 'Temporary'
                   AND id_bus = :bus
                   AND tanggal_pemesanan = :tgl
                   AND kursi IN (" . implode(',', $inPlaceholders) . ")
            ";
            $deleteStmt = $conn->prepare($deleteSql);
            // gabungkan semua parameter
            $deleteStmt->execute(array_merge([
                ':bus' => $id_bus,
                ':tgl' => $tanggal
            ], $inParams));

            // 9.d Simpan data untuk step selanjutnya
            $_SESSION['booking_data'] = compact(
              'booking_id','kursi','nama','telp','email','tanggal','total','id_bus'
            );

            // 9.e Redirect ke halaman pembayaran
            header("Location: pembayaran.php?booking_id=" . urlencode($booking_id));
            exit;
        } catch(Exception $e) {
            $error = 'Kesalahan: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penumpang - Nova Trans</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="datapemesann.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo-mobile-wrap">
            <a href="pesantiket.php" class="logo">
                <img src="Gambar/LOGO.png" alt="Nova Trans" />
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

    <!-- Progress bar booking -->
    <div class="booking-progress">
        <div class="step completed">
            <div class="step-icon"><i class="fas fa-search"></i></div>
            <div class="step-label">Pilih Bus</div>
        </div>
        <div class="step completed">
            <div class="step-icon"><i class="fas fa-chair"></i></div>
            <div class="step-label">Pilih Kursi</div>
        </div>
        <div class="step active">
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
            <!-- Detail perjalanan -->
            <div class="journey-details">
                <div class="route-info">
                    <div class="city-pair">
                        <h2><?= htmlspecialchars($bus['kota_asal']) ?> <i class="fas fa-arrow-right"></i> <?= htmlspecialchars($bus['kota_tujuan']) ?></h2>
                    </div>
                    <div class="journey-meta">
                        <span><i class="far fa-calendar"></i> <?= $tanggal_display ?></span>
                        <span><i class="far fa-clock"></i> <?= date('H:i', strtotime($bus['waktu_berangkat'])) ?> - <?= $waktu_tiba ?> (<?= substr($bus['estimasi_waktu'], 0, 5) ?> jam)</span>
                        <span><i class="fas fa-bus"></i> <?= htmlspecialchars($bus['nama_kelas']) ?></span>
                    </div>
                </div>
                <div class="price-tag">
                    <div class="selected-seats">
                        <strong>Kursi: <?= implode(', ', $selectedSeats) ?></strong>
                    </div>
                    <div class="total-price">
                        <strong>Total: Rp <?= number_format($total, 0, ',', '.') ?></strong>
                    </div>
                </div>
            </div>

            <!-- Form detail penumpang -->
            <div class="passenger-form">
                <h3>Detail Penumpang</h3>

                <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="datapemesan.php" id="passenger-form">
                    <input type="hidden" name="id_bus" value="<?= htmlspecialchars($id_bus) ?>">
                    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                    <input type="hidden" name="kursi" value="<?= htmlspecialchars($kursi) ?>">
                    <input type="hidden" name="total" value="<?= htmlspecialchars($total) ?>">

                    <div class="form-section">
                        <h4>Informasi Pemesan</h4>
                        <div class="form-group">
                            <label for="nama_pemesan">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="nama_pemesan" id="nama_pemesan"
                                   value="<?= htmlspecialchars($_POST['nama_pemesan'] ?? '') ?>"
                                   placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="form-group">
                            <label for="no_telepon">Nomor Telepon <span class="required">*</span></label>
                            <input type="tel" name="no_telepon" id="no_telepon"
                                   value="<?= htmlspecialchars($_POST['no_telepon'] ?? '') ?>"
                                   placeholder="Contoh: 081234567890" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" name="email" id="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   placeholder="Contoh: nama@email.com" required>
                        </div>
                        <div class="form-group">
                            <label for="metode">Metode Pembayaran</label>
                            <select name="metode" id="metode">
                                <option value="COD">COD</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div class="booking-summary">
                        <h4>Ringkasan Pemesanan</h4>
                        <div class="summary-row">
                            <span>Rute</span>
                            <span><?= htmlspecialchars($bus['kota_asal']) ?> → <?= htmlspecialchars($bus['kota_tujuan']) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Tanggal</span>
                            <span><?= $tanggal_display ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Waktu</span>
                            <span><?= date('H:i', strtotime($bus['waktu_berangkat'])) ?> - <?= $waktu_tiba ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Kursi</span>
                            <span><?= implode(', ', $selectedSeats) ?> (<?= count($selectedSeats) ?> kursi)</span>
                        </div>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format(max(0, $total - 5000), 0, ',', '.') ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Biaya Layanan</span>
                            <span>Rp 5.000</span>
                        </div>
                        <div class="summary-row total">
                            <span><strong>Total</strong></span>
                            <span><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="pilihkursi.php?id=<?= urlencode($id_bus) ?>&tanggal=<?= urlencode($tanggal) ?>&jumlah=<?= count($selectedSeats) ?>"
                           class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

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

    <!-- JavaScript validasi form dan mobile menu (tetap sama) -->
    <script>
        document.getElementById('passenger-form').addEventListener('submit', function(e) {
            const nama = document.getElementById('nama_pemesan').value.trim();
            const telepon = document.getElementById('no_telepon').value.trim();
            const email = document.getElementById('email').value.trim();

            if (!nama || !telepon || !email) {
                e.preventDefault();
                alert('Semua field harus diisi!');
                return false;
            }
            const phoneRegex = /^[0-9+\-\s()]+$/;
            if (!phoneRegex.test(telepon)) {
                e.preventDefault();
                alert('Nomor telepon tidak valid!');
                return false;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Format email tidak valid!');
                return false;
            }
        });

        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                navLinks.classList.toggle('active');
            });
        }
    </script>
</body>
</html>
