<?php
session_start();

// Database config
$host = "localhost";
$dbname = "nova_trans";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = "";

// Ambil booking ID dan data dari session / GET
if (!isset($_SESSION['booking_data']) || !isset($_SESSION['booking_data']['booking_id'])) {
    $booking_id = $_GET['booking_id'] ?? '';
    if (empty($booking_id)) {
        header("Location: pilihkursi.php");
        exit();
    }

    $_SESSION['booking_data'] = [
        'booking_id'   => $booking_id,
        'kursi'        => $_GET['kursi'] ?? '',
        'id_pemesan'   => $_SESSION['user_id'] ?? $booking_id,
        'nama_pemesan' => $_GET['nama_pemesan'] ?? '',
        'no_telepon'   => $_GET['no_telepon'] ?? '',
        'email'        => $_GET['email'] ?? '',
        'tanggal'      => $_GET['tanggal'] ?? date('Y-m-d'),
        'total_harga'  => $_GET['total'] ?? 0,
        'id_bus'       => $_GET['id_bus'] ?? '',
    ];
}

$booking    = $_SESSION['booking_data'];
$booking_id = $booking['booking_id'];

// Ambil data booking lengkap jika perlu
if (empty($booking['nama_pemesan']) || empty($booking['no_telepon']) || empty($booking['email'])) {
    try {
        $stmt = $conn->prepare("
            SELECT * 
              FROM booking 
             WHERE id_booking = :booking_id 
             LIMIT 1
        ");
        $stmt->execute([':booking_id' => $booking_id]);
        $booking_from_db = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking_from_db) {
            $booking = array_merge($booking, $booking_from_db);
            $_SESSION['booking_data'] = $booking;
        }
    } catch(PDOException $e) {
        die("Error fetching booking: " . $e->getMessage());
    }
}

// Ambil data bus
$id_bus = $booking['id_bus'] ?? '';
try {
    $stmt = $conn->prepare("SELECT * FROM data_bus WHERE id_bus = :id_bus");
    $stmt->execute([':id_bus' => $id_bus]);
    $bus = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$bus) {
        header("Location: pilihkursi.php");
        exit();
    }
} catch(PDOException $e) {
    die("Error fetching bus data: " . $e->getMessage());
}

// Hitung waktu tiba
$waktu_berangkat = new DateTime($bus['waktu_berangkat']);
$durasi = explode(':', $bus['estimasi_waktu']);
$waktu_berangkat->add(new DateInterval("PT{$durasi[0]}H{$durasi[1]}M"));
$waktu_tiba = $waktu_berangkat->format('H:i');

// Proses konfirmasi pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    try {
        $conn->beginTransaction();

        // Cari entry temporary yang dibuat di pilihkursi.php
        $stmt_select = $conn->prepare("
            SELECT * FROM booking 
            WHERE id_booking = :booking_id 
            AND status = 'Pending' 
            LIMIT 1
        ");
        $stmt_select->execute([':booking_id' => $booking_id]);
        $existing_booking = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if ($existing_booking) {
            // Ubah status dan data booking menjadi final
          $stmt = $conn->prepare("
                UPDATE booking SET 
                status = 'Confirmed',
                metode_pembayaran    = :metode_pembayaran,
                tanggal_pembayaran   = NOW(),
                kursi                = :kursi,
                id_pemesan           = :id_pemesan
                WHERE id_booking = :booking_id 
                AND status = 'Pending'
            ");
            $stmt->execute([
                ':metode_pembayaran' => $_POST['payment_method'],
                ':kursi'             => $booking['kursi'],
                ':id_pemesan'        => $booking['id_pemesan'],
                ':booking_id'        => $booking_id,
            ]);

            $conn->commit();
            unset($_SESSION['booking_data']);
            header("Location: cetaktiket.php?booking_id=" . urlencode($booking_id));
            exit();
        } else {
            $conn->rollback();
            $error = "Booking tidak ditemukan atau sudah dikonfirmasi.";
        }
    } catch(PDOException $e) {
        $conn->rollback();
        $error = "Gagal konfirmasi pembayaran: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran - Nova Trans</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="pembayarann.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo-mobile-wrap">
            <a href="pesantiket.php" class="logo">
                <img src="Gambar/LOGO.png" alt="Nova Trans" />
            </a>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
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
        <div class="step completed">
            <div class="step-icon"><i class="fas fa-chair"></i></div>
            <div class="step-label">Pilih Kursi</div>
        </div>
        <div class="step completed">
            <div class="step-icon"><i class="fas fa-user"></i></div>
            <div class="step-label">Detail Penumpang</div>
        </div>
        <div class="step active">
            <div class="step-icon"><i class="fas fa-credit-card"></i></div>
            <div class="step-label">Pembayaran</div>
        </div>
    </div>

    <div class="booking-section">
        <div class="booking-summary">
            <h2>Ringkasan Pemesanan</h2>
            <div class="route">
                <div class="route-line"></div>
                <div class="route-point departure">
                    <div class="route-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <circle cx="8" cy="8" r="8"/>
                        </svg>
                    </div>
                    <div class="route-details">
                        <div class="route-name"><?= htmlspecialchars($bus['kota_asal']) ?></div>
                        <div class="route-terminal">Terminal, <?= date('H:i', strtotime($bus['waktu_berangkat'])) ?> WITA</div>
                    </div>
                </div>
                <div class="route-point arrival">
                    <div class="route-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <circle cx="8" cy="8" r="8"/>
                        </svg>
                    </div>
                    <div class="route-details">
                        <div class="route-name"><?= htmlspecialchars($bus['kota_tujuan']) ?></div>
                        <div class="route-terminal">Terminal, <?= $waktu_tiba ?> WITA</div>
                    </div>
                </div>
            </div>
            <div class="booking-details">
                <div class="booking-detail">
                    <div class="booking-label">Tanggal Keberangkatan</div>
                    <div class="booking-value"><?= htmlspecialchars($booking['tanggal']) ?></div>
                </div>
                <div class="booking-detail">
                    <div class="booking-label">Jumlah Penumpang</div>
                    <div class="booking-value"><?= count(explode(',', $booking['kursi'])) ?> Orang</div>
                </div>
                <div class="booking-detail">
                    <div class="booking-label">Armada</div>
                    <div class="booking-value"><?= htmlspecialchars($bus['nama_kelas']) ?></div>
                </div>
            </div>
            <div class="price-details">
                <div class="price-item">
                    <div class="price-label">Harga Tiket (<?= count(explode(',', $booking['kursi'])) ?>x)</div>
                    <div class="price-value">Rp <?= number_format($booking['total_harga'] - 5000, 0, ',', '.') ?></div>
                </div>
                <div class="price-item">
                    <div class="price-label">Biaya Layanan</div>
                    <div class="price-value">Rp 5.000</div>
                </div>
                <div class="total-price">
                    <div class="total-label">Total Pembayaran</div>
                    <div class="total-value">Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></div>
                </div>
            </div>
        </div>

        <div class="payment-confirmation">
            <h2>Konfirmasi Pembayaran</h2>
            <div class="passenger-data">
                <h3 style="margin-bottom: 15px;">Data Pemesan</h3>
                <div class="data-item">
                    <div class="data-label">Nama:</div>
                    <div class="data-value">
                        <?= !empty($booking['nama_pemesan']) ? htmlspecialchars($booking['nama_pemesan']) : '<span style="color:gray;">Tidak tersedia</span>' ?>
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-label">Telepon:</div>
                    <div class="data-value">
                        <?= !empty($booking['no_telepon']) ? htmlspecialchars($booking['no_telepon']) : '<span style="color:gray;">Tidak tersedia</span>' ?>
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-label">Email:</div>
                    <div class="data-value">
                        <?= !empty($booking['email']) ? htmlspecialchars($booking['email']) : '<span style="color:gray;">Tidak tersedia</span>' ?>
                    </div>
                </div>

                <h3 style="margin-bottom: 15px;">Metode Pembayaran</h3>
                <form method="POST" action="">
                    <div class="method-option">
                        <input type="radio" name="payment_method" id="cod" value="COD" class="method-radio" required>
                        <div class="method-details">
                            <div class="method-name">
                                <div class="method-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0V4zm0 3v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7H0zm3 2h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1z"/>
                                    </svg>
                                </div>
                                Bayar di Tempat (COD/Offline)
                            </div>
                            <div class="method-description">Bayar tunai saat di loket keberangkatan</div>
                        </div>
                    </div>
                    <div class="method-option">
                        <input type="radio" name="payment_method" id="transfer" value="Transfer Bank" class="method-radio">
                        <div class="method-details">
                            <div class="method-name">
                                <div class="method-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9H5.5zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518l.087.02z"/>
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11zm0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
                                    </svg>
                                </div>
                                Transfer Bank
                            </div>
                            <div class="method-description">Segera tersedia</div>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button type="submit" name="confirm_payment" class="btn-confirm">Konfirmasi dan Lanjut</button>
                        <a href="pilihkursi.php" class="btn-cancel">Batal</a>
                    </div>
                </form>

                <?php if (!empty($error)): ?>
                    <div class="error-message" style="color: red; margin-top: 10px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

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
                <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
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
            © 2025 Nova Trans. All rights reserved.
        </div>
    </footer>
</body>
</html>
