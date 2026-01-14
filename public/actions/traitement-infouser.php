<?php 
	session_start();

	function verif($data){
		$data=trim($data);
		$data=stripcslashes($data);
		$data=htmlspecialchars($data);
		return $data;
	}

	$iduser=$_SESSION['id'];

	if(isset($_POST['sauvegarde'])){
		extract($_POST);
	}

	$nom=verif($nom);
	$prenom=verif($prenom);
	$email=verif($email);

	if(empty($nom)||empty($prenom)||empty($email)||empty($dateden)){
		header("Location: ../infouser.php?error=*Un des champs n'a pas été renseigné !");
		exit();
	}

	include '../includes/config.php';
	$bdd = obtenirConnexion();
		$rqt=$bdd->prepare("UPDATE user SET nom = ?, prenom = ?, email = ?, dateden = ? WHERE id = ?");
		$rqt->bind_param("ssssi", $nom, $prenom, $email, $dateden, $iduser);
		$rqt->execute();
		header("Location: ../profil.php");
		$rqt->close();
		$bdd->close();
		exit();

?>
