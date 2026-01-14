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
    <section class="haut">
        <h1>Nos bonnets spécialement créés pour <i>VOUS</i></h1>
    </section>

    <?php
    //affiche tous les articles sans ordres particuliers
    include 'includes/config.php';
    $connexion = obtenirConnexion();
    $sql = "SELECT * FROM article";
    $resultat = $connexion->query($sql);
    if ($resultat->num_rows > 0) {
        while ($row = $resultat->fetch_assoc()) {
            include("includes/affichage_article.php");
        }
    } else {
        echo "Aucun article trouvé.";
    }
    echo "<br/>";



    $connexion->close();

    include("includes/footer.php");
    ?>