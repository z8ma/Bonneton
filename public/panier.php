<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/config.php';
$connexion = obtenirConnexion();

$user_id = $_SESSION['id'];


$stmt_panier = $connexion->prepare("SELECT panier.article_id, panier.qty, article.article_name, article.price_cents, article.currency, COALESCE(ai.url, article.img) AS image_url FROM panier INNER JOIN article ON panier.article_id = article.id LEFT JOIN article_images ai ON ai.article_id = article.id AND ai.is_primary = 1 WHERE panier.user_id = ?");
$stmt_panier->bind_param("i", $user_id);
$stmt_panier->execute();
$resultat_panier = $stmt_panier->get_result();


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
    <?php if (!empty($_GET['error'])) : ?>
        <p class="erreur"><?php echo e($_GET['error']); ?></p>
    <?php endif; ?>

    <div class="container">
        <div class="left-part">
            <?php if (mysqli_num_rows($resultat_panier) > 0) : ?>
                <?php while ($row = mysqli_fetch_assoc($resultat_panier)) :
                    echo "<div class='article'>";
                    echo "<div class='left-section'>";
                    echo "<br><br><h2>" . e($row["article_name"]) . "</h2><br>";
                    echo "<p>Prix : " . e(format_price($row["price_cents"], $row["currency"] ?? 'EUR')) . "</p>";
                    echo "<p>Quantite : " . e($row["qty"]) . "</p>";
                    echo "<form method='POST' action='actions/supprimer-article-panier.php' class='panier-item-action' onsubmit=\"return confirm('Supprimer cet article du panier ?');\">";
                    echo csrf_field();
                    echo "<input type='hidden' name='article_id' value='" . e($row["article_id"]) . "'>";
                    echo "<button type='submit' class='panier-item-remove'>Supprimer</button>";
                    echo "</form>";
                    echo "</div>";
                    echo "<div class='right-section'>";
                    echo "<img src='" . e($row["image_url"]) . "' alt='" . e($row["article_name"]) . "'>";
                    echo "</div>";
                    echo "</div>"; ?>
                    <?php

                    $nombre_total_articles += (int) $row['qty'];
                    $prix_total_commande += ((int) $row['price_cents']) * ((int) $row['qty']);
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
        <div class="right-part commande-card">
            <h2>Ma Commande</h2>
            <div class="command-details">
                <p>Nombre d'articles : <?php echo $nombre_total_articles; ?></p>
                <p>Prix total : <?php echo e(format_price($prix_total_commande, 'EUR')); ?></p>
            </div>
            <form method="POST" action="actions/valider_panier.php" class="panier-action">
                <?php echo csrf_field(); ?>
                <button type="submit" class="panier-button panier-button-primary">
                    Valider le panier
                </button>
            </form>
            <form method="POST" action="actions/vider-panier.php" class="panier-action panier-action-danger">
                <?php echo csrf_field(); ?>
                <button type="submit" class="panier-button panier-button-ghost">
                    Vider le panier
                </button>
            </form>
        </div>
    </div>
    </div>
    </div>
    <br />
    <br />
    <?php
    include("includes/footer.php");
    if ($stmt_panier) {
        try {
            $stmt_panier->close();
        } catch (Throwable $e) {
        }
    }
    ?>
