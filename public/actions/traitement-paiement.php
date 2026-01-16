<?php
session_start();
include '../includes/config.php';
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: ../accueil.php");
    exit();
}

$order_id = (int) ($_POST['order_id'] ?? 0);
$user_id = $_SESSION['id'];
if ($order_id <= 0) {
    header("Location: ../accueil.php");
    exit();
}

$connexion = obtenirConnexion();
$stmt = $connexion->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    $connexion->close();
    header("Location: ../accueil.php");
    exit();
}

$stmt_pay = $connexion->prepare("UPDATE payments SET status = 'paid' WHERE order_id = ?");
$stmt_pay->bind_param("i", $order_id);
$stmt_pay->execute();
$stmt_pay->close();

$stmt_order = $connexion->prepare("UPDATE orders SET status = 'paid', paid_at = NOW() WHERE id = ?");
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$stmt_order->close();

$stmt_delete = $connexion->prepare("DELETE FROM panier WHERE user_id = ?");
$stmt_delete->bind_param("i", $user_id);
$stmt_delete->execute();
$stmt_delete->close();

$connexion->close();

header("Location: ../confirmation.php");
exit();
