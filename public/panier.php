<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/config.php';
$connexion = obtenirConnexion();

$user_id = $_SESSION['id'];


$requete_panier = "SELECT article_id, article.article_name, article.prix, article.img FROM panier INNER JOIN article ON panier.article_id = article.id WHERE panier.user_id = $user_id";
$resultat_panier = mysqli_query($connexion, $requete_panier);


$nombre_total_articles = 0;
$prix_total_commande = 0;
include("includes/header.php");
?>
<title>Panier</title>
<link rel="stylesheet" href="assets/css/panier.css">
</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <section class="haut">
        <h1><i>Votre Panier</i></h1>
    </section>

    <div class="container">
        <div class="left-part">
            <?php if (mysqli_num_rows($resultat_panier) > 0) : ?>
                <?php while ($row = mysqli_fetch_assoc($resultat_panier)) :
                    echo "<div class='article'>";
                    echo "<div class='left-section'>";
                    echo "<br><br><h2>" . $row["article_name"] . "</h2><br>";
                    echo "<p>Prix : " . $row["prix"] . " €</p>";
                    echo "</div>";
                    echo "<div class='right-section'>";
                    echo "<img src='" . $row["img"] . "' alt='" . $row["article_name"] . "'>";
                    echo "</div>";
                    echo "</div>"; ?>
                    <?php

                    $nombre_total_articles++;
                    $prix_total_commande += $row['prix'];
                    ?>
                <?php endwhile; ?>
            <?php else : ?>
                <p>Votre panier est vide... mais pas pour longtemps<br>Découvrez nos articles</p>
                <section class="haut">
                    <div>
                        <p><a href="article.php">Nos Produits</a></p>
                    </div><br>
                </section>
            <?php endif; ?>
        </div>
        <div class="right-part">
            <h2>Ma Commande</h2>
            <div class="command-details">
                <p>Nombre d'articles : <?php echo $nombre_total_articles; ?></p>
                <p>Prix total : <?php echo $prix_total_commande; ?> €</p>
            </div>
            <a href="actions/valider_panier.php" class="valider-panier">
                <div class="validation-section">

                    <p>Valider le panier</p>
            </a>
        </div>
        <br>
        <a href="actions/vider-panier.php" class="valider-panier">
            <div class="poubelle">
                <p><i>Vider le panier</i></p>
            </div>

        </a>
    </div>
    </div>
    </div>
    <br />
    <br />
    <?php
    include("includes/footer.php");
    ?>
