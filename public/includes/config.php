<?php
function obtenirConnexion()
{
    $serveur = "localhost";
    $utilisateur = "bonneton";
    $motdepasse = "bonneton";
    $basededonnees = "site";
    $connexion = new mysqli($serveur, $utilisateur, $motdepasse, $basededonnees);
    if ($connexion->connect_error) {
        die("Connexion échouée: " . $connexion->connect_error);
    } else {
        return $connexion;
    }
}

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
