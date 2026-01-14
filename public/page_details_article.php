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
            $article_id = $_GET['id'];
            $serveur = "localhost";
            $utilisateur = "root";
            $motdepasse = "";
            $basededonnees = "site";
            $connexion = new mysqli($serveur, $utilisateur, $motdepasse, $basededonnees);

            if ($connexion->connect_error) {
                die("Connexion échouée: " . $connexion->connect_error);
            }

            $sql = "SELECT * FROM article WHERE id = $article_id";
            $resultat = $connexion->query($sql);

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

            $afficher_com = "SELECT * FROM commentaires WHERE article_id = $article_id ORDER BY date_commentaire DESC LIMIT 3";
            $resultat_afficher_com = mysqli_query($connexion, $afficher_com);

            if ($resultat_afficher_com->num_rows > 0) {
                echo "<div class='commentaires'>";
                echo "<h3>Commentaires</h3>";
                while ($row_com = mysqli_fetch_assoc($resultat_afficher_com)) {
                    $user_com = $row_com['user_id'];
                    $requete_prenom = "SELECT prenom FROM user WHERE id = $user_com";
                    $resultat_prenom = mysqli_query($connexion, $requete_prenom);
                    $row_prenom = mysqli_fetch_assoc($resultat_prenom);
                    $prenom = $row_prenom['prenom'];

                    echo '<div class="commentaire">';
                    echo '<div class="commentaire-content">';
                    echo '<p>' . $prenom . ' - <em>' . $row_com['date_commentaire'] . '</em></p>';
                    echo '<p>' . $row_com['contenu'] . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "Pas de commentaires sous cet article";
            }


            echo "<div class='comment-form'>";
            echo "<h3>Ajouter un commentaire</h3>";
            echo "<form method='post' action=''>";
            echo "<textarea name='commentaire' placeholder='Votre commentaire...'></textarea><br>";
            echo "<input type='text' class='text-input' size='25' id='img' name='img' placeholder='Lien de votre image ...'><br /><br />";
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
            $image = $_POST['img'];


            $sql = "INSERT INTO commentaires (user_id, article_id, contenu, date_commentaire, img) VALUES ('$user_id', '$article_id', '$commentaire', NOW(), '$image') ";
            if ($connexion->query($sql) === TRUE) {
                echo "<div class='alert alert-success'style=color: green;>Votre commentaire a été ajouté avec succès !</div>";
            } else {
                echo "Erreur lors de l'ajout du commentaire : " . $connexion->error;
            }
        } else {
            header("Location: login.php");
            exit;
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_SESSION['id'])) {
            $user_id = $_SESSION['id'];
            $sql = "INSERT INTO panier (user_id, article_id) VALUES ('$user_id', '$article_id')";
            if ($connexion->query($sql) === TRUE) {
                echo "<div class='alert alert-success'style=color: green;>" . $_SESSION['message'] . "</div>";
                unset($_SESSION['message']);
            } else {
                echo "Erreur lors de l'ajout de l'article au panier : " . $connexion->error;
            }
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