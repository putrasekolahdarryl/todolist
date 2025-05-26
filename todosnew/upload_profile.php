<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loginindex.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Periksa apakah file diupload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['profile_picture']['tmp_name'];
    $fileName = basename($_FILES['profile_picture']['name']);
    $targetDir = 'upload/';
    $targetPath = $targetDir . uniqid() . '_' . $fileName;

    // Validasi tipe file (hanya gambar)
    $fileType = mime_content_type($fileTmp);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($fileType, $allowedTypes)) {
        die("File harus berupa JPG, PNG, atau GIF.");
    }

    // Pindahkan file ke folder upload/
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($fileTmp, $targetPath)) {
        // Simpan path gambar ke database
        $relativePath = basename($targetPath); // hanya simpan nama file
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $relativePath, $user_id);
        $stmt->execute();
        $stmt->close();

        header("Location: dashboard.php");
        exit();
    } else {
        die("Gagal mengupload file.");
    }
} else {
    die("Tidak ada file yang diupload.");
}
?>
