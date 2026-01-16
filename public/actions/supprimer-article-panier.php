<?php
session_start();
include '../includes/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: ../panier.php");
    exit;
}
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$article_id = (int) ($_POST['article_id'] ?? 0);
if ($article_id <= 0) {
    header("Location: ../panier.php");
    exit;
}

$connexion = obtenirConnexion();
$user_id = $_SESSION['id'];
$stmt_delete = $connexion->prepare("DELETE FROM panier WHERE user_id = ? AND article_id = ?");
$stmt_delete->bind_param("ii", $user_id, $article_id);
$stmt_delete->execute();
$stmt_delete->close();
$connexion->close();

header("Location: ../panier.php");
exit;
