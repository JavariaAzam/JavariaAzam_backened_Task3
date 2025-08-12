<?php
/*
 * db.php
 * Central database connection file. Include this file in other PHP files.
 *
 * Very simple and commented for beginners:
 *  - We create a connection to the MySQL server using mysqli.
 *  - If connection fails, we stop the script and show a friendly message.
 */

// Database credentials
$DB_HOST = 'localhost';   // Hostname (usually 'localhost' on your machine)
$DB_USER = 'root';        // MySQL username (default for many local installs)
$DB_PASS = '';            // MySQL password (empty for many local installs)
$DB_NAME = 'student_portal'; // Database name we created in tasks.sql

// Create a connection object (mysqli)
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check if the connection worked
if ($mysqli->connect_errno) {
    // If connection failed, log detailed error to server log (not shown to user)
    error_log("MySQL connection error: " . $mysqli->connect_error);
    // Show friendly message to user
    die("Database connection failed. Please try again later.");
}

// Set a good default character set (supports emojis and many languages)
$mysqli->set_charset('utf8mb4');
?>
