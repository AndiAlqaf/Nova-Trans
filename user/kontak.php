<?php
// Cek apakah file koneksi ada
$koneksi_file = __DIR__ . '/../koneksi.php';
if (!file_exists($koneksi_file)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'File koneksi database tidak ditemukan.']);
        exit;
    }
    die('File koneksi database tidak ditemukan di: ' . $koneksi_file);
}

require_once $koneksi_file;

// Jika request POST, simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Debug: Log semua data POST yang diterima
    error_log("POST Data: " . print_r($_POST, true));
    
    $nama   = trim($_POST['nama'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan  = trim($_POST['pesan'] ?? '');

    // Validasi input
    if (empty($nama) || empty($email) || empty($subjek) || empty($pesan)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom wajib diisi.']);
        exit;
    }
    
    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Format email tidak valid.']);
        exit;
    }
    
    try {
        // Cek apakah variabel $koneksi tersedia (sesuai dengan nama di koneksi.php)
        if (!isset($koneksi) || !$koneksi instanceof PDO) {
            throw new Exception("Koneksi database tidak tersedia");
        }
        
        // Test koneksi dengan query sederhana
        $test = $koneksi->query("SELECT 1");
        if (!$test) {
            throw new Exception("Database tidak dapat diakses");
        }
        
        // Debug: Log query yang akan dijalankan
        error_log("Executing query: INSERT INTO kontak (nama_lengkap, email, subjek, pesan) VALUES ($nama, $email, $subjek, $pesan)");
        
        // Cek apakah tabel kontak ada
        $check_table = $koneksi->query("SHOW TABLES LIKE 'kontak'");
        if ($check_table->rowCount() == 0) {
            throw new Exception("Tabel 'kontak' tidak ditemukan dalam database");
        }
        
        // Prepare dan execute query
        $stmt = $koneksi->prepare("INSERT INTO kontak (nama_lengkap, email, subjek, pesan) VALUES (:nama, :email, :subjek, :pesan)");
        $result = $stmt->execute([
            ':nama'   => $nama,
            ':email'  => $email,
            ':subjek' => $subjek,
            ':pesan'  => $pesan
        ]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Pesan berhasil dikirim. Terima kasih!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data ke database.']);
        }
        
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
    } catch (Exception $e) {
        error_log("General Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
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
    <style>
      /* Alert styles */
      .alert {
        padding: 15px;
        margin: 20px 0;
        border-radius: 5px;
        font-weight: 600;
        animation: slideIn 0.3s ease-out;
      }
      .alert.success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
      }
      .alert.error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
      }
      .loading {
        opacity: 0.6;
        pointer-events: none;
      }
      @keyframes slideIn {
        from {
          opacity: 0;
          transform: translateY(-10px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
    </style>
  </head>
  <body>
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
              <label for="nama">Nama Lengkap <span style="color: red;">*</span></label>
              <input type="text" id="nama" name="nama" required />
            </div>
            <div class="form-group">
              <label for="email">Alamat Email <span style="color: red;">*</span></label>
              <input type="email" id="email" name="email" required />
            </div>
            <div class="form-group">
              <label for="subjek">Subjek <span style="color: red;">*</span></label>
              <input type="text" id="subjek" name="subjek" required />
            </div>
            <div class="form-group">
              <label for="pesan">Pesan <span style="color: red;">*</span></label>
              <textarea id="pesan" name="pesan" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn-submit" id="btnSubmit">
              <i class="fas fa-paper-plane"></i> Kirim Pesan
            </button>
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
          
          // Clear previous response
          $('#form-response').html('');
          
          // Show loading state
          $('#btnSubmit').html('<i class="fas fa-spinner fa-spin"></i> Mengirim...').addClass('loading');
          
          // Get form data
          var formData = $(this).serialize();
          
          // Debug: Log data yang akan dikirim
          console.log('Sending data:', formData);
          
          // Send AJAX request
          $.ajax({
            url: '', // Send to same page
            type: 'POST',
            data: formData,
            dataType: 'json',
            timeout: 30000, // 30 second timeout
            success: function(res) {
              console.log('Response:', res);
              const cls = res.success ? 'success' : 'error';
              $('#form-response').html(`<div class="alert ${cls}">${res.message}</div>`);
              
              if (res.success) { 
                $('#formKontak')[0].reset(); 
                // Auto hide success message after 5 seconds
                setTimeout(function() {
                  $('#form-response .alert.success').fadeOut();
                }, 5000);
              }
              
              // Reset button
              $('#btnSubmit').html('<i class="fas fa-paper-plane"></i> Kirim Pesan').removeClass('loading');
            },
            error: function(xhr, status, error) {
              console.error('AJAX Error:', error);
              console.error('Status:', status);
              console.error('Response Text:', xhr.responseText);
              
              let errorMsg = 'Terjadi kesalahan saat mengirim pesan.';
              
              if (status === 'timeout') {
                errorMsg = 'Koneksi timeout. Silakan coba lagi.';
              } else if (xhr.status === 0) {
                errorMsg = 'Tidak ada koneksi internet.';
              } else if (xhr.status >= 500) {
                errorMsg = 'Terjadi kesalahan server. Silakan coba lagi.';
              }
              
              $('#form-response').html(`<div class="alert error">${errorMsg}</div>`);
              $('#btnSubmit').html('<i class="fas fa-paper-plane"></i> Kirim Pesan').removeClass('loading');
            }
          });
        });
        
        // Auto clear form validation messages
        $('#formKontak input, #formKontak textarea').on('input', function() {
          if ($(this).val().trim() !== '') {
            $(this).removeClass('error');
          }
        });
      });
    </script>
  </body>
</html>