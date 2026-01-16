<?php
session_start();
include("includes/header.php");
?>
<title>Liste des Articles</title>
</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <section class="haut reveal">
        <h1>Nos bonnets spécialement créés pour <i>VOUS</i></h1>
    </section>

    <?php
    //affiche tous les articles sans ordres particuliers
include 'includes/config.php';
$connexion = obtenirConnexion();
$category_id = isset($_GET['category']) ? (int) $_GET['category'] : 0;
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
$categories = [];
    $stmt_categories = $connexion->prepare("SELECT id, name FROM categories ORDER BY name ASC");
    $stmt_categories->execute();
    $result_categories = $stmt_categories->get_result();
    while ($cat = $result_categories->fetch_assoc()) {
        $categories[] = $cat;
    }
    $stmt_categories->close();

    if ($category_id > 0) {
        $stmt = $connexion->prepare("SELECT article.*, COALESCE(ai.url, article.img) AS image_url FROM article LEFT JOIN article_images ai ON ai.article_id = article.id AND ai.is_primary = 1 JOIN article_categories ac ON ac.article_id = article.id WHERE article.status = 'active' AND ac.category_id = ?");
        $stmt->bind_param("i", $category_id);
    } else {
        $stmt = $connexion->prepare("SELECT article.*, COALESCE(ai.url, article.img) AS image_url FROM article LEFT JOIN article_images ai ON ai.article_id = article.id AND ai.is_primary = 1 WHERE article.status = 'active'");
    }
    $stmt->execute();
    $resultat = $stmt->get_result();
    if (!empty($categories)) {
        echo "<div class='categories reveal'>";
        echo "<a href='article.php'>Toutes</a> ";
        foreach ($categories as $cat) {
            echo "<a href='article.php?category=" . e($cat['id']) . "'>" . e($cat['name']) . "</a> ";
        }
        echo "</div>";
    }

    if ($resultat->num_rows > 0) {
        $reveal_class = 'reveal';
        while ($row = $resultat->fetch_assoc()) {
            include("includes/affichage_article.php");
        }
        unset($reveal_class);
    } else {
        echo "<p class='empty-state'>Aucun article trouvé.</p>";
    }
    echo "<br/>";



    $stmt->close();
    $connexion->close();

    include("includes/footer.php");
    ?>
