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

<body>
    <?php
    include("includes/menu.php");
    ?>
    <section class="haut reveal">
        <h1>Vos dernières <i>commandes</i></h1>
    </section>
    <?php
    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit;
    }
    $user_id = $_SESSION['id'];
    $stmt_hist = $connexion->prepare("SELECT a.id, a.article_name, a.price_cents, a.currency, COALESCE(ai.url, a.img) AS image_url, o.created_at FROM orders o JOIN order_items oi ON oi.order_id = o.id JOIN article a ON a.id = oi.article_id LEFT JOIN article_images ai ON ai.article_id = a.id AND ai.is_primary = 1 WHERE o.user_id = ? ORDER BY o.created_at DESC");
    $stmt_hist->bind_param("i", $user_id);
    $stmt_hist->execute();
    $aff_hist = $stmt_hist->get_result();
    if ($aff_hist->num_rows > 0) {
        $reveal_class = 'reveal';
        while ($row = $aff_hist->fetch_assoc()) {
            include("includes/affichage_article.php");
        }
        unset($reveal_class);
    } else {
        echo "<p class='empty-state'>Oops... il semblerait que vous n'ayez encore rien acheté</p>";
    }



    include("includes/footer.php");
    $stmt_hist->close();
