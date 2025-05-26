<?php
$host = 'localhost';         // Host database
$db   = 'todolist_db';       // Nama database, pastikan sesuai
$user = 'root';              // Username default XAMPP
$pass = '';                  // Password (kosongkan jika tidak ada)

// Buat koneksi menggunakan MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
