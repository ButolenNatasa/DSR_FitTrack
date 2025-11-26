<?php
session_start();
require_once 'config.php';
$conn = connect_db();

if (!isset($_SESSION['uporabnik_id'])) {
    die("Napaka: uporabnik ni prijavljen.");
}

$uporabnik_id = $_SESSION['uporabnik_id'];

$sql = "SELECT * FROM slika_napredka 
        WHERE uporabnik_id = ?
        ORDER BY datum_nalozitve DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uporabnik_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FitTrack - Slike Napredka</title>
        <!-- Vključitev GLOBALNIH STILOV (index.css) -->
        <link rel="stylesheet" href="index.css"> 
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        
        <!-- DODATNI STILI za galerijo slik in modalno okno -->
        <style>
            /* Galerija slik (Grid) */
            .photo-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }

            .photo-card {
                background-color: #161B22; /* Telo kartice */
                border: 1px solid #30363D;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
                transition: transform 0.2s;
            }
            
            .photo-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.5);
            }

            .photo-img-container {
                width: 100%;
                padding-top: 100%; /* Ustvari kvadratni okvir za sliko */
                position: relative;
                overflow: hidden;
                background-color: #0D1117;
            }

            .progress-img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover; /* Pokrij celoten okvir */
                transition: filter 0.3s;
            }
            
            .black-white-filter {
                filter: grayscale(100%);
            }

            .photo-details {
                padding: 15px;
                color: #E6E6E6;
                font-size: 0.9rem;
            }

            .photo-date {
                color: #AAAAAA;
                margin-bottom: 5px;
                font-weight: 600;
            }
            
            .photo-note {
                color: #C9D1D9;
            }
            
            /* Modalno okno za nalaganje */
            .modal {
                display: none; /* Skrij privzeto */
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.7);
                justify-content: center;
                align-items: center;
            }

            .modal-content {
                background-color: #0D1117;
                border: 1px solid #30363D;
                border-radius: 12px;
                width: 90%;
                max-width: 500px;
                animation: slideUp 0.3s forwards;
                padding: 0;
            }
            
            .modal-header {
                padding: 15px 25px;
                border-bottom: 1px solid #30363D;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .modal-header h2 {
                margin: 0;
                font-size: 1.5rem;
                color: #C9D1D9;
            }

            .close-btn {
                color: #AAAAAA;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
                transition: color 0.2s;
            }

            .close-btn:hover,
            .close-btn:focus {
                color: #F7931A;
            }
            
            .modal-form {
                padding: 25px;
            }

            .modal-subtitle {
                color: #AAAAAA;
                margin-top: 0;
                margin-bottom: 20px;
            }
            
            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #C9D1D9;
            }

            .form-group input[type="date"],
            .form-group input[type="text"],
            .form-group input[type="file"],
            .form-group textarea {
                width: 100%;
                padding: 10px;
                border: 1px solid #30363D;
                border-radius: 6px;
                background-color: #161B22;
                color: #E6E6E6;
                font-size: 1rem;
                box-sizing: border-box;
                outline: none;
                transition: border-color 0.2s;
            }

            .form-group input[type="date"]:focus,
            .form-group input[type="text"]:focus,
            .form-group textarea:focus {
                border-color: #4C6EF5; /* Modra ob fokusu */
            }
            
            .modal-submit-btn {
                /* Uporabi stil action-button za pošiljanje */
                width: 100%;
                background-color: #F7931A; 
                color: #0D1117;
                font-weight: 700;
                border: none;
                padding: 12px;
                border-radius: 8px;
                cursor: pointer;
                transition: background-color 0.2s;
            }
            
            .modal-submit-btn:hover {
                background-color: #E08518; 
            }

            @keyframes slideUp {
                from { transform: translateY(100px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
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
                    
                    <!-- Glava Strani (Header) - Posodobljeno na Slike Napredka -->
                    <header class="dashboard-header">
                        <div>
                            <h1>Slike Napredka</h1>
                            <p class="subtitle">Spremljajte svoje potovanje preobrazbe z vizualnimi dokazi.</p>
                        </div>
                        <!-- Gumb za odprtje modalnega okna -->
                        <button class="action-button" id="open-modal-btn">
                            <i class="fas fa-upload"></i> Naloži Sliko
                        </button>
                    </header>

                    <!-- Blok s Galerijo Slik -->
                    <div class="dashboard-block photo-gallery-block">
                        <h2>Galerija Slik</h2>
                        <p class="subtitle">Vaše slike napredka v kronološkem vrstnem redu.</p>
                        
                        <div class="photo-grid">

                        <?php 
                        if ($result->num_rows > 0) {
                            echo "Število slik: " . $result->num_rows;
                        } else {
                            echo "Uporabnik nima nobene slike v bazi.";
                        }

                        while ($row = $result->fetch_assoc()): 
                        ?>
                            <div class="photo-card">
                                <div class="photo-img-container">
                                    <img src="<?= $row['pot_do_slike']; ?>" 
                                        alt="<?= htmlspecialchars($row['naslov']); ?>" 
                                        class="progress-img">
                                </div>

                                <div class="photo-details">
                                    <p class="photo-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?= date("F j, Y", strtotime($row['datum_nalozitve'])); ?>
                                    </p>

                                    <p class="photo-note">
                                        <?= htmlspecialchars($row['naslov']); ?>
                                    </p>

                                    <?php if (!empty($row['opis'])): ?>
                                        <p class="photo-note"><?= htmlspecialchars($row['opis']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>

                        </div>
                    </div>

                    <!-- Modalno okno za nalaganje slik -->
                    <div id="upload-modal" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2>Naloži Sliko Napredka</h2>
                                <span class="close-btn">&times;</span>
                            </div>
                            <form class="modal-form" action="nalozi_sliko.php" method="POST" enctype="multipart/form-data">
                                
                                <p class="modal-subtitle">Dodajte novo sliko, da spremljate svoj napredek.</p>

                                <div class="form-group">
                                    <label for="naslov">Naslov</label>
                                    <input type="text" id="naslov" name="naslov" required>
                                    </div>

                                <div class="form-group">
                                    <label for="photo-file">Slika (Nalaganje datoteke)</label>
                                    <input type="file" id="photo-file" name="slika_napredka">
                                </div>

                                <div class="form-group">
                                    <label for="photo-url">Ali prilepite URL slike (samo za testiranje)</label>
                                    <input type="text" id="photo-url" name="slika_url">

                                </div>

                                <div class="form-group">
                                    <label for="photo-notes">Opombe</label>
                                    <textarea id="photo-notes" name="opis" placeholder="Dodajte opombe o tej sliki..."></textarea>
                                </div>

                                <button type="submit" class="action-button"><i class="fas fa-upload"></i>
                                    Naloži Sliko
                                </button>
                            </form>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const modal = document.getElementById('upload-modal');
                            const openBtn = document.getElementById('open-modal-btn');
                            const closeBtn = document.querySelector('.close-btn');

                            // Odpri modalno okno
                            openBtn.onclick = function() {
                                modal.style.display = 'flex';
                            }

                            // Zapri modalno okno s križcem
                            closeBtn.onclick = function() {
                                modal.style.display = 'none';
                            }

                            // Zapri modalno okno s klikom zunaj njega
                            window.onclick = function(event) {
                                if (event.target === modal) {
                                    modal.style.display = 'none';
                                }
                            }
                            
                        });
                    </script>

                    <?php echo "Prijavljen uporabnik ID: " . $_SESSION['uporabnik_id']; ?>
                    
                </div>
                
            </div>
            
        </body>
</html>