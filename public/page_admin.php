<?php
	session_start();
	include 'includes/header.php';

	if (!isset($_SESSION['accounttype'])) {
		header("Location: accueil.php");
	} else {  
		if($_SESSION['accounttype']!=='a'){
			header("Location: accueil.php");
   			exit();
   		}else{
?>

   <!DOCTYPE html>
   <html lang="fr">

   <head>
      <link rel="stylesheet" href="assets/css/page_admin-style.css">
      <link rel="stylesheet" href="assets/css/admin-base.css">
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Profil</title>
   </head>

   <body class="page-admin admin-surface">
   	<p class="admin-title">Bonneton</p>
      <div id="main">
         <div id="content">
            <a class="contentlien contentlien-simple" href="accueil.php">
               <label class="text-content">Retour a l'accueil</label>
            </a>
            <a class="contentlien" href="infouser.php">
               <img class="image" src="img/lock.png">
               <label class="text-content">Modifier les identifiants admins</label>
            </a>
            <a class="contentlien" href="commentairemanagement.php">
               <img class="image" src="img/commentaire.png">
               <label class="text-content">Voir les commentaires des utilisateurs</label>
            </a>
            <a class="contentlien" href="accountmanagement.php">
            	<img class="image" src="img/compte.png">
            	<label class="text-content">Gérer les comptes des utilisateurs</label>
            </a>
            <a class="contentlien" href="admin-commandes.php">
               <img class="image" src="img/achats.png">
               <label class="text-content">Gerer les commandes</label>
            </a>
            <a class="contentlien" href="admin-categories.php">
               <img class="image" src="img/commentaire.png">
               <label class="text-content">Gerer les categories</label>
            </a>
            <form method="POST" action="actions/deconnexion.php">
               <?php echo csrf_field(); ?>
               <input type="submit" name="deco" value="Quitter le profil admin" id="deco" class="admin-button">
            </form>
         </div>
      </div>
   </body>

   </html>
<?php }
	}
?>
