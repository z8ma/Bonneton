<?php
session_start();
include '../includes/config.php';

$redirect = $_POST['redirect'] ?? '';
if ($redirect === '' || $redirect[0] !== '/') {
    $redirect = '/accueil.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: .." . $redirect);
    exit;
}

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php?redirect=" . urlencode($redirect));
    exit;
}

$comment_id = (int) ($_POST['comment_id'] ?? 0);
if ($comment_id <= 0) {
    header("Location: .." . $redirect);
    exit;
}

$connexion = obtenirConnexion();
$user_id = (int) $_SESSION['id'];

$stmt_check_comment = $connexion->prepare("SELECT id FROM commentaires WHERE id = ? LIMIT 1");
$stmt_check_comment->bind_param("i", $comment_id);
$stmt_check_comment->execute();
$result_comment = $stmt_check_comment->get_result();
if (!$result_comment->fetch_assoc()) {
    $stmt_check_comment->close();
    $connexion->close();
    header("Location: .." . $redirect);
    exit;
}
$stmt_check_comment->close();

$stmt_check = $connexion->prepare("SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ? LIMIT 1");
$stmt_check->bind_param("ii", $comment_id, $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$existing = $result_check->fetch_assoc();
$stmt_check->close();

if ($existing) {
    $stmt_delete = $connexion->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?");
    $stmt_delete->bind_param("ii", $comment_id, $user_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    $stmt_update = $connexion->prepare("UPDATE commentaires SET likes_count = GREATEST(likes_count - 1, 0) WHERE id = ?");
    $stmt_update->bind_param("i", $comment_id);
    $stmt_update->execute();
    $stmt_update->close();
} else {
    $stmt_insert = $connexion->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)");
    $stmt_insert->bind_param("ii", $comment_id, $user_id);
    $stmt_insert->execute();
    $stmt_insert->close();

    $stmt_update = $connexion->prepare("UPDATE commentaires SET likes_count = likes_count + 1 WHERE id = ?");
    $stmt_update->bind_param("i", $comment_id);
    $stmt_update->execute();
    $stmt_update->close();
}

$connexion->close();

header("Location: .." . $redirect);
exit;
