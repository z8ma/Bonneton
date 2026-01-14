<?php
session_start();
include("includes/header.php");
include("includes/menu.php");
?>
<?php if (!isset($_SESSION['prenom'])) {

   header("Location: accueil.php");
   exit();
} else {  ?>

   <!DOCTYPE html>
   <html lang="fr">

   <head>
      <link rel="stylesheet" href="assets/css/profil-style.css">
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Profil</title>
   </head>

   <body>
      <div id="main">
         <div id="content">
            <?php if($_SESSION['accounttype'] === 's'){?>
               <a class="contentlien" href="vendeur.php">
                  <img class="image" src="img/colis.png">
                  <label class="text-content">Gérer vos ventes</label>
               </a>
            <?php } ?>
            <a class="contentlien" href="infouser.php">
               <img class="image" src="img/lock.png">
               <label class="text-content">Modifier vos informations personnelles</label>
            </a>
            <a class="contentlien" href="contactsupport.php">
               <img class="image" src="img/support.png">
               <label class="text-content">Contacter notre Service Client par téléphone ou e-mail</label>
            </a>
            <a class="contentlien" href="historique-achat.php">
               <img class="image" src="img/achats.png">
               <label class="text-content">Accéder à vos anciens achats</label>
            </a>
            <form method="POST" action="actions/deconnexion.php">
               <input type="submit" name="deco" value="Deconnexion" id="deco">
            </form>
         </div>
      </div>
      <br /><br /><br />
   </body>

   </html>

<?php }
include("includes/footer.php");
?>