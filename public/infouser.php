<?php
session_start();
if (!isset($_SESSION['prenom'])) {
    header("Location: accueil.php");
    exit();
} else {
    include("includes/header.php");
?>
    <title>Infos personnelles</title>
    <link rel="stylesheet" href="assets/css/infouser-style.css">
    </head>

    <body>
        <?php include("includes/menu.php"); ?>
        <div id="main">
            <div id="fullcontent">
                <div id="top-content">
                    <h1 id="head-page">MES INFORMATIONS</h1>
                </div>
                <div id="content">
                    <form method="POST" action="actions/traitement-infouser.php">
                        <?php echo csrf_field(); ?>
                        <label for="nom" class="labtext">Votre Nom :</label>
                        <br /><br />
                        <input type="text" class="text-input" size="50" id="nom" name="nom" value="<?php echo e($_SESSION['nom']); ?>">
                        <br />
                        <label for="prenom" class="labtext">Votre Prénom :</label>
                        <br /><br />
                        <input type="text" class="text-input" size="50" id="prenom" name="prenom" value="<?php echo e($_SESSION['prenom']); ?>">
                        <br />
                        <label for="email" class="labtext">Votre E-mail :</label>
                        <br /><br />
                        <input type="email" class="text-input" size="50" id="email" name="email" pattern=".+@.+\..+" size="30" value="<?php echo e($_SESSION['email']); ?>">
                        <br />
                        <label for="dateden" class="labtext">Votre Date de naissance :</label>
                        <br /><br />
                        <input type="Date" id="dateden" name="dateden">
                        <br /><br />
                        <input type="submit" name="sauvegarde" value="Sauvegarder" id="bouton">

                        <a href="modifmdp.php" id="lienmdp">Modifier votre mot de passe </a>
                    </form>
                    <p style="color: red; font-family : sans-serif; font-size: 11px"><?php if (isset($_GET['error'])) {
                                                                                            echo e($_GET['error']);
                                                                                        } ?></p>

                </div>
            </div>
        </div>
    </body>

<?php
    include("includes/footer.php");
}
?>
