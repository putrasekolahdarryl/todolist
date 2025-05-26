<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: loginindex.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$todoId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($todoId <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Ambil data task
$stmt = $conn->prepare("SELECT task, deadline FROM todos_tabel WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $todoId, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$todo = $result->fetch_assoc();
$stmt->close();

if (!$todo) {
    header('Location: dashboard.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_todo'])) {
        $task = trim($_POST['task']);
        $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;

        if ($task === '') {
            $error = "Tugas tidak boleh kosong.";
        } else {
            $stmt = $conn->prepare("UPDATE todos_tabel SET task = ?, deadline = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ssii", $task, $deadline, $todoId, $user_id);
            if ($stmt->execute()) {
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Gagal memperbarui tugas.";
            }
            $stmt->close();
        }
    }

    if (isset($_POST['cancel'])) {
        header('Location: dashboard.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Tugas</title>
    <link rel="stylesheet" href="dashboard.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .edit-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 2rem;
            background: #222;
            border-radius: 16px;
            box-shadow: 0 0 20px #00f2ff88;
            color: white;
        }

        .edit-container input {
            width: 100%;
            margin: 0.5rem 0 1rem;
            padding: 0.5rem;
            border-radius: 8px;
            border: none;
            background: #333;
            color: white;
        }

        .button-group {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .btn.green {
            background-color: #00f2ff;
            color: black;
        }

        .btn.grey {
            background-color: #666;
            color: white;
        }

        .alert.error {
            background: #ff4d4d;
            padding: 1rem;
            border-radius: 8px;
            color: white;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Tugas</h2>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="task">Tugas:</label>
            <input type="text" name="task" id="task" required value="<?= htmlspecialchars($todo['task']) ?>">

            <label for="deadline">Deadline:</label>
            <input type="date" name="deadline" id="deadline" value="<?= htmlspecialchars($todo['deadline']) ?>">

            <div class="button-group">
                <button type="submit" name="save_todo" class="btn green">Simpan</button>
                <button type="submit" name="cancel" class="btn grey">Kembali</button>
            </div>
        </form>
    </div>
</body>
</html>
