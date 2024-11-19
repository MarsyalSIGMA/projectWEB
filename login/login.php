<?php
session_start();

// Cek jika user sudah login, redirect ke halaman lain
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php"); // Ubah 'dashboard.php' ke halaman tujuan setelah login
    exit;
}

// Proses login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'koneksi.php'; // Pastikan koneksi.php berisi koneksi ke database

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query ke database
    $query = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username']; // Simpan data user ke session
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username tidak ditemukan.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <section>
    <!-- Animasi Background -->
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>

    <!-- Form Login -->
    <div class="signin">
      <div class="content">
        <h2>Sign In</h2>
        <?php if ($error): ?>
          <p style="color: red; text-align: center;"><?= $error ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
          <div class="form">
            <div class="inputBox">
              <input type="text" name="username" required>
              <i>Username</i>
            </div>
            <div class="inputBox">
              <input type="password" name="password" required>
              <i>Password</i>
            </div>
            <div class="links">
              <a href="#">Forgot Password</a>
              <a href="signup.php">Signup</a>
            </div>
            <div class="inputBox">
              <input type="submit" value="Login">
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
</body>

</html>
