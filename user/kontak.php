<?php
// Proses AJAX dan koneksi database
$host = "localhost";
$dbname = "nova_trans";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . $e->getMessage()]);
    exit;
}

// Jika request POST, simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $nama   = trim($_POST['nama'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan  = trim($_POST['pesan'] ?? '');

    if ($nama === '' || $email === '' || $subjek === '' || $pesan === '') {
        echo json_encode(['success' => false, 'message' => 'Semua kolom wajib diisi.']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO kontak (nama_lengkap, email, subjek, pesan) VALUES (:nama, :email, :subjek, :pesan)");
        $stmt->execute([
            ':nama'   => $nama,
            ':email'  => $email,
            ':subjek' => $subjek,
            ':pesan'  => $pesan
        ]);
        echo json_encode(['success' => true, 'message' => 'Pesan berhasil dikirim. Terima kasih!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kontak Kami - Nova Trans</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="kontak.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  </head>
  <body>
    <!-- Navbar dan konten sama seperti sebelumnya -->
   <!-- Navbar -->
    <nav class="navbar">
      <div class="logo-mobile-wrap">
        <a href="pesantiket.php" class="logo">
          <img src="Gambar/LOGO.png" alt="Logo Nova Trans" />
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
    <div class="main-content">
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
      </div>
    </div>

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

    <script>
      $(function() {
        $('#formKontak').on('submit', function(e) {
          e.preventDefault();
          $('#form-response').html('');
          $.post('', $(this).serialize(), function(res) {
            const cls = res.success ? 'success' : 'error';
            $('#form-response').html(`<div class="alert ${cls}">${res.message}</div>`);
            if (res.success) { $('#formKontak')[0].reset(); }
          }, 'json');
        });
      });
    </script>
  </body>
</html>
