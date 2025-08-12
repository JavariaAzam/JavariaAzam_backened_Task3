<?php
// login.php - Log a user in using email and password.
// After login, we set session variables used by tasks pages.

session_start();
require 'db.php';

// If already logged in, go straight to tasks
if (isset($_SESSION['user_id'])) {
    header("Location: tasks.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = "Please enter email and password.";
    } else {
        // Select user row where email matches
        $sql = "SELECT id, name, password FROM users WHERE email = ? LIMIT 1";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $name, $hashed);
                $stmt->fetch();
                // Check password using password_verify
                if (password_verify($pass, $hashed)) {
                    // Password ok -> set session and redirect
                    session_regenerate_id(true); // security: new session id
                    $_SESSION['user_id'] = $id;
                    $_SESSION['name'] = $name;
                    header("Location: tasks.php");
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
            $stmt->close();
        } else {
            error_log("Login prepare failed: " . $mysqli->error);
            $error = "Server error. Try again later.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - Task Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title mb-3">Login</h3>

          <?php if (!empty($_GET['registered'])): ?>
            <div class="alert alert-success">Registration successful. Please login.</div>
          <?php endif; ?>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>

          <form method="post" novalidate>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input name="password" type="password" class="form-control" required>
            </div>
            <button class="btn btn-primary">Login</button>
            <a href="register.php" class="btn btn-link">Register</a>
          </form>

        </div>
      </div>
      <p class="text-muted small mt-2">If you forget password, recreate account for demo purposes.</p>
    </div>
  </div>
</div>
</body>
</html>
