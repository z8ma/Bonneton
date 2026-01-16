<?php
session_start();
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();
?>
<title>Commentaires</title>
<link rel="stylesheet" href="assets/css/commentaire.css">
</head>

<body>
    <?php
    include("includes/menu.php");
    ?>

    <div class="container">
        <?php
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo "ID d'article non spécifié.";
        } else {
            $article_id = (int) $_GET['id'];

            $stmt_article = $connexion->prepare("SELECT article_name FROM article WHERE id = ?");
            $stmt_article->bind_param("i", $article_id);
            $stmt_article->execute();
            $result_article = $stmt_article->get_result();
            $article = $result_article->fetch_assoc();
            $stmt_article->close();

            if ($article) {
                echo "<h2>Tous les commentaires pour : " . e($article['article_name']) . "</h2>";
                echo "<a class='commentaire-all-link' href='page_details_article.php?id=" . e($article_id) . "'>Retour a l'article</a>";
            } else {
                echo "Article introuvable.";
            }

            $stmt_comments = $connexion->prepare("SELECT c.id, c.user_id, c.contenu, c.date_commentaire, c.img, c.rating, u.prenom FROM commentaires c JOIN user u ON c.user_id = u.id WHERE c.article_id = ? ORDER BY c.date_commentaire DESC");
            $stmt_comments->bind_param("i", $article_id);
            $stmt_comments->execute();
            $result_comments = $stmt_comments->get_result();

            if ($result_comments->num_rows > 0) {
                echo "<div class='commentaires'>";
                while ($row_com = $result_comments->fetch_assoc()) {
                    echo '<div class="commentaire">';
                    echo '<div class="commentaire-content">';
                    echo '<p>' . e($row_com['prenom']) . ' - <em>' . e($row_com['date_commentaire']) . '</em></p>';
                    if (!empty($row_com['rating'])) {
                        $rating = (int) $row_com['rating'];
                        echo "<div class='rating-stars'>";
                        for ($i = 1; $i <= 5; $i++) {
                            $class = $i <= $rating ? "star filled" : "star";
                            echo "<span class='" . $class . "'>★</span>";
                        }
                        echo "</div>";
                    }
                    echo '<p>' . e($row_com['contenu']) . '</p>';
                    if (!empty($row_com['img'])) {
                        echo "<img src='" . e($row_com['img']) . "' alt='Image du commentaire'>";
                    }
                    if (!empty($_SESSION['accounttype']) && $_SESSION['accounttype'] === 'a' || (!empty($_SESSION['id']) && (int) $row_com['user_id'] === (int) $_SESSION['id'])) {
                        echo "<form method='POST' action='actions/traitement-suppression-message.php' class='commentaire-actions' onsubmit=\"return confirm('Supprimer ce commentaire ?');\">";
                        echo csrf_field();
                        echo "<input type='hidden' name='id' value='" . e($row_com['id']) . "'>";
                        echo "<input type='hidden' name='redirect' value='" . e($_SERVER['REQUEST_URI'] ?? '/commentaires.php') . "'>";
                        echo "<button type='submit' class='commentaire-delete'>Supprimer</button>";
                        echo "</form>";
                    }
                    echo '</div>';
                    echo '</div>';
                }
                echo "</div>";
            } else {
                echo "<p class='empty-state'>Pas de commentaires pour cet article.</p>";
            }

            $stmt_comments->close();
        }

        $connexion->close();
        ?>
    </div>

    <?php
    include("includes/footer.php");
    ?>
