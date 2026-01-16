<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
include("includes/header.php");
?>
<title>Votre Espace vendeur</title>
</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <section class="haut reveal">
        <h1>Vendez vos dernières <i>créations</i></h1>
        <div>
            <p><a href="ajoutArticle.php">Ajouter un article</a></p>
        </div>
    </section>
    <section class="bas reveal">
        <h2>Tous vos <i>Articles</i></h2>
    </section>
    <?php
    include 'includes/config.php';
    $connexion = obtenirConnexion();
    $user_id = $_SESSION['id'];
    $favorites_map = [];
    $stmt_fav = $connexion->prepare("SELECT article_id FROM favorites WHERE user_id = ?");
    $stmt_fav->bind_param("i", $user_id);
    $stmt_fav->execute();
    $result_fav = $stmt_fav->get_result();
    while ($fav = $result_fav->fetch_assoc()) {
        $favorites_map[(int) $fav['article_id']] = true;
    }
    $stmt_fav->close();

    $stmt_articles = $connexion->prepare("SELECT article.*, COALESCE(ai.url, article.img) AS image_url FROM article LEFT JOIN article_images ai ON ai.article_id = article.id AND ai.is_primary = 1 WHERE article.user_id = ?");
    $stmt_articles->bind_param("i", $user_id);
    $stmt_articles->execute();
    $resultat_mes_articles = $stmt_articles->get_result();

    if ($resultat_mes_articles->num_rows > 0) {

        $reveal_class = 'reveal';
        while ($row = $resultat_mes_articles->fetch_assoc()) {
            include("includes/affichage_article.php");
        }
        unset($reveal_class);
    } else {
        echo "<p class='empty-state'>Oops... il semblerait que vous n'ayez encore rien vendu</p>";
    }
    ?>



    <?php
    include("includes/footer.php");
    $stmt_articles->close();
    ?>
    </section>
