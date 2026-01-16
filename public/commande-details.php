<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();
?>
<title>Details commande</title>
<link rel="stylesheet" href="assets/css/commande-details.css">
</head>

<body>
    <?php include("includes/menu.php"); ?>
    <div class="container commande-details-layout">
        <?php
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo "Commande non specifiee.";
        } else {
            $order_id = (int) $_GET['id'];
            $user_id = $_SESSION['id'];

            $stmt_order = $connexion->prepare("SELECT id, status, total_cents, currency, created_at FROM orders WHERE id = ? AND user_id = ?");
            $stmt_order->bind_param("ii", $order_id, $user_id);
            $stmt_order->execute();
            $result_order = $stmt_order->get_result();
            $order = $result_order->fetch_assoc();
            $stmt_order->close();

            if (!$order) {
                echo "Commande introuvable.";
            } else {
                $stmt_items = $connexion->prepare("SELECT oi.qty, oi.unit_price_cents, oi.total_cents, a.id AS article_id, a.article_name, COALESCE(ai.url, a.img) AS image_url FROM order_items oi JOIN article a ON a.id = oi.article_id LEFT JOIN article_images ai ON ai.article_id = a.id AND ai.is_primary = 1 WHERE oi.order_id = ?");
                $stmt_items->bind_param("i", $order_id);
                $stmt_items->execute();
                $result_items = $stmt_items->get_result();

                echo "<div class='commande-details-left'>";
                if ($result_items->num_rows > 0) {
                    echo "<div class='commande-gallery'>";
                    while ($item = $result_items->fetch_assoc()) {
                        $article_link = "page_details_article.php?id=" . e($item['article_id']);
                        echo "<div class='commande-gallery-item'>";
                        echo "<a href='" . $article_link . "'>";
                        echo "<img src='" . e($item['image_url']) . "' alt='" . e($item['article_name']) . "'>";
                        echo "<p>" . e($item['article_name']) . "</p>";
                        echo "</a>";
                        echo "</div>";
                    }
                    echo "</div>";
                } else {
                    echo "<p>Aucun article.</p>";
                }
                echo "</div>";

                echo "<div class='commande-details-right'>";
                echo "<h2>Commande #" . e($order['id']) . "</h2>";
                echo "<p>Status : " . e($order['status']) . "</p>";
                echo "<p>Total : " . e(format_price($order['total_cents'], $order['currency'])) . "</p>";
                echo "<p>Date : " . e($order['created_at']) . "</p>";
                echo "</div>";
                $stmt_items->close();
            }
        }
        $connexion->close();
        ?>
    </div>
    <?php include("includes/footer.php"); ?>
