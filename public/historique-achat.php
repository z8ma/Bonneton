<?php
session_start();
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();
?>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <section class="haut">
        <h1>Vos dernières <i>commandes</i></h1>
    </section>
    <?php
    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit;
    }
    $user_id = $_SESSION['id'];
    $stmt_hist = $connexion->prepare("SELECT a.id, a.article_name, a.prix, h.date_achat, a.img FROM historique_achat h JOIN article a ON h.article_id = a.id WHERE h.user_id = ? ORDER BY h.date_achat DESC");
    $stmt_hist->bind_param("i", $user_id);
    $stmt_hist->execute();
    $aff_hist = $stmt_hist->get_result();
    if ($aff_hist->num_rows > 0) {

        while ($row = $aff_hist->fetch_assoc()) {

            include("includes/affichage_article.php");
        }
    } else {
        echo "Oops... il semblerait que vous n'ayez encore rien acheté";
    }



    include("includes/footer.php");
    $stmt_hist->close();
