<?php
// edit_task.php - GET: show form, POST: update task

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = intval($_SESSION['user_id']);

// If GET: show form with current task data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $task_id = intval($_GET['id'] ?? 0);
    if ($task_id <= 0) {
        header("Location: tasks.php?error=" . urlencode("Invalid task id."));
        exit;
    }

    // Fetch the task only if it belongs to this user
    $sql = "SELECT id, title, description FROM tasks WHERE id = ? AND user_id = ? LIMIT 1";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param('ii', $task_id, $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $task = $res->fetch_assoc();
        $stmt->close();

        if (!$task) {
            header("Location: tasks.php?error=" . urlencode("Task not found or not authorized."));
            exit;
        }
    } else {
        error_log("Edit fetch prepare failed: " . $mysqli->error);
        header("Location: tasks.php?error=" . urlencode("Server error."));
        exit;
    }

    // Show edit form (HTML)
    ?>
    <!doctype html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>Edit Task</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/style.css">
    </head>
    <body class="bg-light">
    <div class="container my-4">
      <a href="tasks.php" class="btn btn-secondary mb-3">← Back</a>
      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Edit Task</h5>
          <form method="post" action="edit_task.php">
            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input name="title" class="form-control" required value="<?php echo htmlspecialchars($task['title']); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="6" required><?php echo htmlspecialchars($task['description']); ?></textarea>
            </div>
            <button class="btn btn-primary">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// If POST: perform update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($task_id <= 0 || $title === '' || $description === '') {
        header("Location: tasks.php?error=" . urlencode("Invalid input."));
        exit;
    }

    // Update only if this user owns the task
    $sql = "UPDATE tasks SET title = ?, description = ? WHERE id = ? AND user_id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param('ssii', $title, $description, $task_id, $user_id);
        if ($stmt->execute()) {
            header("Location: tasks.php?success=" . urlencode("Task updated."));
            exit;
        } else {
            error_log("Edit execute error: " . $stmt->error);
            header("Location: tasks.php?error=" . urlencode("Unable to update."));
            exit;
        }
    } else {
        error_log("Edit prepare error: " . $mysqli->error);
        header("Location: tasks.php?error=" . urlencode("Server error."));
        exit;
    }
}
