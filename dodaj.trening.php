<?php
// Za realno aplikacijo, bi tukaj preverili avtentikacijo in vključili header/sidebar datoteko
// Vključitev sidebar in header dela bi bila na primer:
// include 'header.php'; 
// include 'sidebar.php'; 

$page_title = "Dodaj nov trening"; // Naslov za glavo strani
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack - <?php echo $page_title; ?></title>
    <link rel="stylesheet" href="style.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="index.css"> </head>
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
            
            <div class="dashboard-block auth-form">
                
                <form action="shrani_trening.php" method="POST">
                    
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label for="date">Datum treninga</label>
                            <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="type">Vrsta treninga</label>
                            <select id="type" name="type" required>
                                <option value="moč">Trening moči (Weightlifting)</option>
                                <option value="kardio">Kardio (Running/Cycling)</option>
                                <option value="hiit">HIIT</option>
                                <option value="joga">Joga/Fleksibilnost</option>
                                <option value="drugo">Drugo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="duration">Trajanje (v minutah)</label>
                            <input type="number" id="duration" name="duration" placeholder="Npr. 60" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="calories">Porabljene kalorije (pribl.)</label>
                            <input type="number" id="calories" name="calories" placeholder="Npr. 500" min="0">
                        </div>
                        
                    </div>
                    
                    <div class="form-group full-width" style="margin-bottom: 20px;">
                        <label for="description">Opis treninga in vaje (Neobvezno)</label>
                        <textarea id="description" name="description" rows="4" placeholder="Npr. '5x5 počep, 3x8 potisk s prsi, 30 min tek'"></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label>Intenzivnost</label>
                        <div class="role-options" style="margin-top: 5px;">
                            <input type="radio" id="low" name="intensity" value="low" required>
                            <label for="low" class="role-label" style="background-color: #4C6EF5; border-color: #4C6EF5; color: white;">Nizka</label>

                            <input type="radio" id="medium" name="intensity" value="medium">
                            <label for="medium" class="role-label" style="background-color: #F7931A; border-color: #F7931A; color: #161B22;">Srednja</label>
                            
                            <input type="radio" id="high" name="intensity" value="high" checked>
                            <label for="high" class="role-label" style="background-color: #F54C4C; border-color: #F54C4C; color: white;">Visoka</label>
                        </div>
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