<?php
// ============================================================
// auth.php
// Very simple login-check helper.
// We use PHP sessions to remember that a user is logged in.
// ============================================================

// Start the session only if one hasn't been started yet.
// (Without this check, calling session_start() twice causes a warning.)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Call this function at the top of any page that should be protected.
// If nobody is logged in, send them to the login page.
function require_login()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}
