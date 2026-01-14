<?php
//affiche les commantaires selon la date de publication
$stmt_comments = $connexion->prepare("SELECT c.contenu, c.date_commentaire, c.img, u.prenom FROM commentaires c JOIN user u ON c.user_id = u.id WHERE c.article_id = ? ORDER BY c.date_commentaire DESC LIMIT 3");
$stmt_comments->bind_param("i", $article_id);
$stmt_comments->execute();
$resultat_afficher_com = $stmt_comments->get_result();

if ($resultat_afficher_com->num_rows > 0) {
    echo "<div class='commentaires'>";
    echo "<h3>Commentaires</h3>";
    while ($row_com = $resultat_afficher_com->fetch_assoc()) {
        $prenom = $row_com['prenom'];

        echo '<div class="commentaire">';
        echo '<div class="commentaire-content">';
        echo '<p>' . $prenom . ' - <em>' . $row_com['date_commentaire'] . '</em></p>';
        echo '<p>' . $row_com['contenu'] . '</p>';
        echo '</div>';
        echo '</div>';
    }


    echo "<div class='comment-form'>";
    echo "<h3>Ajouter un commentaire</h3>";
    echo "<form method='post' action=''>";
    echo "<textarea name='commentaire' placeholder='Votre commentaire...'></textarea><br>";
    echo "<input type='text' class='text-input' size='25' id='img' name='img' placeholder='Lien de votre image ...'><br /><br />";
    echo "<input type='submit' value='Envoyer'name='Envoyer'>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
} else {
    echo "Pas de commentaires sous cet article";
}
$stmt_comments->close();
