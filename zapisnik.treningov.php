<html>
    <head>    <link rel="stylesheet" href="index.css"> 
 </head>
    <body>    
         <div class="app-container">
        <?php include 'nav.bar.php';?>
    <div class="main-content">
       
    <header class="dashboard-header">
    <div class="header-text">
        <h1>Zapisnik Treningov</h1>
        <p>Spremljajte in upravljajte vse svoje treninge in zgodovino.</p>
    </div>
    <button class="action-button">
        <i class="fas fa-plus"></i> Dodaj Trening
    </button>
    <link rel="stylesheet" href="index.css">
</header>

<div class="dashboard-block workout-log-controls">
    <h2>Vsi Treningi</h2>
    <p class="subtitle">Iskanje, filtriranje in upravljanje zgodovine treningov</p>

    <div class="controls-row">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Išči treninge...">
        </div>
        
        <div class="filter-dropdown">
            <select name="workout-type-filter">
                <option value="all">Vsi Tipi</option>
                <option value="running">Tek</option>
                <option value="weights">Vadba z Utežmi</option>
                <option value="cardio">Kardio</option>
                <option value="yoga">Joga</option>
            </select>
        </div>
    </div>
    
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
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nov 25, 2025</td>
                    <td><span class="workout-type-name">Tek</span></td>
                    <td>30 min</td>
                    <td>300 cal</td>
                    <td><span class="intensity-badge medium">Srednja</span></td>
                    <td>Jutranji tek</td>
                    <td class="actions">
                        <button class="action-btn edit-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn delete-btn"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>Nov 24, 2025</td>
                    <td><span class="workout-type-name">Vadba z Utežmi</span></td>
                    <td>45 min</td>
                    <td>250 cal</td>
                    <td><span class="intensity-badge high">Visoka</span></td>
                    <td>Trening zgornjega dela telesa</td>
                    <td class="actions">
                        <button class="action-btn edit-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn delete-btn"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>Nov 23, 2025</td>
                    <td><span class="workout-type-name">Kolesarjenje</span></td>
                    <td>60 min</td>
                    <td>400 cal</td>
                    <td><span class="intensity-badge medium">Srednja</span></td>
                    <td>Večerna vožnja</td>
                    <td class="actions">
                        <button class="action-btn edit-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn delete-btn"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>Nov 22, 2025</td>
                    <td><span class="workout-type-name">Joga</span></td>
                    <td>40 min</td>
                    <td>150 cal</td>
                    <td><span class="intensity-badge low">Nizka</span></td>
                    <td>Vadba gibljivosti</td>
                    <td class="actions">
                        <button class="action-btn edit-btn"><i class="fas fa-edit"></i></button>
                        <button class="action-btn delete-btn"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</body>
</html><!DOCTYPE html>

<html lang="sl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FitTrack - Zapisnik Treningov</title>

    <!-- Vključitev GLOBALNIH STILOV (index.css) -->

    <link rel="stylesheet" href="index.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

   

    <!-- DODATNI STILI za tabele in kontrole na strani Zapisnik Treningov, ki jih moramo dodati v index.css! -->

    <style>

        /* Posebni stili za kontrole na strani Zapisnik Treningov, ki niso v glavnem index.css */

        .controls-row {

            display: flex;

            gap: 20px;

            margin: 20px 0;

            flex-wrap: wrap;

        }



        .search-box {

            display: flex;

            align-items: center;

            background: #0D1117; /* Temnejše ozadje za iskalno polje */

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

       

        /* Tabela */

        .workout-table-container {

            overflow-x: auto; /* Omogoča drsenje na mobilnih napravah */

            margin-top: 20px;

        }



        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 700px; /* Minimalna širina tabele za boljši prikaz */

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



        /* Oznake intenzivnosti */

        .intensity-badge {

            padding: 4px 10px;

            border-radius: 20px;

            font-size: 0.8rem;

            font-weight: 600;

            display: inline-block;

        }

       

        .intensity-badge.low {

            background: rgba(105, 201, 87, 0.2);

            color: #69C957; /* Zelen */

        }

       

        .intensity-badge.medium {

            background: rgba(76, 110, 245, 0.2);

            color: #4C6EF5; /* Moder */

        }

       

        .intensity-badge.high {

            background: rgba(247, 147, 26, 0.2);

            color: #F7931A; /* Oranžen */

        }

       

        /* Akcijski gumbi v tabeli */

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

            color: #F54C4C; /* Rdeča ikona */

        }

       

        .action-btn.delete-btn:hover {

             color: #FFFFFF;

             background: #F54C4C;

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

                    <h1>Zapisnik Treningov</h1>

                    <p class="subtitle">Spremljajte in upravljajte vse svoje treninge in zgodovino.</p>

                </div>

                <a href="dodaj.trening.php" class="action-button">

                    <i class="fas fa-plus"></i> Dodaj Trening

                </a>

            </header>



            <!-- Blok s Kontrolami in Tabela -->

            <div class="dashboard-block workout-log-controls">

                <h2>Vsi Treningi</h2>

                <p class="subtitle">Iskanje, filtriranje in upravljanje zgodovine treningov</p>



                <div class="controls-row">

                    <div class="search-box">

                        <i class="fas fa-search"></i>

                        <input type="text" placeholder="Išči treninge..." aria-label="Iskanje treningov">

                    </div>

                   

                    <div class="filter-dropdown">

                        <select name="workout-type-filter" aria-label="Filter tipa treninga">

                            <option value="all">Vsi Tipi</option>

                            <option value="running">Tek</option>

                            <option value="weights">Vadba z Utežmi</option>

                            <option value="cardio">Kardio</option>

                            <option value="yoga">Joga</option>

                        </select>

                    </div>

                </div>

               

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

                                <th>Akcije</th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr>

                                <td>Nov 25, 2025</td>

                                <td><span class="workout-type-name">Tek</span></td>

                                <td>30 min</td>

                                <td>300 cal</td>

                                <td><span class="intensity-badge medium">Srednja</span></td>

                                <td>Jutranji tek</td>

                                <td class="actions">

                                    <button class="action-btn edit-btn" aria-label="Uredi"><i class="fas fa-edit"></i></button>

                                    <button class="action-btn delete-btn" aria-label="Izbriši"><i class="fas fa-trash-alt"></i></button>

                                </td>

                            </tr>

                            <tr>

                                <td>Nov 24, 2025</td>

                                <td><span class="workout-type-name">Vadba z Utežmi</span></td>

                                <td>45 min</td>

                                <td>250 cal</td>

                                <td><span class="intensity-badge high">Visoka</span></td>

                                <td>Trening zgornjega dela telesa</td>

                                <td class="actions">

                                    <button class="action-btn edit-btn" aria-label="Uredi"><i class="fas fa-edit"></i></button>

                                    <button class="action-btn delete-btn" aria-label="Izbriši"><i class="fas fa-trash-alt"></i></button>

                                </td>

                            </tr>

                            <tr>

                                <td>Nov 23, 2025</td>

                                <td><span class="workout-type-name">Kolesarjenje</span></td>

                                <td>60 min</td>

                                <td>400 cal</td>

                                <td><span class="intensity-badge medium">Srednja</span></td>

                                <td>Večerna vožnja</td>

                                <td class="actions">

                                    <button class="action-btn edit-btn" aria-label="Uredi"><i class="fas fa-edit"></i></button>

                                    <button class="action-btn delete-btn" aria-label="Izbriši"><i class="fas fa-trash-alt"></i></button>

                                </td>

                            </tr>

                            <tr>

                                <td>Nov 22, 2025</td>

                                <td><span class="workout-type-name">Joga</span></td>

                                <td>40 min</td>

                                <td>150 cal</td>

                                <td><span class="intensity-badge low">Nizka</span></td>

                                <td>Vadba gibljivosti</td>

                                <td class="actions">

                                    <button class="action-btn edit-btn" aria-label="Uredi"><i class="fas fa-edit"></i></button>

                                    <button class="action-btn delete-btn" aria-label="Izbriši"><i class="fas fa-trash-alt"></i></button>

                                </td>

                            </tr>

                            <tr>

                                <td>Nov 21, 2025</td>

                                <td><span class="workout-type-name">Plavanje</span></td>

                                <td>45 min</td>

                                <td>350 cal</td>

                                <td><span class="intensity-badge medium">Srednja</span></td>

                                <td>Kardio dan</td>

                                <td class="actions">

                                    <button class="action-btn edit-btn" aria-label="Uredi"><i class="fas fa-edit"></i></button>

                                    <button class="action-btn delete-btn" aria-label="Izbriši"><i class="fas fa-trash-alt"></i></button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


        </div>

    </div>

</body>

</html>