<?php
session_start();
include '../includes/config.php';
$connexion = obtenirConnexion();
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}
$user_id = $_SESSION['id'];
$stmt_select = $connexion->prepare("SELECT article_id FROM panier WHERE user_id = ?");
$stmt_select->bind_param("i", $user_id);
$stmt_select->execute();
$resultat_select_panier = $stmt_select->get_result();

if ($resultat_select_panier) {

    $stmt_update = $connexion->prepare("UPDATE article SET nb_vente = nb_vente + 1 WHERE id = ?");
    $stmt_insert = $connexion->prepare("INSERT INTO historique_achat (user_id, article_id, date_achat) VALUES (?, ?, NOW())");
    while ($row = $resultat_select_panier->fetch_assoc()) {
        $article_id = $row['article_id'];

        $stmt_update->bind_param("i", $article_id);
        $stmt_update->execute();

        $stmt_insert->bind_param("ii", $user_id, $article_id);
        $stmt_insert->execute();
    }
    $stmt_update->close();
    $stmt_insert->close();
} else {
    echo "Erreur lors de la sélection des articles du panier : " . mysqli_error($connexion);
}

$stmt_delete = $connexion->prepare("DELETE FROM panier WHERE user_id = ?");
$stmt_delete->bind_param("i", $user_id);
$stmt_delete->execute();
$stmt_delete->close();


header("Location: ../confirmation.php");
exit;
