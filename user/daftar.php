<?php
// Konfigurasi database
$host = "localhost";
$dbname = "nova_trans";
$username = "root";
$password = "";

$error = "";
$success = "";

try {
    // Koneksi database menggunakan PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cek jika form disubmit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST["nama_pengguna"]);
        $phone = trim($_POST["nomor_telepon"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $confirm_password = trim($_POST["confirm-password"]);

        // Validasi input
        if (empty($name) || empty($phone) || empty($email) || empty($password) || empty($confirm_password)) {
            $error = "Semua field harus diisi.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Format email tidak valid.";
        } elseif ($password !== $confirm_password) {
            $error = "Password tidak cocok.";
        } elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
            $error = "Nomor telepon tidak valid.";
        } else {
            // Cek apakah email sudah digunakan
            $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Email sudah terdaftar. Silakan gunakan email lain.";
            } else {
                // Cek apakah nomor telepon sudah digunakan
                $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE nomor_telepon = ?");
                $stmt->execute([$phone]);
                if ($stmt->fetchColumn() > 0) {
                    $error = "Nomor telepon sudah terdaftar. Silakan gunakan nomor lain.";
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Simpan ke database
                    $stmt = $conn->prepare("INSERT INTO user (nama_pengguna, nomor_telepon, email, password) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $phone, $email, $hashed_password]);

                    $success = "Pendaftaran berhasil! Silakan login.";
                    header("refresh:2;url=masuk.php");
                    exit;
                }
            }
        }
    }
} catch (PDOException $e) {
    $error = "Koneksi atau Query gagal: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NOVA TRANS - Daftar Akun</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="daftarr.css" />
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo-mobile-wrap">
            <a href="pesantiket.php" class="logo">
                <img src="Gambar/LOGO.png" alt="Nova Trans" />
            </a>
        </div>
        <div class="nav-links" id="navLinks">
            <a href="pesantiket.php"><i class="fas fa-ticket-alt"></i> Pesan Tiket</a>
            <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
            <a href="outlet.php"><i class="fas fa-store"></i> Outlet</a>
            <a href="kontak.php"><i class="fas fa-phone"></i> Kontak</a>
            <a href="blog.php"><i class="fas fa-newspaper"></i> Blog</a>
        </div>
        <div class="auth-buttons" id="authButtons">
            <a class="btn btn-primary" href="cetaktiket.php"><i class="fas fa-qrcode"></i> Cek Tiket</a>
            <a class="btn btn-outline" href="masuk.php"><i class="fas fa-user"></i> Daftar/Masuk</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="register-container">
            <h2><i class="fas fa-user-plus"></i> Daftar Akun Baru</h2>
            <p class="register-subtitle">Bergabunglah dengan NOVA TRANS untuk mendapatkan pengalaman perjalanan terbaik</p>

            <form class="form-register" id="registerForm" method="POST" action="">
                <div class="form-group">
                    <label for="nama">
                        <i class="fas fa-user"></i>
                        Nama Lengkap
                    </label>
                    <input 
                        type="text" 
                        id="nama" 
                        name="nama_pengguna" 
                        placeholder="Masukkan nama lengkap Anda" 
                        required 
                        autocomplete="name"
                    />
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email Aktif
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="contoh@email.com" 
                        required 
                        autocomplete="email"
                    />
                </div>

                <div class="form-group">
                    <label for="no_hp">
                        <i class="fas fa-phone"></i>
                        Nomor HP
                    </label>
                    <input 
                        type="tel" 
                        id="no_hp" 
                        name="nomor_telepon" 
                        placeholder="081234567890" 
                        pattern="[0-9]{10,15}" 
                        required 
                        autocomplete="tel"
                    />
                    <div class="input-hint">
                        <i class="fas fa-info-circle"></i>
                        Format: 10-15 digit angka
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Kata Sandi
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Masukkan kata sandi" 
                        minlength="6" 
                        required 
                        autocomplete="new-password"
                    />
                    <div class="input-hint">
                        <i class="fas fa-shield-alt"></i>
                        Minimal 6 karakter
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm-password">
                        <i class="fas fa-lock"></i>
                        Konfirmasi Kata Sandi
                    </label>
                    <input 
                        type="password" 
                        id="confirm-password" 
                        name="confirm-password" 
                        placeholder="Ketik ulang kata sandi" 
                        minlength="6" 
                        required 
                        autocomplete="new-password"
                    />
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-user-plus"></i>
                    Daftar Sekarang
                </button>

                <div class="divider">
                    <span>atau</span>
                </div>

                <div class="auth-links">
                    Sudah punya akun? <a href="masuk.php">Masuk di sini</a>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section footer-logo">
                <img src="Gambar/LOGO2.png" alt="Logo Nova Trans" />
                <p>Solusi Transportasi Terbaik dan Terpercaya Rute Makassar - Luwu Timur</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Call Center</h3>
                <a href="tel:0822-9215-9543"><i class="fas fa-phone"></i> 0822-9215-9543</a>
                <a href="mailto:info@novatrans.co.id"><i class="fas fa-envelope"></i> info@novatrans.co.id</a>
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
  
</body>
</html>