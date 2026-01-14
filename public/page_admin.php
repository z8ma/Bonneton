<?php
	session_start();

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
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Profil</title>
   </head>

   <body>
   	<p>Bonneton</p>
      <div id="main">
         <div id="content">
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
            <form method="POST" action="actions/deconnexion.php">
               <input type="submit" name="deco" value="Quitter le profil admin" id="deco">
            </form>
         </div>
      </div>
   </body>

   </html>
<?php }
	}
?>