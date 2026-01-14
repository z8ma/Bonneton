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
		$rqt=("SELECT motdepasse FROM user WHERE id='$id'");
		$result= mysqli_query($bdd,$rqt);
		$row=mysqli_fetch_assoc($result);
		$mdpactuhash=$row['motdepasse'];
		if(!password_verify($ancienmdp, $mdpactuhash)){
			header("Location: ../modifmdp.php?error=*Le mot de passe actuel n'est pas valide !");
			exit();
			$bdd->close();
		}else{
			if($nouveaumdp===$confirmmdp){
				$nouveaumdphash=password_hash($nouveaumdp, PASSWORD_DEFAULT);
				//on vérifie la chaine de charactère avant d'hacher le mot de passe
				$upt=$bdd->prepare("UPDATE user SET motdepasse='$nouveaumdphash' WHERE id='$id'");
				$upt->execute();
				header("Location: ../profil.php");
				$upt->close();
				$bdd->close();
				exit();
			}else{
				header("Location: ../modifmdp.php?error=*Les mots de passe ne correspondent pas !");
				exit();
				$bdd->close();
			}
		}
	
?>
