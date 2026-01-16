<?php
session_start();
include '../includes/config.php';
$connexion = obtenirConnexion();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: ../panier.php");
    exit;
}
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}
$user_id = $_SESSION['id'];
$stmt_delete = $connexion->prepare("DELETE FROM panier WHERE user_id = ?");
$stmt_delete->bind_param("i", $user_id);
$stmt_delete->execute();
$stmt_delete->close();


header("Location: ../panier.php");
exit;
