<link rel="stylesheet" href="assets/css/menu-style.css">
<nav>
    <ul>
        <?php
        //barre de recherche

        if (
            stripos($_SERVER['REQUEST_URI'], 'accueil.php') ||
            stripos($_SERVER['REQUEST_URI'], 'article.php') ||
            stripos($_SERVER['REQUEST_URI'], 'pageNews.php') ||
            stripos($_SERVER['REQUEST_URI'], 'traitement-searchbar.php')
        ) {
        ?>

            <div id="menu-content">
                <li id="li-bar">
                    <form method="POST" action="traitement-searchbar.php">
                        <div id="content-searchbar">
                            <input type="text" id="searchbar" placeholder="Recherche ..." name="req" />
                        </div>
                    </form>
                </li>
            <?php } ?>

            <li class="normal"><a href="article.php">Boutique</a></li>
            <li class="normal"><a href="pageNews.php">Nouveautés</a></li>
            <li class="normal"><a href="pageApropos.php">Qui sommes-nous ?</a></li>
            <?php if (!isset($_SESSION['id'])) { ?>
                <li class="normal"><a href="login.php">Connexion</a></li>
            <?php } else { ?>
                <li class="diffconnecte"><a href="profil.php"><?php echo e($_SESSION['prenom']); ?></a></li>
            <?php } ?>
            <li class="diff"><a href="panier.php">Panier</a></li>
            </div>
    </ul>
    <p><a href="accueil.php" class="logo">Bonneton</a></p>
</nav>
<br>
