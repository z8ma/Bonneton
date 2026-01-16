<?php
session_start();
include("includes/header.php");

?>
<title>Bienvenue sur Bonneton</title>
<link rel="stylesheet" href="assets/css/Apropos-style.css">

</head>

<body>
    <?php
    include("includes/menu.php");
    ?>
    <section class="ss">
        <h1>Une histoire <i>innovante</i>, et l'amour de la <i>création</i> textile depuis 2023</h1>
        <div class="apropos-video">
            <video autoplay loop playsinline controls preload="metadata" poster="img/photo1.jpeg">
                <source src="img/Chaleur et Élégance Artisanale.mp4" type="video/mp4">
                Votre navigateur ne supporte pas la lecture vidéo.
            </video>
        </div><br>
    </section>
    <br><br>


    <?php
    include("includes/footer.php");
    ?>
