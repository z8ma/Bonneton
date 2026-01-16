<?php
session_start();
include("includes/header.php");
include 'includes/config.php';
$connexion = obtenirConnexion();
$categories = [];
$stmt_categories = $connexion->prepare("SELECT id, name FROM categories ORDER BY name ASC");
$stmt_categories->execute();
$result_categories = $stmt_categories->get_result();
while ($cat = $result_categories->fetch_assoc()) {
    $categories[] = $cat;
}
$stmt_categories->close();
$connexion->close();
?>
<title>Vendez vos articles</title>
</head>
<link rel="stylesheet" href="assets/css/vente-style.css">

<body>
    <?php
    include("includes/menu.php");
    ?>

    <body>

        <div id="main">
            <div id="fullcontent">
                <div id="top-content">
                    <div id="top-content-gauche">
                        <h1 id="head-page">AJOUTER UN ARTICLE</h1>
                    </div>
                </div>

                <div id="content">

                    <form method="POST" action="actions/traitement-vente.php">
                        <?php echo csrf_field(); ?>
                        <div id="text-content">
                            <label for="nom" class="labtext">Nom de l'article </label>
                            <br /><br />
                            <input type="text" class="text-input" size="50" id="nom" name="nom" placeholder="Nom du produit ...">
                            <br /><br />
                            <label for="caract" class="labtext">Caractéristiques</label>
                            <br /><br />
                            <input type="text" class="text-input" size="200" id="caract" name="caract" placeholder="Description de votre article ...">
                            <br /><br />

                            <label for="img" class="labtext">Ajouter une image</label>
                            <br /><br />
                            <input type="text" class="text-input" size="200" id="img" name="img" placeholder="Lien de votre image ...">
                            <br /><br />
                            <label for="category_id" class="labtext">Categorie</label>
                            <br /><br />
                            <select id="category_id" name="category_id" class="text-input">
                                <option value="">Aucune</option>
                                <?php foreach ($categories as $cat) { ?>
                                    <option value="<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></option>
                                <?php } ?>
                            </select>
                            <br /><br />
                            <label for="prix" class="labtext">Prix </label><br /><br />
                            <input class="text-input" id="prix" type="number" name="prix" placeholder="100 ..." />
                            <br /><br />
                            <label for="stock" class="labtext">Stock </label><br /><br />
                            <input class="text-input" id="stock" type="number" name="stock" placeholder="10 ..." />
                            <br /><br />
                            <input type="checkbox" id="notif" name="notif">
                            <label for="notif" id="cochetexte">En cochant ici, vous recevrez une notification pour chaque article vendu.</label>
                            <br /><br />

                            <input type="submit" id="bouton-ajouter" value="Ajouter" name="ajout">
                            <br /><br /><br />

                            <p id="politique-texte">En vendant un article, vous acceptez notre politique commerciale. Veuillez lire notre <a style="text-decoration: none;" href="politique.php">politique commerciale</a>.</p>

                            <p style="color: red; font-family : sans-serif; font-size: 11px">
                                <?php if (isset($_GET['error'])) {
                                    echo e($_GET['error']);
                                } ?></p>
                            <br />
                        </div>
                    </form>

                </div>
            </div>
        </div>



    </body>

    </html>

    <?php
    include("includes/footer.php");
    ?>
