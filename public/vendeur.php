<?php
session_start();
include("includes/header.php");
?>
<title>Votre Espace vendeur</title>
</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <section class="haut">
        <h1>Vendez vos dernières <i>créations</i></h1>
        <div>
            <p><a href="ajoutArticle.php">Ajouter un article</a></p>
        </div>
    </section>
    <section class="bas">
        <h2>Tous vos <i>Articles</i></h2>
    </section>
    <?php
    include 'includes/config.php';
    $connexion = obtenirConnexion();
    $user_id = $_SESSION['id'];

    $requete_mes_articles = "SELECT * FROM article WHERE user_id = '$user_id'";
    $resultat_mes_articles = $connexion->query($requete_mes_articles);

    if ($resultat_mes_articles->num_rows > 0) {

        while ($row = $resultat_mes_articles->fetch_assoc()) {
            include("includes/affichage_article.php");
        }
    } else {
        echo "Oops... il semblerait que vous n'ayez encore rien vendu";
    }
    ?>



    <?php
    include("includes/footer.php");
    ?>
    </section>
