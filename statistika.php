<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack - Statistika in Analiza</title>
    <!-- Vključitev GLOBALNIH STILOV (index.css) -->
    <link rel="stylesheet" href="index.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- DODATNI STILI za postavitev grafov in kontrol na strani Statistika -->
    <style>
        /* Postavitev kontrol (kontrolniki časa in tipa vadbe) */
        .controls-row {
            display: flex;
            gap: 20px;
            margin: 20px 0 30px;
            flex-wrap: wrap;
        }

        .filter-dropdown select {
            background: #0D1117;
            border: 1px solid #30363D;
            color: #E6E6E6;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            outline: none;
            min-width: 150px;
        }
        
        /* Postavitve grafov (Grid) */
        .chart-grid {
            display: grid;
            grid-template-columns: 1fr; /* Privzeto en stolpec za mobilne naprave */
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Dva velika grafa v dveh stolpcih na desktopu */
        .chart-grid:not(.chart-small-grid) {
            grid-template-columns: 1fr;
        }

        /* Grid za manjše grafe */
        .chart-small-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* 2-4 majhni grafi */
        }

        /* Nastavitve kartic z grafi */
        .chart-card {
            min-height: 300px; /* Minimalna višina za velike grafe */
            display: flex;
            flex-direction: column;
        }
        
        .chart-placeholder {
            background: #1A1F27; /* Ozadje za simulacijo grafa */
            border: 1px solid #30363D;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #AAAAAA;
            font-style: italic;
            font-size: 0.9rem;
            flex-grow: 1; /* Graf zavzame ves preostali prostor */
            padding: 20px;
            margin-top: 15px;
        }

        .chart-placeholder.large {
            min-height: 250px;
        }
        
        .chart-placeholder.small {
            min-height: 180px;
        }
        
        /* Gumb za poročilo */
        .report-button {
            background-color: #F7931A; /* Oranžna barva */
            color: #0D1117;
            font-weight: 700;
        }
        
        .report-button:hover {
            background-color: #E08518; 
        }

        /* Postavitev za tablice in desktop (nad 768px) */
        @media (min-width: 768px) {
            .chart-grid:not(.chart-small-grid) {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    
    <!-- Glavni Kontejner, ki uporablja 'display: flex' za postavitev nav.bar.php OBSTRANI -->
    <div class="app-container">
        
        <!-- LEVA STRAN: VKLJUČITEV nav.bar.php (Sidebar) -->
        <?php include 'nav.bar.php';?>
        
        <!-- DESNA STRAN: Glavna Vsebina (main-content) -->
        <div class="main-content">
            
            <!-- Glava Strani (Header) -->
            <header class="dashboard-header">
                <div>
                    <h1>Statistika in Analiza</h1>
                    <p class="subtitle">Poglobljena vizualna analiza vašega fitnes napredka skozi čas.</p>
                </div>
                <!-- Spremenjen gumb za izvoz poročila -->
                <button class="action-button report-button">
                    <i class="fas fa-file-pdf"></i> Izvoz Poročila (PDF)
                </button>
            </header>

            <!-- Blok s Kontrolami za Analizo -->
            <div class="dashboard-block chart-controls">
                <h2>Filtri Analize</h2>
                <div class="controls-row">
                    <!-- Filter: Časovno obdobje -->
                    <div class="filter-dropdown">
                        <select name="time-period" aria-label="Izbira časovnega obdobja">
                            <option value="last-7">Zadnjih 7 dni</option>
                            <option value="last-30">Zadnjih 30 dni</option>
                            <option value="last-year">Zadnje leto</option>
                            <option value="all-time">Ves čas</option>
                        </select>
                    </div>

                    <!-- Filter: Tip Vadbe -->
                    <div class="filter-dropdown">
                        <select name="workout-type" aria-label="Izbira tipa vadbe">
                            <option value="all">Vsi Treningi</option>
                            <option value="weights">Vadba z Utežmi</option>
                            <option value="cardio">Kardio</option>
                            <option value="running">Tek</option>
                            <option value="yoga">Joga</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Glavni Grafi (dva stolpca na desktopu) -->
            <div class="chart-grid">
                
                <div class="dashboard-block chart-card">
                    <h2>Trend Moči (Bench Press)</h2>
                    <p class="subtitle">Povečanje največje teže skozi čas.</p>
                    <div class="chart-placeholder large">
                        [Chart.js Graf: Teža v kilogramih (Line Chart)]
                    </div>
                </div>

                <div class="dashboard-block chart-card">
                    <h2>Skupni Tedenski Volumen</h2>
                    <p class="subtitle">Skupno število ur vadbe in pokurjenih kalorij na teden.</p>
                    <div class="chart-placeholder large">
                        [Chart.js Graf: Kombinirani graf (Trajanje/Kalorije)]
                    </div>
                </div>

            </div>

            <!-- Manjši Grafi (dva stolpca na desktopu) -->
            <div class="chart-grid chart-small-grid">
                
                <div class="dashboard-block chart-card">
                    <h2>Porazdelitev Vadb</h2>
                    <p class="subtitle">Delež posameznih tipov vadb.</p>
                    <div class="chart-placeholder small">
                        [Chart.js Graf: Pie/Doughnut Chart]
                    </div>
                </div>

                <div class="dashboard-block chart-card">
                    <h2>Povprečna Intenzivnost</h2>
                    <p class="subtitle">Povprečna zaznana intenzivnost.</p>
                    <div class="chart-placeholder small">
                        [Chart.js Graf: Bar/Gauge Chart]
                    </div>
                </div>

            </div>
            
        </div>
        
    </div>
    
</body>
</html>