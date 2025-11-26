<?php
// config.php
// Nastavitve za povezavo z bazo podatkov
define('DB_SERVER', 'localhost'); // Host: 127.0.0.1
define('DB_USERNAME', 'root');   // Predpostavljena uporabnik in geslo za XAMPP/MAMP
define('DB_PASSWORD', '');
define('DB_NAME', 'dsr_fittrack'); // Ime baze iz SQL dumpa

// Funkcija za vzpostavitev povezave z bazo
function connect_db() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Preveri povezavo
    if ($conn->connect_error) {
        // V produkciji prikaži le splošno napako, ne tehnične podrobnosti
        die("Povezava z bazo podatkov ni uspela: " . $conn->connect_error);
    }
    
    // Nastavi kodiranje na UTF-8
    $conn->set_charset("utf8mb4");
    
    return $conn;
}
?>