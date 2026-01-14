<?php
session_start();
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();
?>
<title>Nouveaux Articles</title>
</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <section class="haut">
        <h1>Nos nouvelles Créations pour <i>VOUS</i></h1>
    </section>

    <?php
    $date_deux_semaines_avant = date("Y-m-d", strtotime("-2 weeks"));

    $requete_nouveaux_articles = "SELECT * FROM article WHERE mise_en_vente  >= '$date_deux_semaines_avant'ORDER BY id DESC ";
    $resultat_nouveaux_articles = $connexion->query($requete_nouveaux_articles);

    if ($resultat_nouveaux_articles->num_rows > 0) {

        while ($row = $resultat_nouveaux_articles->fetch_assoc()) {
            include("includes/affichage_article.php");
        }
    } else {
        echo "Aucun article trouvé.";
    }

    include("includes/footer.php");
    ?>