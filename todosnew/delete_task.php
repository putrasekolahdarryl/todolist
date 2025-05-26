<?php
session_start();
include 'koneksi.php';

// Cek autentikasi
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Unauthorized access";
    exit();
}

$user_id = $_SESSION['user_id'];

// Validasi parameter id via GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $task_id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM todos_tabel WHERE id = ? AND user_id = ?");
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
        echo "Failed to delete task.";
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Invalid request. Use GET and provide ?id=";
}
?>
