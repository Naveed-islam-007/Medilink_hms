<?php
// ============================================================
// db.php
// This file connects our PHP code to the MySQL database using PDO.
// Every other page includes this file first so it can use $pdo
// to run SQL queries.
// ============================================================

// --- Database settings (default XAMPP values) ---
$host    = "localhost";
$dbname  = "medilink";
$user    = "root";
$pass    = "";
$charset = "utf8mb4";

// Data Source Name: tells PDO which driver, host and database to use
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// Options: make PDO throw real errors and return rows as associative arrays
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Try to connect. If it fails (e.g. MySQL not running), stop with a clear message.
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed. Make sure MySQL is running in XAMPP and the 'medilink' database has been imported.");
}
