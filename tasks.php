<?php
// tasks.php - Main task manager page.
// Shows the Add Task form and a table listing current user's tasks.

session_start();
require 'db.php';

// Protect the page: only logged-in users can see it
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get logged-in user's details
$user_id = intval($_SESSION['user_id']);
$user_name = $_SESSION['name'] ?? 'User';

// Fetch tasks belonging to this user (prepared statement)
$tasks = [];
$sql = "SELECT id, title, description, created_at FROM tasks WHERE user_id = ? ORDER BY created_at DESC";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $tasks = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    error_log("tasks fetch prepare failed: " . $mysqli->error);
    $error = "Unable to load tasks right now.";
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Task Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">Task Manager</a>
    <div class="d-flex">
      <span class="navbar-text me-3">Hello, <?php echo htmlspecialchars($user_name); ?></span>
      <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <div class="row">
    <!-- Left: Add Task -->
    <div class="col-lg-4 mb-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Add New Task</h5>
          <?php if (!empty($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
          <?php endif; ?>
          <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
          <?php endif; ?>

          <form method="post" action="add_task.php" novalidate>
            <div class="mb-3">
              <label class="form-label">Title <span class="text-danger">*</span></label>
              <input name="title" class="form-control" maxlength="255" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Description <span class="text-danger">*</span></label>
              <textarea name="description" class="form-control" rows="5" required></textarea>
            </div>
            <button class="btn btn-primary w-100">Add Task</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Right: Task List -->
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Your Tasks</h5>
          <?php if (empty($tasks)): ?>
            <p class="text-muted">No tasks yet — add one from the left.</p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-striped align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($tasks as $t): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($t['title']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($t['description'])); ?></td>
                    <td><?php echo htmlspecialchars($t['created_at']); ?></td>
                    <td>
                      <!-- Edit opens edit_task.php?id=... -->
                      <a class="btn btn-sm btn-outline-primary" href="edit_task.php?id=<?php echo $t['id']; ?>">Edit</a>
                      <!-- Delete: confirm then call delete_task.php?id=... -->
                      <a class="btn btn-sm btn-outline-danger" href="delete_task.php?id=<?php echo $t['id']; ?>" onclick="return confirm('Delete this task?');">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
