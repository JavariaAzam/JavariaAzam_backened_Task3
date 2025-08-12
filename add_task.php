<?php
// add_task.php - handle POST from tasks.php Add Task form

session_start();
require 'db.php';

// Only accept POST and only logged-in user
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

// Validate
if ($title === '' || $description === '') {
    header("Location: tasks.php?error=" . urlencode("Please fill required fields."));
    exit;
}

// Insert using prepared statement to avoid SQL injection
$sql = "INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('iss', $user_id, $title, $description);
    if ($stmt->execute()) {
        header("Location: tasks.php?success=" . urlencode("Task added successfully."));
        exit;
    } else {
        error_log("Add task execute error: " . $stmt->error);
        header("Location: tasks.php?error=" . urlencode("Unable to add task."));
        exit;
    }
} else {
    error_log("Add task prepare error: " . $mysqli->error);
    header("Location: tasks.php?error=" . urlencode("Server error."));
    exit;
}
