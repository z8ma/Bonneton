<?php
session_start();
include("includes/header.php");
$_SESSION['message'] = "L'article a été ajouté au panier avec succès !";
?>
<title>Détails de l'Article</title>
<link rel="stylesheet" href="assets/css/commentaire.css">
<style>
    .container {
        width: 80%;
        margin: 0 auto;
        overflow: hidden;
    }

    .left-section {
        width: 40%;
        float: left;
    }

    .right-section {
        width: 50%;
        float: left;
        margin-left: 10%;
    }

    .article-image {
        width: 100%;
        display: block;
        margin-bottom: 20px;
    }

    .description {
        margin-top: 20px;
    }

    .add-to-cart {
        margin-top: 20px;
    }

    .submit:hover {
        background-color: #7D8491;
    }
</style>

</head>

<body>
    <?php
    include("includes/menu.php");
    ?>

    <div class="container">
        <?php


        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $article_id = (int) $_GET['id'];
            include 'includes/config.php';
            $connexion = obtenirConnexion();

            if ($connexion->connect_error) {
                die("Connexion échouée: " . $connexion->connect_error);
            }

            $stmt_article = $connexion->prepare("SELECT * FROM article WHERE id = ?");
            $stmt_article->bind_param("i", $article_id);
            $stmt_article->execute();
            $resultat = $stmt_article->get_result();

            if ($resultat->num_rows > 0) {
                $row = $resultat->fetch_assoc();
                echo "<div class='left-section'>";
                echo "<img class='article-image' src='" . $row["img"] . "' alt='" . $row["article_name"] . "'>";
                echo "</div>";
                echo "<div class='right-section'>";
                echo "<h2>" . $row["article_name"] . "</h2>";
                echo "<p>Prix : €" . $row["prix"] . "</p>";
                echo "<div class='description'>";
                echo "<h3>Description :</h3>";
                echo "<p>" . $row["caract"] . "</p><br>";
                echo "</div>";
                echo "<div class='add-to-cart'>";
                echo " <form method='post'>";
                echo " <input type='submit' name='add_to_cart' value='Ajouter au panier  🛒' style='background-color: black; color: white; padding: 15px 30px; font-size: 20px; border: none; cursor: pointer; transition: background-color 0.3s;'>";
                echo "</form>";
                echo "</div>";
            } else {
                echo "Aucun article trouvé.";
            }
            $stmt_article->close();

            $stmt_comments = $connexion->prepare("SELECT c.contenu, c.date_commentaire, c.img, u.prenom FROM commentaires c JOIN user u ON c.user_id = u.id WHERE c.article_id = ? ORDER BY c.date_commentaire DESC LIMIT 3");
            $stmt_comments->bind_param("i", $article_id);
            $stmt_comments->execute();
            $resultat_afficher_com = $stmt_comments->get_result();

            if ($resultat_afficher_com->num_rows > 0) {
                echo "<div class='commentaires'>";
                echo "<h3>Commentaires</h3>";
                while ($row_com = $resultat_afficher_com->fetch_assoc()) {
                    $prenom = $row_com['prenom'];
                    echo '<div class="commentaire">';
                    echo '<div class="commentaire-content">';
                    echo '<p>' . $prenom . ' - <em>' . $row_com['date_commentaire'] . '</em></p>';
                    echo '<p>' . $row_com['contenu'] . '</p>';
                    if (!empty($row_com['img'])) {
                        echo "<img src='" . $row_com['img'] . "' alt='Image du commentaire'>";
                    }
                    echo '</div>';
                    echo '</div>';
                }
                echo "<a class='commentaire-all-link' href='commentaires.php?id=" . $article_id . "'>Voir tous les commentaires</a>";
                echo "</div>";
            } else {
                echo "Pas de commentaires sous cet article";
                echo "<a class='commentaire-all-link' href='commentaires.php?id=" . $article_id . "'>Voir tous les commentaires</a>";
            }
            $stmt_comments->close();


            echo "<div class='comment-form'>";
            echo "<h3>Ajouter un commentaire</h3>";
            echo "<form method='post' action='' enctype='multipart/form-data'>";
            echo "<textarea name='commentaire' placeholder='Votre commentaire...'></textarea><br>";
            echo "<input type='file' class='text-input' id='img' name='img' accept='image/*'><br /><br />";
            echo "<input type='submit' value='Envoyer'name='Envoyer'>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "ID d'article non spécifié.";
        }

        ?>
    </div>

    <?php
    if (isset($_POST['Envoyer'])) {
        if (isset($_SESSION['id'])) {
            $user_id = $_SESSION['id'];
            $commentaire = $_POST['commentaire'];
            $image = '';
            $uploadErrorMessage = '';
            if (isset($_FILES['img'])) {
                $uploadErrors = [
                    UPLOAD_ERR_OK => 'OK',
                    UPLOAD_ERR_INI_SIZE => "Le fichier depasse la limite upload_max_filesize.",
                    UPLOAD_ERR_FORM_SIZE => "Le fichier depasse la limite MAX_FILE_SIZE.",
                    UPLOAD_ERR_PARTIAL => "Le fichier n'a ete que partiellement envoye.",
                    UPLOAD_ERR_NO_FILE => "Aucun fichier n'a ete envoye.",
                    UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant.",
                    UPLOAD_ERR_CANT_WRITE => "Ecriture du fichier impossible.",
                    UPLOAD_ERR_EXTENSION => "Envoi bloque par une extension PHP.",
                ];
                if ($_FILES['img']['error'] !== UPLOAD_ERR_OK) {
                    $uploadErrorMessage = $uploadErrors[$_FILES['img']['error']] ?? "Erreur lors de l'envoi de l'image (code " . $_FILES['img']['error'] . ").";
                } else {
                $uploadDir = __DIR__ . '/uploads/comments';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                if (!is_writable($uploadDir)) {
                    $uploadErrorMessage = "Dossier d'upload non accessible en ecriture.";
                } else {
                $tmpName = $_FILES['img']['tmp_name'];
                $imageInfo = getimagesize($tmpName);
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if ($imageInfo && in_array($imageInfo['mime'], $allowedTypes, true) && $_FILES['img']['size'] <= 2 * 1024 * 1024) {
                    $extension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
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


            $stmt = $connexion->prepare("INSERT INTO commentaires (user_id, article_id, contenu, date_commentaire, img) VALUES (?, ?, ?, NOW(), ?)");
            $stmt->bind_param("iiss", $user_id, $article_id, $commentaire, $image);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'style=color: green;>Votre commentaire a été ajouté avec succès !</div>";
                if ($uploadErrorMessage !== '') {
                    echo "<div class='alert alert-warning'style=color: orange;>" . $uploadErrorMessage . "</div>";
                }
            } else {
                echo "Erreur lors de l'ajout du commentaire : " . $connexion->error;
            }
            $stmt->close();
        } else {
            header("Location: login.php");
            exit;
        }
    }
    if (isset($_POST['add_to_cart'])) {
            if (isset($_SESSION['id'])) {
                $user_id = $_SESSION['id'];
                $stmt_panier = $connexion->prepare("INSERT INTO panier (user_id, article_id) VALUES (?, ?)");
                $stmt_panier->bind_param("ii", $user_id, $article_id);
                if ($stmt_panier->execute()) {
                    echo "<div class='alert alert-success'style=color: green;>" . $_SESSION['message'] . "</div>";
                    unset($_SESSION['message']);
                } else {
                    echo "Erreur lors de l'ajout de l'article au panier : " . $connexion->error;
                }
                $stmt_panier->close();
            } else {
                header("Location: login.php");
                exit;
        }
        $connexion->close();
    }
    ?>


    <?php
    include("includes/footer.php");
    ?>
