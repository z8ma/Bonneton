<?php
session_start();

if (!isset($_SESSION['accounttype'])) {
    header("Location: ../accueil.php");
} else {
    if ($_SESSION['accounttype'] !== 'a') {
        header("Location: ../accueil.php");
        exit();
    } else {

        $bdd = mysqli_connect("localhost", "root", "", "site");

        $bdd = new mysqli('localhost', 'root', '', 'site');
        if ($bdd->connect_error) {
            die('Connection failed : ' . $bdd->connect_error);
        } else {
            $id = $_GET['id'];
            $rqt = $bdd->prepare("DELETE FROM commentaires WHERE id='$id'");
            $rqt->execute();
            header("Location: ../commentairemanagement.php");
            $rqt->close();
            $bdd->close();
            exit();
        }
    }
}
