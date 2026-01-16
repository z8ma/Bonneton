<?php 
session_start();
include '../includes/config.php';

	function verif($data){
		$data=trim($data);
		$data=stripcslashes($data);
		$data=htmlspecialchars($data);
		return $data;
	}

	$iduser=$_SESSION['id'];

	$nom = $_POST['nom'] ?? '';
	$prenom = $_POST['prenom'] ?? '';
	$email = $_POST['email'] ?? '';
	$dateden = $_POST['dateden'] ?? '';
	if (!verify_csrf()) {
		header("Location: ../infouser.php?error=*Requete invalide !");
		exit();
	}

	$nom=verif($nom);
	$prenom=verif($prenom);
	$email=verif($email);

	if(empty($nom)||empty($prenom)||empty($email)||empty($dateden)){
		header("Location: ../infouser.php?error=*Un des champs n'a pas été renseigné !");
		exit();
	}

	$bdd = obtenirConnexion();
		$rqt=$bdd->prepare("UPDATE user SET nom = ?, prenom = ?, email = ?, dateden = ? WHERE id = ?");
		$rqt->bind_param("ssssi", $nom, $prenom, $email, $dateden, $iduser);
		$rqt->execute();
		header("Location: ../infouser.php");
		$rqt->close();
		$bdd->close();
		exit();

?>
