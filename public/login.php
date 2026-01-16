<?php
session_start();
include("includes/header.php");
?>
<title>Connexion</title>
<link rel="stylesheet" href="assets/css/login-style.css">
</head>
<body>
	<?php include("includes/menu.php"); ?>

	<?php
	$redirect = $_GET['redirect'] ?? '';
	if (!is_string($redirect) || $redirect === '' || $redirect[0] !== '/' || strpos($redirect, '://') !== false) {
		$redirect = '';
	}
	?>
	<div id="main">
		<div id="fullcontent">
			<div id="top-content">
				<div id="top-content-gauche"><a href="register.php" id="head-lien">CRÉER UN COMPTE</a></div>
				<div id="top-content-droite"><h1 id="head-page">CONNEXION</h1></div>
			</div>

			<h1 id="titre-h1">S'Identifier</h1>

			<div id="content">

				<form method="POST" action="actions/traitement_connexion.php">
					<?php echo csrf_field(); ?>
					<?php if (!empty($redirect)) { ?>
						<input type="hidden" name="redirect" value="<?php echo e($redirect); ?>">
					<?php } ?>
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
						<p style="color: red; font-family : sans-serif; font-size: 11px"><?php if(isset($_GET['error'])){echo e($_GET['error']);}?></p>

					</div>

				</form>

			</div>
		</div>
	</div>
<?php
include("includes/footer.php");
?>
