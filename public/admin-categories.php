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
<title>Gestion categories</title>
<link rel="stylesheet" href="assets/css/admin-base.css">
</head>

<body class="admin-surface">
    <?php include("includes/menu.php"); ?>
    <div class="admin-card">
        <h1 class="admin-title">Categories</h1>
        <form method="POST" action="actions/admin-add-category.php" class="admin-form">
            <?php echo csrf_field(); ?>
            <input type="text" name="name" class="admin-input" placeholder="Nouvelle categorie" />
            <button type="submit" class="admin-button">Ajouter</button>
        </form>
        <?php
        $stmt = $connexion->prepare("SELECT id, name FROM categories ORDER BY name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . e($row['name']) . " ";
                echo "<form method='POST' action='actions/admin-delete-category.php' style='display:inline'>";
                echo csrf_field();
                echo "<input type='hidden' name='id' value='" . e($row['id']) . "'>";
                echo "<button type='submit' class='admin-button admin-button-ghost'>Supprimer</button>";
                echo "</form>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='empty-state'>Aucune categorie.</p>";
        }
        $stmt->close();
        $connexion->close();
        ?>
    </div>
    <?php include("includes/footer.php"); ?>
