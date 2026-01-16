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
	<section class="haut reveal">
		<h1>Votre <i>Recherche :</i></h1>
	</section>
	<?php
	include 'includes/config.php';
	$bdd = obtenirConnexion();
	$favorites_map = [];
	if (isset($_SESSION['id'])) {
		$stmt_fav = $bdd->prepare("SELECT article_id FROM favorites WHERE user_id = ?");
		$stmt_fav->bind_param("i", $_SESSION['id']);
		$stmt_fav->execute();
		$result_fav = $stmt_fav->get_result();
		while ($fav = $result_fav->fetch_assoc()) {
			$favorites_map[(int) $fav['article_id']] = true;
		}
		$stmt_fav->close();
	}

	if (!verify_csrf()) {
		echo "<p class=erreur>Requete invalide.</p>";
		include("includes/footer.php");
		exit;
	}
	if (isset($_POST['req'])) {
		$search = trim($_POST['req']);
		$requete = $bdd->prepare("SELECT article.*, COALESCE(ai.url, article.img) AS image_url FROM article LEFT JOIN article_images ai ON ai.article_id = article.id AND ai.is_primary = 1 WHERE article.status = 'active' AND article.article_name LIKE ?");
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
			$reveal_class = 'reveal';
			while ($row = $run_requete->fetch_assoc()) {
				include("includes/affichage_article.php");
			}
			unset($reveal_class);
			// $i++;
		}
		$requete->close();
	} else {
		echo "<p class=erreur>Oops ... Il semblerait que cet article n'existe pas.</p>";
	}

	include("includes/footer.php");
	?>
