<?php
session_start();
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();

//affiche les articles selon le nombres de vente
$favorites_map = [];
if (isset($_SESSION['id'])) {
    $stmt_fav = $connexion->prepare("SELECT article_id FROM favorites WHERE user_id = ?");
    $stmt_fav->bind_param("i", $_SESSION['id']);
    $stmt_fav->execute();
    $result_fav = $stmt_fav->get_result();
    while ($fav = $result_fav->fetch_assoc()) {
        $favorites_map[(int) $fav['article_id']] = true;
    }
    $stmt_fav->close();
}

$stmt_selection = $connexion->prepare("SELECT article.*, COALESCE(ai.url, article.img) AS image_url FROM article LEFT JOIN article_images ai ON ai.article_id = article.id AND ai.is_primary = 1 WHERE article.status = 'active' ORDER BY article.nb_vente DESC LIMIT 4");
$stmt_selection->execute();
$resultat_selection = $stmt_selection->get_result();
?>
<title>Bienvenue sur Bonneton</title>
</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <br>
    <section class="haut reveal">
        <h1><span class="accent-phrase">Des <i>créateurs</i>, <i>designers</i>, et <i>vendeurs</i> de bonnets <i>100% français</i>.</span></h1>
    </section>
    <div class="image-container reveal parallax-group">
        <img src="img/photo1.jpeg" id=img1 class="parallax-img" data-speed="0.08">
        <img src="img/photo2.jpg" id=img2 class="parallax-img" data-speed="0.12">
        <img src="img/photo3.jpeg" id=img3 class="parallax-img" data-speed="0.1">
    </div>
    <section class="bas reveal">
        <h2>Notre <i>sélection</i></h2>
    </section>
    <div class="marquee">
        <div class="marquee-track">
            <div class="marquee-group">
                <span>Nouveautés · Édition limitée · Fabrication française · Matières premium ·</span>
                <span>Nouveautés · Édition limitée · Fabrication française · Matières premium ·</span>
            </div>
            <div class="marquee-group">
                <span>Nouveautés · Édition limitée · Fabrication française · Matières premium ·</span>
                <span>Nouveautés · Édition limitée · Fabrication française · Matières premium ·</span>
            </div>
        </div>
    </div>
    <?php if (mysqli_num_rows($resultat_selection) > 0) {
        echo "<div class='selection-slider'>";
        $reveal_class = 'reveal blur-in';
        $stagger_index = 0;
        while ($row = $resultat_selection->fetch_assoc()) {
            $reveal_style = "style='--stagger-delay: " . (120 * $stagger_index) . "ms;'";
            include("includes/affichage_article.php");
            $stagger_index++;
        }
        unset($reveal_class, $reveal_style, $stagger_index);
        echo "</div>";
    } else {
        echo "<p class='empty-state'>Aucun article trouvé.</p>";
    } ?>
    <br /><br />
    <section class="haut reveal">
        <div>
            <p><a href="article.php">Charger plus</a></p>
        </div>
    </section>
    <br />
    <?php
    $stmt_selection->close();
    $connexion->close();
    include("includes/footer.php");
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var images = document.querySelectorAll(".parallax-img");
            if (!images.length) {
                return;
            }
            var update = function () {
                var offset = window.scrollY || window.pageYOffset;
                images.forEach(function (img) {
                    var speed = parseFloat(img.getAttribute("data-speed")) || 0.1;
                    img.style.transform = "translateY(" + (offset * speed) + "px)";
                });
            };
            update();
            window.addEventListener("scroll", update, { passive: true });
        });
    </script>
