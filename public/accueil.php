<?php
session_start();
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();

//affiche les articles selon le nombres de vente

$requete_selection = "SELECT * FROM article ORDER BY nb_vente DESC LIMIT 3";
$resultat_selection = mysqli_query($connexion, $requete_selection);
?>
<title>Bienvenue sur Bonneton</title>
</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <br>
    <section class="haut">
        <h1>Des <i>créateurs</i>, <i>designers</i>, et <i>vendeurs</i> de bonnets <i>100% français</i>.</h1>
    </section>
    <div class="image-container">
        <img src="img/photo1.jpeg" id=img1>
        <img src="img/photo2.jpg" id=img2>
        <img src="img/photo3.jpeg" id=img3>
    </div>
    <section class="bas">
        <h2>Notre <i>sélection</i></h2>
    </section>
    <?php if (mysqli_num_rows($resultat_selection) > 0) {
        while ($row = $resultat_selection->fetch_assoc()) {
            include("includes/affichage_article.php");
        }
    } else {
        echo "Aucun article trouvé.";
    } ?>
    <br /><br />
    <section class="haut">
        <div>
            <p><a href="article.php">Charger plus</a></p>
        </div>
    </section>
    <br />
    <?php
    $connexion->close();
    include("includes/footer.php");
    ?>