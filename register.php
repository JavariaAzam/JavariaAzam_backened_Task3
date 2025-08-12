<?php
// register.php
// Simple registration page that creates a new user in 'users' table.
// Passwords are hashed before saving (so we don't store plain text).

session_start();
require 'db.php';

// If user is already logged in, send them to tasks page
if (isset($_SESSION['user_id'])) {
    header("Location: tasks.php");
    exit;
}

// When the form is submitted, request method will be POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and clean input values (trim removes extra spaces)
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    // Basic validation: required fields
    if ($name === '' || $email === '' || $pass === '') {
        $error = "Please fill in all required fields.";
    } else {
        // Hash password securely using PHP's password_hash
        $hashed = password_hash($pass, PASSWORD_DEFAULT);

        // Use prepared statement to avoid SQL injection
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('sss', $name, $email, $hashed);
            $ok = $stmt->execute();
            if ($ok) {
                // Registration successful, redirect to login
                header("Location: login.php?registered=1");
                exit;
            } else {
                // If email already exists, MySQL UNIQUE constraint triggers error
                if ($mysqli->errno === 1062) {
                    $error = "This email is already registered. Try logging in.";
                } else {
                    error_log("Register execute error: " . $stmt->error);
                    $error = "Registration failed. Please try again.";
                }
            }
            $stmt->close();
        } else {
            error_log("Register prepare failed: " . $mysqli->error);
            $error = "Server error. Please try later.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register - Task Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title mb-3">Create an account</h3>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>

          <form method="post" novalidate>
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input name="name" class="form-control" required value="<?php echo htmlspecialchars($name ?? ''); ?>">
              <div class="form-text">Use your real name so you recognise account later.</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input name="password" type="password" class="form-control" required>
              <div class="form-text">A strong password keeps your account safe.</div>
            </div>
            <button class="btn btn-primary">Register</button>
            <a href="login.php" class="btn btn-link">Already have an account? Login</a>
          </form>

        </div>
      </div>
      <p class="text-muted small mt-2">This is a demo project — never use a production password here.</p>
    </div>
  </div>
</div>
</body>
</html>
