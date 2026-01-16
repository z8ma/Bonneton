<?php
if (!function_exists('obtenirConnexion')) {
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
}

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}
if (!function_exists('verify_csrf')) {
    function verify_csrf()
    {
        return isset($_POST['csrf_token'])
            && isset($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
}
if (!function_exists('format_price')) {
    function format_price($cents, $currency = 'EUR')
    {
        $amount = number_format(((int) $cents) / 100, 2, ',', ' ');
        return $amount . ' ' . $currency;
    }
}
