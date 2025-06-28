<?php
require_once __DIR__ . '/../koneksi.php';
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
    <link rel="stylesheet" href="tentanng.css" />
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
      <a href="pesantiket.php" class="logo">
        <img src="Gambar/LOGO.png" alt="Logo Nova Trans"/>
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
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </nav>

    <header class="header">
        <div class="container">
            <h1>Tentang Kami</h1>
            <p>Solusi Transportasi Terbaik Anda</p>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    
    <div class="container">
        <section class="about-section">
            <div class="about-image">
                <img src="Gambar/LOGO.png" alt="Nova Trans Bus">
            </div>
            <div class="about-content">
                <h2>Apa itu Nova Trans?</h2>
                <p>Sejak berdiri pada tahun 2025, Bus Nova Trans telah menjadi pilihan utama untuk perjalanan darat berkelas. Kami menawarkan berbagai <span class="highlight">bus kelas</span> seperti <span class="bus-classes">Ekonomi Class, Sleeper Class Limited, dan dirancang untuk memberikan kenyamanan dan keamanan yang optimal</span> dalam setiap perjalanan.</p>
                <p>Dengan armada yang terawat, kami berkomitmen untuk memberikan pelayanan terbaik, menjaga waktu kedatangan, dan memastikan perjalanan Anda aman, nyaman, dan terjangkau bagi seluruh penumpang kami. Bus Nova Trans siap melayani Anda dengan berbagai rute populer.</p>
            </div>
        </section>
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
          <a href="tel:0821-9439-2804"><i class="fas fa-phone"></i> 0821-9439-2804</a>
          <a href="mailto:novatransbus@gmail.com"><i class="fas fa-envelope"></i> novatransbus@gmail.com</a>
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
          <a href="#">Makassar - Toroja</a>
          <a href="#">Makassar - Pare-Pare</a>
          <a href="#">Makassar - Palopo</a>
        </div>
      </div>
      <div class="footer-bottom">
        &copy; 2025 Nova Trans. All rights reserved.
      </div>
    </footer>
  </body>
</html>
