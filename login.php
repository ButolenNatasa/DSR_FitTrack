<?php
    // Zaženemo sejo, da lahko shranjujemo podatke o uporabniku po prijavi
    session_start();

    // Če je uporabnik že prijavljen, ga preusmerimo na domačo stran
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        header('location: domaca.stran.php');
        exit;
    }

    // Vključitev konfiguracijske datoteke in povezave z bazo
    require_once 'config.php';

    // Spremenljivke za shranjevanje sporočil o napakah
    $email = $password = '';
    $login_err = '';

    // Preveri, ali je obrazec poslan (POST zahteva)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // 1. Zbiranje in preverjanje podatkov
        if (empty(trim($_POST['email']))) {
            $login_err = 'Vnesite e-poštni naslov.';
        } else {
            $email = trim($_POST['email']);
        }

        if (empty(trim($_POST['password']))) {
            // Dodamo v obstoječo napako ali nastavimo novo
            $login_err .= ($login_err ? ' ' : '') . 'Vnesite geslo.';
        } else {
            $password = trim($_POST['password']);
        }

        // 2. Preverjanje poverilnic v bazi
        if (empty($login_err)) {
            
            $conn = connect_db();
            
            // Priprava SQL stavka (uporaba pripravljenih stavkov preprečuje SQL injection)
            $sql = 'SELECT uporabnik_id, geslo, ime, vloga_id FROM uporabnik WHERE email = ?';
            
            if ($stmt = $conn->prepare($sql)) {
                // Poveži spremenljivko kot parameter
                $stmt->bind_param('s', $param_email);
                $param_email = $email;
                
                if ($stmt->execute()) {
                    // Shrani rezultat
                    $stmt->store_result();
                    
                    // Preveri, ali e-pošta obstaja
                    if ($stmt->num_rows == 1) {
                        // Poveži rezultat z lokalnimi spremenljivkami
                        $stmt->bind_result($id, $hashed_password, $ime, $vloga_id);
                        
                        if ($stmt->fetch()) {
                            // Preveri geslo (uporabljamo password_verify za preverjanje shranjene zgoščene vrednosti)
                            if (password_verify($password, $hashed_password)) {
                                // Prijava je uspešna, shrani podatke v sejo
                                $_SESSION['loggedin'] = true;
                                $_SESSION['uporabnik_id'] = $id;
                                $_SESSION['ime'] = $ime;
                                $_SESSION['vloga_id'] = $vloga_id;
                                
                                // Preusmeri na domačo stran
                                header('location: domaca.stran.php');
                                exit;
                            } else {
                                // Napačno geslo
                                $login_err = 'Napačen e-poštni naslov ali geslo.';
                            }
                        }
                    } else {
                        // E-pošta ne obstaja
                        $login_err = 'Napačen e-poštni naslov ali geslo.';
                    }
                } else {
                    // Napaka pri izvajanju poizvedbe
                    $login_err = 'Prišlo je do napake v sistemu. Prosimo, poskusite znova pozneje.';
                }
                
                // Zapri stavek
                $stmt->close();
            }
            
            // Zapri povezavo
            $conn->close();
        }
    }
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FitTrack - Prijava</title>
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
                            <i class="fas fa-sign-in-alt" style="color: #4C6EF5;"></i>
                        </div>
                        <h2>Prijava</h2>
                        <p>Vnesite svoje podatke za dostop do aplikacije FitTrack.</p>
                    </div>
                    
                    <form id="login-form" class="auth-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        
                        <?php if (!empty($login_err)): ?>
                            <div class="form-group" style="color: #F54C4C; padding: 10px; border: 1px solid #F54C4C; border-radius: 8px; margin-bottom: 15px; background: #211616;">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $login_err; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="login-email">E-pošta</label>
                            <input type="email" id="login-email" name="email" placeholder="Vnesite svojo e-pošto" value="<?php echo $email; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="login-password">Geslo</label>
                            <input type="password" id="login-password" name="password" placeholder="Vnesite svoje geslo" required>
                        </div>
                        
                        <button type="submit" class="auth-button" style="margin-top: 25px;">
                            <i class="fas fa-sign-in-alt"></i> Prijava
                        </button>
                        
                    </form>

                    <p style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
                        Še niste registrirani? <a href="register.php">Registrirajte se tukaj.</a>
                    </p>

                </div>
            </div>
            
        </body>
</html>