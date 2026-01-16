<?php
session_start();
include("includes/header.php");
include("includes/menu.php");
?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<link rel="stylesheet" href="assets/css/register-style.css">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Inscription</title>

</head>

<body>

	<div id="main">
		<div id="fullcontent">
			<div id="top-content">
				<div id="top-content-gauche">
					<h1 id="head-page">CRÉER UN COMPTE</h1>
				</div>
				<div id="top-content-droite"><a href="login.php" id="head-lien">CONNEXION</a></div>
			</div>

			<div id="content">

				<form method="POST" action="actions/traitement_inscription.php">
					<?php echo csrf_field(); ?>
					<div id="text-content">
						<label for="titre" class="labtext">Titre</label><br />
						<input type="radio" class="titre" name="titre" value="m">M.
						<input type="radio" class="titre" name="titre" value="mme">Mme
						<input type="radio" class="titre" name="titre" value="mlle">Mlle
						<input type="radio" class="titre" name="titre" value="o">Autre
						<br /><br />
						<label for="nom" class="labtext">Nom :</label>
						<br /><br />
						<input type="text" class="text-input" size="50" id="nom" name="nom" placeholder="Votre nom ...">
						<br />

						<label for="prenom" class="labtext">Prénom :</label>
						<br /><br />
						<input type="text" class="text-input" size="50" id="prenom" name="prenom" placeholder="Votre prenom ...">
						<br />

						<label for="email" class="labtext">E-mail :</label>
						<br /><br />
						<input type="email" class="text-input" size="50" id="email" name="email" pattern=".+@.+\..+" size="30" placeholder="Votre e-mail ...">
						<br />

						<label for="motdepasse" class="labtext">Mot De Passe :</label>
						<br /><br />
						<input type="password" class="text-input" size="20" id="motdepasse" name="motdepasse" placeholder="Votre mot de passe ...">
						<br />

						<label for="dateden" class="labtext">Date de naissance :</label>
						<br /><br />
						<input type="Date" id="dateden" name="dateden">
						<br /><br />
						<input type="radio" class="accounttype" name="accounttype" value="s"><label for="accounttype" class="labtext">Profil Vendeur</label>
						<br />
						<input type="radio" class="accounttype" name="accounttype" value="b"><label for="accounttype" class="labtext">Profil Acheteur</label>
						<br /><br />
						<input type="checkbox" id="notif" name="notif">
						<label for="notif" id="cochetexte">En cochant ici, recevez les nouveautés, offres et dernières tendances par e-mail. Sélectionnez les contenus qui vous intéressent ou désabonnez-vous à tout moment.</label>
						<br /><br />
						<input type="submit" id="bouton-enregistrer" value="Enregistrer" name="inscription">


						<p id="politique-texte">En vous enregistrant pour créer un compte, vous acceptez nos modalités d’utlisation. Veuillez lire notre <a style="text-decoration: none;" href="politique.php">politique de confidentialité</a>.</p>

						<p style="color: red; font-family : sans-serif; font-size: 11px"><?php if (isset($_GET['error'])) {
																								echo e($_GET['error']);
																							} ?></p>
						<br />
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
