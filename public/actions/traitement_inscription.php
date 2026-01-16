<?php 
session_start();
include '../includes/config.php';
	
	function verif($data){
		$data=trim($data);
		$data=stripcslashes($data);
		$data=htmlspecialchars($data);
		return $data;
	}



	$titre = $_POST['titre'] ?? '';
	$nom = $_POST['nom'] ?? '';
	$prenom = $_POST['prenom'] ?? '';
	$email = $_POST['email'] ?? '';
	$motdepasse = $_POST['motdepasse'] ?? '';
	$dateden = $_POST['dateden'] ?? '';
	$accounttype = $_POST['accounttype'] ?? '';
	if (!verify_csrf()) {
		header("Location: ../register.php?error=*Requete invalide !");
		exit();
	}

	$nom=verif($nom);
	$prenom=verif($prenom);
	$email=verif($email);
	$motdepasse=verif($motdepasse);

	if(empty($titre)||empty($nom)||empty($prenom)||empty($email)||empty($motdepasse)||empty($dateden)||empty($accounttype)){
		header("Location: ../register.php?error=*Un des champs n'a pas été renseigné !");
		exit();
	}

	$hashmotdepasse=password_hash($motdepasse, PASSWORD_DEFAULT);


	



	/*var_dump($_POST)*/
	$bdd = obtenirConnexion();
		//regardes si il existe déjà un compte avec le même mail
		$stmt_check = $bdd->prepare("SELECT 1 FROM user WHERE email = ?");
		$stmt_check->bind_param("s", $email);
		$stmt_check->execute();
		$result = $stmt_check->get_result();
		$count = $result->num_rows;
		if($count>=1){
			$stmt_check->close();
			header("Location: ../register.php?error=*Vous avez déja crée un compte !");
			exit();
		}else{
			$rqt=$bdd->prepare("insert into user(titre,nom,prenom,email,motdepasse,dateden,accounttype)
				values(?,?,?,?,?,?,?)");
			$rqt->bind_param("sssssss",$titre, $nom, $prenom, $email, $hashmotdepasse, $dateden,$accounttype);
			$rqt->execute();
			$id = $bdd->insert_id;
			session_regenerate_id(true);
			$_SESSION['titre']=$titre;
			$_SESSION['nom']=$nom;
			$_SESSION['prenom']=$prenom;
			$_SESSION['email']=$email;
			$_SESSION['accounttype']=$accounttype;
			$_SESSION['id']=$id;
			header("Location: ../accueil.php");
			$rqt->close();
			$stmt_check->close();
			$bdd->close();
			exit();
		}

		$stmt_check->close();



?>

