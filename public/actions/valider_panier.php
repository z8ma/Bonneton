<?php
session_start();
include '../includes/config.php';
$connexion = obtenirConnexion();
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}
$user_id = $_SESSION['id'];
$requete_select_panier = "SELECT article_id FROM panier WHERE user_id = $user_id";
$resultat_select_panier = mysqli_query($connexion, $requete_select_panier);

if ($resultat_select_panier) {

    while ($row = mysqli_fetch_assoc($resultat_select_panier)) {
        $article_id = $row['article_id'];

        $requete_update_ventes = "UPDATE article SET nb_vente = nb_vente + 1 WHERE id = $article_id";
        mysqli_query($connexion, $requete_update_ventes);

        $requete_insert_achat = "INSERT INTO historique_achat (user_id, article_id, date_achat) VALUES ($user_id, $article_id, NOW())";
        mysqli_query($connexion, $requete_insert_achat);
    }
} else {
    echo "Erreur lors de la sélection des articles du panier : " . mysqli_error($connexion);
}

$requete_supprimer_panier = "DELETE FROM panier WHERE user_id = $user_id";
mysqli_query($connexion, $requete_supprimer_panier);


header("Location: ../confirmation.php");
exit;
