<?php
// dashboard.php – Admin Dashboard NOVA TRANS

// 1. Cegah browser cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 2. Session & Auth
session_start();
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../masuk.php');
    exit;
}

// 3. Koneksi DB (sesuaikan path jika perlu)
$host = "localhost";
$dbname = "nova_trans";
$username = "root";
$password = "";
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$adminName = htmlspecialchars($_SESSION['email']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin – NOVA TRANS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo-container">
      <img src="../user/Gambar/LOGO.png" alt="Nova Trans Logo" style="width:32px;height:auto;margin-right:8px;">
      <h2>NOVA TRANS</h2>
    </div>
    <div class="menu-item"><a href="dashboard.php" class="active"><i class="fas fa-home"></i><span>Dashboard</span></a></div>
    <div class="menu-item"><a href="data_regist.php"><i class="fas fa-user-cog"></i><span>Data Registrasi</span></a></div>
    <div class="menu-item"><a href="data_bus.php"><i class="fas fa-database"></i><span>Data Bus</span></a></div>
    <div class="menu-item"><a href="kelola_kendaraan.php"><i class="fas fa-bus"></i><span>Kelola Kendaraan</span></a></div>
    <div class="menu-item"><a href="booking.php"><i class="fas fa-ticket-alt"></i><span>Kelola Booking</span></a></div>
    <div class="menu-item"><a href="kontak.php"><i class="fas fa-address-book"></i><span>Kelola Kontak</span></a></div>
    <div class="menu-item"><a href="laporan.php"><i class="fas fa-file-alt"></i><span>Laporan</span></a></div>
    <div class="menu-item" style="margin-top:auto;position:absolute;bottom:20px;">
      <a href="../user/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>

    </div>
  </div>

  <div class="main">
    <div class="header">
      <h1>Dashboard Admin</h1>
      <div class="user-info">
        <img src="https://i.pinimg.com/736x/18/f5/ba/18f5bab8f9181e8d7c371b16833a6849.jpg" alt="Admin" class="avatar">
        <span><?= $adminName ?></span>
      </div>
    </div>

    <div class="welcome-card">
      <h2>Selamat Datang, <?= $adminName ?>!</h2>
      <p>Gunakan menu di samping untuk mengelola sistem.</p>
    </div>

    <div class="stats-row">
      <div class="stat-card">
        <i class="fas fa-ticket-alt stat-icon"></i>
        <div class="stat-info">
          <h3><?= $conn->query("SELECT COUNT(*) FROM booking")->fetchColumn() ?></h3>
          <p>Total Booking</p>
        </div>
      </div>
      <div class="stat-card">
        <i class="fas fa-bus stat-icon"></i>
        <div class="stat-info">
          <h3><?= $conn->query("SELECT COUNT(*) FROM data_bus WHERE status='Tersedia'")->fetchColumn() ?></h3>
          <p>Bus Tersedia</p>
        </div>
      </div>
      <div class="stat-card">
        <i class="fas fa-user stat-icon"></i>
        <div class="stat-info">
          <h3><?= $conn->query("SELECT COUNT(*) FROM user")->fetchColumn() ?></h3>
          <p>Pengguna Terdaftar</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    // disable browser back
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', function() {
      history.pushState(null, null, location.href);
    });

     // Simple animation for the welcome card elements
    document.addEventListener('DOMContentLoaded', function() {
      const welcomeCard = document.querySelector('.welcome-card');
      welcomeCard.style.opacity = '0';
      welcomeCard.style.transform = 'translateY(20px)';

      setTimeout(() => {
        welcomeCard.style.opacity = '1';
        welcomeCard.style.transform = 'translateY(0)';
      }, 300);

      // Make menu items interactive
      const menuItems = document.querySelectorAll('.sidebar a');
      menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
          menuItems.forEach(i => i.classList.remove('active'));
          this.classList.add('active');
        });
      });
    });
// buang entry sebelumnya dan cegah kembali ke belakang
history.pushState(null, null, location.href);
window.addEventListener('popstate', function () {
  history.pushState(null, null, location.href);
});
  </script>
</body>
</html>
