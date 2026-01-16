<?php
session_start();
if (!isset($_SESSION['id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/favoris.php');
    header("Location: login.php?redirect=" . $redirect);
    exit();
}
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();
?>
<title>Mes favoris</title>
</head>

<body>
    <?php include("includes/menu.php"); ?>
    <section class="haut reveal">
        <h1>Mes <i>favoris</i></h1>
    </section>
    <div class="container">
        <?php
        $user_id = $_SESSION['id'];
        $stmt_fav = $connexion->prepare("SELECT a.*, COALESCE(ai.url, a.img) AS image_url FROM favorites f JOIN article a ON a.id = f.article_id LEFT JOIN article_images ai ON ai.article_id = a.id AND ai.is_primary = 1 WHERE f.user_id = ? ORDER BY f.created_at DESC");
        $stmt_fav->bind_param("i", $user_id);
        $stmt_fav->execute();
        $result_fav = $stmt_fav->get_result();

        $favorites_map = [];
        if ($result_fav->num_rows > 0) {
            $reveal_class = 'reveal';
            while ($row = $result_fav->fetch_assoc()) {
                $favorites_map[(int) $row['id']] = true;
                include("includes/affichage_article.php");
            }
            unset($reveal_class);
        } else {
            echo "<p class='empty-state'>Aucun favori pour le moment.</p>";
        }

        $stmt_fav->close();
        $connexion->close();
        ?>
    </div>
    <?php include("includes/footer.php"); ?>
