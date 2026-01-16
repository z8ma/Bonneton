<?php
session_start();
if (!isset($_SESSION['id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/paiement.php');
    header("Location: login.php?redirect=" . $redirect);
    exit();
}
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();
?>
<title>Paiement</title>
<link rel="stylesheet" href="assets/css/paiement.css">
</head>

<body class="paiement-page">
    <?php include("includes/menu.php"); ?>
    <div class="container paiement-container">
        <?php
        $order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;
        $user_id = $_SESSION['id'];
        if ($order_id <= 0) {
            echo "<p>Commande invalide.</p>";
        } else {
            $stmt = $connexion->prepare("SELECT id, status, total_cents, currency FROM orders WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $order_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();
            $stmt->close();

            if (!$order) {
                echo "<p>Commande introuvable.</p>";
            } else {
                $stmt_items = $connexion->prepare("SELECT oi.qty, a.article_name, COALESCE(ai.url, a.img) AS image_url FROM order_items oi JOIN article a ON a.id = oi.article_id LEFT JOIN article_images ai ON ai.article_id = a.id AND ai.is_primary = 1 WHERE oi.order_id = ?");
                $stmt_items->bind_param("i", $order_id);
                $stmt_items->execute();
                $result_items = $stmt_items->get_result();

                echo "<h1>Paiement</h1>";
                echo "<p>Commande #" . e($order['id']) . "</p>";
                echo "<p>Total : " . e(format_price($order['total_cents'], $order['currency'])) . "</p>";
                if ($result_items && $result_items->num_rows > 0) {
                    echo "<div class='paiement-items'>";
                    while ($item = $result_items->fetch_assoc()) {
                        echo "<div class='paiement-item'>";
                        echo "<img src='" . e($item['image_url']) . "' alt='" . e($item['article_name']) . "'>";
                        echo "<div class='paiement-item-meta'>";
                        echo "<p>" . e($item['article_name']) . "</p>";
                        echo "<p>x" . e($item['qty']) . "</p>";
                        echo "</div>";
                        echo "</div>";
                    }
                    echo "</div>";
                }
                echo "<form method='POST' action='actions/traitement-paiement.php'>";
                echo csrf_field();
                echo "<input type='hidden' name='order_id' value='" . e($order['id']) . "'>";
                echo "<button type='submit' class='paiement-button'>Confirmer le paiement</button>";
                echo "</form>";

                if (isset($stmt_items)) {
                    $stmt_items->close();
                }
            }
        }
        $connexion->close();
        ?>
    </div>
    <?php include("includes/footer.php"); ?>
