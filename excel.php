<?php
    session_start();
    require_once 'config.php';
    $conn = connect_db();

    // Preveri prijavo
    $uporabnik_id = $_SESSION['uporabnik_id'] ?? null;
    if (!$uporabnik_id) {
        die("Napaka: uporabnik ni prijavljen.");
    }

    // Pridobi podatke
    $sql = "SELECT datum, tip_treninga, trajanje_min, porabljene_kalorije, opombe 
            FROM trening WHERE uporabnik_id = ? ORDER BY datum ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uporabnik_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $workouts = $result->fetch_all(MYSQLI_ASSOC);

    // Nastavitve glave za prenos datoteke
    $filename = "FitTrack_Treningi_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '";');

    // Odpri "output" tok za pisanje
    $f = fopen('php://output', 'w');

    // DODAJ BOM za pravilne šumnike v Excelu (zelo pomembno!)
    fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));

    // Glava tabele (stolpci)
    fputcsv($f, ['Datum', 'Tip Treninga', 'Trajanje (min)', 'Kalorije (cal)', 'Opombe'], ';');

    // Zapisi podatke
    foreach ($workouts as $row) {
        // Preoblikujemo datum v lepši format za Excel
        $row['datum'] = date('d.m.Y', strtotime($row['datum']));
        fputcsv($f, $row, ';');
    }

    fclose($f);
    exit;
?>