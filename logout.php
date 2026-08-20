<?php
// ============================================================
// logout.php
// Destroys the session and sends the user back to the login page.
// ============================================================
require_once 'config/auth.php';

session_destroy();

header("Location: login.php");
exit;
