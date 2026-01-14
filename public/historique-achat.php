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
    $historique = "SELECT a.id, a.article_name, a.prix, h.date_achat, a.img FROM historique_achat h JOIN article a ON h.article_id = a.id WHERE h.user_id=$user_id ORDER BY h.date_achat DESC";
    $aff_hist = mysqli_query($connexion, $historique);
    if ($aff_hist->num_rows > 0) {

        while ($row = $aff_hist->fetch_assoc()) {

            include("includes/affichage_article.php");
        }
    } else {
        echo "Oops... il semblerait que vous n'ayez encore rien acheté";
    }



    include("includes/footer.php");
