<?php
// user/masuk.php – Login Nova Trans
require_once __DIR__ . '/../koneksi.php';

// cegah browser cache agar form selalu di-reload
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
if (isset($_SESSION['user_id'])) {
    if (strtolower($_SESSION['role']) === 'admin') {
        header('Location: ../adminn/dashboard.php');
    } else {
        header('Location: pesantiket.php');
    }
    exit;
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"]    ?? '');
    $password = trim($_POST["password"] ?? '');
    
    if ($email === '' || $password === '') {
        $error = "Email dan password harus diisi.";
    } else {
        $stmt = $koneksi->prepare("SELECT * FROM `user` WHERE email = :email");
        $stmt->execute([':email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id_pengguna"];
                $_SESSION["email"]   = $user["email"];
                $_SESSION["role"]    = $user["role"];
                
                if (strtolower($user["role"]) === "admin") {
                    header('Location: ../adminn/dashboard.php');
                } else {
                    header('Location: pesantiket.php');
                }
                exit;
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
