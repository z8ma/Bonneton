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
	include 'includes/config.php';
	$bdd = obtenirConnexion();

	if (isset($_POST['req'])) {
		$search = trim($_POST['req']);
		$requete = $bdd->prepare("SELECT * FROM article WHERE article_name LIKE ?");
		$pattern = '%' . $search . '%';
		$requete->bind_param("s", $pattern);
		//On a essayer une version où la barre de recherche prend l'info la plus large avec $resultat sous forme de tableau
		/*$resultat = explode(' ', $search);
		$i = 0;
		while (isset($resultat[$i])){
			$requete = "SELECT * FROM article WHERE article_name LIKE '%$resultat[$i]%'";*/

		$requete->execute();
		$run_requete = $requete->get_result();
		if ($run_requete->num_rows > 0) {
			while ($row = $run_requete->fetch_assoc()) {
				include("includes/affichage_article.php");
			}
			// $i++;
		}
	} else {
		echo "<p class=erreur>Oops ... Il semblerait que cet article n'existe pas.</p>";
	}

	include("includes/footer.php");
	?>
