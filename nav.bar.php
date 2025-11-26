<?php
    // Ta datoteka vsebuje samo vsebino stranske navigacije in jo je treba vključiti na vsako stran aplikacije.

    // 1. Določitev imena trenutne datoteke (npr. 'domaca.stran.php')
    $current_file = basename($_SERVER['PHP_SELF']);

    // 2. Funkcija za preverjanje in vračanje razreda 'active'
    function get_active_class($current_page, $target_page) {
        // Posebno ravnanje za Zapisnik Treningov:
        // Povezava mora biti aktivna tudi, ko je uporabnik na 'dodaj_trening.php' (glede na prejšnje vprašanje)
        if ($target_page === 'zapisnik.treningov.php' && ($current_page === 'dodaj_trening.php')) {
            return 'active';
        }
        
        if ($current_page === $target_page) {
            return 'active';
        }
        return '';
    }
?>

<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-heartbeat logo-icon" style="color: #9370DB;"></i>
        <h2>FitTrack</h2>
    </div>

    <nav class="nav-menu">
        
        <a href="domaca.stran.php" class="nav-item <?php echo get_active_class($current_file, 'domaca.stran.php'); ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Nadzorna Plošča</span>
        </a>
        
        <a href="zapisnik.treningov.php" class="nav-item 
            <?php echo get_active_class($current_file, 'zapisnik.treningov.php'); ?>">
            <i class="fas fa-clipboard-list"></i>
            <span>Zapisnik Treningov</span>
        </a>
        
        <a href="statistika.php" class="nav-item <?php echo get_active_class($current_file, 'statistika.php'); ?>">
            <i class="fas fa-chart-line"></i>
            <span>Statistika</span>
        </a>
        
        <a href="galerija.slik.php" class="nav-item <?php echo get_active_class($current_file, 'galerija.slik.php'); ?>">
            <i class="fas fa-camera"></i>
            <span>Slike Napredka</span>
        </a>
        
        <a href="profil.php" class="nav-item <?php echo get_active_class($current_file, 'profil.php'); ?>">
            <i class="fas fa-user-circle"></i>
            <span>Profil / Nastavitve</span>
        </a>
    </nav>

    <div class="logout-section">
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Odjava</span>
        </a>
    </div>
</div>