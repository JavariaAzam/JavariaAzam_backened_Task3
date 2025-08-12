<?php
// delete.php - delete a task belonging to the logged-in user

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$task_id = intval($_GET['id'] ?? 0);

if ($task_id <= 0) {
    header("Location: tasks.php?error=" . urlencode("Invalid task id."));
    exit;
}

// Delete only if this user owns the task
$sql = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('ii', $task_id, $user_id);
    if ($stmt->execute()) {
        header("Location: tasks.php?success=" . urlencode("Task deleted."));
        exit;
    } else {
        error_log("Delete execute error: " . $stmt->error);
        header("Location: tasks.php?error=" . urlencode("Unable to delete."));
        exit;
    }
} else {
    error_log("Delete prepare error: " . $mysqli->error);
    header("Location: tasks.php?error=" . urlencode("Server error."));
    exit;
}
