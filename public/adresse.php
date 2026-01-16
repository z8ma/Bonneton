<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
include("includes/header.php");
?>
<title>Mes adresses</title>
<link rel="stylesheet" href="assets/css/adresse.css">
</head>

<body class="adresse-page">
    <?php include("includes/menu.php"); ?>
    <div id="main">
        <div id="fullcontent">
            <div id="top-content">
                <div id="top-content-gauche">
                    <h1 id="head-page">MES ADRESSES</h1>
                </div>
            </div>
            <?php
            include 'includes/config.php';
            $connexion = obtenirConnexion();
            $user_id = $_SESSION['id'];
            $stmt_addr = $connexion->prepare("SELECT id, type, line1, line2, city, zip, country, phone FROM addresses WHERE user_id = ? ORDER BY created_at DESC");
            $stmt_addr->bind_param("i", $user_id);
            $stmt_addr->execute();
            $result_addr = $stmt_addr->get_result();
            $addresses = [];
            while ($addr = $result_addr->fetch_assoc()) {
                $addresses[] = $addr;
            }
            $stmt_addr->close();
            $connexion->close();
            $show_form = isset($_GET['add']) || count($addresses) === 0;
            ?>
            <div id="content" class="adresse-list">
                <h3>Adresses existantes</h3>
                <?php if (count($addresses) > 0) : ?>
                    <ul class="adresse-items">
                        <?php foreach ($addresses as $addr) :
                            $line2 = $addr['line2'] ? " - " . e($addr['line2']) : "";
                        ?>
                            <li class="adresse-item">
                                <div class="adresse-item-text">
                                    <?php echo e($addr['type']) . " : " . e($addr['line1']) . $line2 . ", " . e($addr['zip']) . " " . e($addr['city']) . ", " . e($addr['country']); ?>
                                </div>
                                <form method="POST" action="actions/supprimer-adresse.php" class="adresse-delete-form">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="address_id" value="<?php echo e($addr['id']); ?>">
                                    <button type="submit" class="adresse-button adresse-button-danger">Supprimer</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="adresse-empty">Oups, vous n'avez aucune adresse. Ajoutez-en une.</p>
                <?php endif; ?>
                <div class="adresse-actions">
                    <a class="adresse-button" href="adresse.php?add=1#adresse-form">Ajouter une adresse</a>
                </div>
                <p style="color: red; font-family : sans-serif; font-size: 11px"><?php if (isset($_GET['error'])) { echo e($_GET['error']); } ?></p>
            </div>
            <?php if ($show_form) : ?>
                <div id="content" class="adresse-form">
                    <div id="adresse-form"></div>
                    <form method="POST" action="actions/traitement-adresse.php">
                        <?php echo csrf_field(); ?>
                        <label for="type" class="labtext">Type</label><br /><br />
                        <select id="type" name="type" class="text-input">
                            <option value="shipping">Livraison</option>
                            <option value="billing">Facturation</option>
                        </select>
                        <br /><br />
                        <label for="line1" class="labtext">Adresse</label><br /><br />
                        <input type="text" class="text-input" id="line1" name="line1" placeholder="12 rue ..." />
                        <br /><br />
                        <label for="line2" class="labtext">Complement</label><br /><br />
                        <input type="text" class="text-input" id="line2" name="line2" placeholder="Batiment, etage ..." />
                        <br /><br />
                        <label for="city" class="labtext">Ville</label><br /><br />
                        <input type="text" class="text-input" id="city" name="city" placeholder="Paris" />
                        <br /><br />
                        <label for="zip" class="labtext">Code postal</label><br /><br />
                        <input type="text" class="text-input" id="zip" name="zip" placeholder="75000" />
                        <br /><br />
                        <label for="country" class="labtext">Pays</label><br /><br />
                        <input type="text" class="text-input" id="country" name="country" placeholder="France" />
                        <br /><br />
                        <label for="phone" class="labtext">Telephone</label><br /><br />
                        <input type="text" class="text-input" id="phone" name="phone" placeholder="+33..." />
                        <br /><br />
                        <input type="submit" name="save" value="Sauvegarder" id="bouton">
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

<?php include("includes/footer.php"); ?>
