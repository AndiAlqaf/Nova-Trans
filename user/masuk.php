<?php
// masuk.php – Login Nova Trans

// 1. Cegah browser cache agar form selalu di-reload
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 2. Mulai session & cek apakah sudah login
session_start();
if (isset($_SESSION['user_id'])) {
    // Jika sudah login, langsung redirect sesuai role
    if (strtolower($_SESSION['role']) === 'admin') {
        header('Location: ../adminn/dashboard.php');
    } else {
        header('Location: pesantiket.php');
    }
    exit;
}

// 3. Konfigurasi database
$host     = "localhost";
$dbname   = "nova_trans";
$username = "root";
$password = "";

// 4. Koneksi PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = "";

// 5. Proses login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"]    ?? '');
    $password = trim($_POST["password"] ?? '');
    
    if ($email === '' || $password === '') {
        $error = "Email dan password harus diisi.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user["password"])) {
                // Sukses: set session
                $_SESSION["user_id"] = $user["id_pengguna"];
                $_SESSION["email"]   = $user["email"];
                $_SESSION["role"]    = $user["role"];
                // Redirect
                if (strtolower($user["role"]) === "admin") {
                    echo "<script>location.replace('../adminn/dashboard.php');</script>";
                } else {
                    echo "<script>location.replace('pesantiket.php');</script>";
                }
                exit();
            } else {
                $error = "Password tidak valid.";
            }
        } else {
            $error = "Email tidak terdaftar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login – Nova Trans</title>
  <!-- Font -->
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
    rel="stylesheet"
  />
  <!-- FontAwesome -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="masukk.css"/>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-container">
      <h1>Masuk ke Akun</h1>

      <?php if ($error !== ""): ?>
      <div class="alert alert-error">
        <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <div class="input-icon">
            <i class="fas fa-envelope"></i>
            <input
              type="email"
              id="email"
              name="email"
              required
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            />
            <label for="email">Email</label>
          </div>
        </div>

        <div class="form-group">
          <div class="input-icon">
            <i class="fas fa-lock"></i>
            <input
              type="password"
              id="password"
              name="password"
              required
            />
            <label for="password">Password</label>
            <span class="toggle-password"><i class="fas fa-eye"></i></span>
          </div>
        </div>

        <div class="extra-options">
          <label class="remember">
            <input type="checkbox" name="remember" /> Ingat saya
          </label>
        </div>

        <button type="submit" class="btn submit-btn">Masuk</button>

        <p class="signup-link">
          Belum punya akun? <a href="daftar.php">Daftar Sekarang</a>
        </p>
      </form>
    </div>
  </div>

  <!-- Toggle password script -->
  <script>
    document.querySelector('.toggle-password').addEventListener('click', function(){
      const pwd = document.getElementById('password');
      const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
      pwd.setAttribute('type', type);
      this.innerHTML = type === 'password'
        ? '<i class="fas fa-eye"></i>'
        : '<i class="fas fa-eye-slash"></i>';
    });
  </script>
</body>
</html>
