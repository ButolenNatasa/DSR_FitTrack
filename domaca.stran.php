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

    // 1. Skupno število treningov
    $sql = "SELECT COUNT(*) AS total_trening FROM trening WHERE uporabnik_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uporabnik_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_trening = $result->fetch_assoc()['total_trening'] ?? 0;
    $stmt->close();

    // 2. Število aktivnih dni (unikatni datumi treningov)
    $sql = "SELECT COUNT(DISTINCT datum) AS active_days FROM trening WHERE uporabnik_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uporabnik_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $active_days = $result->fetch_assoc()['active_days'] ?? 0;
    $stmt->close();

    // 3. Skupno pokurjene kalorije
    $sql = "SELECT SUM(porabljene_kalorije) AS total_calories FROM trening WHERE uporabnik_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uporabnik_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_calories = $result->fetch_assoc()['total_calories'] ?? 0;
    $stmt->close();

    // 4. Trenutni niz (npr. koliko dni zapored je imel vsaj en trening, lahko poenostavljeno: zadnjih 7 dni)
    $current_streak = 0;
    $sql = "SELECT datum FROM trening WHERE uporabnik_id = ? ORDER BY datum DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uporabnik_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dates = [];
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['datum'];
    }
    $stmt->close();

    // Preprost izračun zaporednih dni (od danes nazaj)
    $today = new DateTime();
    $streak = 0;
    foreach ($dates as $d) {
        $date = new DateTime($d);
        $diff = $today->diff($date)->days;
        if ($diff === $streak) {
            $streak++;
        } else {
            break;
        }
    }
    $current_streak = $streak;

    $graph_data = [];
    $sql = "SELECT datum, SUM(porabljene_kalorije) AS total_calories 
            FROM trening 
            WHERE uporabnik_id = ? AND datum >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
            GROUP BY datum 
            ORDER BY datum ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uporabnik_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $graph_data[$row['datum']] = intval($row['total_calories']);
    }
    $stmt->close();

    // Pripravimo polje vseh 14 dni (da so tudi dnevi brez vnosa)
    $days = [];
    for ($i = 13; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $days[$date] = $graph_data[$date] ?? 0;
    }

    // 5. Zadnji 3 treningi
    $sql = "SELECT * FROM trening WHERE uporabnik_id = ? ORDER BY datum DESC LIMIT 3";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uporabnik_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recent_workouts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $conn->close();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FitTrack - Nadzorna Plošča</title>
        <link rel="stylesheet" href="index.css"> 
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
        .trend-chart {
            display: flex;
            align-items: flex-end;
            height: 200px;
            border-left: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
            width: 100%; /* raztegne celoten container */
            gap: 4px;
        }

        .trend-chart .bar {
        flex: 1;
        background-color: #4C6EF5;
        border-radius: 4px 4px 0 0;
        transition: 0.5s;
        transform: scaleY(0);
        transform-origin: bottom;
        }

        .trend-chart .bar.loaded {
            transform: scaleY(1);
        }

        .trend-chart .bar:hover {
            background-color: #69C957;
        }
        </style>
    </head>
        <body>
            <div class="app-container">
                <?php include 'nav.bar.php';?>
                <div class="main-content">
                    <header class="dashboard-header">
                        <div>
                            <h1>Nadzorna Plošča</h1>
                            <p class="subtitle">Dobrodošli nazaj! Tukaj je pregled vaše telesne pripravljenosti.</p>
                        </div>
                        <a href="dodaj_trening.php" class="action-button">
                            <i class="fas fa-plus"></i> Dodaj Trening
                        </a>
                    </header>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div>
                                <p class="stat-label">Skupno Treningov</p>
                                <h3 class="stat-value"><?php echo $total_trening; ?></h3>
                            </div>
                            <div class="stat-icon" style="color: #4C6EF5; background: rgba(76, 110, 245, 0.2);">
                                <i class="fas fa-dumbbell"></i>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div>
                                <p class="stat-label">Trenutni Niz</p>
                                <h3 class="stat-value"><?php echo $current_streak; ?> dni</h3>
                            </div>
                            <div class="stat-icon" style="color: #69C957; background: rgba(105, 201, 87, 0.2);">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div>
                                <p class="stat-label">Pokurjene Kalorije</p>
                                <h3 class="stat-value"><?php echo $total_calories; ?></h3>
                            </div>
                            <div class="stat-icon" style="color: #F7931A; background: rgba(247, 147, 26, 0.2);">
                                <i class="fas fa-fire-alt"></i>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div>
                                <p class="stat-label">Aktivni Dnevi</p>
                                <h3 class="stat-value"><?php echo $active_days; ?></h3>
                            </div>
                            <div class="stat-icon" style="color: #9370DB; background: rgba(147, 112, 219, 0.2);">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-block trend-card">
                        <h2>Trendi Treningov</h2>
                        <p class="subtitle">Vaše pokurjene kalorije v zadnjih 14 dneh</p>
                        <div class="trend-chart">
                        <?php foreach ($days as $date => $calories): 
                            $height = $calories > 0 ? min($calories / 10, 200) : 2; // višina stolpca max 200px
                        ?>
                        <div class="bar" style="height: <?php echo $height; ?>px;" title="<?php echo date('M d', strtotime($date)) . ': ' . $calories . ' cal'; ?>"></div>
                        <?php endforeach; ?>
                        </div>
                    </div>

                    <script>
                    let bars = document.querySelectorAll('.trend-chart .bar');
                    bars.forEach(bar => {
                        setTimeout(() => bar.classList.add('loaded'), 100);
                    });
                    </script>

                    <div class="dashboard-block recent-workouts">
                        <h2>Zadnji Treningi</h2>
                        <p class="subtitle">Vaše najnovejše fitnes aktivnosti</p>

                        <?php if (!empty($recent_workouts)): ?>
                            <?php foreach ($recent_workouts as $workout): ?>
                                <div class="workout-item">
                                    <div class="icon-details">
                                        <?php 
                                            $icon = "fa-running";
                                            if ($workout['tip_treninga'] === "moč") $icon = "fa-weight-lifting";
                                            elseif ($workout['tip_treninga'] === "kardio") $icon = "fa-biking";
                                            elseif ($workout['tip_treninga'] === "joga") $icon = "fa-yoga"; // lahko default
                                        ?>
                                        <i class="fas <?php echo $icon; ?> workout-icon" style="color: #4C6EF5;"></i>
                                        <div class="details">
                                            <span class="workout-type" style="font-weight: 600;"><?php echo htmlspecialchars($workout['tip_treninga']); ?></span>
                                            <span class="subtitle" style="font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($workout['datum'])); ?></span>
                                        </div>
                                    </div>
                                    <div class="stats-time">
                                        <span class="time" style="color: #CCCCCC;"><?php echo intval($workout['trajanje_min']); ?> min</span>
                                        <span class="calories"><?php echo intval($workout['porabljene_kalorije']); ?> cal</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Ni še nobenih treningov.</p>
                        <?php endif; ?>
                        
                    </div>
                <?php echo "Prijavljen uporabnik ID: " . $_SESSION['uporabnik_id']; ?>
                </div>
            </div>
        </body>
</html>