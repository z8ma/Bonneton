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

            $rank_update = $connexion->prepare("UPDATE commentaires SET rank_score = likes_count, rank_date = CURDATE() WHERE article_id = ? AND (rank_date IS NULL OR rank_date <> CURDATE())");
            $rank_update->bind_param("i", $article_id);
            $rank_update->execute();
            $rank_update->close();

            $viewer_id = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
            $stmt_comments = $connexion->prepare("SELECT c.id, c.user_id, c.contenu, c.date_commentaire, c.img, c.rating, c.likes_count, u.prenom, cl.id AS liked_id FROM commentaires c JOIN user u ON c.user_id = u.id LEFT JOIN comment_likes cl ON cl.comment_id = c.id AND cl.user_id = ? WHERE c.article_id = ? ORDER BY c.rank_score DESC, c.date_commentaire DESC");
            $stmt_comments->bind_param("ii", $viewer_id, $article_id);
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
                    $likes_count = isset($row_com['likes_count']) ? (int) $row_com['likes_count'] : 0;
                    $is_liked = !empty($row_com['liked_id']);
                    echo "<div class='commentaire-actions'>";
                    if (!empty($_SESSION['id'])) {
                        echo "<form method='POST' action='actions/toggle-comment-like.php' class='commentaire-like-form'>";
                        echo csrf_field();
                        echo "<input type='hidden' name='comment_id' value='" . e($row_com['id']) . "'>";
                        echo "<input type='hidden' name='redirect' value='" . e($_SERVER['REQUEST_URI'] ?? '/commentaires.php') . "'>";
                        echo "<button type='submit' class='commentaire-like" . ($is_liked ? " is-liked" : "") . "' aria-label='Aimer ce commentaire'>";
                        echo "♥ <span class='commentaire-like-count'>" . e($likes_count) . "</span>";
                        echo "</button>";
                        echo "</form>";
                    } else {
                        $login_redirect = "/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI'] ?? '/commentaires.php');
                        echo "<a class='commentaire-like' href='" . e($login_redirect) . "' aria-label='Se connecter pour aimer'>♥ <span class='commentaire-like-count'>" . e($likes_count) . "</span></a>";
                    }
                    if (!empty($_SESSION['accounttype']) && $_SESSION['accounttype'] === 'a' || (!empty($_SESSION['id']) && (int) $row_com['user_id'] === (int) $_SESSION['id'])) {
                        echo "<form method='POST' action='actions/traitement-suppression-message.php' class='commentaire-delete-form' onsubmit=\"return confirm('Supprimer ce commentaire ?');\">";
                        echo csrf_field();
                        echo "<input type='hidden' name='id' value='" . e($row_com['id']) . "'>";
                        echo "<input type='hidden' name='redirect' value='" . e($_SERVER['REQUEST_URI'] ?? '/commentaires.php') . "'>";
                        echo "<button type='submit' class='commentaire-delete'>Supprimer</button>";
                        echo "</form>";
                    }
                    echo "</div>";
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
