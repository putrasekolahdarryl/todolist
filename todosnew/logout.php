<?php
// Mulai sesi untuk mengakses session yang aktif
session_start();

// Hapus semua data sesi
session_unset();

// Hancurkan sesi
session_destroy();

// Arahkan pengguna kembali ke halaman login
header("Location: loginindex.php");
exit;
?>
