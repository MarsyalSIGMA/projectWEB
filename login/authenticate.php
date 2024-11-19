<?php
session_start();

// Koneksi ke database
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "user";

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tangani form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action']; // login atau signup
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($action == "login") {
        // Proses Login
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Login berhasil
                $_SESSION['username'] = $user['username'];
                echo "Login berhasil. Selamat datang, " . htmlspecialchars($user['username']) . "!";
                echo "<br><a href='dashboard.php'>Lanjut ke Dashboard</a>";
                exit;
            } else {
                echo "Password salah.";
            }
        } else {
            echo "Username tidak ditemukan.";
        }
        $stmt->close();
    } elseif ($action == "signup") {
        // Proses Signup
        $sql_check = "SELECT id FROM users WHERE username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo "Username sudah digunakan. Silakan pilih username lain.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ss", $username, $hashed_password);

            if ($stmt_insert->execute()) {
                echo "Pendaftaran berhasil! <a href='auth.php'>Login di sini</a>";
            } else {
                echo "Terjadi kesalahan: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    } else {
        echo "Aksi tidak valid.";
    }
}

$conn->close();
?>