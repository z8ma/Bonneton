<link rel="stylesheet" href="assets/css/menu-style.css">
<nav>
    <p class="nav-logo">
        <a href="accueil.php" class="logo">Bonneton</a>
        <span class="logo-tagline">Depuis 2023</span>
    </p>
    <?php
    //barre de recherche
    if (
        stripos($_SERVER['REQUEST_URI'], 'accueil.php') ||
        stripos($_SERVER['REQUEST_URI'], 'article.php') ||
        stripos($_SERVER['REQUEST_URI'], 'pageNews.php') ||
        stripos($_SERVER['REQUEST_URI'], 'traitement-searchbar.php')
    ) {
    ?>
        <div class="nav-search">
            <form method="POST" action="traitement-searchbar.php">
                <?php echo csrf_field(); ?>
                <div id="content-searchbar">
                    <input type="text" id="searchbar" placeholder="Rechercher" name="req" />
                    <span class="search-loading" aria-hidden="true">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </span>
                </div>
            </form>
        </div>
    <?php } ?>
    <?php
    $panier_items = [];
    $panier_total_cents = 0;
    $panier_currency = 'EUR';
    if (isset($_SESSION['id'])) {
        include_once 'includes/config.php';
        $menu_connexion = obtenirConnexion();
        $user_id = $_SESSION['id'];
        $stmt_panier = $menu_connexion->prepare("SELECT p.qty, a.article_name, a.price_cents, a.currency, COALESCE(ai.url, a.img) AS image_url FROM panier p JOIN article a ON a.id = p.article_id LEFT JOIN article_images ai ON ai.article_id = a.id AND ai.is_primary = 1 WHERE p.user_id = ? ORDER BY p.id DESC LIMIT 2");
        $stmt_panier->bind_param("i", $user_id);
        $stmt_panier->execute();
        $result_panier = $stmt_panier->get_result();
        while ($row = $result_panier->fetch_assoc()) {
            $panier_total_cents += ((int) $row['price_cents']) * ((int) $row['qty']);
            $panier_currency = $row['currency'] ?? 'EUR';
            $panier_items[] = $row;
        }
        $stmt_panier->close();
        $stmt_total = $menu_connexion->prepare("SELECT SUM(p.qty * a.price_cents) AS total_cents, MAX(a.currency) AS currency FROM panier p JOIN article a ON a.id = p.article_id WHERE p.user_id = ?");
        $stmt_total->bind_param("i", $user_id);
        $stmt_total->execute();
        $result_total = $stmt_total->get_result();
        $total_row = $result_total->fetch_assoc();
        if (!empty($total_row['total_cents'])) {
            $panier_total_cents = (int) $total_row['total_cents'];
        }
        if (!empty($total_row['currency'])) {
            $panier_currency = $total_row['currency'];
        }
        $stmt_total->close();
        $menu_connexion->close();
    }
    ?>
    <ul class="nav-links">
        <li class="normal"><a href="article.php">Boutique</a></li>
        <li class="normal"><a href="pageNews.php">Nouveautés</a></li>
        <li class="normal"><a href="pageApropos.php">Qui sommes-nous ?</a></li>
        <?php if (!isset($_SESSION['id'])) { ?>
            <?php $redirect = $_SERVER['REQUEST_URI'] ?? '/accueil.php'; ?>
            <li class="normal"><a href="login.php?redirect=<?php echo e($redirect); ?>">Connexion</a></li>
        <?php } else { ?>
            <li class="account-menu diffconnecte">
                <a href="infouser.php" class="account-trigger">
                    <?php echo e($_SESSION['prenom']); ?>
                    <?php if (!empty($_SESSION['accounttype']) && $_SESSION['accounttype'] === 'a') { ?>
                        <span class="account-badge">Admin</span>
                    <?php } ?>
                </a>
                <ul class="account-dropdown">
                    <li><a href="infouser.php">Infos perso</a></li>
                    <li><a href="mes-commandes.php">Commandes</a></li>
                    <li><a href="favoris.php">Favoris</a></li>
                    <li><a href="adresse.php">Adresses</a></li>
                    <?php if (!empty($_SESSION['accounttype']) && $_SESSION['accounttype'] === 's') { ?>
                        <li><a href="vendeur.php">Mes ventes</a></li>
                    <?php } ?>
                    <?php if (!empty($_SESSION['accounttype']) && $_SESSION['accounttype'] === 'a') { ?>
                        <li><a href="page_admin.php">Espace admin</a></li>
                    <?php } ?>
                    <li><a href="contactSupport.php">Contacter</a></li>
                    <li class="account-logout">
                        <form method="POST" action="actions/deconnexion.php">
                            <?php echo csrf_field(); ?>
                            <button type="submit">Déconnexion</button>
                        </form>
                    </li>
                </ul>
            </li>
        <?php } ?>
        <?php
        $panier_count = 0;
        if (isset($_SESSION['id'])) {
            include_once 'includes/config.php';
            $menu_connexion_count = obtenirConnexion();
            $user_id = $_SESSION['id'];
            $stmt_count = $menu_connexion_count->prepare("SELECT COALESCE(SUM(qty), 0) AS total_qty FROM panier WHERE user_id = ?");
            $stmt_count->bind_param("i", $user_id);
            $stmt_count->execute();
            $result_count = $stmt_count->get_result();
            $count_row = $result_count->fetch_assoc();
            if (!empty($count_row['total_qty'])) {
                $panier_count = (int) $count_row['total_qty'];
            }
            $stmt_count->close();
            $menu_connexion_count->close();
        }
        ?>
        <li class="diff panier-menu">
            <a href="panier.php" class="panier-trigger">
                Panier
                <?php if ($panier_count > 0) : ?>
                    <span class="panier-badge" data-count="<?php echo e($panier_count); ?>"><?php echo e($panier_count); ?></span>
                <?php endif; ?>
            </a>
            <div class="panier-dropdown">
                <?php if (count($panier_items) > 0) : ?>
                    <?php foreach ($panier_items as $item) : ?>
                        <div class="panier-item">
                            <img src="<?php echo e($item['image_url']); ?>" alt="<?php echo e($item['article_name']); ?>">
                            <div class="panier-item-text">
                                <p><?php echo e($item['article_name']); ?></p>
                                <p class="panier-item-qty">x<?php echo e($item['qty']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="panier-total">
                        <span>Total</span>
                        <span><?php echo e(format_price($panier_total_cents, $panier_currency)); ?></span>
                    </div>
                    <div class="panier-actions">
                        <a href="panier.php" class="panier-cta">Voir le panier</a>
                    </div>
                <?php else : ?>
                    <p class="panier-empty">Oups, votre panier est vide.</p>
                <?php endif; ?>
            </div>
        </li>
    </ul>
</nav>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var form = document.querySelector(".nav-search form");
        if (!form) {
            return;
        }
        form.addEventListener("submit", function (event) {
            event.preventDefault();
            var loader = form.querySelector(".search-loading");
            if (loader) {
                loader.classList.add("is-active");
            }
            setTimeout(function () {
                form.submit();
            }, 2000);
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var badge = document.querySelector(".panier-badge");
        if (!badge) {
            return;
        }
        var current = badge.getAttribute("data-count") || "0";
        var key = "panier_count";
        var previous = window.sessionStorage.getItem(key);
        if (previous === null) {
            window.sessionStorage.setItem(key, current);
            return;
        }
        if (previous !== current) {
            badge.classList.remove("badge-animate");
            void badge.offsetWidth;
            badge.classList.add("badge-animate");
            window.sessionStorage.setItem(key, current);
        }
    });
</script>
<br>
<main class="page-content">
