<?php
session_start();
include 'includes/config.php';
$bdd = obtenirConnexion();
?>
<link rel="stylesheet" type="text/css" href="assets/css/accountmanagement-style.css">
<link rel="stylesheet" type="text/css" href="assets/css/admin-base.css">
<?php
//Vérifie que c'est la session d'un admin
if (!isset($_SESSION['accounttype'])) {
    header("Location: accueil.php");
} else {
    if ($_SESSION['accounttype'] !== 'a') {
        header("Location: accueil.php");
        exit();
    } else {

        $user_id = $_SESSION['id'];

        $sql_user = "SELECT * FROM user";
        $result_users = mysqli_query($bdd, $sql_user);

?>

        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Liste des utilisateurs</title>
        </head>
        
        <body class="admin-surface">
            <h1 class="admin-title">Liste des utilisateurs</h1>
            <div id="main">
                <div id="content" class="admin-card">
                    <table class="admin-table">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Supprimer</th>
                        </tr>
                <?php
                // Parcourir les résultats de la requête pour afficher les utilisateurs
                while ($row_user = $result_users->fetch_assoc()) {
                    if ($row_user['accounttype'] !== 'a') {
                        echo "<tr>";
                        echo "<td>" . e($row_user['id']) . "</td>";
                        echo "<td>" . e($row_user['nom']) . "</td>";
                        echo "<td>" . e($row_user['prenom']) . "</td>";
                        echo "<td>";
                        echo "<form method='POST' action='actions/traitement-suppression.php'>";
                        echo csrf_field();
                        echo "<input type='hidden' name='id' value='" . e($row_user['id']) . "'>";
                        echo "<button type='submit' class='admin-button admin-button-ghost'>Supprimer</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
            }
        }

                ?>
                    </table>
                </div>
            </div>
            <a class="admin-button" href="page_admin.php">Revenir à la page admin</a>
        </body>

        </html>
