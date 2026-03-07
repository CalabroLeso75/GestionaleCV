<?php 
// nomefile: effemeridi.php
// Questa pagina mostra le effemeridi (alba e tramonto) per un comune, mese e anno selezionati

// Abilito la visualizzazione degli errori in fase di debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Titolo della pagina (usato in <title> e nell'header)
$title = "Effemeridi Solari";

include("header.php"); 
include '../data/comuni.php'; // Includo il file con l'elenco dei comuni e relative coordinate
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>

    <!-- Librerie esterne -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/suncalc/1.8.0/suncalc.min.js"></script>
    <script src="../js/effemeridi.js"></script>

    <!-- Stile personalizzato -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        #ephemeridesTable {
            width: 100%;
            font-size: 0.85em;
        }
        #ephemeridesTable th, #ephemeridesTable td {
            padding: 4px 6px;
        }
        .festivo {
            color: red;
        }
    </style>
</head>

<body onload="scrollToContent()">
<?php include("menubar.php"); ?>

<header>
    <h1><?php echo $title; ?></h1>
</header>

<!-- CONTENUTO DELLA PAGINA -->
<main id="content">
    <section>
        <p align="center">Seleziona il mese, l'anno e il comune per calcolare le effemeridi.</p>

        <div class="conversion-container">
            <label for="month">Mese:</label>
            <select id="month">
                <?php
                $mesi = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];
                for ($i = 1; $i <= 12; $i++) : ?>
                    <option value="<?php echo $i; ?>"><?php echo $mesi[$i - 1]; ?></option>
                <?php endfor; ?>
            </select>

            <label for="year">Anno:</label>
            <input type="number" id="year" min="1900" max="2100" placeholder="Inserisci l'anno">
            
            <input type="hidden" id="selectedLat">
            <input type="hidden" id="selectedLon">
            
            <label for="location">Seleziona comune:</label>
            <select id="location" onchange="updateCoordinates()">
                <option value="" disabled selected>Seleziona un comune</option>
                <?php foreach ($comuni as $comune): ?>
                    <option value="<?php echo $comune['nome']; ?>" data-lat="<?php echo $comune['lat']; ?>" data-lon="<?php echo $comune['lon']; ?>">
                        <?php echo $comune['nome']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button onclick="calculateEphemerides()">Calcola</button>

            <div id="result">
                <h3>Risultato:</h3>
                <table id="ephemeridesTable">
                    <thead>
                        <tr>
                            <th colspan="4">
                                Effemeridi: <span id="monthYearHeader"></span> <span id="locationHeader"></span>
                            </th>
                        </tr>
                        <tr>
                            <th>Giorno</th>
                            <th>Alba</th>
                            <th>Tramonto</th>
                            <th>Ora</th>
                        </tr>
                    </thead>
                    <tbody id="ephemerides"></tbody>
                </table>
            </div>

            <button onclick="printTable()">Stampa</button>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2024 Strumenti DOS - Azienda Calabria Verde</p>
</footer>

</body>
</html>
