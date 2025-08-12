<?php
// logout.php - clears the session and redirects to login
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
