<?php
    session_start();
    require_once 'config.php';
    $conn = connect_db();

    // Preveri prijavljenega uporabnika
    $uporabnik_id = $_SESSION['uporabnik_id'] ?? null;
    if (!$uporabnik_id) {
        header("Location: login.php");
        exit();
    }

    // 1. Pridobitev vseh treningov uporabnika za analizo
    $sql = "SELECT * FROM trening WHERE uporabnik_id = ? ORDER BY datum ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uporabnik_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $all_workouts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // 2. Skupni tedenski volumen (ure in kalorije)
    $weekly_stats = [];
    foreach ($all_workouts as $workout) {
        $week = date('oW', strtotime($workout['datum'])); // o: year for ISO-8601, W: week number
        if (!isset($weekly_stats[$week])) {
            $weekly_stats[$week] = ['minutes' => 0, 'calories' => 0];
        }
        $weekly_stats[$week]['minutes'] += intval($workout['trajanje_min']);
        $weekly_stats[$week]['calories'] += intval($workout['porabljene_kalorije']);
    }

    // 3. Porazdelitev vadb
    $workout_distribution = [];
    foreach ($all_workouts as $workout) {
        $type = $workout['tip_treninga'];
        if (!isset($workout_distribution[$type])) {
            $workout_distribution[$type] = 0;
        }
        $workout_distribution[$type]++;
    }

    $intensity_avg = count($all_workouts) > 0 ? round(array_sum(array_map(function($w){return rand(1,3);}, $all_workouts)) / count($all_workouts), 2) : 0;

    $weekly_stats = [];
    foreach ($all_workouts as $workout) {
        $week = date('oW', strtotime($workout['datum']));
        if (!isset($weekly_stats[$week])) {
            $weekly_stats[$week] = ['minutes' => 0, 'calories' => 0];
        }
        $weekly_stats[$week]['minutes'] += intval($workout['trajanje_min']);
    }


    $conn->close();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FitTrack - Statistika in Analiza</title>
        <link rel="stylesheet" href="index.css"> 
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            /* ... tvoj CSS ostaja enak ... */
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
            .chart-grid { 
                display: grid; 
                grid-template-columns: 1fr; 
                gap: 20px; 
                margin-bottom: 20px; 
            }
            .chart-grid:not(.chart-small-grid) { 
                grid-template-columns: 1fr;
            }
            .chart-small-grid { 
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            }
            .chart-card { 
                min-height: 300px; 
                display: flex; 
                flex-direction: column; 
            }
            .chart-placeholder { 
                background: #1A1F27; 
                border: 1px solid #30363D; 
                border-radius: 6px; 
                display: flex; 
                align-items: center; 
                justify-content: center;
                color: #AAAAAA; 
                font-style: italic; 
                font-size: 0.9rem; 
                flex-grow: 1; 
                padding: 20px; 
                margin-top: 15px; 
            }
            .chart-placeholder.large { 
                min-height: 250px; 
            }
            .chart-placeholder.small { 
                min-height: 180px; 
            }
            .report-button { 
                background-color: #F7931A; 
                color: #0D1117; 
                font-weight: 700; 
            }
            .report-button:hover { 
                background-color: #E08518; 
            }
            .trend-chart {
                display: flex;
                align-items: flex-end;
                height: 100%; /* zavzame celotno višino starševskega boxa */
                border-left: 1px solid #30363D;
                border-bottom: 1px solid #30363D;
                padding: 0; /* odstranimo vertikalni padding */
                width: 100%;
                gap: 8px;
                justify-content: flex-start;
                box-sizing: border-box;
            }

            .bar-wrapper {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-end; /* stolpci se držijo spodnjega roba */
                height: 100%; /* vzame celotno višino grafa */
            }

            .bar {
                width: 30px;
                background-color: #4C6EF5; 
                border-radius: 4px 4px 0 0;
                transition: 0.5s;
                transform: scaleY(0);
                transform-origin: bottom;
                margin-bottom: 4px;
            }

            .bar-label {
                font-size: 0.75rem;
                color: #E6E6E6;
                text-align: center;
                margin-top: 0;
            }
            .bar.loaded {
                transform: scaleY(1);
            }
            .bar-value {
                font-size: 0.75rem;
                color: #E6E6E6;
                margin-bottom: 4px; /* malo prostora do stolpca */
                text-align: center;
            }
            .dist-chart {
                display: flex;
                align-items: flex-end;
                height: 100%;
                border-left: 1px solid #30363D;
                border-bottom: 1px solid #30363D;
                padding: 0;
                width: 100%;
                gap: 12px;
                justify-content: flex-start;
                box-sizing: border-box;
            }

            .dist-bar-wrapper {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-end;
                height: 100%;
            }

            .dist-bar {
                width: 40px;
                background-color: #F7931A;
                border-radius: 4px 4px 0 0;
                transition: 0.5s;
                transform: scaleY(0);
                transform-origin: bottom;
                margin-bottom: 4px;
            }

            .dist-bar.loaded {
                transform: scaleY(1);
            }

            .dist-bar-value {
                font-size: 0.75rem;
                color: #E6E6E6;
                margin-bottom: 4px;
                text-align: center;
            }
            @media (min-width: 768px) { .chart-grid:not(.chart-small-grid) { grid-template-columns: 1fr 1fr; } }
        </style>
    </head>
        <body>
            <div class="app-container">
                <?php include 'nav.bar.php';?>
                <div class="main-content">
                    <header class="dashboard-header">
                        <div>
                            <h1>Statistika in Analiza</h1>
                            <p class="subtitle">Poglobljena vizualna analiza vašega fitnes napredka skozi čas.</p>
                        </div>
                        <a href="pdf.php" class="action-button report-button" target="_blank">
                            <i class="fas fa-file-pdf"></i> Izvoz Poročila (PDF)
                        </a>
                        <a href="excel.php" class="action-button report-button" >
                            <i class="fas fa-file-excel"></i> Izvoz v Excel (CSV)
                        </a>
                    </header>

                    <div class="chart-grid">

                        <div class="dashboard-block chart-card">
                            <h2>Skupni Tedenski Volumen</h2>
                            <p class="subtitle">Skupno število ur vadbe in pokurjenih kalorij na teden.</p>
                            <div class="trend-chart" id="weekly-minutes-chart">
                                <?php 
                                $max_minutes = max(array_column($weekly_stats, 'minutes'));
                                foreach ($weekly_stats as $week => $data): 
                                    $height = $max_minutes ? ($data['minutes'] / $max_minutes * 200) : 2;
                                    $week_number = substr($week, -2); // vzame zadnji dve številki iz "oW"
                                ?>
                                <div class="bar-wrapper">
                                    <!-- Število minut nad stolpcem -->
                                    <span class="bar-value"><?php echo $data['minutes']; ?> min</span>
                                    <div class="bar" style="height: <?php echo $height; ?>px;" title="Teden <?php echo $week_number; ?>: <?php echo $data['minutes']; ?> min"></div>
                                    <span class="bar-label"><?php echo "Teden ", $week_number; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="dashboard-block chart-card">
                            <h2>Porazdelitev Vadb</h2>
                            <p class="subtitle">Delež posameznih tipov vadb.</p>
                            <div class="dist-chart">
                                <?php
                                $max_count = max($workout_distribution); // za sorazmerne višine
                                foreach ($workout_distribution as $type => $count):
                                    $height = $max_count ? ($count / $max_count * 150) : 2; // max višina 150px
                                ?>
                                    <div class="dist-bar-wrapper">
                                        <span class="dist-bar-value"><?php echo $count; ?></span>
                                        <div class="dist-bar" style="height: <?php echo $height; ?>px;" title="<?php echo $type; ?>: <?php echo $count; ?>"></div>
                                        <span class="bar-label"><?php echo ucfirst($type); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                        <?php echo "Prijavljen uporabnik ID: " . $_SESSION['uporabnik_id']; ?>
                </div>
            </div>
            <script>
                let bars = document.querySelectorAll('#weekly-minutes-chart .bar');
                bars.forEach(bar => {
                    setTimeout(() => bar.classList.add('loaded'), 100);
                });
                let distBars = document.querySelectorAll('.dist-bar');
                distBars.forEach(bar => {
                    setTimeout(() => bar.classList.add('loaded'), 100);
                });
            </script>
        </body>
</html>