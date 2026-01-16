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

$type = $_POST['type'] ?? 'shipping';
$line1 = trim($_POST['line1'] ?? '');
$line2 = trim($_POST['line2'] ?? '');
$city = trim($_POST['city'] ?? '');
$zip = trim($_POST['zip'] ?? '');
$country = trim($_POST['country'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($line1 === '' || $city === '' || $zip === '' || $country === '') {
    header("Location: ../adresse.php?error=*Champs obligatoires manquants !");
    exit();
}

$user_id = $_SESSION['id'];
$connexion = obtenirConnexion();
$stmt = $connexion->prepare("INSERT INTO addresses (user_id, type, line1, line2, city, zip, country, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssss", $user_id, $type, $line1, $line2, $city, $zip, $country, $phone);
$stmt->execute();
$stmt->close();
$connexion->close();

header("Location: ../adresse.php");
exit();
