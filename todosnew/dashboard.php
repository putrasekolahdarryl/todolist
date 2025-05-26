<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loginindex.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$profilePicturePath = '';
$email = '';

// Ambil email & foto profil user dari database
$stmt = $conn->prepare("SELECT email, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $profilePicture);
if ($stmt->fetch()) {
    if (!empty($profilePicture)) {
        $profilePicturePath = 'upload/' . $profilePicture;
    } else {
        $initial = strtoupper(substr($email, 0, 1));
        $profilePicturePath = "https://ui-avatars.com/api/?name={$initial}&background=00f2ff&color=ffffff&size=150";
    }
}
$stmt->close();

// Tambah task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'], $_POST['deadline'])) {
    $task = mysqli_real_escape_string($conn, $_POST['task']);
    $deadline = $_POST['deadline'];
    $query = "INSERT INTO todos_tabel (user_id, task, deadline, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $user_id, $task, $deadline);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit();
}

// Ambil data task
$query = "SELECT * FROM todos_tabel WHERE user_id = ? AND status = 'pending' ORDER BY deadline ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Futuristic To-Do List</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="todo-container">
    <div class="todo-header">
        <h1>DO UR LIST!</h1>
        <div class="avatar" onclick="toggleProfileModal()" title="Profil"></div>
    </div>

    <form method="POST" class="todo-form">
        <input type="text" name="task" placeholder="Create task" required>
        <input type="datetime-local" name="deadline" required>
        <button type="submit">ADD TASK</button>
    </form>

    <input type="text" id="searchInput" placeholder="Search task..." onkeyup="filterTasks()" />

    <ul class="todo-list" id="taskList">
        <?php while ($row = $result->fetch_assoc()):
            $task_id = $row['id'];
            $task = htmlspecialchars($row['task']);
            $deadline = date('c', strtotime($row['deadline'])); // ISO format
        ?>
        <li class="todo-item" data-task="<?= strtolower($task) ?>">
            <div class="task-card">
                <div class="count-task">
                    <span class="task-name" data-id="<?= $task_id ?>"><?= $task ?></span>
                    <span class="countdown" data-deadline="<?= $deadline ?>">⏳ Loading...</span>
                </div>
                <div class="task-actions">
                    <button class="edit-btn" data-id="<?= $task_id ?>" title="Edit Tugas">✏️</button>
                    <button class="complete-btn" data-id="<?= $task_id ?>" title="Selesaikan Tugas">✅</button>
                    <button class="delete-btn" data-id="<?= $task_id ?>" title="Hapus Tugas">🗑️</button>
                </div>
            </div>
        </li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- MODAL PROFIL -->
<div id="profileModal" class="profile-modal">
    <div class="profile-content">
        <form action="upload_profile.php" method="POST" enctype="multipart/form-data">
            <div class="profile-pic-wrapper">
                <label for="profileUpload">
                    <img src="<?= htmlspecialchars($profilePicturePath) ?>" class="profile-pic" alt="Foto Profil"
                         onerror="this.src='https://placehold.co/150x150/00f2ff/ffffff?text=Profile'">
                    <div class="edit-icon">✎</div>
                </label>
                <input type="file" name="profile_picture" id="profileUpload" onchange="this.form.submit()" hidden>
            </div>
        </form>
        <div class="profile-text">Profil Pengguna</div>
        <form action="logout.php" method="POST">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</div>

<!-- SCRIPT -->
<script>
function toggleProfileModal() {
    const modal = document.getElementById('profileModal');
    modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
}

function filterTasks() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const tasks = document.querySelectorAll("#taskList .todo-item");
    tasks.forEach(task => {
        const taskName = task.getAttribute("data-task");
        task.style.display = taskName.includes(filter) ? "flex" : "none";
    });
}

// COUNTDOWN
document.addEventListener("DOMContentLoaded", function () {
    function updateCountdowns() {
        const now = new Date().getTime();
        document.querySelectorAll('.countdown').forEach(el => {
            const deadlineStr = el.getAttribute("data-deadline");
            const deadline = new Date(deadlineStr).getTime();

            if (!deadlineStr || isNaN(deadline)) {
                el.textContent = "❌ Invalid";
                return;
            }

            const distance = deadline - now;
            if (distance <= 0) {
                el.textContent = "⏰ Waktu habis!";
                el.classList.add("expired");
            } else {
                const d = Math.floor(distance / (1000 * 60 * 60 * 24));
                const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((distance % (1000 * 60)) / 1000);
                el.textContent = `${d ? d + "d " : ""}${h}h ${m}m ${s}s`;
            }
        });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 1000);
});
</script>

</body>
</html>
