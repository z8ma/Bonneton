<?php 
	session_start();
	
	function verif($data){
		$data=trim($data);
		$data=stripcslashes($data);
		$data=htmlspecialchars($data);
		return $data;
	}



	if(isset($_POST['inscription'])){
		extract($_POST);
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
	$bdd = new mysqli('localhost','root','','site');
	if($bdd->connect_error){
		die('Connection failed : '.$bdd->connect_error);
	}else{
		//regardes si il existe déjà un compte avec le même mail
		$rqt=("SELECT * from user WHERE email='$email'");
		$result= mysqli_query($bdd,$rqt);
		$count=mysqli_num_rows($result);
		if($count>=1){
			header("Location: ../register.php?error=*Vous avez déja crée un compte !");
			exit();
		}else{
			$rqt=$bdd->prepare("insert into user(titre,nom,prenom,email,motdepasse,dateden,accounttype)
				values(?,?,?,?,?,?,?)");
			$rqt->bind_param("sssssss",$titre, $nom, $prenom, $email, $hashmotdepasse, $dateden,$accounttype);
			$rqt->execute();
			$rqtid=("SELECT id from user WHERE email='$email'");
			$result= mysqli_query($bdd,$rqtid);
			$row=mysqli_fetch_assoc($result);
			$id=$row['id'];
			$_SESSION['titre']=$titre;
			$_SESSION['nom']=$nom;
			$_SESSION['prenom']=$prenom;
			$_SESSION['email']=$email;
			$_SESSION['accounttype']=$accounttype;
			$_SESSION['id']=$id;
			header("Location: ../accueil.php");
			$rqt->close();
			$bdd->close();
			exit();
		}
	}


?>

