<?php
session_start();
require_once __DIR__ . '/db.php';

function isLoggedIn() {
    return isset($_SESSION['user_email']);
}

function getCurrentUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

function getCurrentUserName($conn) {
    $email = getCurrentUserEmail();
    if (!$email) return 'Pengguna';

    $stmt = $conn->prepare("SELECT full_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($fullName);
    $stmt->fetch();
    $stmt->close();

    return $fullName ?: 'Pengguna';
}

function authenticateUser($conn, $email, $password) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_email'] = $email;
            return true;
        }
    }
    $stmt->close();
    return false;
}

function registerUser($conn, $fullName, $email, $password) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $fullName = htmlspecialchars(trim($fullName));

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return false;
    }
    $stmt->close();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullName, $email, $hashedPassword);
    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

function logoutUser() {
    session_unset();
    session_destroy();
}

function addTodo($conn, $email, $task, $deadline = null) {
    if (empty($task)) return false;

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO todos_tabel (user_id, task, deadline, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $userId, $task, $deadline);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function getUserTodos($conn, $email) {
    $stmt = $conn->prepare("
        SELECT t.id, t.task, t.deadline, t.created_at, t.completed 
        FROM todos_tabel t
        INNER JOIN users u ON t.user_id = u.id
        WHERE u.email = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $todos = [];
    while ($row = $result->fetch_assoc()) {
        $todos[$row['id']] = [
            'task' => $row['task'],
            'deadline' => $row['deadline'],
            'created_at' => $row['created_at'],
            'completed' => $row['completed']
        ];
    }
    $stmt->close();
    return $todos;
}

function getTodo($conn, $email, $id) {
    $stmt = $conn->prepare("
        SELECT t.task, t.deadline, t.created_at, t.completed 
        FROM todos_tabel t
        INNER JOIN users u ON t.user_id = u.id
        WHERE t.id = ? AND u.email = ?
    ");
    $stmt->bind_param("is", $id, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $todo = $result->fetch_assoc();
    $stmt->close();

    return $todo;
}

function updateTodo($conn, $email, $id, $task, $deadline = null) {
    $stmt = $conn->prepare("
        UPDATE todos_tabel t
        INNER JOIN users u ON t.user_id = u.id
        SET t.task = ?, t.deadline = ?
        WHERE t.id = ? AND u.email = ?
    ");
    $stmt->bind_param("ssis", $task, $deadline, $id, $email);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function toggleComplete($conn, $email, $id) {
    $stmt = $conn->prepare("
        UPDATE todos_tabel t
        INNER JOIN users u ON t.user_id = u.id
        SET t.completed = NOT t.completed
        WHERE t.id = ? AND u.email = ?
    ");
    $stmt->bind_param("is", $id, $email);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function deleteTodo($conn, $email, $id) {
    $stmt = $conn->prepare("
        DELETE t FROM todos_tabel t
        INNER JOIN users u ON t.user_id = u.id
        WHERE t.id = ? AND u.email = ?
    ");
    $stmt->bind_param("is", $id, $email);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function formatDeadline($deadline) {
    return $deadline ? date('d/m/Y', strtotime($deadline)) : '';
}

function isOverdue($deadline) {
    return $deadline && strtotime($deadline) < time();
}
