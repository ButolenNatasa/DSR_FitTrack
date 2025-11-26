<?php
    session_start();
    require_once 'config.php';
    $conn = connect_db();

    // preveri prijavo
    $uporabnik_id = $_SESSION['uporabnik_id'] ?? null;
    if (!$uporabnik_id) {
        header("Location: login.php");
        exit();
    }

    // preveri ID treninga
    if (!isset($_GET['id'])) {
        header("Location: zapisnik.treningov.php");
        exit();
    }

    $trening_id = intval($_GET['id']);

    // izbriši le, če trening pripada temu uporabniku
    $sql = "DELETE FROM trening WHERE trening_id = ? AND uporabnik_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $trening_id, $uporabnik_id);
    $stmt->execute();
    $stmt->close();

    header("Location: zapisnik.treningov.php");
    exit();
    ?>
