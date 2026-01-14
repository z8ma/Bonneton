<?php
session_start();
include '../includes/config.php';
$connexion = obtenirConnexion();
$user_id = $_SESSION['id'];
$stmt_delete = $connexion->prepare("DELETE FROM panier WHERE user_id = ?");
$stmt_delete->bind_param("i", $user_id);
$stmt_delete->execute();
$stmt_delete->close();


header("Location: ../panier.php");
exit;
