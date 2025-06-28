<?php
// daftar.php — Halaman Registrasi Nova Trans
session_start();
require_once __DIR__ . '/../koneksi.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tangkap data
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $hp       = trim($_POST['hp'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    
    // Validasi dasar
    if (!$nama || !$email || !$hp || !$password || !$confirm) {
        $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid.";
    } elseif (!preg_match('/^\d{10,15}$/', $hp)) {
        $error = "Nomor HP harus 10–15 digit.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $confirm) {
        $error = "Password dan konfirmasi tidak cocok.";
    } else {
        // Simpan ke DB
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "INSERT INTO user (nama, email, hp, password, role) VALUES (?, ?, ?, ?, 'user')"
        );
        $stmt->execute([$nama, $email, $hp, $hash]);
        header('Location: masuk.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar – Nova Trans</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <!-- FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="daftaarr.css" rel="stylesheet" />

</head>
<body>
  <div class="wrapper">
    <div class="container">
      <h2><i class="fas fa-user-plus"></i> Daftar Akun Baru</h2>
      <p class="intro text-center mb-4">
  Bergabunglah dengan NOVA TRANS Untuk mendapatkan Pengalaman Perjalanan yang Terbaik!
</p>
      <?php if($error): ?><div class="alert"><?=htmlspecialchars($error)?></div><?php endif; ?>
      <form method="POST" action="">
        <div class="input-icon">
          <i class="fas fa-user"></i>
          <input type="text" name="nama" placeholder=" " value="<?=htmlspecialchars($_POST['nama']??'')?>" required />
          <label>Nama Lengkap</label>
        </div>
        <div class="input-icon">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" placeholder=" " value="<?=htmlspecialchars($_POST['email']??'')?>" required />
          <label>Email Aktif</label>
        </div>
        <div class="input-icon">
          <i class="fas fa-phone"></i>
          <input type="text" name="hp" placeholder=" " value="<?=htmlspecialchars($_POST['hp']??'')?>" required />
          <label>Nomor HP</label>
        </div>
        <div class="input-icon">
          <i class="fas fa-lock"></i>
          <input type="password" id="pass" name="password" placeholder=" " required />
          <label>Kata Sandi</label>
          <span class="toggle-password"><i class="fas fa-eye"></i></span>
        </div>
        <div class="input-icon">
          <i class="fas fa-lock"></i>
          <input type="password" id="confirm" name="confirm" placeholder=" " required />
          <label>Konfirmasi Kata Sandi</label>
          <span class="toggle-password" data-target="confirm"><i class="fas fa-eye"></i></span>
        </div>
        <button type="submit" class="btn btn-submit"><i class="fas fa-user-plus"></i> Daftar Sekarang</button>
      </form>
      <p class="bottom">Sudah punya akun? <a href="masuk.php">Masuk di sini</a></p>
    </div>
  </div>

  <script>
    document.querySelectorAll('.toggle-password').forEach(btn => {
      btn.addEventListener('click', () => {
        const target = btn.getAttribute('data-target') || 'pass';
        const input  = document.getElementById(target);
        const type   = input.type === 'password' ? 'text' : 'password';
        input.type  = type;
        btn.innerHTML = type === 'password'
          ? '<i class="fas fa-eye"></i>'
          : '<i class="fas fa-eye-slash"></i>';
      });
    });
  </script>
</body>
</html>
