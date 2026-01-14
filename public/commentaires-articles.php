<?php
//affiche les commantaires selon la date de publication
$afficher_com = "SELECT * FROM commentaires WHERE article_id = $article_id ORDER BY date_commentaire DESC LIMIT 3";
$resultat_afficher_com = mysqli_query($connexion, $afficher_com);

if ($resultat_afficher_com->num_rows > 0) {
    echo "<div class='commentaires'>";
    echo "<h3>Commentaires</h3>";
    while ($row_com = mysqli_fetch_assoc($resultat_afficher_com)) {
        $user_com = $row_com['user_id'];
        $requete_prenom = "SELECT prenom FROM user WHERE id = $user_com";
        $resultat_prenom = mysqli_query($connexion, $requete_prenom);
        $row_prenom = mysqli_fetch_assoc($resultat_prenom);
        $prenom = $row_prenom['prenom'];

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
