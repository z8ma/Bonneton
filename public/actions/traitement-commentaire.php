<?php
session_start();
include '../includes/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: ../accueil.php");
    exit;
}
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$article_id = (int) ($_POST['article_id'] ?? 0);
if ($article_id <= 0) {
    header("Location: ../accueil.php");
    exit;
}

$user_id = $_SESSION['id'];
$commentaire = $_POST['commentaire'] ?? '';
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : null;
if ($rating !== null && ($rating < 1 || $rating > 5)) {
    $rating = null;
}

$image = '';
$uploadErrorMessage = '';
if (isset($_FILES['img'])) {
    $uploadErrors = [
        UPLOAD_ERR_OK => 'OK',
        UPLOAD_ERR_INI_SIZE => "Le fichier depasse la limite upload_max_filesize.",
        UPLOAD_ERR_FORM_SIZE => "Le fichier depasse la limite MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL => "Le fichier n'a ete que partiellement envoye.",
        UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant.",
        UPLOAD_ERR_CANT_WRITE => "Ecriture du fichier impossible.",
        UPLOAD_ERR_EXTENSION => "Envoi bloque par une extension PHP.",
    ];
    if ($_FILES['img']['error'] === UPLOAD_ERR_NO_FILE) {
        $uploadErrorMessage = '';
    } elseif ($_FILES['img']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrorMessage = $uploadErrors[$_FILES['img']['error']] ?? "Erreur lors de l'envoi de l'image (code " . $_FILES['img']['error'] . ").";
    } else {
        $uploadDir = dirname(__DIR__) . '/uploads/comments';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (!is_writable($uploadDir)) {
            $uploadErrorMessage = "Dossier d'upload non accessible en ecriture.";
        } else {
            $tmpName = $_FILES['img']['tmp_name'];
            $imageInfo = getimagesize($tmpName);
            $mimeToExt = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
            ];
            if ($imageInfo && isset($mimeToExt[$imageInfo['mime']]) && $_FILES['img']['size'] <= 2 * 1024 * 1024) {
                $extension = $mimeToExt[$imageInfo['mime']];
                $filename = uniqid('comment_', true) . '.' . $extension;
                $destination = $uploadDir . '/' . $filename;
                if (move_uploaded_file($tmpName, $destination)) {
                    $image = 'uploads/comments/' . $filename;
                } else {
                    $uploadErrorMessage = "Impossible d'enregistrer l'image sur le serveur.";
                }
            } else {
                $uploadErrorMessage = "Format ou taille d'image invalide (max 2 Mo).";
            }
        }
    }
}

$connexion = obtenirConnexion();
if ($rating === null) {
    $stmt = $connexion->prepare("INSERT INTO commentaires (user_id, article_id, contenu, date_commentaire, img) VALUES (?, ?, ?, NOW(), ?)");
    $stmt->bind_param("iiss", $user_id, $article_id, $commentaire, $image);
} else {
    $stmt = $connexion->prepare("INSERT INTO commentaires (user_id, article_id, contenu, date_commentaire, img, rating) VALUES (?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("iissi", $user_id, $article_id, $commentaire, $image, $rating);
}
if ($stmt->execute()) {
    $_SESSION['comment_overlay'] = true;
} else {
    $_SESSION['comment_overlay_error'] = "Erreur lors de l'ajout du commentaire.";
}
$stmt->close();
$connexion->close();

header("Location: ../page_details_article.php?id=" . $article_id);
exit;
