<?php
session_start();
include '../includes/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: ../accueil.php");
    exit;
}
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$article_id = (int) ($_POST['article_id'] ?? 0);
if ($article_id <= 0) {
    header("Location: ../accueil.php");
    exit;
}

$redirect = $_POST['redirect'] ?? '';
if ($redirect === '' || $redirect[0] !== '/') {
    $redirect = '/accueil.php';
}

$connexion = obtenirConnexion();
$user_id = $_SESSION['id'];

$stmt_check = $connexion->prepare("SELECT id FROM favorites WHERE user_id = ? AND article_id = ? LIMIT 1");
$stmt_check->bind_param("ii", $user_id, $article_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$existing = $result_check->fetch_assoc();
$stmt_check->close();

if ($existing) {
    $stmt_delete = $connexion->prepare("DELETE FROM favorites WHERE user_id = ? AND article_id = ?");
    $stmt_delete->bind_param("ii", $user_id, $article_id);
    $stmt_delete->execute();
    $stmt_delete->close();
} else {
    $stmt_insert = $connexion->prepare("INSERT INTO favorites (user_id, article_id) VALUES (?, ?)");
    $stmt_insert->bind_param("ii", $user_id, $article_id);
    $stmt_insert->execute();
    $stmt_insert->close();
}

$connexion->close();

header("Location: .." . $redirect);
exit;
