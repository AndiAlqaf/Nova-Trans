<?php
// pesantiket.php – Halaman Utama User

// 1. Cegah cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 2. Session & Auth
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: masuk.php');
    exit;
}

// 3. Koneksi DB (jika butuh query)
$host = "localhost";
$dbname = "nova_trans";
$username = "root";
$password = "";
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pesan Tiket – Nova Trans</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="pesantikeet.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo-mobile-wrap">
      <a href="pesantiket.php" class="logo">
        <img src="Gambar/LOGO.png" alt="Nova Trans"/>
      </a>
      <button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
    </div>
    <div class="nav-links">
      <a href="pesantiket.php"><i class="fas fa-ticket-alt"></i> Pesan Tiket</a>
      <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
      <a href="outlet.php"><i class="fas fa-store"></i> Outlet</a>
      <a href="kontak.php"><i class="fas fa-phone"></i> Kontak</a>
      <a href="blog.php"><i class="fas fa-newspaper"></i> Blog</a>
    </div>
    <div class="auth-buttons">
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </nav>

  <!-- Hero -->
  <section class="hero">
    <h1>Jelajahi Sulawesi Selatan dengan Nyaman</h1>
    <p>Pesan tiket bus sesuai kebutuhan Anda.</p>
  </section>

  <!-- Pencarian Tiket -->
  <section class="search-box">
    <form class="search-form" action="keberangkatan.php" method="GET">
      <div class="form-group">
        <label for="kotaAsal">Kota Asal</label>
        <div class="input-icon"><i class="fas fa-map-marker-alt"></i>
          <select id="kotaAsal" name="kota_asal" required>
            <option value="" disabled selected>Pilih lokasi</option>
            <option>Makassar</option>
            <option>Palopo</option>
            <option value="Masamba">Masamba</option>
            <option value="Malili">Malili</option>
            <option value="Pare-pare">Pare-Pare</option>
            <option value="Pangkajene">Pangkajene</option>
            <option value="Mangkutana">Mangkutana</option>
            <option value="Sorowako">Sorowako</option>
            <option value="Toraja">Toraja</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label for="kotaTujuan">Kota Tujuan</label>
        <div class="input-icon"><i class="fas fa-map-marker-alt"></i>
          <select id="kotaTujuan" name="kota_tujuan" required>
            <option value="" disabled selected>Pilih lokasi</option>
            <option value="Makassar">Makassar</option>
            <option value="Palopo">Palopo</option>
            <option value="Masamba">Masamba</option>
            <option value="Malili">Malili</option>
            <option value="Pare-pare">Pare-Pare</option>
            <option value="Pangkajene">Pangkajene</option>
            <option value="Mangkutana">Mangkutana</option>
            <option value="Sorowako">Sorowako</option>
            <option value="Toraja">Toraja</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label for="tanggalBerangkat">Tanggal Berangkat</label>
        <div class="input-icon"><i class="fas fa-calendar-alt"></i>
          <input type="date" id="tanggalBerangkat" name="tanggal_berangkat" required>
        </div>
      </div>

      <div class="form-group">
        <label for="tanggalPulang">Tanggal Pulang</label>
        <div class="input-icon">
            <i class="fas fa-calendar-alt"></i>
            <input type="date" id="tanggalPulang" name="tanggal_pulang">
        </div>
    </div>

      <div class="form-group">
        <label for="penumpang">Penumpang</label>
        <div class="input-icon"><i class="fas fa-user"></i>
          <input type="number" id="penumpang" name="jumlah_penumpang" min="1" max="10" value="1" required>
        </div>
      </div>
      <button type="submit" class="search-btn"><i class="fas fa-search"></i> Cari Tiket</button>
    </form>
  </section>

  <section class="booking-steps">
    <div class="booking-steps-container container">
        <!-- Decorative elements -->
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        
        <!-- Section heading -->
        <div class="section-header">
            <span class="preheading">Proses Pemesanan Cepat</span>
            <h2>Pesan tiket bus hanya dalam 3 langkah</h2>
            <p>Proses pemesanan tiket kurang dari <strong>5 menit</strong> dengan sistem yang aman dan terpercaya</p>
        </div>
        
        <!-- Steps wrapper -->
        <div class="steps">
            <!-- Step 1 -->
            <div class="step" data-step="1">
                <div class="step-icon-wrapper">
                    <div class="step-number">01</div>
                    <div class="icon-overlay">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                </div>
                <div class="step-content">
                    <h3>LOKASI</h3>
                    <p>Pilih tempat keberangkatan dan tujuan Anda dengan mudah</p>
                    <div class="step-extra">
                        <span class="badge">Mudah</span>
                    </div>
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="step" data-step="2">
                <div class="step-icon-wrapper">
                    <div class="step-number">02</div>
                    <div class="icon-overlay">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
                <div class="step-content">
                    <h3>TARIF</h3>
                    <p>Pilih tiket dan kelas sesuai dengan budget Anda</p>
                    <div class="step-extra">
                        <span class="badge">Terjangkau</span>
                    </div>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="step" data-step="3">
                <div class="step-icon-wrapper">
                    <div class="step-number">03</div>
                    <div class="icon-overlay">
                        <i class="fas fa-credit-card"></i>
                    </div>
                </div>
                <div class="step-content">
                    <h3>PEMBAYARAN</h3>
                    <p>Bayar dengan kartu, e-wallet, atau transfer bank</p>
                    <div class="step-extra">
                        <span class="badge">Aman</span>
                    </div>
                </div>
            </div>
        </div>

  <section class="benefits">
    <div class="container">
        <h2>Mengapa Memilih Nova Trans untuk Perjalanan Singkat Anda</h2>
        <div class="benefit-cards">
            <div class="benefit-card">
                <h3>CEPAT & PRAKTIS</h3>
                <p>Pemesanan instan untuk perjalanan mendadak, check-in mobile tanpa antre</p>
            </div>
            <div class="benefit-card highlight">
                <h3>HARGA BERSAHABAT</h3>
                <p>Tarif ekonomis dengan opsi diskon 10% untuk pengguna setia</p>
            </div>
            <div class="benefit-card">
                <h3>NYAMAN SEPANJANG PERJALANAN</h3>
                <p>Kursi ergonomis dengan ruang kaki lebih lega untuk perjalanan singkat</p>
            </div>
            <div class="benefit-card blue">
                <h3>LAYANAN LOKAL</h3>
                <p>Staf yang memahami kebutuhan perjalanan lokal dan berbahasa daerah</p>
            </div>
            <div class="benefit-card">
                <h3>PILIHAN WAKTU FLEKSIBEL</h3>
                <p>Jadwal keberangkatan beragam dari pagi hingga malam sesuai agenda Anda</p>
            </div>
            <div class="benefit-card">
                <h3>KONEKSI TERJAMIN</h3>
                <p>Terhubung dengan transportasi lanjutan di lokasi tujuan (ojek, taksi)</p>
            </div>
        </div>
    </div>
</section>
 <section class="faq">
        <div class="container">
            <h2>Pertanyaan Umum</h2>
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Bagaimana cara pesan tiket?</h3>
                        <button class="faq-toggle"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="faq-item open">
                    <div class="faq-question">
                        <h3>Apa yang bisa saya bawa dalam perjalanan?</h3>
                        <button class="faq-toggle"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="faq-answer">
                        <ol>
                            <li>Makanan dan minuman ringan</li>
                            <li>Tas atau koper dengan berat maksimal 20kg</li>
                            <li>Barang berharga yang harus Anda jaga sendiri</li>
                            <li>Dokumen perjalanan dan identitas</li>
                        </ol>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Bagaimana cara mengubah tanggal tiket?</h3>
                        <button class="faq-toggle"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Dapatkah saya mengubah nama di tiket?</h3>
                        <button class="faq-toggle"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact">
        <div class="container">
            <h3>Ada pertanyaan?</h3>
            <p>Tim layanan pelanggan kami siap membantu Anda</p>
            <a href="kontak.php" class="btn-primary">Ajukan Pertanyaan</a>
        </div>
      </section>


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
          <a href="pesantiket.php"><i class="fas fa-ticket-alt"></i> Pesan Tiket</a>
          <a href="kontak.php"><i class="fas fa-address-book"></i> Kontak</a>
          <a href="blog.php"><i class="fas fa-newspaper"></i> Blog</a>
        </div>
        <div class="footer-section">
          <h3>Rute Populer</h3>
          <a href="#">Makassar - Sorowako</a>
          <a href="#">Makassar - Malili</a>
          <a href="#">Makassar - Morowali</a>
          <a href="#">Makassar - Bahodopi</a>
        </div>
      </div>
      <div class="footer-bottom">
        &copy; 2025 Nova Trans. All rights reserved.
      </div>
    </footer>


<script>
    // disable back button
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', function() {
      history.pushState(null, null, location.href);
    });

// buang entry sebelumnya dan cegah kembali ke belakang
history.pushState(null, null, location.href);
window.addEventListener('popstate', function () {
  history.pushState(null, null, location.href);
});
</script>
</body>
</html>
