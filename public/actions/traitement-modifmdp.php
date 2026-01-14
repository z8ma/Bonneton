<?php 
	session_start();

	function verif($data){
		$data=trim($data);
		$data=stripcslashes($data);
		$data=htmlspecialchars($data);
		return $data;
	}


	if(isset($_POST['sauvegarde'])){
		extract($_POST);
	}

	$ancienmdp=verif($ancienmdp);
	$nouveaumdp=verif($nouveaumdp);
	$confirmmdp=verif($confirmmdp);

	if(empty($ancienmdp)||empty($nouveaumdp)||empty($confirmmdp)){
		header("Location: ../modifmdp.php?error=*Un des champs n'a pas été renseigné !");
		exit();
	}

	include '../includes/config.php';
	$bdd = obtenirConnexion();
		$id=$_SESSION['id'];
		$stmt = $bdd->prepare("SELECT motdepasse FROM user WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if (!$row) {
			header("Location: ../modifmdp.php?error=*Utilisateur introuvable !");
			exit();
		}
		$mdpactuhash=$row['motdepasse'];
		if(!password_verify($ancienmdp, $mdpactuhash)){
			header("Location: ../modifmdp.php?error=*Le mot de passe actuel n'est pas valide !");
			exit();
			$bdd->close();
		}else{
			if($nouveaumdp===$confirmmdp){
				$nouveaumdphash=password_hash($nouveaumdp, PASSWORD_DEFAULT);
				//on vérifie la chaine de charactère avant d'hacher le mot de passe
				$upt=$bdd->prepare("UPDATE user SET motdepasse = ? WHERE id = ?");
				$upt->bind_param("si", $nouveaumdphash, $id);
				$upt->execute();
				header("Location: ../profil.php");
				$upt->close();
				$stmt->close();
				$bdd->close();
				exit();
			}else{
				header("Location: ../modifmdp.php?error=*Les mots de passe ne correspondent pas !");
				exit();
				$stmt->close();
				$bdd->close();
			}
		}
	
?>
