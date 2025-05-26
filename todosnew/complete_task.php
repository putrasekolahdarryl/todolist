<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo "Unauthorized access";
    exit();
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Validasi parameter 'id' dari GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $task_id = intval($_GET['id']);

    // Update status task jadi 'completed' untuk task milik user ini
    $stmt = $conn->prepare("UPDATE todos_tabel SET status = 'completed' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo "success";
        } else {
            http_response_code(404);
            echo "Task not found or not yours.";
        }
    } else {
        http_response_code(500);
        echo "Failed to update task.";
    }

    $stmt->close();
} else {
    http_response_code(400); // Bad request
    echo "Invalid request. Use GET and provide ?id=";
}
?>
