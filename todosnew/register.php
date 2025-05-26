<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_email'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Konfirmasi password tidak sesuai.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, NULL)");
            $stmt->bind_param("sss", $fullName, $email, $hashedPassword);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $fullName;
                $stmt->close();
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Terjadi kesalahan saat mendaftarkan pengguna.';
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - ToDo List</title>
    <link rel="stylesheet" href="style.css">
    <meta name="description" content="Buat akun baru dan mulai mengatur tugas-tugas Anda.">
    <meta name="keywords" content="register, daftar, todo, task manager">
    <meta name="author" content="Tim ToDo List">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta property="og:title" content="Daftar - ToDo List">
    <meta property="og:description" content="Buat akun gratis dan kelola tugas harian Anda dengan mudah.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="URL_HALAMAN_DAFTAR">
</head>
<body>
    <div class="container register-container">
        <header>
            <h1><i class="fa fa-user-plus"></i> Daftar Akun</h1>
            <p class="tagline">Bergabung dan mulai kelola semua tugas Anda dengan mudah.</p>
        </header>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" method="post" action="">
            <div class="form-group">
                <label for="full_name"><i class="fa fa-user"></i> Nama Lengkap:</label>
                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Masukkan nama lengkap Anda" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fa fa-envelope"></i> Alamat Email:</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email aktif" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fa fa-lock"></i> Kata Sandi:</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
            </div>
            <div class="form-group">
                <label for="confirm_password"><i class="fa fa-check-double"></i> Konfirmasi Sandi:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Ulangi kata sandi" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Daftar</button>
        </form>

        <footer class="mt-3 text-center">
            <p>Sudah punya akun? <a href="loginindex.php" class="index-link"><i class="fa fa-sign-in-alt"></i> Masuk di sini</a></p>
            <p>&copy; 2025 ToDo List App. Hak Cipta Dilindungi.</p>
        </footer>
    </div>

    <script src="js/form-enhancements.js"></script>
</body>
</html>
