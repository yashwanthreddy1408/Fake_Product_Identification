<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}
session_unset();
session_destroy();
header("Location: signin.php?logout=success");
exit();
