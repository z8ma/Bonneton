<?php
session_start();
include '../includes/config.php';

function verif($data)
{
    $data = trim($data);
    $data = stripcslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}



    $nom = $_POST['nom'] ?? '';
    $caract = $_POST['caract'] ?? '';
    $prix = $_POST['prix'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $img = $_POST['img'] ?? '';
    $category_id = (int) ($_POST['category_id'] ?? 0);
if (!verify_csrf()) {
    header("Location: ../ajoutArticle.php?error=*Requete invalide !");
    exit();
}

$nom = verif($nom);
$caract = verif($caract);
$prix = verif($prix);
$stock = isset($stock) ? verif($stock) : 0;
$img = verif($img);

if (empty($nom) || empty($prix)) {
    header("Location: ../ajoutArticle.php?error=*Un des champs n'a pas été renseigné !");
    exit();
}
$connexion = obtenirConnexion();
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];

    $price_cents = (int) round(((float) $prix) * 100);
    $stock_value = (int) $stock;
    $status = $stock_value > 0 ? 'active' : 'out_of_stock';
    $currency = 'EUR';
    $stmt = $connexion->prepare("INSERT INTO article (user_id, article_name, caract, img, price_cents, currency, stock, status, mise_en_vente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssdiss", $user_id, $nom, $caract, $img, $price_cents, $currency, $stock_value, $status);
    $stmt->execute();
    $article_id = $connexion->insert_id;
    $stmt->close();

    if ($img !== '') {
        $stmt_img = $connexion->prepare("INSERT INTO article_images (article_id, url, position, is_primary) VALUES (?, ?, 0, 1)");
        $stmt_img->bind_param("is", $article_id, $img);
        $stmt_img->execute();
        $stmt_img->close();
    }

    if ($category_id > 0) {
        $stmt_cat = $connexion->prepare("INSERT INTO article_categories (article_id, category_id) VALUES (?, ?)");
        $stmt_cat->bind_param("ii", $article_id, $category_id);
        $stmt_cat->execute();
        $stmt_cat->close();
    }

    header("Location: ../vendeur.php");
    $connexion->close();
    exit();
}
