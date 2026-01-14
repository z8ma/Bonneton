<?php
session_start();
include 'includes/config.php';
$bdd = obtenirConnexion();
?>
<link rel="stylesheet" type="text/css" href="assets/css/accountmanagement-style.css">
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
        
        <body>
            <h1>Liste des utilisateurs</h1>
            <div id="main">
                <div id="content">
                    <table>
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
                        echo "<td><a href='actions/traitement-suppression.php?id=" . $row_user['id'] . "'>Supprimer</a></td>";
                        echo "</tr>";
                    }
                }
            }
        }

                ?>
                    </table>
                </div>
            </div>
            <a class=button href="page_admin.php">Revenir à la page admin</a>
        </body>

        </html>
