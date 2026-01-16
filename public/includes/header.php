<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/stylebase.css">
    <link rel="stylesheet" href="assets/css/article.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,300&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,300&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <?php
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
    ?>
