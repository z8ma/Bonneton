<?php
session_start();
?>
<link rel="stylesheet" type="text/css" href="assets/css/commentairemanagement-style.css" />
<link rel="stylesheet" type="text/css" href="assets/css/admin-base.css">
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
			<body class="admin-surface">
			<h1 class="admin-title">Les commentaires</h1>
			<div id='main' class="admin-card">
				<table class="admin-table">
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
						echo "<td>";
						echo "<form method='POST' action='actions/traitement-suppression-message.php'>";
						echo csrf_field();
						echo "<input type='hidden' name='id' value='" . e($row['id']) . "'>";
						echo "<button type='submit' class='admin-button admin-button-ghost'>Supprimer</button>";
						echo "</form>";
						echo "</td>";
						echo "</tr>";
					}
					?>
					<table>
			</div>
			<a class="admin-button" href="page_admin.php">Revenir à la page admin</a>
			</body>
<?php

	}
}
?>
