<?php
session_start();
if (!isset($_SESSION['accounttype']) || $_SESSION['accounttype'] !== 'a') {
    header("Location: accueil.php");
    exit();
}
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();
?>
<title>Gestion commandes</title>
<link rel="stylesheet" href="assets/css/admin-base.css">
</head>

<body class="admin-surface">
    <?php include("includes/menu.php"); ?>
    <div class="admin-card">
        <h1 class="admin-title">Commandes</h1>
        <?php
        $stmt = $connexion->prepare("SELECT o.id, o.status, o.total_cents, o.currency, o.created_at, u.email FROM orders o JOIN user u ON u.id = o.user_id ORDER BY o.created_at DESC");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table class='admin-table'>";
            echo "<tr><th>ID</th><th>Client</th><th>Statut</th><th>Total</th><th>Date</th><th>Action</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . e($row['id']) . "</td>";
                echo "<td>" . e($row['email']) . "</td>";
                echo "<td>" . e($row['status']) . "</td>";
                echo "<td>" . e(format_price($row['total_cents'], $row['currency'])) . "</td>";
                echo "<td>" . e($row['created_at']) . "</td>";
                echo "<td>";
                echo "<form method='POST' action='actions/admin-update-commande.php'>";
                echo csrf_field();
                echo "<input type='hidden' name='id' value='" . e($row['id']) . "'>";
                $current_status = $row['status'];
                echo "<select name='status' class='admin-input'>";
                echo "<option value='pending'" . ($current_status === 'pending' ? " selected" : "") . ">pending</option>";
                echo "<option value='paid'" . ($current_status === 'paid' ? " selected" : "") . ">paid</option>";
                echo "<option value='shipped'" . ($current_status === 'shipped' ? " selected" : "") . ">shipped</option>";
                echo "<option value='cancelled'" . ($current_status === 'cancelled' ? " selected" : "") . ">cancelled</option>";
                echo "<option value='refunded'" . ($current_status === 'refunded' ? " selected" : "") . ">refunded</option>";
                echo "</select>";
                echo "<button type='submit' class='admin-button'>Mettre a jour</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='empty-state'>Aucune commande.</p>";
        }
        $stmt->close();
        $connexion->close();
        ?>
    </div>
    <?php include("includes/footer.php"); ?>
