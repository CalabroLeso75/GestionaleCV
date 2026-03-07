<?php include("header.php"); ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trova i 10 punti acqua più Vicini</title>
    <link rel="stylesheet" href="../css/style.css">
    
</head>
<body>
<?php include("menubar.php"); ?>
<header>
    <h1>Trova i 10 punti acqua più vicini</h1>
</header>

<!-- CONTENUTO DELLA PAGINA -->
<div class="conversion-container">
    <button onclick="getCurrentPosition()">📍 Posizione Corrente</button><br><br>

    <h3>Inserisci le coordinate in formato DMS:</h3>
    <input type="number" id="lat_deg" placeholder="Gradi">
    <input type="number" id="lat_min" placeholder="Minuti">
    <input type="number" id="lat_sec" placeholder="Secondi">
    <select id="lat_dir"><option value="N">N</option><option value="S">S</option></select><br>

    <input type="number" id="lng_deg" placeholder="Gradi">
    <input type="number" id="lng_min" placeholder="Minuti">
    <input type="number" id="lng_sec" placeholder="Secondi">
    <select id="lng_dir"><option value="E">E</option><option value="W">W</option></select><br><br>

    <button onclick="processCoordinates()">🔍 Trova Punti</button>

    <div id="results"></div>
</div>

<footer>
    <p>&copy; 2024 Strumenti DOS - Azienda Calabria Verde</p>
</footer>
<script src="../js/acqua.js" defer></script> <!-- Importa il file JS esterno -->
</body>
</html>
