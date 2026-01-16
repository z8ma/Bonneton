<?php
session_start();
include '../includes/config.php';
if (!isset($_SESSION['accounttype']) || $_SESSION['accounttype'] !== 'a') {
    header("Location: ../accueil.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: ../admin-commandes.php");
    exit();
}

$order_id = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? 'pending';
$allowed = ['pending','paid','shipped','cancelled','refunded'];
if ($order_id <= 0 || !in_array($status, $allowed, true)) {
    header("Location: ../admin-commandes.php");
    exit();
}

$connexion = obtenirConnexion();
$stmt = $connexion->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();
$stmt->close();
$connexion->close();

header("Location: ../admin-commandes.php");
exit();
