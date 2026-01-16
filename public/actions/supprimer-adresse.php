<?php
session_start();
include '../includes/config.php';
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}
if (!verify_csrf()) {
    header("Location: ../adresse.php?error=*Requete invalide !");
    exit();
}

$address_id = (int) ($_POST['address_id'] ?? 0);
if ($address_id <= 0) {
    header("Location: ../adresse.php?error=*Adresse invalide !");
    exit();
}

$user_id = $_SESSION['id'];
$connexion = obtenirConnexion();
$stmt = $connexion->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $address_id, $user_id);
$stmt->execute();
$stmt->close();
$connexion->close();

header("Location: ../adresse.php");
exit();
