<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack - Nadzorna Plošča</title>
    <!-- Vključitev GLOBALNIH STILOV (index.css) -->
    <link rel="stylesheet" href="index.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    
    <!-- Glavni Kontejner, ki uporablja 'display: flex' za postavitev nav.bar.php OBSTRANI -->
    <div class="app-container">
        
        <!-- LEVA STRAN: VKLJUČITEV nav.bar.php (Sidebar) -->
        <?php include 'nav.bar.php';?>
        
        <!-- DESNA STRAN: Glavna Vsebina (main-content) -->
        <div class="main-content">
            
            <!-- Vsebina, ki ste jo določili, s popravljenimi PHP razredi za naš CSS -->
            <header class="dashboard-header">
                <div>
                    <h1>Nadzorna Plošča</h1>
                    <p class="subtitle">Dobrodošli nazaj! Tukaj je pregled vaše telesne pripravljenosti.</p>
                </div>
                <a href="dodaj.trening.php" class="action-button">
                    <i class="fas fa-plus"></i> Beleži Trening
                </a>
            </header>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div>
                        <p class="stat-label">Skupno Treningov</p>
                        <h3 class="stat-value">20</h3>
                    </div>
                    <div class="stat-icon" style="color: #4C6EF5; background: rgba(76, 110, 245, 0.2);">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div>
                        <p class="stat-label">Trenutni Niz</p>
                        <h3 class="stat-value">7 dni</h3>
                    </div>
                    <div class="stat-icon" style="color: #69C957; background: rgba(105, 201, 87, 0.2);">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div>
                        <p class="stat-label">Pokurjene Kalorije</p>
                        <h3 class="stat-value">5.780</h3>
                    </div>
                    <div class="stat-icon" style="color: #F7931A; background: rgba(247, 147, 26, 0.2);">
                        <i class="fas fa-fire-alt"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div>
                        <p class="stat-label">Aktivni Dnevi</p>
                        <h3 class="stat-value">20</h3>
                    </div>
                    <div class="stat-icon" style="color: #9370DB; background: rgba(147, 112, 219, 0.2);">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-block trend-card">
                <h2>Trendi Treningov</h2>
                <p class="subtitle">Vaše pokurjene kalorije v zadnjih 14 dneh</p>
                <div class="chart-placeholder large">
                    [Tukaj se bo naložil dinamičen Chart.js graf]
                </div>
            </div>

            <div class="dashboard-block recent-workouts">
                <h2>Zadnji Treningi</h2>
                <p class="subtitle">Vaše najnovejše fitnes aktivnosti</p>
                
                <div class="workout-item">
                    <div class="icon-details">
                        <i class="fas fa-running workout-icon" style="color: #4C6EF5;"></i>
                        <div class="details">
                            <span class="workout-type" style="font-weight: 600;">Tek</span>
                            <span class="subtitle" style="font-size: 0.85rem;">Nov 25, 2025</span>
                        </div>
                    </div>
                    <div class="stats-time">
                        <span class="time" style="color: #CCCCCC;">30 min</span>
                        <span class="calories">300 cal</span>
                    </div>
                </div>

                <div class="workout-item">
                    <div class="icon-details">
                        <i class="fas fa-weight-lifting workout-icon" style="color: #9370DB;"></i>
                        <div class="details">
                            <span class="workout-type" style="font-weight: 600;">Vadba z Utežmi</span>
                            <span class="subtitle" style="font-size: 0.85rem;">Nov 24, 2025</span>
                        </div>
                    </div>
                    <div class="stats-time">
                        <span class="time" style="color: #CCCCCC;">45 min</span>
                        <span class="calories">250 cal</span>
                    </div>
                </div>
                
                <div class="workout-item">
                    <div class="icon-details">
                        <i class="fas fa-biking workout-icon" style="color: #69C957;"></i>
                        <div class="details">
                            <span class="workout-type" style="font-weight: 600;">Kolesarjenje</span>
                            <span class="subtitle" style="font-size: 0.85rem;">Nov 23, 2025</span>
                        </div>
                    </div>
                    <div class="stats-time">
                        <span class="time" style="color: #CCCCCC;">60 min</span>
                        <span class="calories">400 cal</span>
                    </div>
                </div>
                
            </div>
            
        </div>
        
    </div>
    
</body>
</html>