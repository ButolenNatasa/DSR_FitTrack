<?php
    // Zaženemo sejo, da lahko shranjujemo morebitna sporočila o uspehu/napaki
    session_start();

    // Če je uporabnik že prijavljen, ga preusmerimo na domačo stran
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        header('location: domaca.stran.php');
        exit;
    }

    // Vključitev konfiguracijske datoteke (predpostavljamo, da config.php obstaja)
    require_once 'config.php'; 

    // Inicializacija spremenljivk
    $ime = $priimek = $email = $password = $confirm_password = $role = '';
    $ime_err = $priimek_err = $email_err = $password_err = $confirm_password_err = $role_err = '';
    $success_msg = '';

    // Preveri, ali je obrazec poslan (POST zahteva)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Validacija Imena
        if (empty(trim($_POST['ime']))) {
            $ime_err = 'Vnesite ime.';
        } else {
            $ime = trim($_POST['ime']);
        }

        // Validacija Priimka
        if (empty(trim($_POST['priimek']))) {
            $priimek_err = 'Vnesite priimek.';
        } else {
            $priimek = trim($_POST['priimek']);
        }

        // Validacija Vloge (Role)
        if (empty($_POST['role'])) {
            $role_err = 'Izberite vlogo.';
        } elseif ($_POST['role'] !== 'user' && $_POST['role'] !== 'coach') {
            $role_err = 'Neveljavna izbira vloge.';
        } else {
            $role = $_POST['role'];
        }

        // Validacija E-pošte
        if (empty(trim($_POST['email']))) {
            $email_err = 'Vnesite e-poštni naslov.';
        } else {
            $email = trim($_POST['email']);
            
            // Preveri, ali e-pošta že obstaja
            $conn = connect_db();
            $sql = 'SELECT uporabnik_id FROM uporabnik WHERE email = ?';
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('s', $param_email);
                $param_email = $email;
                
                if ($stmt->execute()) {
                    $stmt->store_result();
                    if ($stmt->num_rows > 0) {
                        $email_err = 'Ta e-poštni naslov je že registriran.';
                    }
                } else {
                    echo 'Ups! Nekaj je šlo narobe pri preverjanju e-pošte. Poskusite znova.';
                }
                $stmt->close();
            }
            // Povezava se bo zaprla kasneje
        }

        // Validacija Gesla
        if (empty(trim($_POST['password']))) {
            $password_err = 'Vnesite geslo.';
        } elseif (strlen(trim($_POST['password'])) < 6) {
            $password_err = 'Geslo mora imeti vsaj 6 znakov.';
        } else {
            $password = trim($_POST['password']);
        }

        // Validacija Potrditve Gesla
        if (empty(trim($_POST['confirm_password']))) {
            $confirm_password_err = 'Potrdite geslo.';
        } else {
            $confirm_password = trim($_POST['confirm_password']);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = 'Gesli se ne ujemata.';
            }
        }

        // 3. Vstavljanje uporabnika v bazo, če ni napak
        if (empty($ime_err) && empty($priimek_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($role_err)) {
            
            // Določitev Vloga_ID na podlagi izbrane vloge
            // Vloga_ID: 1 = Uporabnik, 2 = Trener (glede na dsr_fittrack (1).sql)
            $vloga_id = ($role === 'user') ? 1 : 2; 

            // Zgoščevanje gesla (Hashed Password) za varno shranjevanje
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Priprava INSERT stavka
            $sql = 'INSERT INTO uporabnik (vloga_id, ime, priimek, email, geslo, datum_registracije) VALUES (?, ?, ?, ?, ?, NOW())';
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('issss', $param_vloga_id, $param_ime, $param_priimek, $param_email, $param_geslo);
                
                // Nastavitev parametrov
                $param_vloga_id = $vloga_id;
                $param_ime = $ime;
                $param_priimek = $priimek;
                $param_email = $email;
                $param_geslo = $hashed_password;
                
                if ($stmt->execute()) {
                    // Registracija uspešna
                    $success_msg = 'Uspešna registracija! Zdaj se lahko prijavite.';
                    // Ponastavi polja, da se ne prikažejo po uspešni registraciji
                    $ime = $priimek = $email = $password = $confirm_password = '';
                } else {
                    $email_err = 'Napaka pri shranjevanju podatkov. Prosimo, poskusite znova.';
                }
                $stmt->close();
            }
            $conn->close();
        }
    }
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FitTrack - Registracija</title>
        <link rel="stylesheet" href="style.css"> 
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="index.css">
    </head>
        <body class="auth-body">
            
            <div class="auth-container">
                
                <div class="auth-card">
                    
                    <div class="auth-header">
                        <div class="logo-icon-auth">
                            <i class="fas fa-user-plus" style="color: #9370DB;"></i>
                        </div>
                        <h2>Registracija</h2>
                        <p>Ustvarite nov račun in se pridružite FitTrack skupnosti.</p>
                    </div>
                    
                    <?php if (!empty($success_msg)): ?>
                        <div class="form-group" style="color: #69C957; padding: 10px; border: 1px solid #69C957; border-radius: 8px; margin-bottom: 15px; background: #18281a;">
                            <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="register-form" class="auth-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        
                        <div class="form-group role-selection">
                            <label style="<?php echo (!empty($role_err)) ? 'color: #F54C4C;' : ''; ?>">Izberi Vlogo</label>
                            <div class="role-options">
                                <input type="radio" id="role-user" name="role" value="user" <?php echo (empty($role) || $role === 'user') ? 'checked' : ''; ?> required>
                                <label for="role-user" class="role-label">Uporabnik</label>

                                <input type="radio" id="role-coach" name="role" value="coach" <?php echo ($role === 'coach') ? 'checked' : ''; ?> required>
                                <label for="role-coach" class="role-label">Trener</label>
                            </div>
                            <?php if (!empty($role_err)): ?>
                                <span style="color: #F54C4C; font-size: 0.85rem; display: block; margin-top: 5px;"><i class="fas fa-exclamation-circle"></i> <?php echo $role_err; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="register-ime" style="<?php echo (!empty($ime_err)) ? 'color: #F54C4C;' : ''; ?>">Ime</label>
                                <input type="text" id="register-ime" name="ime" placeholder="Vnesite svoje ime" value="<?php echo htmlspecialchars($ime); ?>" required>
                                <?php if (!empty($ime_err)): ?>
                                    <span style="color: #F54C4C; font-size: 0.85rem; display: block; margin-top: 5px;"><i class="fas fa-exclamation-circle"></i> <?php echo $ime_err; ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="register-priimek" style="<?php echo (!empty($priimek_err)) ? 'color: #F54C4C;' : ''; ?>">Priimek</label>
                                <input type="text" id="register-priimek" name="priimek" placeholder="Vnesite svoj priimek" value="<?php echo htmlspecialchars($priimek); ?>" required>
                                <?php if (!empty($priimek_err)): ?>
                                    <span style="color: #F54C4C; font-size: 0.85rem; display: block; margin-top: 5px;"><i class="fas fa-exclamation-circle"></i> <?php echo $priimek_err; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="register-email" style="<?php echo (!empty($email_err)) ? 'color: #F54C4C;' : ''; ?>">E-pošta</label>
                            <input type="email" id="register-email" name="email" placeholder="Vnesite svojo e-pošto" value="<?php echo htmlspecialchars($email); ?>" required>
                            <?php if (!empty($email_err)): ?>
                                <span style="color: #F54C4C; font-size: 0.85rem; display: block; margin-top: 5px;"><i class="fas fa-exclamation-circle"></i> <?php echo $email_err; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="register-password" style="<?php echo (!empty($password_err)) ? 'color: #F54C4C;' : ''; ?>">Geslo</label>
                            <input type="password" id="register-password" name="password" placeholder="Ustvarite geslo" required>
                            <?php if (!empty($password_err)): ?>
                                <span style="color: #F54C4C; font-size: 0.85rem; display: block; margin-top: 5px;"><i class="fas fa-exclamation-circle"></i> <?php echo $password_err; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="register-confirm-password" style="<?php echo (!empty($confirm_password_err)) ? 'color: #F54C4C;' : ''; ?>">Potrditev Gesla</label>
                            <input type="password" id="register-confirm-password" name="confirm_password" placeholder="Potrdite geslo" required>
                            <?php if (!empty($confirm_password_err)): ?>
                                <span style="color: #F54C4C; font-size: 0.85rem; display: block; margin-top: 5px;"><i class="fas fa-exclamation-circle"></i> <?php echo $confirm_password_err; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="auth-button register-link" style="margin-top: 25px;">
                            <i class="fas fa-user-plus"></i> Registracija
                        </button>
                        
                    </form>

                    <p style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
                        Imate že račun? <a href="login.php">Prijavite se tukaj.</a>
                    </p>

                </div>
            </div>
            
        </body>
</html>