<!DOCTYPE html>
<html lang="sl">
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
                    
                    <div class="photo-card">
                        <div class="photo-img-container">
                            <!-- Uporabljamo placeholder z opisom, ker zunanjih slik ne smemo nalagati -->
                            <img src="https://placehold.co/500x500/161B22/C9D1D9?text=Začetna+točka" alt="Začetna točka" class="progress-img">
                        </div>
                        <div class="photo-details">
                            <p class="photo-date"><i class="fas fa-calendar-alt"></i> November 01, 2025</p>
                            <p class="photo-note">Začetna točka (Starting point)</p>
                        </div>
                    </div>

                    <div class="photo-card">
                        <div class="photo-img-container">
                             <img src="https://placehold.co/500x500/161B22/C9D1D9?text=1.+Teden" alt="Napredek 1. teden" class="progress-img">
                        </div>
                        <div class="photo-details">
                            <p class="photo-date"><i class="fas fa-calendar-alt"></i> November 08, 2025</p>
                            <p class="photo-note">1. teden napredka (Week 1 progress)</p>
                        </div>
                    </div>

                    <div class="photo-card">
                        <div class="photo-img-container">
                             <!-- Dodan črno-beli filter preko CSS razreda black-white-filter -->
                             <img src="https://placehold.co/500x500/161B22/C9D1D9?text=2.+Teden" alt="Napredek 2. teden" class="progress-img black-white-filter">
                        </div>
                        <div class="photo-details">
                            <p class="photo-date"><i class="fas fa-calendar-alt"></i> November 15, 2025</p>
                            <p class="photo-note">2. teden (Moč)</p>
                        </div>
                    </div>

                    <div class="photo-card">
                        <div class="photo-img-container">
                            <img src="https://placehold.co/500x500/161B22/C9D1D9?text=3.+Teden" alt="Napredek 3. teden" class="progress-img">
                        </div>
                        <div class="photo-details">
                            <p class="photo-date"><i class="fas fa-calendar-alt"></i> November 22, 2025</p>
                            <p class="photo-note">3. teden - Vidni rezultati</p>
                        </div>
                    </div>
                    
                </div>
            </div>

            <!-- Modalno okno za nalaganje slik -->
            <div id="upload-modal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Naloži Sliko Napredka</h2>
                        <span class="close-btn">&times;</span>
                    </div>
                    <form class="modal-form" onsubmit="event.preventDefault(); alert('Funkcionalnost nalaganja še ni implementirana.');">
                        
                        <p class="modal-subtitle">Dodajte novo sliko, da spremljate svoj napredek.</p>

                        <div class="form-group">
                            <label for="photo-date">Datum</label>
                            <input type="date" id="photo-date" value="2025-11-25" required>
                        </div>

                        <div class="form-group">
                            <label for="photo-file">Slika (Nalaganje datoteke)</label>
                            <input type="file" id="photo-file" name="progress_photo">
                        </div>

                        <div class="form-group">
                            <label for="photo-url">Ali prilepite URL slike (samo za testiranje)</label>
                            <input type="text" id="photo-url" placeholder="https://placehold.co/500x500">
                        </div>

                        <div class="form-group">
                            <label for="photo-notes">Opombe</label>
                            <textarea id="photo-notes" rows="3" placeholder="Dodajte opombe o tej sliki..."></textarea>
                        </div>

                        <button type="submit" class="modal-submit-btn">
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
                    
                    // Preprečimo ponastavitev modalnega okna ob oddaji obrazca
                    const form = document.querySelector('.modal-form');
                    form.onsubmit = function(event) {
                        event.preventDefault();
                        // Namesto 'alert' uporabimo začasno sporočilo (sicer v UI modal)
                        console.log('Slika pripravljena za nalaganje!'); 
                        modal.style.display = 'none'; // Zapremo modal po simulaciji nalaganja
                    };
                });
            </script>
            
        </div>
        
    </div>
    
</body>
</html>