<?php
    // Ta preprosta PHP koda določa, katero vsebino vključiti v glavni del.
    // To je osrednja datoteka, ki se bo uporabljala kot "stran" po prijavi.

    // Privzeta vsebina, če ni določena, je domaca.stran.php (Nadzorna plošča).
    $content_page = $_GET['content'] ?? 'domaca.stran';

    // Preslikava URL-jev na dejanske PHP datoteke (za dinamično vključitev)
    $allowed_pages = [
        'domaca.stran' => 'domaca.stran.php',
        'zapisnik.treningov' => 'zapisnik.treningov.php',
        'statistika' => 'statistika.php',
        'galerija.slik' => 'galerija.slik.php',
        'profil' => 'profil.php',
        'dodaj.trening' => 'dodaj.trening.php',
    ];

    $file_to_include = $allowed_pages[$content_page] ?? 'domaca.stran.php';

    // Lahko bi dodali tudi PHP logiko za preverjanje avtentikacije tukaj:
    // if (!is_authenticated()) { header('Location: login.php'); exit; }
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FitTrack - Nadzorna Plošča</title>
        <!-- Vključitev GLOBALNIH STILOV (index.css) -->
        <link rel="stylesheet" href="index.css"> 
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>
        <body>
            <!-- Glavni Kontejner -->
            <div class="app-container">
                
                <!-- LEVA STRAN: Stranska navigacija (vsebina iz nav.bar.php) -->
                <?php include 'nav.bar.php';?> 
                
                <!-- DESNA STRAN: Glavna Vsebina -->
                <div class="main-content">
                    
                    <!-- Vključitev vsebine glede na URL parameter 'content' (npr. domaca.stran.php, statistika.php, itd.) -->
                    <?php 
                    if (file_exists($file_to_include)) {
                        include $file_to_include;
                    } else {
                        // Prikaz preprostega bloka za napako 404
                        echo '<div class="dashboard-block"><h1>404 Napaka</h1><p class="subtitle">Zahtevana stran vsebine ni bila najdena.</p></div>';
                    }
                    ?>
                </div>
            </div>
        </body>
</html>