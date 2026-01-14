<?php
	session_start();
									//Char spéciaux, backlashs, espaces

	function verif($data){
		$data=trim($data);
		$data=stripcslashes($data);
		$data=htmlspecialchars($data);
		return $data;
	}

	
	if(isset($_POST['connexion'])){
		extract($_POST);
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
	$bdd = new mysqli('localhost','root','','site');
	if($bdd->connect_error){
		die('Connection failed : '.$bdd->connect_error);
	}else{
		$rqt= "select * from user where email = '$email'";
		$result= mysqli_query($bdd,$rqt);
		$row=mysqli_fetch_assoc($result);//tableau associatif pour enregistrer dans la session
		$count=mysqli_num_rows($result); //compte le nombre de ligne dans le tableau reçu par la requête
		if($count!==1){
			header("Location: ../login.php?error=*Email incorrect !");
			exit();
		}else{
			if (password_verify($motdepasse, $row['motdepasse'])){
				$_SESSION['titre']=$row['titre'];
				$_SESSION['nom']=$row['nom'];
				$_SESSION['prenom']=$row['prenom'];
				$_SESSION['email']=$email;
				$_SESSION['accounttype']=$row['accounttype'];
				$_SESSION['id']=$row['id'];

				if($_SESSION['accounttype']==='a'){
					header("Location: ../page_admin.php");
					exit();
				}else{
				header("Location: ../accueil.php");
				exit();
				}
			}else{
				header("Location: ../login.php?error=*Mot de passe incorrect !");
				exit();
			}
		}}





?>