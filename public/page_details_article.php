<?php
session_start();
include 'includes/config.php';

if (isset($_POST['add_to_cart'])) {
    if (!verify_csrf()) {
        $_SESSION['panier_overlay_error'] = "Requete invalide.";
        header("Location: page_details_article.php?id=" . (int) ($_GET['id'] ?? 0));
        exit;
    }
    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit;
    }
    $article_id = (int) ($_GET['id'] ?? 0);
    if ($article_id > 0) {
        $connexion = obtenirConnexion();
        $user_id = $_SESSION['id'];
        $stmt_panier = $connexion->prepare("INSERT INTO panier (user_id, article_id, qty) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE qty = qty + 1");
        $stmt_panier->bind_param("ii", $user_id, $article_id);
        if ($stmt_panier->execute()) {
            $_SESSION['panier_overlay'] = true;
        }
        $stmt_panier->close();
        $connexion->close();
    }
    header("Location: page_details_article.php?id=" . $article_id);
    exit;
}

include("includes/header.php");
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

    .article-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .favorite-form-detail {
        margin: 0;
    }

    .panier-overlay {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    .panier-overlay.show {
        opacity: 1;
        pointer-events: auto;
    }

    .panier-overlay .panier-message {
        background: #111;
        color: #fff;
        padding: 18px 28px;
        border-radius: 14px;
        font-size: 18px;
        letter-spacing: 0.3px;
        transform: translateY(6px) scale(0.98);
        opacity: 0;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .panier-overlay.show .panier-message {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
</style>

</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <?php
    $overlay_message = null;
    if (!empty($_SESSION['panier_overlay_error'])) {
        $overlay_message = $_SESSION['panier_overlay_error'];
    } elseif (!empty($_SESSION['panier_overlay'])) {
        $overlay_message = "Article ajouté au panier";
    } elseif (!empty($_SESSION['comment_overlay_error'])) {
        $overlay_message = $_SESSION['comment_overlay_error'];
    } elseif (!empty($_SESSION['comment_overlay'])) {
        $overlay_message = "Commentaire ajouté";
    }
    ?>
    <div class="panier-overlay<?php echo !empty($overlay_message) ? ' show' : ''; ?>">
        <div class="panier-message">
            <?php echo $overlay_message ? e($overlay_message) : ''; ?>
        </div>
    </div>
    <?php unset($_SESSION['panier_overlay'], $_SESSION['panier_overlay_error'], $_SESSION['comment_overlay'], $_SESSION['comment_overlay_error']); ?>

    <div class="container">
        <?php


        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $article_id = (int) $_GET['id'];
            include 'includes/config.php';
            $connexion = obtenirConnexion();

            if ($connexion->connect_error) {
                die("Connexion échouée: " . $connexion->connect_error);
            }

            $stmt_article = $connexion->prepare("SELECT article.*, COALESCE(ai.url, article.img) AS image_url FROM article LEFT JOIN article_images ai ON ai.article_id = article.id AND ai.is_primary = 1 WHERE article.id = ? AND article.status = 'active'");
            $stmt_article->bind_param("i", $article_id);
            $stmt_article->execute();
            $resultat = $stmt_article->get_result();

            if ($resultat->num_rows > 0) {
                $row = $resultat->fetch_assoc();
                $avg_rating = null;
                $rating_count = 0;
                $stmt_rating = $connexion->prepare("SELECT AVG(rating) AS avg_rating, COUNT(rating) AS rating_count FROM commentaires WHERE article_id = ? AND rating IS NOT NULL");
                $stmt_rating->bind_param("i", $article_id);
                $stmt_rating->execute();
                $result_rating = $stmt_rating->get_result();
                $rating_row = $result_rating->fetch_assoc();
                if ($rating_row && $rating_row['avg_rating'] !== null) {
                    $avg_rating = (float) $rating_row['avg_rating'];
                    $rating_count = (int) $rating_row['rating_count'];
                }
                $stmt_rating->close();

                $is_favorite_detail = false;
                if (isset($_SESSION['id'])) {
                    $stmt_fav = $connexion->prepare("SELECT id FROM favorites WHERE user_id = ? AND article_id = ? LIMIT 1");
                    $stmt_fav->bind_param("ii", $_SESSION['id'], $article_id);
                    $stmt_fav->execute();
                    $result_fav = $stmt_fav->get_result();
                    $is_favorite_detail = (bool) $result_fav->fetch_assoc();
                    $stmt_fav->close();
                }
                echo "<div class='left-section'>";
                echo "<img class='article-image' src='" . e($row["image_url"]) . "' alt='" . e($row["article_name"]) . "'>";
                echo "</div>";
                echo "<div class='right-section'>";
                echo "<div class='article-header'>";
                echo "<div>";
                echo "<h2>" . e($row["article_name"]) . "</h2>";
                if ($avg_rating !== null) {
                    $filled = (int) round($avg_rating);
                    echo "<div class='rating-stars' aria-label='Note moyenne'>";
                    for ($i = 1; $i <= 5; $i++) {
                        $class = $i <= $filled ? "star filled" : "star";
                        echo "<span class='" . $class . "'>★</span>";
                    }
                    echo "<span class='rating-count'>(" . e($rating_count) . ")</span>";
                    echo "</div>";
                }
                echo "</div>";
                if (isset($_SESSION['id'])) {
                    echo "<form method='POST' action='actions/toggle-favori.php' class='favorite-form-detail'>";
                    echo csrf_field();
                    echo "<input type='hidden' name='article_id' value='" . e($article_id) . "'>";
                    echo "<input type='hidden' name='redirect' value='" . e($_SERVER['REQUEST_URI'] ?? '/accueil.php') . "'>";
                    echo "<button type='submit' class='favorite-button" . ($is_favorite_detail ? " is-active" : "") . "' aria-label='Favori' title='Favori'>♥</button>";
                    echo "</form>";
                }
                echo "</div>";
                echo "<p>Prix : " . e(format_price($row["price_cents"], $row["currency"] ?? 'EUR')) . "</p>";
                echo "<div class='description'>";
                echo "<h3>Description :</h3>";
                echo "<p>" . e($row["caract"]) . "</p><br>";
                echo "</div>";
                $stmt_gallery = $connexion->prepare("SELECT url FROM article_images WHERE article_id = ? ORDER BY is_primary DESC, position ASC");
                $stmt_gallery->bind_param("i", $article_id);
                $stmt_gallery->execute();
                $result_gallery = $stmt_gallery->get_result();
                if ($result_gallery->num_rows > 0) {
                    echo "<div class='commentaires'>";
                    echo "<h3>Galerie</h3>";
                    while ($img = $result_gallery->fetch_assoc()) {
                        echo "<img class='article-image' src='" . e($img['url']) . "' alt='Image article'>";
                    }
                    echo "</div>";
                }
                $stmt_gallery->close();

                echo "<div class='add-to-cart'>";
                echo " <form method='post'>";
                echo csrf_field();
                echo " <input type='submit' name='add_to_cart' value='Ajouter au panier  🛒' style='background-color: black; color: white; padding: 15px 30px; font-size: 20px; border: none; cursor: pointer; transition: background-color 0.3s;'>";
                echo "</form>";
                echo "</div>";
            } else {
                echo "<p class='empty-state'>Aucun article trouvé.</p>";
            }
            $stmt_article->close();

            $stmt_comments = $connexion->prepare("SELECT c.id, c.user_id, c.contenu, c.date_commentaire, c.img, c.rating, u.prenom FROM commentaires c JOIN user u ON c.user_id = u.id WHERE c.article_id = ? ORDER BY c.date_commentaire DESC LIMIT 3");
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
                    echo '<p>' . e($prenom) . ' - <em>' . e($row_com['date_commentaire']) . '</em></p>';
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
                        echo "<input type='hidden' name='redirect' value='" . e($_SERVER['REQUEST_URI'] ?? '/page_details_article.php') . "'>";
                        echo "<button type='submit' class='commentaire-delete'>Supprimer</button>";
                        echo "</form>";
                    }
                    echo '</div>';
                    echo '</div>';
                }
                echo "<a class='commentaire-all-link' href='commentaires.php?id=" . e($article_id) . "'>Voir tous les commentaires</a>";
                echo "</div>";
            } else {
                echo "<p class='empty-state'>Pas de commentaires sous cet article</p>";
                echo "<a class='commentaire-all-link commentaire-all-link-block' href='commentaires.php?id=" . e($article_id) . "'>Voir tous les commentaires</a>";
            }
            $stmt_comments->close();


            echo "<div class='comment-form'>";
            echo "<h3>Ajouter un commentaire</h3>";
            echo "<form method='post' action='actions/traitement-commentaire.php' enctype='multipart/form-data' class='comment-form-inner'>";
            echo csrf_field();
            echo "<input type='hidden' name='article_id' value='" . e($article_id) . "'>";
            echo "<textarea name='commentaire' placeholder='Votre commentaire...' class='comment-input'></textarea>";
            echo "<div class='rating-input'>";
            echo "<span class='rating-label'>Note :</span>";
            echo "<div class='rating-stars-input'>";
            for ($i = 5; $i >= 1; $i--) {
                echo "<input type='radio' name='rating' id='rating-" . $i . "' value='" . $i . "'>";
                echo "<label for='rating-" . $i . "'>★</label>";
            }
            echo "</div>";
            echo "</div>";
            echo "<label class='comment-file'>";
            echo "<input type='file' id='img' name='img' accept='image/*'>";
            echo "<span>Ajouter une photo</span>";
            echo "</label>";
            echo "<input type='submit' value='Envoyer' name='Envoyer' class='comment-submit'>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "ID d'article non spécifié.";
        }

        ?>
    </div>

    <?php ?>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var overlay = document.querySelector(".panier-overlay.show");
            if (!overlay) {
                return;
            }
            setTimeout(function () {
                overlay.classList.remove("show");
            }, 1000);
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var form = document.querySelector(".comment-form-inner");
            if (!form) {
                return;
            }
            form.addEventListener("submit", function () {
                var btn = form.querySelector(".comment-submit");
                if (!btn) {
                    return;
                }
                btn.disabled = true;
                btn.textContent = "Envoi...";
            });
        });
    </script>
    <?php
    include("includes/footer.php");
    ?>
