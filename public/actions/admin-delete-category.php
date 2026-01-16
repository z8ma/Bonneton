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

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: ../admin-categories.php");
    exit();
}

$connexion = obtenirConnexion();
$stmt = $connexion->prepare("DELETE FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$connexion->close();

header("Location: ../admin-categories.php");
exit();
