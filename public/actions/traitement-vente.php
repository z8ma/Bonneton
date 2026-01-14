<?php
session_start();

function verif($data)
{
    $data = trim($data);
    $data = stripcslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}



if (isset($_POST['ajout'])) {
    extract($_POST);
}

$nom = verif($nom);
$caract = verif($caract);
$prix = verif($prix);
$img = verif($img);

if (empty($nom) || empty($prix)) {
    header("Location: ../ajoutArticle.php?error=*Un des champs n'a pas été renseigné !");
    exit();
}
include '../includes/config.php';
$connexion = obtenirConnexion();
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];

    $rqt = "INSERT INTO article (user_id,article_name,caract,img,prix,mise_en_vente)	
    VALUES ($user_id,'$nom','$caract','$img','$prix',NOW())";
    $resultat = $connexion->query($rqt);

    header("Location: ../vendeur.php");
    $connexion->close();
    exit();
}
