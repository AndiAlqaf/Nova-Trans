<?php
// Database configuration
$host = "localhost";
$dbname = "nova_trans";
$username = "tmj";
$password = "databasetmj";
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kontak Kami - Nova Trans</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="kontak.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  </head>
  <body>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Navbar -->
    <nav class="navbar">
      <div class="logo-mobile-wrap">
        <a href="cetaktiket.php" class="logo">
          <img src="Gambar/LOGO.png" alt="Nava Trans" />
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
        <a class="btn btn-primary" href="cetaktiket.php"><i class="fas fa-qrcode"></i> Cek Tiket</a>
        <a class="btn btn-outline" href="masuk.php"><i class="fas fa-user"></i> Daftar/Masuk</a>
      </div>
    </nav>

    <!-- Konten Utama Kontak -->
    <div class="main-content">
      <div class="contact-header">
        <h1>Hubungi Kami</h1>
        <p>Butuh bantuan atau informasi lebih lanjut? Isi formulir di samping atau hubungi kami secara langsung.</p>
      </div>

      <div class="contact-wrapper">
        <div class="contact-form-section">
          <h2>Kirim Pesan Anda</h2>
          <form id="formKontak">
            <div class="form-group">
              <label for="nama">Nama Lengkap</label>
              <input type="text" id="nama" name="nama" required />
            </div>
            <div class="form-group">
              <label for="email">Alamat Email</label>
              <input type="email" id="email" name="email" required />
            </div>
            <div class="form-group">
              <label for="subjek">Subjek</label>
              <input type="text" id="subjek" name="subjek" required />
            </div>
            <div class="form-group">
              <label for="pesan">Pesan</label>
              <textarea id="pesan" name="pesan" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn-submit">Kirim Pesan</button>
          </form>
          <div id="form-response"></div>
          <a href="https://wa.me/6282292159543" class="btn-wa" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" class="wa-icon" viewBox="0 0 32 32">
              <path d="M16.003 3.2c-7.057 0-12.8 5.743-12.8 12.8 0 2.256.592 4.454 1.712 6.398L3.2 28.8l6.586-1.705c1.866 1.019 3.976 1.553 6.217 1.553 7.057 0 12.8-5.743 12.8-12.8s-5.743-12.8-12.8-12.8zm0 23.2c-1.886 0-3.731-.495-5.351-1.43l-.383-.222-3.909 1.011 1.045-3.815-.248-.393c-1.06-1.676-1.62-3.605-1.62-5.55 0-5.709 4.641-10.35 10.35-10.35s10.35 4.641 10.35 10.35-4.641 10.35-10.35 10.35zm5.655-7.67c-.312-.156-1.843-.91-2.13-1.013s-.494-.156-.703.156-.805 1.013-.986 1.222-.363.234-.675.078-1.312-.483-2.498-1.537c-.922-.822-1.546-1.836-1.727-2.148s-.02-.468.136-.624c.14-.14.312-.364.468-.546.156-.182.208-.312.312-.52s.052-.39-.026-.546-.703-1.69-.962-2.316c-.252-.603-.507-.522-.703-.531l-.598-.01c-.208 0-.546.078-.832.39s-1.09 1.064-1.09 2.594 1.117 3.005 1.272 3.211c.156.208 2.195 3.35 5.314 4.697.743.321 1.322.513 1.773.656.746.238 1.427.204 1.963.124.599-.089 1.843-.753 2.104-1.482.26-.728.26-1.353.182-1.482-.078-.13-.286-.208-.598-.364z" fill="#fff"/>
            </svg>
            Kirim via WhatsApp
          </a>
        </div>

        <div class="contact-info-section">
          <h2>Informasi Kontak</h2>
          <div class="info-item">
            <h3>Call Center</h3>
            <a href="tel:082292159543">0822-9215-9543</a>
          </div>
          <div class="info-item">
            <h3>Email</h3>
            <p>cs@novatrans.co.id</p>
          </div>
          <div class="info-item">
            <h3>Alamat Kantor</h3>
            <p>Jl. Poros Malili - Makassar, Sulawesi Selatan</p>
          </div>
          <div class="info-item">
            <h3>Kunjungi Kami di Peta</h3>
            <iframe src="https://www.google.com/maps/embed?..." width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
          </div>
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

    <script>
      $(document).ready(function () {
        $("#formKontak").submit(function (e) {
          e.preventDefault();

          let formData = $(this).serialize();
          console.log("Data terkirim:", formData);

          $.ajax({
            url: "proses_kontak.php",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (res) {
              if (res.success) {
                $("#form-response").html(`<div class="alert success">${res.message}</div>`);
                $("#formKontak")[0].reset();
              } else {
                $("#form-response").html(`<div class="alert error">${res.message}</div>`);
              }
            },
            error: function () {
              $("#form-response").html(`<div class="alert error">Terjadi kesalahan koneksi.</div>`);
            }
          });
        });
      });
    </script>
  </body>
</html>
