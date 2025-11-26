<?php
    session_start();
    require_once 'config.php';
    $conn = connect_db();

    // Preveri prijavo
    $uporabnik_id = $_SESSION['uporabnik_id'] ?? null;
    if (!$uporabnik_id) {
        die("Napaka: uporabnik ni prijavljen.");
    }

    // Naloži vse treninge
    $sql = "SELECT * FROM trening WHERE uporabnik_id = ? ORDER BY datum ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uporabnik_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $workouts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // -------------------------------------------
    // PDF generator (FPDF – vgrajen, brez instalacij)
    // -------------------------------------------
    require('fpdf/fpdf.php');
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 18);

    // Naslov
    $pdf->Cell(0, 15, "FitTrack - Poročilo o Treningih", 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "Uporabnik ID: " . $uporabnik_id, 0, 1);

    // --------------------------------------------------
    // Povzetek – minutaže in kalorije
    // --------------------------------------------------
    $total_minutes = 0;
    $total_calories = 0;

    foreach ($workouts as $w) {
        $total_minutes += intval($w['trajanje_min']);
        $total_calories += intval($w['porabljene_kalorije']);
    }

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Povzetek Treningov", 0, 1);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, "Skupni treningi: " . count($workouts), 0, 1);
    $pdf->Cell(0, 8, "Skupni cas: " . $total_minutes . " min", 0, 1);
    $pdf->Cell(0, 8, "Porabljene kalorije: " . $total_calories . " cal", 0, 1);

    $pdf->Ln(5);

    // --------------------------------------------------
    // Seznam treningov
    // --------------------------------------------------
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Zgodovina Treningov", 0, 1);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(35, 8, "Datum", 1);
    $pdf->Cell(35, 8, "Vrsta", 1);
    $pdf->Cell(25, 8, "Min", 1);
    $pdf->Cell(30, 8, "Kalorije", 1);
    $pdf->Cell(60, 8, "Opombe", 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);

    foreach ($workouts as $w) {
        $pdf->Cell(35, 8, date('d.m.Y', strtotime($w['datum'])), 1);
        $pdf->Cell(35, 8, $w['tip_treninga'], 1);
        $pdf->Cell(25, 8, $w['trajanje_min'] . " min", 1);
        $pdf->Cell(30, 8, $w['porabljene_kalorije'] . " cal", 1);
        $pdf->Cell(60, 8, $w['opombe'], 1);
        $pdf->Ln();
    }

    $pdf->Output();
?>