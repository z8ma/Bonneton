<?php
session_start();
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: ../accueil.php");
    exit();
}

if (!isset($_SESSION['id'])) {
    header("Location: ../accueil.php");
    exit();
}

$bdd = obtenirConnexion();
$id = (int) $_POST['id'];
$stmt = $bdd->prepare("SELECT user_id FROM commentaires WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();
$stmt->close();

if (!$comment) {
    $bdd->close();
    header("Location: ../accueil.php");
    exit();
}

$is_admin = isset($_SESSION['accounttype']) && $_SESSION['accounttype'] === 'a';
$is_owner = ((int) $comment['user_id']) === (int) $_SESSION['id'];

if (!$is_admin && !$is_owner) {
    $bdd->close();
    header("Location: ../accueil.php");
    exit();
}

$rqt = $bdd->prepare("DELETE FROM commentaires WHERE id = ?");
$rqt->bind_param("i", $id);
$rqt->execute();
$redirect = $_POST['redirect'] ?? '';
if (is_string($redirect) && $redirect !== '' && strpos($redirect, '://') === false && $redirect[0] === '/') {
    header("Location: .." . $redirect);
} else {
    header("Location: ../commentairemanagement.php");
}
$rqt->close();
$bdd->close();
exit();
