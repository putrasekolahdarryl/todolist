<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "todolist_db"; // ganti dengan nama database kamu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
