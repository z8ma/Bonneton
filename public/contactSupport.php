<?php
session_start();
include("includes/header.php");
?>
<title>Support</title>
<link rel="stylesheet" href="assets/css/support.css">
</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <section class="haut reveal">
        <h1>Service <i>Client</i></h1>
    </section>
    <div class="support-card reveal">
        <p>Besoin d'aide ? Contactez-nous :</p>
        <div class="support-actions">
            <a class="support-link" href="mailto:contact@bonneton.com">contact@bonneton.com</a>
            <span class="support-sep">ou</span>
            <a class="support-link" href="tel:+33123456789">+33 1 23 45 67 89</a>
        </div>
        <p class="support-note">Réponse sous 24h ouvrées.</p>
    </div>
    <?php
    include("includes/footer.php");
    ?>
