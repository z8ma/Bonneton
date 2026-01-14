<?php
session_start();
?>
<link rel="stylesheet" type="text/css" href="assets/css/commentairemanagement-style.css" />
<?php

if (!isset($_SESSION['accounttype'])) {
	header("Location: accueil.php");
} else {
	if ($_SESSION['accounttype'] !== 'a') {
		header("Location: accueil.php");
		exit();
	} else {

		include 'includes/config.php';
		$bdd = obtenirConnexion();
		$req = ("SELECT id,user_id,contenu FROM commentaires");
		$resultat = $bdd->query($req);
?>
			<h1>Les commentaires</h1>
			<div id='main'>
				<table>
					<tr>
						<th>USER ID</th>
						<th>Contenu</th>
						<th>Supprimer</th>
					</tr>
					<?php
					//on affiche les commentaires sous formes d'un tableau pour permettre à l'admin de gérer les comms
					while ($row = $resultat->fetch_assoc()) {
						echo "<tr>";
						echo "<div id='containermessage'>";
                        echo "<td>" . e($row['user_id']) . "</td>";
                        echo "<td>" . e($row['contenu']) . "</td>";
						echo "</div>";
						echo "<td><a href='actions/traitement-suppression-message.php?id=" . $row['id'] . "'>Supprimer</a></td>";
						echo "</tr>";
					}
					?>
					<table>
			</div>
			<a class=button href="page_admin.php">Revenir à la page admin</a>
<?php

	}
}
?>
