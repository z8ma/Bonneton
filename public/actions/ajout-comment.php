<?php
session_start();
include '../includes/config.php';
$connexion = obtenirConnexion();

if (isset($_POST['Envoyer'])) {
    if (isset($_SESSION['id'])) {
        $user_id = $_SESSION['id'];
        $commentaire = $_POST['commentaire'];
        $image = $_POST['img'];


        $sql = "INSERT INTO commentaires (user_id, article_id, contenu, date_commentaire, img) VALUES ('$user_id', '$article_id', '$commentaire', NOW(), '$image') ";
        if ($connexion->query($sql) === TRUE) {
            echo "<div class='alert alert-success'style=color: green;>Votre commentaire a été ajouté avec succès !</div>";
        } else {
            echo "Erreur lors de l'ajout du commentaire : " . $connexion->error;
        }
    } else {
        header("Location: ../login.php");
        exit;
    }
    $connexion->close();
}
