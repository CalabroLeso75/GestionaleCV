<?php $title = "Segnala un Incendio"; ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body onload="scrollToContent()">

<header>
    <h1><?php echo $title; ?></h1>
</header>

<!-- MENU -->
<div class="menu-container">
    <button class="menu-toggle">☰ Menu</button>
    <nav class="menu" id="menu">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="pages/convert_misure.php">Conversioni di Misure</a></li>
            <li><a href="pages/convert_geografic.php">Conversione Coordinate</a></li>
            <li><a href="pages/map.php">Strumenti su mappa</a></li>
            <li><a href="pages/effemeridi.php">Ricerca Effemeridi</a></li>
            <li><a href="pages/foto.php">Scatta una foto</a></li>
            <li><a href="pages/segnala.php">Segnala un incendio</a></li>
            <li><a href="pages/profile.php">Profilo Personale</a></li>
        </ul>
        <button class="hide-menu">Nascondi barra dei menu</button>
    </nav>
</div>

<!-- CONTENUTO DELLA PAGINA -->
<main id="content">
    <section>
        <h2>Segnala un Incendio</h2>
        <p>Compila il modulo per segnalare un incendio.</p>
    </section>
</main>

<footer>
    <p>&copy; 2024 Fire Master - Tutti i diritti riservati.</p>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var menu = document.getElementById("menu");
        var toggleButton = document.querySelector(".menu-toggle");
        var hideMenuButton = document.querySelector(".hide-menu");

        // Mostra il menu quando si clicca sull'hamburger
        toggleButton.addEventListener("click", function () {
            menu.style.display = "block";
            toggleButton.style.display = "none";
        });

        // Nasconde il menu quando si clicca sul pulsante "Nascondi barra dei menu"
        hideMenuButton.addEventListener("click", function () {
            menu.style.display = "none";
            toggleButton.style.display = "block";
        });
    });
</script>


</body>
</html>
