<?php 
// ===========================
// File: /dos/dostools.php
// Descrizione: dashboard operativa della piattaforma FireMaster
// Il contenuto delle funzionalità è incluso direttamente in questa pagina
// Il menu e il footer sono gestiti da include separati
// ===========================

include_once("pages/header.php"); // Includo il titolo dinamico
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Menu principale -->
<?php include("menubar_index.php"); ?>

<header>
    <h1><?php echo $title; ?></h1>
</header>

<!-- Sezione funzionalità -->
<main id="content">
    <section class="dashboard-section">
        <h2>Funzionalità del sito <br> DOS Tools</h2>

        <p><strong>🧭 Bussola e orientamento del dispositivo</strong><br>
        Mostra l’azimut, la rosa dei venti e i dati GPS (quota, coordinate, comune, provincia).</p>

        <p><strong>💧 Ricerca delle vasche di approvvigionamento idrico</strong><br>
        Calcola i punti acqua più vicini e stima l’efficienza di carico e lancio.</p>

        <p><strong>🔥 Mappa per calcolo area e perimetro incendio</strong><br>
        Permette al DOS di disegnare l’incendio su mappa e scaricare il KML.</p>

        <p><strong>🌙 Calcolo delle effemeridi per un comune calabrese</strong><br>
        Calendario giornaliero con alba, tramonto, festività, orari legali.</p>

        <p><strong>📷 Scatto fotografico con logo e metadati</strong><br>
        Scatta foto con logo e geotag automatico, visibili nell’immagine.</p>

        <p><strong>🧭 Conversione coordinate geografiche DD ⇄ DMS</strong><br>
        Converte in entrambi i formati in modo rapido e preciso.</p>

        <p><strong>📏 Conversione di misure utili al DOS</strong><br>
        Converti distanze e superfici per uso tecnico e operativo.</p>

        <p><strong>🛩️ Richiesta intervento aereo (flotta COAU e Regionale)</strong><br>
        Compila e invia la RIA secondo il modello nazionale COAU.</p>
    </section>
</main>

<!-- Footer -->
<?php include("includes/footer.php"); ?>

</body>
</html>
