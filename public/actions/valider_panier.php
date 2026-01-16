<?php
session_start();
include '../includes/config.php';
$connexion = obtenirConnexion();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    header("Location: ../panier.php?error=*Requete invalide.");
    exit;
}
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}
$user_id = $_SESSION['id'];
$stmt_select = $connexion->prepare("SELECT p.article_id, p.qty, a.price_cents FROM panier p JOIN article a ON a.id = p.article_id WHERE p.user_id = ?");
$stmt_select->bind_param("i", $user_id);
$stmt_select->execute();
$resultat_select_panier = $stmt_select->get_result();

if ($resultat_select_panier) {

    $total_cents = 0;
    $items = [];
    while ($row = $resultat_select_panier->fetch_assoc()) {
        $total_cents += ((int) $row['price_cents']) * ((int) $row['qty']);
        $items[] = $row;
    }
    if (count($items) === 0) {
        header("Location: ../panier.php?error=*Panier vide.");
        exit;
    }

    $stmt_addr = $connexion->prepare("SELECT id FROM addresses WHERE user_id = ? AND type = 'shipping' ORDER BY created_at DESC LIMIT 1");
    $stmt_addr->bind_param("i", $user_id);
    $stmt_addr->execute();
    $result_addr = $stmt_addr->get_result();
    $address_row = $result_addr->fetch_assoc();
    $stmt_addr->close();
    if (!$address_row) {
        header("Location: ../adresse.php?error=*Ajoutez une adresse de livraison avant de valider.");
        exit;
    }
    $shipping_address_id = (int) $address_row['id'];
    $billing_address_id = $shipping_address_id;

    $stmt_order = $connexion->prepare("INSERT INTO orders (user_id, status, total_cents, currency, created_at, shipping_address_id, billing_address_id) VALUES (?, 'pending', ?, 'EUR', NOW(), ?, ?)");
    $stmt_order->bind_param("iiii", $user_id, $total_cents, $shipping_address_id, $billing_address_id);
    $stmt_order->execute();
    $order_id = $connexion->insert_id;
    $stmt_order->close();

    $stmt_payment = $connexion->prepare("INSERT INTO payments (order_id, provider, status, amount_cents, currency, provider_ref) VALUES (?, 'manual', 'pending', ?, 'EUR', NULL)");
    $stmt_payment->bind_param("ii", $order_id, $total_cents);
    $stmt_payment->execute();
    $payment_id = $connexion->insert_id;
    $stmt_payment->close();

    $stmt_item = $connexion->prepare("INSERT INTO order_items (order_id, article_id, qty, unit_price_cents, total_cents) VALUES (?, ?, ?, ?, ?)");
    $stmt_update = $connexion->prepare("UPDATE article SET nb_vente = nb_vente + ?, stock = GREATEST(stock - ?, 0), status = IF(stock - ? <= 0, 'out_of_stock', status) WHERE id = ?");
    foreach ($items as $row) {
        $article_id = (int) $row['article_id'];
        $qty = (int) $row['qty'];
        $unit_price = (int) $row['price_cents'];
        $line_total = $unit_price * $qty;

        $stmt_item->bind_param("iiiii", $order_id, $article_id, $qty, $unit_price, $line_total);
        $stmt_item->execute();

        $stmt_update->bind_param("iiii", $qty, $qty, $qty, $article_id);
        $stmt_update->execute();
    }
    $stmt_item->close();
    $stmt_update->close();
} else {
    echo "Erreur lors de la sélection des articles du panier.";
}
$stmt_select->close();

header("Location: ../paiement.php?order_id=" . $order_id);
exit;
