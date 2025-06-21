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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outlet & Perwakilan - Nova Trans</title>
    <link rel="stylesheet" href="outlet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
      <div class="logo-mobile-wrap">
        <a href="cetaktiket.php" class="logo">
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

    <section class="hero">
        <div class="container">
            <h2>Outlet & Perwakilan</h2>
            <p>Temukan lokasi outlet kami yang tersebar di berbagai kota di Sulawesi</p>
        </div>
    </section>

    <section class="outlets">
        <div class="container">
            <div class="outlet-filter">
                <label for="kota">Pilih Kota:</label>
                <select id="kota">
                    <option value="all">Semua Kota</option>
                    <option value="makassar">Makassar</option>
                    <option value="palopo">Palopo</option>
                    <option value="luwu-utara">Luwu Utara</option>
                    <option value="luwu-timur">Luwu Timur</option>
                    <option value="pare-pare">Pare-Pare</option>
                    <option value="sidrap">Sidrap</option>
                </select>
            </div>

            <div class="outlet-list">
                <!-- Makassar -->
                <div class="outlet-card" data-kota="makassar">
                    <div class="outlet-info">
                        <h3>Outlet Makassar Pusat</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Perintis Kemerdekaan No. 123, Makassar</p>
                        <p><i class="fas fa-phone"></i> 0411-123456</p>
                        <p><i class="fas fa-clock"></i> 08.00 - 21.00 WITA (Setiap Hari)</p>
                        <a href="https://maps.google.com/?q=-5.1477,119.4327" class="maps-link" target="_blank">
                            <i class="fas fa-location-arrow"></i> Lihat di Google Maps
                        </a>
                    </div>
                    <div class="outlet-image">
                        <img src="https://via.placeholder.com/300x200" alt="Outlet Makassar Pusat">
                    </div>
                </div>
                
                <div class="outlet-card" data-kota="makassar">
                    <div class="outlet-info">
                        <h3>Outlet Makassar Panakkukang</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Boulevard Raya No. 45, Mall Panakkukang, Makassar</p>
                        <p><i class="fas fa-phone"></i> 0411-234567</p>
                        <p><i class="fas fa-clock"></i> 09.00 - 22.00 WITA (Setiap Hari)</p>
                        <a href="https://maps.google.com/?q=-5.1674,119.4516" class="maps-link" target="_blank">
                            <i class="fas fa-location-arrow"></i> Lihat di Google Maps
                        </a>
                    </div>
                    <div class="outlet-image">
                        <img src="https://via.placeholder.com/300x200" alt="Outlet Makassar Panakkukang">
                    </div>
                </div>

                <!-- Palopo -->
                <div class="outlet-card" data-kota="palopo">
                    <div class="outlet-info">
                        <h3>Outlet Palopo</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Andi Djemma No. 78, Palopo</p>
                        <p><i class="fas fa-phone"></i> 0471-987654</p>
                        <p><i class="fas fa-clock"></i> 08.00 - 21.00 WITA (Setiap Hari)</p>
                        <a href="https://maps.google.com/?q=-3.0045,120.1964" class="maps-link" target="_blank">
                            <i class="fas fa-location-arrow"></i> Lihat di Google Maps
                        </a>
                    </div>
                    <div class="outlet-image">
                        <img src="https://via.placeholder.com/300x200" alt="Outlet Palopo">
                    </div>
                </div>

                <!-- Luwu Utara -->
                <div class="outlet-card" data-kota="luwu-utara">
                    <div class="outlet-info">
                        <h3>Outlet Masamba</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Trans Sulawesi No. 56, Masamba, Luwu Utara</p>
                        <p><i class="fas fa-phone"></i> 0473-321456</p>
                        <p><i class="fas fa-clock"></i> 08.00 - 20.00 WITA (Setiap Hari)</p>
                        <a href="https://maps.google.com/?q=-2.5548,120.3245" class="maps-link" target="_blank">
                            <i class="fas fa-location-arrow"></i> Lihat di Google Maps
                        </a>
                    </div>
                    <div class="outlet-image">
                        <img src="https://via.placeholder.com/300x200" alt="Outlet Masamba">
                    </div>
                </div>

                <!-- Luwu Timur -->
                <div class="outlet-card" data-kota="luwu-timur">
                    <div class="outlet-info">
                        <h3>Outlet Malili</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Soekarno Hatta No. 34, Malili, Luwu Timur</p>
                        <p><i class="fas fa-phone"></i> 0474-678901</p>
                        <p><i class="fas fa-clock"></i> 08.00 - 20.00 WITA (Setiap Hari)</p>
                        <a href="https://maps.google.com/?q=-2.9814,121.1035" class="maps-link" target="_blank">
                            <i class="fas fa-location-arrow"></i> Lihat di Google Maps
                        </a>
                    </div>
                    <div class="outlet-image">
                        <img src="https://via.placeholder.com/300x200" alt="Outlet Malili">
                    </div>
                </div>

                <!-- Pare-Pare -->
                <div class="outlet-card" data-kota="pare-pare">
                    <div class="outlet-info">
                        <h3>Outlet Pare-Pare</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Bau Massepe No. 89, Pare-Pare</p>
                        <p><i class="fas fa-phone"></i> 0421-876543</p>
                        <p><i class="fas fa-clock"></i> 08.00 - 21.00 WITA (Setiap Hari)</p>
                        <a href="https://maps.google.com/?q=-4.0096,119.6338" class="maps-link" target="_blank">
                            <i class="fas fa-location-arrow"></i> Lihat di Google Maps
                        </a>
                    </div>
                    <div class="outlet-image">
                        <img src="https://via.placeholder.com/300x200" alt="Outlet Pare-Pare">
                    </div>
                </div>

                <!-- Sidrap -->
                <div class="outlet-card" data-kota="sidrap">
                    <div class="outlet-info">
                        <h3>Outlet Pangkajene</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Jenderal Sudirman No. 112, Pangkajene, Sidrap</p>
                        <p><i class="fas fa-phone"></i> 0421-234567</p>
                        <p><i class="fas fa-clock"></i> 08.00 - 20.00 WITA (Setiap Hari)</p>
                        <a href="https://maps.google.com/?q=-3.9429,119.6770" class="maps-link" target="_blank">
                            <i class="fas fa-location-arrow"></i> Lihat di Google Maps
                        </a>
                    </div>
                    <div class="outlet-image">
                        <img src="https://via.placeholder.com/300x200" alt="Outlet Pangkajene">
                    </div>
                </div>
            </div>
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
        &copy; 2025 Nova Trans. All rights reserved.
      </div>
    </footer>

    <script src="outlet.js"></script>
</body>
</html>