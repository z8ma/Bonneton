<?php
   session_start();
   include("includes/header.php");
   include("includes/menu.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<link rel="stylesheet" href="assets/css/login-style.css">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Connexion</title>

</head>
<body>

	<div id="main">
		<div id="fullcontent">
			<div id="top-content">
				<div id="top-content-gauche"><a href="register.php" id="head-lien">CRÉER UN COMPTE</a></div>
				<div id="top-content-droite"><h1 id="head-page">CONNEXION</h1></div>
			</div>

			<h1 id="titre-h1">S'Identifier</h1>

			<div id="content">

				<form method="POST" action="actions/traitement_connexion.php">
					<div id="text-content">
						<label class="labtext">E-mail :</label>
						<br/><br/>
						<input type="text" size="20" class="text-input" id="email" name="email" pattern=".+@.+\..+" size="30" placeholder="Votre E-mail ...">
						<br/>

						<label class="labtext">Mot De Passe :</label>
						<br/><br/>
						<input type="password" size="20" class="text-input" id="motdepasse" name="motdepasse" placeholder="Votre mot de passe ...">
						<br/><br/><br/>
						<input type="submit" id="bouton-connexion" value="connexion" name="connexion">
						<br/>
						<p style="color: red; font-family : sans-serif; font-size: 11px"><?php if(isset($_GET['error'])){echo $_GET['error'];}?></p>

					</div>

				</form>

			</div>
		</div>
	</div>



</body>
</html>
<?php
include("includes/footer.php");
?>