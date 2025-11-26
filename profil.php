<?php
    // profil.php

    // 1. ZAČETEK SEJE IN ZAŠČITA STRANI
    session_start();

    // Vključitev konfiguracije baze podatkov
    require_once 'config.php'; 

    // Preveri, ali je uporabnik prijavljen, sicer preusmeri na prijavno stran
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('location: login.php');
        exit;
    }

    $uporabnik_id = $_SESSION['uporabnik_id'];
    $user_data = []; // Za shranjevanje vseh podatkov uporabnika
    $error_msg = '';
    $success_msg = '';

    // 2. PRIDOBIVANJE PODATKOV UPORABNIKA IN CILJEV
    $conn = connect_db();

    // Združitev podatkov o uporabniku, vlogi in zadnjem cilju
    $sql = "
        SELECT 
            u.ime, u.priimek, u.email, u.datum_rojstva, u.visina, u.teza, u.geslo,
            v.naziv_vloge AS vloga_naziv, 
            c.tip_cilja
        FROM 
            uporabnik u
        JOIN 
            vloga v ON u.vloga_id = v.vloga_id
        LEFT JOIN 
            cilj c ON u.uporabnik_id = c.uporabnik_id
        WHERE 
            u.uporabnik_id = ?
        ORDER BY c.cilj_id DESC 
        LIMIT 1
    ";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $uporabnik_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user_data = $result->fetch_assoc();
        } else {
            $error_msg = 'Napaka: Podatkov o uporabniku ni bilo mogoče naložiti.';
        }
        $stmt->close();
    }

    // 3. UPRAVLJANJE OBRAZCEV (Vse logike posodobitev so tukaj)

    function update_password($conn, $uporabnik_id, $current_password, $new_password, $confirm_password, $hashed_current_password) {
        global $error_msg, $success_msg;
        
        if (!password_verify($current_password, $hashed_current_password)) {
            $error_msg = 'Trenutno geslo ni pravilno.';
            return;
        }
        
        if ($new_password !== $confirm_password) {
            $error_msg = 'Novo geslo in potrditev se ne ujemata.';
            return;
        }
        
        if (strlen($new_password) < 6) {
            $error_msg = 'Novo geslo mora imeti vsaj 6 znakov.';
            return;
        }
        
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE uporabnik SET geslo = ? WHERE uporabnik_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('si', $new_hashed_password, $uporabnik_id);
            if ($stmt->execute()) {
                $success_msg = 'Geslo je bilo uspešno spremenjeno.';
            } else {
                $error_msg = 'Napaka pri shranjevanju gesla.';
            }
            $stmt->close();
        } else {
            $error_msg = 'Priprava SQL stavka za geslo ni uspela.';
        }
    }


    function update_personal_data($conn, $uporabnik_id, $ime_priimek, $datum_rojstva) {
        global $error_msg, $success_msg;
        
        $name_parts = explode(' ', $ime_priimek, 2);
        $ime = $name_parts[0];
        $priimek = isset($name_parts[1]) ? $name_parts[1] : ''; 
        
        if (empty($ime) || empty($priimek)) {
            $error_msg = 'Vnesite celotno Ime in Priimek.';
            return;
        }

        $sql = "UPDATE uporabnik SET ime = ?, priimek = ?, datum_rojstva = ? WHERE uporabnik_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('sssi', $ime, $priimek, $datum_rojstva, $uporabnik_id);
            if ($stmt->execute()) {
                $success_msg = 'Osebni podatki so bili uspešno posodobljeni.';
                $_SESSION['ime'] = $ime;
            } else {
                $error_msg = 'Napaka pri shranjevanju osebnih podatkov.';
            }
            $stmt->close();
        } else {
            $error_msg = 'Priprava SQL stavka za osebne podatke ni uspela.';
        }
    }

    function update_metrics($conn, $uporabnik_id, $teza, $visina, $cilj) {
        global $error_msg, $success_msg;
        
        // Posodobitev teže in višine v tabeli uporabnik
        $sql_u = "UPDATE uporabnik SET teza = ?, visina = ? WHERE uporabnik_id = ?";
        if ($stmt_u = $conn->prepare($sql_u)) {
            $stmt_u->bind_param('dsi', $teza, $visina, $uporabnik_id); 
            
            if (!$stmt_u->execute()) {
                $error_msg = 'Napaka pri posodobitvi metrik.';
                $stmt_u->close();
                return;
            }
            $stmt_u->close();
        } else {
            $error_msg = 'Priprava SQL stavka za metrike ni uspela.';
            return;
        }
        
        // Posodobitev/vstavitev novega cilja v tabelo cilj 
        if (!empty($cilj)) {
            
            // 1. IZBRIŠI VSE PREJŠNJE CILJE TEGA UPORABNIKA
            // To zagotavlja, da je v bazi shranjen samo en (najaktualnejši) cilj.
            $sql_delete = "DELETE FROM cilj WHERE uporabnik_id = ?";
            if ($stmt_del = $conn->prepare($sql_delete)) {
                $stmt_del->bind_param('i', $uporabnik_id);
                $stmt_del->execute();
                $stmt_del->close();
            } 
            // Napake pri brisanju ne ustavijo celotnega procesa.

            // 2. VSTAVI NOV CILJ
            $sql_c = "INSERT INTO cilj (uporabnik_id, tip_cilja, status, datum_nalozitve) VALUES (?, ?, 'Aktiven', NOW())"; 
            
            if ($stmt_c = $conn->prepare($sql_c)) {
                $stmt_c->bind_param('is', $uporabnik_id, $cilj);
                $stmt_c->execute();
                $stmt_c->close();
            }
        }
        
        $success_msg = 'Telesne metrike in cilji so bili uspešno posodobljeni.';
    }


    // Upravljanje POST zahtev glede na gumb
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        if (isset($_POST['update_personal'])) {
            update_personal_data($conn, $uporabnik_id, $_POST['profile_name'], $_POST['profile_dob']);
            header('location: profil.php?status=personal_success');
            exit;
            
        } elseif (isset($_POST['update_metrics'])) {
            $teza = filter_var($_POST['profile_weight'], FILTER_VALIDATE_FLOAT);
            $visina = filter_var($_POST['profile_height'], FILTER_VALIDATE_INT);
            $cilj = $_POST['profile_goal'];
            
            if ($teza === false || $visina === false) {
                $error_msg = 'Teža in višina morata biti veljavni številki.';
            } else {
                update_metrics($conn, $uporabnik_id, $teza, $visina, $cilj);
                header('location: profil.php?status=metrics_success');
                exit;
            }
            
        } elseif (isset($_POST['update_password'])) {
            update_password($conn, $uporabnik_id, $_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'], $user_data['geslo']);
            header('location: profil.php?status=password_success');
            exit;
        }
        
        if ($conn->ping()) {
            $conn->close();
        }
    }

    // Upravljanje statusnih sporočil po preusmeritvi
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'personal_success') {
            $success_msg = 'Osebni podatki so bili uspešno posodobljeni.';
        } elseif ($_GET['status'] == 'metrics_success') {
            $success_msg = 'Telesne metrike in cilji so bili uspešno posodobljeni.';
        } elseif ($_GET['status'] == 'password_success') {
            $success_msg = 'Geslo je bilo uspešno spremenjeno.';
        }
    }

    // Če ni POST-a, zapri povezavo
    if ($conn && $conn->ping()) {
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack - Profil in Nastavitve</title>
    <link rel="stylesheet" href="index.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        .profile-section {
            margin-bottom: 30px;
        }
        
        .profile-section h2 {
            font-size: 1.6rem;
            margin-bottom: 15px;
            color: #C9D1D9;
        }

        .profile-form {
            background-color: #161B22; 
            border: 1px solid #30363D;
            border-radius: 12px;
            padding: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1; 
        }

        .form-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #AAAAAA;
            font-size: 0.95rem;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="date"],
        .form-group input[type="number"],
        .form-group input[type="password"],
        .form-group textarea {
            padding: 10px 12px;
            border: 1px solid #30363D;
            border-radius: 6px;
            background-color: #0D1117;
            color: #E6E6E6;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #4C6EF5; 
        }
        
        .form-group input:disabled {
            background-color: #1A202A;
            color: #6A737D;
            cursor: not-allowed;
        }
        
        .form-group textarea {
            resize: vertical;
        }

        /* Gumbi za shranjevanje */
        .small-action-btn {
            padding: 10px 20px;
            font-size: 1rem;
            max-width: 250px;
            background-color: #F7931A;
            color: #0D1117;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }
        
        .small-action-btn:hover {
            background-color: #E08518;
        }
        
        .security-btn {
            background-color: #E33C3C; 
            color: #FFFFFF;
        }
        
        .security-btn:hover {
            background-color: #C63131;
        }
        
        .error-message, .success-message {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error-message {
            color: #F54C4C; border: 1px solid #F54C4C; background: #211616;
        }
        .success-message {
            color: #69C957; border: 1px solid #69C957; background: #18281a;
        }
    </style>
</head>
<body>
    
    <div class="app-container">
        
        <?php 
        include 'nav.bar.php'; 
        ?>
        
        <div class="main-content">
            
            <header class="dashboard-header">
                <div>
                    <h1>Profil in Nastavitve</h1>
                    <p class="subtitle">Posodobite svoje osebne podatke, metrike in varnostne nastavitve.</p>
                </div>
            </header>

            <?php if (!empty($error_msg)): ?>
                <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success_msg)): ?>
                <div class="success-message"><i class="fas fa-check-circle"></i> <?php echo $success_msg; ?></div>
            <?php endif; ?>

            <div class="dashboard-block profile-section">
                <h2><i class="fas fa-user-edit"></i> Osebni Podatki</h2>
                <form class="profile-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="update_personal" value="1">
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label for="profile-name">Ime in Priimek</label>
                            <input type="text" id="profile-name" name="profile_name" 
                                   value="<?php echo htmlspecialchars($user_data['ime'] . ' ' . $user_data['priimek'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="profile-email">E-pošta (Ne more se spremeniti)</label>
                            <input type="email" id="profile-email" 
                                   value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="profile-dob">Datum Rojstva</label>
                            <input type="date" id="profile-dob" name="profile_dob" 
                                   value="<?php echo htmlspecialchars($user_data['datum_rojstva'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="profile-role">Vloga v Sistem (Osnova)</label>
                            <input type="text" id="profile-role" 
                                   value="<?php echo htmlspecialchars($user_data['vloga_naziv'] ?? ''); ?>" disabled>
                        </div>
                        
                    </div>
                    <button type="submit" class="action-button small-action-btn">
                        <i class="fas fa-save"></i> Shrani Osebne Podatke
                    </button>
                </form>
            </div>

            <div class="dashboard-block profile-section">
                <h2><i class="fas fa-weight"></i> Telesne Metrike in Cilji</h2>
                <form class="profile-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="update_metrics" value="1">
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label for="profile-weight">Trenutna Teža (kg)</label>
                            <input type="number" id="profile-weight" name="profile_weight" 
                                   value="<?php echo htmlspecialchars($user_data['teza'] ?? ''); ?>" step="0.1" min="1">
                        </div>

                        <div class="form-group">
                            <label for="profile-height">Višina (cm)</label>
                            <input type="number" id="profile-height" name="profile_height" 
                                   value="<?php echo htmlspecialchars($user_data['visina'] ?? ''); ?>" min="1">
                        </div>

                        <div class="form-group full-width">
                            <label for="profile-goal">Glavni Cilj</label>
                            <textarea id="profile-goal" name="profile_goal" rows="2" 
                                      placeholder="Npr.: Povečanje mišične mase in zmanjšanje maščobe..."><?php echo htmlspecialchars($user_data['tip_cilja'] ?? ''); ?></textarea>
                        </div>

                    </div>
                    <button type="submit" class="action-button small-action-btn">
                        <i class="fas fa-chart-bar"></i> Posodobi Metrike
                    </button>
                </form>
            </div>

            <div class="dashboard-block profile-section security-section">
                <h2><i class="fas fa-lock"></i> Varnost in Geslo</h2>
                <form class="profile-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="update_password" value="1">
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label for="current-password">Trenutno Geslo</label>
                            <input type="password" id="current-password" name="current_password" placeholder="Vnesite trenutno geslo" required>
                        </div>

                        <div class="form-group">
                            <label for="new-password">Novo Geslo</label>
                            <input type="password" id="new-password" name="new_password" placeholder="Vnesite novo geslo" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm-password">Potrditev Novega Gesla</label>
                            <input type="password" id="confirm-password" name="confirm_password" placeholder="Potrdite novo geslo" required>
                        </div>

                    </div>
                    <button type="submit" class="action-button small-action-btn security-btn">
                        <i class="fas fa-key"></i> Spremeni Geslo
                    </button>
                </form>
            </div>
            <?php echo "Prijavljen uporabnik ID: " . $_SESSION['uporabnik_id']; ?>
        </div>
        
    </div>
    
</body>
</html>