<?php
session_start();
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();
$favorites_map = [];
if (isset($_SESSION['id'])) {
    $stmt_fav = $connexion->prepare("SELECT article_id FROM favorites WHERE user_id = ?");
    $stmt_fav->bind_param("i", $_SESSION['id']);
    $stmt_fav->execute();
    $result_fav = $stmt_fav->get_result();
    while ($fav = $result_fav->fetch_assoc()) {
        $favorites_map[(int) $fav['article_id']] = true;
    }
    $stmt_fav->close();
}
?>
<title>Nouveaux Articles</title>
</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <section class="haut reveal">
        <h1>Nos nouvelles Créations pour <i>VOUS</i></h1>
    </section>

    <?php
    $date_deux_semaines_avant = date("Y-m-d", strtotime("-2 weeks"));

    $stmt_news = $connexion->prepare("SELECT article.*, COALESCE(ai.url, article.img) AS image_url FROM article LEFT JOIN article_images ai ON ai.article_id = article.id AND ai.is_primary = 1 WHERE article.status = 'active' AND article.mise_en_vente >= ? ORDER BY article.id DESC");
    $stmt_news->bind_param("s", $date_deux_semaines_avant);
    $stmt_news->execute();
    $resultat_nouveaux_articles = $stmt_news->get_result();

    if ($resultat_nouveaux_articles->num_rows > 0) {

        $reveal_class = 'reveal';
        while ($row = $resultat_nouveaux_articles->fetch_assoc()) {
            include("includes/affichage_article.php");
        }
        unset($reveal_class);
    } else {
        echo "<p class='empty-state'>Aucun article trouvé.</p>";
    }

    include("includes/footer.php");
    $stmt_news->close();
    ?>
