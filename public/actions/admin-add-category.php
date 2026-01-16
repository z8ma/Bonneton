<?php
session_start();
include '../includes/config.php';
if (!isset($_SESSION['accounttype']) || $_SESSION['accounttype'] !== 'a') {
    header("Location: ../accueil.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: ../admin-categories.php");
    exit();
}

$name = trim($_POST['name'] ?? '');
if ($name === '') {
    header("Location: ../admin-categories.php");
    exit();
}

$connexion = obtenirConnexion();
$stmt = $connexion->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->close();
$connexion->close();

header("Location: ../admin-categories.php");
exit();
