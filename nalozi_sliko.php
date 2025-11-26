<?php
    require_once 'config.php';
    session_start();

    // vzpostavi povezavo
    $conn = connect_db();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $naslov = $_POST['naslov'] ?? null;
        $opis = $_POST['opis'] ?? null;
        $url = $_POST['slika_url'] ?? null;
        
        // Pridobi ID prijavljenega uporabnika
        $uporabnik_id = $_SESSION['uporabnik_id'] ?? null;
        if (!$uporabnik_id) {
            die("Napaka: uporabnik ni prijavljen.");
        }

        $pot_do_slike = "";

        // Upload slike
        if (!empty($_FILES['slika_napredka']['name'])) {
            $targetDir = "slike/";
            
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . "_" . basename($_FILES["slika_napredka"]["name"]);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["slika_napredka"]["tmp_name"], $targetFile)) {
                $pot_do_slike = $targetFile;
            }
        }

        // Če je dodan URL
        if (empty($pot_do_slike) && !empty($url)) {
            $pot_do_slike = $url;
        }

        if (empty($pot_do_slike)) {
            die("Napaka: Ni slike.");
        }

        // SQL za vnos v bazo
        $sql = "INSERT INTO slika_napredka (uporabnik_id, naslov, opis, pot_do_slike, datum_nalozitve) 
                VALUES (?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $uporabnik_id, $naslov, $opis, $pot_do_slike);

        $stmt->execute();

        // VRNEMO SE NA ISTO STRAN
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
?>