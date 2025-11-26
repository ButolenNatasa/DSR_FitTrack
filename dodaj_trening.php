<?php
    // Zaženemo sejo, če še ni zagnana
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // 1. VKLJUČITEV IN POVEZAVA Z BAZO
    require_once 'config.php';
    $conn = connect_db();

    // Nastavimo naslov strani
    $page_title = "Dodajanje Treninga";

    // 2. PREVERJANJE PRIJAVE
    $uporabnik_id = $_SESSION['uporabnik_id'] ?? null;
    if (!$uporabnik_id) {
        header("Location: login.php"); // Predpostavimo, da imate stran za prijavo
        exit();
    }

    // 3. OBRAVNAVA OBRAZCA
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datum = $_POST['datum'] ?? null;
    $tip_treninga = $_POST['tip'] ?? null;
    $trajanje = $_POST['trajanje'] ?? null;
    $kalorije = $_POST['kalorije'] ?? null;
    $opis = $_POST['opis'] ?? null;
        // Odstranjeno: $intenzivnost = $_POST['intensity'] ?? null;


    $sql = "INSERT INTO trening 
    (uporabnik_id, datum, tip_treninga, trajanje_min, porabljene_kalorije, opombe)
    VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
            
            // Vezava parametrov: 6 parametrov, odstranjen `$intenzivnost`
            // "ississ": i:uporabnik_id, s:datum, s:tip, i:trajanje, s:kalorije (Decimal), s:opis
    $stmt->bind_param(
    "issiis", 
    $uporabnik_id, 
    $datum, 
    $tip_treninga, 
    $trajanje, 
    $kalorije, 
    $opis
    );
    if ($stmt->execute()) {

    header("Location: dodaj_trening.php?status=success");
    exit();
    } else {
    $error_message = "Napaka pri shranjevanju: " . $stmt->error;
    }
            $stmt->close();
    }


    $conn->close();

    // Preveri, ali je prišlo do preusmeritve po uspešnem vnosu
    $success_message = '';
    if (isset($_GET['status']) && $_GET['status'] == 'success') {
        $success_message = "Trening je bil uspešno shranjen!";
    }
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FitTrack - <?php echo $page_title; ?></title>
        <link rel="stylesheet" href="style.css"> 
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="index.css"> 
    </head>
        <body>
            
            <div class="app-container">
                
                <?php include 'nav.bar.php';?>
                
                <div class="main-content">
                    
                    <div class="dashboard-header">
                        <div>
                            <h1><?php echo $page_title; ?></h1>
                            <p class="subtitle">Vnesite podrobnosti o svojem zadnjem treningu.</p>
                        </div>
                    </div>
                    <?php if (!empty($success_message)): ?>
                        <div class="message-box success">
                            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="message-box error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="dashboard-block auth-form">
                        
                        <form action="dodaj_trening.php" method="POST">
                            
                            <div class="form-grid">
                                
                                <div class="form-group">
                                    <label for="datum">Datum treninga</label>
                                    <input type="date" id="datum" name="datum" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tip">Vrsta treninga</label>
                                    <select id="type" name="tip" required>
                                        <option value="moč">Trening moči (Weightlifting)</option>
                                        <option value="kardio">Kardio (Running/Cycling)</option>
                                        <option value="hiit">HIIT</option>
                                        <option value="joga">Joga/Fleksibilnost</option>
                                        <option value="drugo">Drugo</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="trajanje">Trajanje (v minutah)</label>
                                    <input type="number" id="trajanje" name="trajanje" placeholder="Npr. 60" min="1" required>
                                </div>

                                <div class="form-group">
                                    <label for="kalorije">Porabljene kalorije (pribl.)</label>
                                    <input type="number" id="kalorije" name="kalorije" placeholder="Npr. 500" min="0">
                                </div>
                                
                            </div>
                            
                            <div class="form-group full-width" style="margin-bottom: 20px;">
                                <label for="opis">Opis treninga in vaje (Neobvezno)</label>
                                <textarea id="opis" name="opis" rows="4" placeholder="Npr. '5x5 počep, 3x8 potisk s prsi, 30 min tek'"></textarea>
                            </div>
                            
                            <div class="form-group full-width" style="margin-top: 30px;">
                                <button type="submit" class="action-button">
                                    <i class="fas fa-save"></i> Shrani trening
                                </button>
                            </div>
                            
                        </form>

                    </div>
                    
                </div>
            </div>
            
        </body>
</html>