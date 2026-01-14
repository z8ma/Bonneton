<?php
session_start();



include("includes/header.php");
?>
<title>Votre Recherche</title>
<style>
	p.erreur {
		text-align: center;
		font-weight: 500;
		font-size: large;
		font-style: oblique;
	}
</style>
</head>

<body>
	<?php
	include("includes/menu.php");
	?>
	<section class="haut">
		<h1>Votre <i>Recherche :</i></h1>
	</section>
	<?php
	$bdd = new mysqli('localhost', 'root', '', 'site');

	if ($bdd->connect_error) {
		die("Connexion échouée: " . $bdd->connect_error);
	}

	if (isset($_POST['req'])) {
		$search = mysqli_real_escape_string($bdd, htmlspecialchars($_POST['req']));
		$requete = "SELECT * FROM article WHERE article_name LIKE '%$search%'";
		//On a essayer une version où la barre de recherche prend l'info la plus large avec $resultat sous forme de tableau
		/*$resultat = explode(' ', $search);
		$i = 0;
		while (isset($resultat[$i])){
			$requete = "SELECT * FROM article WHERE article_name LIKE '%$resultat[$i]%'";*/

		$run_requete = mysqli_query($bdd, $requete);
		if ($run_requete->num_rows > 0) {
			while ($row = mysqli_fetch_array($run_requete)) {
				include("includes/affichage_article.php");
			}
			// $i++;
		}
	} else {
		echo "<p class=erreur>Oops ... Il semblerait que cet article n'existe pas.</p>";
	}

	include("includes/footer.php");
	?>