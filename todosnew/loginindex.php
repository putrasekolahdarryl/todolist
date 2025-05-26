<?php
session_start();
require_once 'db.php';

// Jika user sudah login dan id valid, langsung redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        header('Location: dashboard.php');
        exit;
    } else {
        session_destroy(); // Jika ID tidak valid
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $fullName, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $fullName;
                $stmt->close();
                header('Location: dashboard.php');
                exit;
            }
        }

        $stmt->close();
        $error = 'Email atau kata sandi salah, atau akun tidak terdaftar.';
    } else {
        $error = 'Harap isi email dan password dengan benar.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke ToDo List Anda</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="description" content="Halaman login untuk aplikasi ToDo List interaktif Anda.">
</head>
<body>
    <div class="container login-container">
        <header>
            <h1><i class="fa fa-sign-in-alt"></i> Masuk</h1>
            <p class="tagline">Kelola tugas Anda dengan lebih baik dan produktif.</p>
        </header>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="email"><i class="fa fa-envelope"></i> Alamat Email:</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fa fa-lock"></i> Kata Sandi:</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi Anda" required>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe" disabled>
                <label class="form-check-label" for="rememberMe">Ingat saya (belum aktif)</label>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-arrow-right-to-bracket"></i> Masuk</button>
        </form>

        <footer class="mt-3 text-center">
            <p>Belum punya akun? <a href="register.php" class="register-link"><i class="fa fa-user-plus"></i> Daftar di sini</a></p>
            <p>&copy; 2025 ToDo List App. Hak Cipta Dilindungi.</p>
        </footer>
    </div>

    <script src="js/form-enhancements.js"></script>
</body>
</html>
