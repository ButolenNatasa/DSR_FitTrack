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

    // --- LOGIKA ZA ISKANJE IN FILTRIRANJE ---
    $search = $_GET['search'] ?? '';
    $filter_tip = $_GET['workout-type-filter'] ?? 'all';

    // Osnovni SQL
    $sql = "SELECT * FROM trening WHERE uporabnik_id = ?";
    $params = [$uporabnik_id];
    $types = "i";

    // Dodajanje iskalnega niza (išče v tipu treninga in opombah)
    if (!empty($search)) {
        $sql .= " AND (tip_treninga LIKE ? OR opombe LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "ss";
    }

    // Dodajanje filtra za tip
    if ($filter_tip !== 'all') {
        $sql .= " AND tip_treninga = ?";
        $params[] = $filter_tip;
        $types .= "s";
    }

    // Sortiranje (vedno po datumu nazaj)
    $sql .= " ORDER BY datum DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $treningi = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FitTrack - Zapisnik Treningov</title>
        <link rel="stylesheet" href="index.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            /* Posebni stili za kontrole na strani Zapisnik Treningov */
            .controls-row { 
                display: flex; 
                gap: 20px; 
                margin: 20px 0; 
                flex-wrap: wrap; 
            }
            .search-box { 
                display: flex; 
                align-items: center; 
                background: #0D1117; 
                border: 1px solid #30363D; 
                border-radius: 8px; 
                padding: 8px 15px; 
                flex-grow: 1; 
                max-width: 400px; 
            }
            .search-box i { 
                color: #AAAAAA; 
                margin-right: 10px; 
            }
            .search-box input[type="text"] { 
                background: none; 
                border: none; 
                color: #E6E6E6; 
                padding: 0; 
                font-size: 1rem;
                outline: none; 
                width: 100%; 
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
            }
            .workout-table-container { 
                overflow-x: auto; 
                margin-top: 20px; 
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                min-width: 700px; 
            }
            thead tr { 
                background-color: #21262D; 
                color: #FFFFFF; 
                font-weight: 600; 
            }
            th, td { 
                padding: 12px 15px; 
                text-align: left; 
                border-bottom: 1px solid #30363D; 
            }
            tbody tr:hover { 
                background-color: #1A1F27; 
            }
            .intensity-badge { 
                padding: 4px 10px; 
                border-radius: 20px; 
                font-size: 0.8rem; 
                font-weight: 600; 
                display: inline-block; 
            }
            .intensity-badge.low { 
                background: rgba(105, 201, 87, 0.2); 
                color: #69C957; 
            }
            .intensity-badge.medium { 
                background: rgba(76, 110, 245, 0.2); 
                color: #4C6EF5; 
            }
            .intensity-badge.high { 
                background: rgba(247, 147, 26, 0.2); 
                color: #F7931A; 
            }
            .actions { 
                display: flex; 
                gap: 5px; 
            }
            .action-btn { 
                background: none; 
                border: 1px solid #30363D; 
                color: #AAAAAA; 
                padding: 6px 10px; 
                border-radius: 6px; 
                cursor: pointer; 
                transition: all 0.2s; 
                font-size: 0.85rem; 
            }
            .action-btn:hover { 
                background: #30363D; 
                color: #FFFFFF; 
            }
            .action-btn.delete-btn { 
                color: #F54C4C; 
            }
            .action-btn.delete-btn:hover { 
                color: #FFFFFF; 
                background: #F54C4C; 
            }
        </style>
    </head>
        <body>
            <div class="app-container">
                <?php include 'nav.bar.php';?>
                <div class="main-content">
                    <header class="dashboard-header">
                        <div>
                            <h1>Zapisnik Treningov</h1>
                            <p class="subtitle">Spremljajte in upravljajte vse svoje treninge in zgodovino.</p>
                        </div>
                        <a href="dodaj_trening.php" class="action-button">
                            <i class="fas fa-plus"></i> Dodaj Trening
                        </a>
                    </header>

                    <div class="dashboard-block workout-log-controls">
                        <h2>Vsi Treningi</h2>
                        <p class="subtitle">Iskanje, filtriranje in upravljanje zgodovine treningov</p>

                        <form method="GET" action="" class="controls-row">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" placeholder="Išči treninge..." 
                                    value="<?php echo htmlspecialchars($search); ?>" aria-label="Iskanje treningov">
                            </div>
                            
                            <div class="filter-dropdown">
                                <select name="workout-type-filter" onchange="this.form.submit()" aria-label="Filter tipa treninga">
                                    <option value="all" <?php echo $filter_tip == 'all' ? 'selected' : ''; ?>>Vsi Tipi</option>
                                    <option value="moč" <?php echo $filter_tip == 'moč' ? 'selected' : ''; ?>>Trening moči</option>
                                    <option value="kardio" <?php echo $filter_tip == 'kardio' ? 'selected' : ''; ?>>Kardio</option>
                                    <option value="hiit" <?php echo $filter_tip == 'hiit' ? 'selected' : ''; ?>>HIIT</option>
                                    <option value="joga" <?php echo $filter_tip == 'joga' ? 'selected' : ''; ?>>Joga</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="action-btn">Filtriraj</button>
                            <?php if(!empty($search) || $filter_tip != 'all'): ?>
                                <a href="zapisnik.treningov.php" class="action-btn" style="text-decoration:none;">Počisti</a>
                            <?php endif; ?>
                        </form>

                        <div class="workout-table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Vadba</th>
                                        <th>Trajanje</th>
                                        <th>Kalorije</th>
                                        <th>Intenzivnost</th>
                                        <th>Opombe</th>
                                        <th>Izbris</th>
                                    </tr>
                                </thead>
                                <?php if (!empty($treningi)): ?>
                                    <?php foreach ($treningi as $trening): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($trening['datum'])); ?></td>
                                            <td><span class="workout-type-name"><?php echo htmlspecialchars($trening['tip_treninga']); ?></span></td>
                                            <td><?php echo intval($trening['trajanje_min']); ?> min</td>
                                            <td><?php echo intval($trening['porabljene_kalorije']); ?> cal</td>
                                            <td>
                                                <?php 
                                                    // avtomatski izračun intenzivnosti po kalorijah
                                                    $cal = intval($trening['porabljene_kalorije']);

                                                    if ($cal <= 200) {
                                                        $intenzivnost = 'low';
                                                    } elseif ($cal <= 400) {
                                                        $intenzivnost = 'medium';
                                                    } else {
                                                        $intenzivnost = 'high';
                                                    }

                                                    echo '<span class="intensity-badge ' . $intenzivnost . '">' . ucfirst($intenzivnost) . '</span>';
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($trening['opombe']); ?></td>
                                            <td class="actions">
                                                <a href="izbris.trening.php?id=<?php echo $trening['trening_id']; ?>" 
                                                class="action-btn delete-btn" 
                                                onclick="return confirm('Res želiš izbrisati ta trening?');"
                                                aria-label="Izbriši">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>                                            
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7">Ni še nobenih treningov.</td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                    <?php echo "Prijavljen uporabnik ID: " . $_SESSION['uporabnik_id']; ?>
                </div>
            </div>
        </body>
</html>
