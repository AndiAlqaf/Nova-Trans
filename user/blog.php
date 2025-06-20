<?php
// Database configuration
$host = "localhost";
$dbname = "nova_trans";
$username = "root";
$password = "";

// Establish database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = "";
$success = "";
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Promo & Berita | Nova Trans</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="blog.css" />
    
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar">
      <div class="logo-mobile-wrap">
        <a href="cetaktiket.php" class="logo">
          <img src="Gambar/LOGO.png" alt="Madinah Trans" />
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
        <a class="btn btn-primary" href="cetaktiket.php"><i class="fas fa-qrcode"></i> Cek Tiket</a>
        <a class="btn btn-outline" href="masuk.php"><i class="fas fa-user"></i> Daftar/Masuk</a>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <h1>Promo & Berita Terbaru</h1>
        <p>Dapatkan informasi menarik seputar promo dan berita terbaru dari Nova Trans</p>
      </div>
    </section>

    <!-- Promo Section -->
    <section class="promo-section">
      <div class="section-title">
        <h2>Promo dan Berita Nova Trans</h2>
      </div>
      <div class="promo-grid">
        <div class="promo-card">
          <img src="Gambar/millenium_limited.png" alt="Bus Makassar Luwu Timur" />
          <span class="label">Millenium Big Class Limited</span>
          <div class="promo-content">
            <h3>Madinah Trans: Rute Perjalanan Bus Makassar Sorowako</h3>
            <p>
              Nikmati perjalanan kelas premium dengan armada terbaru dan fasilitas lengkap. Tiket mulai dari Rp 250.000 dengan diskon 10% untuk pemesanan online.
            </p>
            <a href="#" class="read-more">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>

        <div class="promo-card">
          <img
            src="Gambar/Millenium Big Class.png"
            alt="Bus Makassar Luwu Timur"
          />
          <span class="label">Millenium Big Class</span>
          <div class="promo-content">
            <h3>Nova Trans Makassar Sorowako Berbagai Pilihan Kelas!</h3>
            <p>
              Pesan Tiket Nova Trans Makassar Sorowako dengan beragam pilihan kelas. Kenyamanan perjalanan dengan harga terjangkau!
            </p>
            <a href="#" class="read-more">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>

        <div class="promo-card">
          <img src="Gambar/newarmada.png" alt="Bus Royal Sleeper Class" />
          <span class="label">Royal Sleeper Class</span>
          <div class="promo-content">
            <h3>Royal Sleeper Class, Jadwal, Tarif, dan Fasilitas</h3>
            <p>
              Nikmati perjalanan nyaman dengan harga terjangkau bersama kelas tidur premium. Tersedia rute Makassar - Luwu Timur setiap hari.
            </p>
            <a href="#" class="read-more">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
      <div class="section-title">
        <h2>Keunggulan Madinah Trans</h2>
      </div>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-bus"></i>
          </div>
          <h3>Armada Terbaru</h3>
          <p>Armada bus modern dengan perawatan berkala untuk perjalanan nyaman dan aman</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-headset"></i>
          </div>
          <h3>Customer Service 24/7</h3>
          <p>Layanan pelanggan siap melayani Anda kapanpun dibutuhkan</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-hand-holding-usd"></i>
          </div>
          <h3>Harga Terjangkau</h3>
          <p>Berbagai pilihan kelas dan harga untuk menyesuaikan kebutuhan dan budget Anda</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-wifi"></i>
          </div>
          <h3>Fasilitas Lengkap</h3>
          <p>Wi-Fi, stop kontak, toilet, dan fasilitas lengkap lainnya untuk kenyamanan perjalanan</p>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section">
      <div class="section-title">
        <h2>Apa Kata Mereka?</h2>
      </div>
      <div class="testimonial-card">
        <p class="testimonial-text">
          "Sangat puas dengan layanan Madinah Trans. Bus bersih, nyaman, dan sopir yang profesional. Perjalanan Makassar-Sorowako menjadi tidak membosankan dengan fasilitas lengkap di bus."
        </p>
        <div class="testimonial-author">
          <img src="/api/placeholder/50/50" alt="Testimonial Author" />
          <div class="author-info">
            <h4>Ahmad Fauzi</h4>
            <p>Pelanggan Setia</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter-section">
      <div class="newsletter-content">
        <h3>Dapatkan Promo Menarik</h3>
        <p>Langganan newsletter kami untuk mendapatkan info promo terbaru dan diskon khusus</p>
        <form class="newsletter-form">
          <input type="email" placeholder="Masukkan email Anda" required />
          <button type="submit">Langganan</button>
        </form>
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
          <a href="mailto:info@madinahtrans.co.id"><i class="fas fa-envelope"></i> info@madinahtrans.co.id</a>
          <a href="#"><i class="fab fa-whatsapp"></i> WhatsApp CS</a>
        </div>
        <div class="footer-section">
          <h3>Informasi</h3>
          <a href="cetaktiket.php"><i class="fas fa-home"></i> Beranda</a>
          <a href="jadwal.php"><i class="fas fa-calendar-alt"></i> Jadwal</a>
          <a href="pesan-tiket.php"><i class="fas fa-ticket-alt"></i> Pesan Tiket</a>
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
        &copy; 2025 Madinah Trans. All rights reserved.
      </div>
    </footer>

    <script src="blog.js"></script>
  </body>
</html>