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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nova Trans - Tentang</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="tentang.css" />
  </head>
  <!-- Font Awesome CDN -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
/>
  <body>
     <!-- Navbar -->
    <nav class="navbar">
      <div class="logo-mobile-wrap">
        <a href="cetaktiket.php" class="logo">
          <img src="Gambar/LOGO.png" alt="Logo Nova Trans" />
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

    <!-- MAIN CONTENT -->
    <div class="container">
      <div class="image-section">
        <img src="Gambar/visual_1.png" alt="Ilustrasi penumpang ANS bus" />
      </div>
      <div class="content-section">
        <h1>Apa itu Nova Trans?</h1>
        <p>
          Sejak berdiri pada tahun 2024, Bus Madinah Trans telah menjadi pilihan
          utama perjalanan darat untuk
          <span class="highlight">rute Makassar-Luwu Timur</span>. Kami menawarkan
          berbagai kelas bus, mulai dari
          <span class="bus-classes"
            >Millenium Big Class, Royal Sleeper Class, dan Millenium Big Class
            Limited</span
          >, yang dirancang untuk memberikan kenyamanan dan kemewahan dalam
          setiap perjalanan.
        </p>
        <p>
          Dengan armada yang baru, kami berkomitmen menghadirkan pengalaman
          berkendara yang aman, nyaman, dan terjangkau bagi seluruh penumpang
          kami. Bus Nova Trans siap mengantarkan Anda dengan layanan terbaik.
        </p>
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
        &copy; 2025 Nova Trans. All rights reserved.
      </div>
    </footer>
  </body>
</html>
