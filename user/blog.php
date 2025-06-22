
  <?php
// Mulai output buffering (supaya header redirect bisa dijalankan sebelum output)
ob_start();

// Database configuration
$host     = "localhost";
$dbname   = "nova_trans";
$username = "root";
$password = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error   = "";
$success = "";

// 1) Tangani POST, lalu Redirect jika sukses
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonial'])) {
    $name    = trim($_POST['name']);
    $message = trim($_POST['message']);

    if ($name === '' || $message === '') {
        $error = "Nama dan testimoni tidak boleh kosong.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO testimoni (name, message)
            VALUES (:name, :message)
        ");
        $stmt->bindParam(':name',    $name);
        $stmt->bindParam(':message', $message);
        if ($stmt->execute()) {
            // Redirect untuk mencegah resubmit
            header("Location: ".$_SERVER['PHP_SELF']."?berhasil=1");
            exit;
        } else {
            $error = "Gagal mengirim testimoni. Silakan coba lagi.";
        }
    }
}

// 2) Tampilkan pesan sukses jika ada flag ?berhasil=1
if (isset($_GET['berhasil']) && $_GET['berhasil'] == '1') {
    $success = "Terima kasih, testimoni Anda sudah terkirim!";
}

// 3) Ambil semua testimoni terbaru
$stmt = $conn->query("
    SELECT name, message, submitted_at
    FROM testimoni
    ORDER BY submitted_at DESC
");
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Akhiri output buffering dan kirim header
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Promo & Berita - Nova Trans</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
    rel="stylesheet"
  />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
  />
  <link rel="stylesheet" href="blog.css" />

  <style>
    .testimonial-form {
      margin-bottom: 2rem;
      display: flex;
      flex-direction: column;
    }
    .testimonial-form input,
    .testimonial-form textarea {
      padding: 0.75rem;
      margin-bottom: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-family: inherit;
    }
    .testimonial-form button {
      align-self: flex-start;
      padding: 0.5rem 1rem;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-family: inherit;
    }
    .alert {
      padding: 0.75rem 1rem;
      margin-bottom: 1rem;
      border-radius: 4px;
      font-family: inherit;
    }
    .alert-error {
      background: #f8d7da;
      color: #721c24;
    }
    .alert-success {
      background: #d4edda;
      color: #155724;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo-mobile-wrap">
      <a href="cetaktiket.php" class="logo">
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
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
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
          <h3>Nova Trans: Rute Perjalanan Bus Makassar Sorowako</h3>
          <p>
            Nikmati perjalanan kelas premium dengan armada terbaru dan fasilitas lengkap. Tiket mulai dari Rp 250.000 dengan diskon 10% untuk pemesanan online.
          </p>
          <a href="#" class="read-more">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
        </div>
      </div>

      <div class="promo-card">
        <img src="Gambar/Millenium Big Class.png" alt="Bus Makassar Luwu Timur" />
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
      <h2>Keunggulan Nova Trans</h2>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-bus"></i></div>
        <h3>Armada Terbaru</h3>
        <p>Armada bus modern dengan perawatan berkala untuk perjalanan nyaman dan aman</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-headset"></i></div>
        <h3>Customer Service 24/7</h3>
        <p>Layanan pelanggan siap melayani Anda kapanpun dibutuhkan</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-hand-holding-usd"></i></div>
        <h3>Harga Terjangkau</h3>
        <p>Berbagai pilihan kelas dan harga untuk menyesuaikan kebutuhan dan budget Anda</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-wifi"></i></div>
        <h3>Fasilitas Lengkap</h3>
        <p>Wi-Fi, stop kontak, toilet, dan fasilitas lengkap lainnya untuk kenyamanan perjalanan</p>
      </div>
    </div>
  </section>


  <!-- Testimonials Section -->
  <section class="testimonials-section">
    <div class="section-title">
      <h2>Apa Kata Mereka?</h2>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form class="testimonial-form" method="post" action="">
      <input
        type="text"
        name="name"
        placeholder="Nama Anda"
        required
      />
      <textarea
        name="message"
        rows="4"
        placeholder="Tulis testimoni Anda di sini..."
        required
      ></textarea>
      <button type="submit" name="submit_testimonial">
        Kirim Testimoni
      </button>
    </form>

    <?php if (count($testimonials)): ?>
      <?php foreach ($testimonials as $t): ?>
        <div class="testimonial-card">
          <p class="testimonial-text">
            "<?= nl2br(htmlspecialchars($t['message'])) ?>"
          </p>
          <div class="testimonial-author">
            <h4><?= htmlspecialchars($t['name']) ?></h4>
            <small><?= date('d M Y, H:i', strtotime($t['submitted_at'])) ?></small>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Belum ada testimoni. Jadilah yang pertama!</p>
    <?php endif; ?>
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

  <script src="blog.js"></script>
</body>
</html>
