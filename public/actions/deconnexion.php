<?php
session_start();
include '../includes/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: ../accueil.php");
    exit();
}
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();
header("Location: ../accueil.php");
exit();
?>
