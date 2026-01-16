<?php
session_start();
include '../includes/config.php';
									//Char spéciaux, backlashs, espaces

	function verif($data){
		$data=trim($data);
		$data=stripcslashes($data);
		$data=htmlspecialchars($data);
		return $data;
	}

	
	$email = $_POST['email'] ?? '';
	$motdepasse = $_POST['motdepasse'] ?? '';
if (!verify_csrf()) {
		header("Location: ../login.php?error=*Requete invalide !");
		exit();
	}

	$email=verif($email);
	$motdepasse=verif($motdepasse);



									//Verifie si un des champs est vide

	if(empty($email)){
		header("Location: ../login.php?error=*Veuillez renseigner un email !");
		exit();
	}else if(empty($motdepasse)){
		header("Location: ../login.php?error=*Veuillez renseigner un mot de passe !");
		exit();
	}


	//Connexion + teste si pseudo et motdepasse correct
	$bdd = obtenirConnexion();
		$stmt = $bdd->prepare("SELECT * FROM user WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();//tableau associatif pour enregistrer dans la session
		$count = $result->num_rows; //compte le nombre de ligne dans le tableau reçu par la requête
		if($count!==1){
			header("Location: ../login.php?error=*Email incorrect !");
			exit();
		}else{
			if (password_verify($motdepasse, $row['motdepasse'])){
				session_regenerate_id(true);
				$update_login = $bdd->prepare("UPDATE user SET last_login_at = NOW() WHERE id = ?");
				$update_login->bind_param("i", $row['id']);
				$update_login->execute();
				$update_login->close();
				$_SESSION['titre']=$row['titre'];
				$_SESSION['nom']=$row['nom'];
				$_SESSION['prenom']=$row['prenom'];
				$_SESSION['email']=$email;
				$_SESSION['accounttype']=$row['accounttype'];
				$_SESSION['id']=$row['id'];

				$redirect = $_POST['redirect'] ?? '';
				if (is_string($redirect) && $redirect !== '' && $redirect[0] === '/' && strpos($redirect, '://') === false) {
					header("Location: .." . $redirect);
				} else {
					header("Location: ../accueil.php");
				}
				exit();
			}else{
				header("Location: ../login.php?error=*Mot de passe incorrect !");
				exit();
			}
		}
		$stmt->close();






?>
