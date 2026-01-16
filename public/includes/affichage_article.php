<?php
$article_classes = 'article';
if (!empty($reveal_style)) {
    $article_style = $reveal_style;
} else {
    $article_style = '';
}
if (!empty($reveal_class)) {
    $article_classes .= ' ' . $reveal_class;
}
$article_id = (int) ($row["id"] ?? 0);
$is_favorite = !empty($favorites_map) && isset($favorites_map[$article_id]);
echo "<div class='article-card'>";
if (isset($_SESSION['id'])) {
    echo "<form method='POST' action='actions/toggle-favori.php' class='favorite-form'>";
    echo csrf_field();
    echo "<input type='hidden' name='article_id' value='" . e($article_id) . "'>";
    echo "<input type='hidden' name='redirect' value='" . e($_SERVER['REQUEST_URI'] ?? '/accueil.php') . "'>";
    echo "<button type='submit' class='favorite-button" . ($is_favorite ? " is-active" : "") . "' aria-label='Favori' title='Favori'>♥</button>";
    echo "</form>";
}
echo "<a class='article-link' href='page_details_article.php?id=" . e($article_id) . "'><div class='" . e($article_classes) . "' " . $article_style . ">";
echo "<div class='left-section'>";
echo "<br><br><h2>" . e($row["article_name"]) . "</h2><br>";
$price_cents = $row["price_cents"] ?? null;
$currency = $row["currency"] ?? 'EUR';
if ($price_cents !== null && function_exists('format_price')) {
    echo "<p>Prix : " . e(format_price($price_cents, $currency)) . "</p>";
} elseif ($price_cents !== null) {
    echo "<p>Prix : " . e($price_cents) . " €</p>";
}
echo "</div>";
echo "<div class='right-section'>";
$image_url = $row["image_url"] ?? $row["img"];
echo "<img src='" . e($image_url) . "' alt='" . e($row["article_name"]) . "'>";
echo "</div>";
echo "</div></a>";
echo "</div>";
