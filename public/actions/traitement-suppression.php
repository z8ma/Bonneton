<?php
session_start();
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
        header("Location: ../accueil.php");
        exit();
    }

if (!isset($_SESSION['accounttype'])) {
        header("Location: ../accueil.php");
    } else {  
        if($_SESSION['accounttype']!=='a'){
            header("Location: ../accueil.php");
            exit();
        }else{

            $bdd = obtenirConnexion();
                $user_id = (int) $_POST['id'];
                $rqt=$bdd->prepare("DELETE FROM user WHERE id = ?");
                $rqt->bind_param("i", $user_id);
                $rqt->execute();
                header("Location: ../accountmanagement.php");
                $rqt->close();
                $bdd->close();
                exit();
            
        }
    }
    ?>
