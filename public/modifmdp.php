<?php
   session_start();
   include("includes/header.php");
   include("includes/menu.php");
   if(!isset($_SESSION['prenom'])){

   header("Location: accueil.php");
   exit();
}else{  ?>

   <!DOCTYPE html>
   <html lang="fr">
   <head>
      <link rel="stylesheet" href="assets/css/modifmdp.css">
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title></title>
   </head>
   <body>
      <div id="main">
          <div id="fullcontent">
            <div id="top-content"><h1 id="head-page">MON MOT DE PASSE</h1></div>
            <div id="content">
                <form method="POST" action="actions/traitement-modifmdp.php">
                    <label for="nom" class="labtext">Votre mot de passe actuel :</label>
                        <br/><br/>
                    <input type="password" class="text-input" size="50" id="ancienmdp" name="ancienmdp">
                        <br/>
                    <label for="prenom" class="labtext">Votre nouveau mot de passe :</label>
                        <br/><br/>
                    <input type="password" class="text-input" size="50" id="nouveaumdp" name="nouveaumdp">
                        <br/>
                    <label for="email" class="labtext">Confirmer votre nouveau mot de passe :</label>
                        <br/><br/>
                    <input type="password" class="text-input" size="50" id="confirmmdp" name="confirmmdp">
                    <br/>
                    <input type="submit" name="sauvegarde" value="Sauvegarder" id="bouton">
                    <br/>
                    <a href="infouser.php" id="lienuser">Modifier mes informations</a>
            </form>
            <p style="color: red; font-family : sans-serif; font-size: 11px"><?php if(isset($_GET['error'])){echo $_GET['error'];}?></p>

        </div>
    </div>
   

      </div>
   </body>
   </html>












<?php } include("includes/footer.php");
?>