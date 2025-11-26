<?php
    // index.php

    // Zaženemo sejo, da lahko preverimo, ali je uporabnik že prijavljen
    session_start();

    // Če je uporabnik že prijavljen (loggedin je nastavljen na true), ga preusmerimo na domačo stran aplikacije
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        header('location: domaca.stran.php');
        exit;
    }

    // Če ni prijavljen, prikažemo stran za dobrodošlico z možnostjo prijave/registracije
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FitTrack - Dobrodošli</title>
        <link rel="stylesheet" href="style.css"> 
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body class="auth-body">
        
        <div class="auth-container">
            
            <div class="auth-card welcome-card">
                
                <div class="auth-header">
                    <div class="logo-icon-auth">
                        <i class="fas fa-dumbbell" style="color: #9370DB; font-size: 3rem;"></i>
                    </div>
                    <h1>Dobrodošli v FitTrack</h1>
                    <p>Vaša pot do boljšega počutja in fitnes uspehov se začne tukaj.</p>
                    <p class="subtitle" style="margin-top: 20px;">Za nadaljevanje izberite eno od možnosti:</p>
                </div>
                
                <div class="welcome-actions">
                    <a href="login.php" class="auth-button">
                        <i class="fas fa-sign-in-alt"></i> Prijava
                    </a>

                    <a href="register.php" class="auth-button register-link">
                        <i class="fas fa-user-plus"></i> Registracija
                    </a>
                </div>

            </div>
        </div>
        
    </body>
</html>