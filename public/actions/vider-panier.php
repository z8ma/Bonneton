<?php
session_start();
include '../includes/config.php';
$connexion = obtenirConnexion();
$user_id = $_SESSION['id'];
$requete_supprimer_panier = "DELETE FROM panier WHERE user_id = $user_id";
mysqli_query($connexion, $requete_supprimer_panier);


header("Location: ../panier.php");
exit;
