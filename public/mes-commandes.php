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
<title>Mes commandes</title>
<link rel="stylesheet" href="assets/css/commandes.css">
</head>

<body>
    <?php include("includes/menu.php"); ?>
    <section class="haut reveal">
        <h1>Mes <i>commandes</i></h1>
    </section>
    <div class="container commandes-container">
        <?php
        $user_id = $_SESSION['id'];
        $stmt_orders = $connexion->prepare("SELECT id, status, total_cents, currency, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt_orders->bind_param("i", $user_id);
        $stmt_orders->execute();
        $result_orders = $stmt_orders->get_result();

        $en_cours = [];
        $livrees = [];
        $retours = [];
        $order_ids = [];

        $status_livrees = ['delivered'];
        $status_retours = ['return_requested', 'returned'];

        while ($order = $result_orders->fetch_assoc()) {
            $status = $order['status'] ?? '';
            $order_ids[] = (int) $order['id'];
            if (in_array($status, $status_livrees, true)) {
                $livrees[] = $order;
            } elseif (in_array($status, $status_retours, true)) {
                $retours[] = $order;
            } else {
                $en_cours[] = $order;
            }
        }

        $order_items_map = [];
        if (!empty($order_ids)) {
            $placeholders = implode(',', array_fill(0, count($order_ids), '?'));
            $types = str_repeat('i', count($order_ids));
            $sql_items = "SELECT oi.order_id, a.article_name, COALESCE(ai.url, a.img) AS image_url FROM order_items oi JOIN article a ON a.id = oi.article_id LEFT JOIN article_images ai ON ai.article_id = a.id AND ai.is_primary = 1 WHERE oi.order_id IN ($placeholders) ORDER BY oi.order_id DESC, oi.id ASC";
            $stmt_items = $connexion->prepare($sql_items);
            $stmt_items->bind_param($types, ...$order_ids);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();
            while ($item = $result_items->fetch_assoc()) {
                $order_id = (int) $item['order_id'];
                if (!isset($order_items_map[$order_id])) {
                    $order_items_map[$order_id] = [];
                }
                if (count($order_items_map[$order_id]) < 2) {
                    $order_items_map[$order_id][] = $item;
                }
            }
            $stmt_items->close();
        }

        $stmt_orders->close();
        $connexion->close();
        ?>
        <div class="commande-section reveal">
            <h2>En cours</h2>
            <?php if (!empty($en_cours)) : ?>
                <?php foreach ($en_cours as $order) : ?>
                    <a class="commande-card" href="commande-details.php?id=<?php echo e($order['id']); ?>">
                        <?php if (!empty($order_items_map[$order['id']])) : ?>
                            <div class="commande-thumbs">
                                <?php foreach ($order_items_map[$order['id']] as $item) : ?>
                                    <img src="<?php echo e($item['image_url']); ?>" alt="<?php echo e($item['article_name']); ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="commande-meta">
                            <p class="commande-title">Commande #<?php echo e($order['id']); ?></p>
                            <span class="commande-status"><?php echo e($order['status']); ?></span>
                        </div>
                        <p>Total : <?php echo e(format_price($order['total_cents'], $order['currency'])); ?></p>
                        <p>Date : <?php echo e($order['created_at']); ?></p>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="commande-empty">Aucune commande en cours.</p>
            <?php endif; ?>
        </div>

        <div class="commande-section reveal">
            <h2>Livrées</h2>
            <?php if (!empty($livrees)) : ?>
                <?php foreach ($livrees as $order) : ?>
                    <a class="commande-card" href="commande-details.php?id=<?php echo e($order['id']); ?>">
                        <?php if (!empty($order_items_map[$order['id']])) : ?>
                            <div class="commande-thumbs">
                                <?php foreach ($order_items_map[$order['id']] as $item) : ?>
                                    <img src="<?php echo e($item['image_url']); ?>" alt="<?php echo e($item['article_name']); ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="commande-meta">
                            <p class="commande-title">Commande #<?php echo e($order['id']); ?></p>
                            <span class="commande-status"><?php echo e($order['status']); ?></span>
                        </div>
                        <p>Total : <?php echo e(format_price($order['total_cents'], $order['currency'])); ?></p>
                        <p>Date : <?php echo e($order['created_at']); ?></p>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="commande-empty">Aucune commande livrée pour le moment.</p>
            <?php endif; ?>
        </div>

        <div class="commande-section reveal">
            <h2>Retours</h2>
            <?php if (!empty($retours)) : ?>
                <?php foreach ($retours as $order) : ?>
                    <a class="commande-card" href="commande-details.php?id=<?php echo e($order['id']); ?>">
                        <?php if (!empty($order_items_map[$order['id']])) : ?>
                            <div class="commande-thumbs">
                                <?php foreach ($order_items_map[$order['id']] as $item) : ?>
                                    <img src="<?php echo e($item['image_url']); ?>" alt="<?php echo e($item['article_name']); ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="commande-meta">
                            <p class="commande-title">Commande #<?php echo e($order['id']); ?></p>
                            <span class="commande-status"><?php echo e($order['status']); ?></span>
                        </div>
                        <p>Total : <?php echo e(format_price($order['total_cents'], $order['currency'])); ?></p>
                        <p>Date : <?php echo e($order['created_at']); ?></p>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="commande-empty">Aucun retour pour le moment. Option de retour bientôt.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include("includes/footer.php"); ?>
