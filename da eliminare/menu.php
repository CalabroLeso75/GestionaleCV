<!-- Pulsante di apertura menu mobile (solo su mobile) -->
<div class="menu-toggle" onclick="toggleMenu()">☰ Menu</div>

<nav class="menu" id="sideMenu">
    <ul>
        <li><a onclick="loadPage('pages/home.php')">Home</a></li>
        <li><a onclick="loadPage('pages/convert_misure.php')">Conversioni di Misure</a></li>
        <li><a onclick="loadPage('pages/convert_geografic.php')">Conversione Coordinate</a></li>
        <li><a onclick="loadPage('pages/map.php')">Strumenti su mappa</a></li>
        <li><a onclick="loadPage('pages/effemeridi.php')">Ricerca Effemeridi</a></li>
        <li><a onclick="loadPage('pages/foto.php')">Scatta una foto</a></li>
        <li><a onclick="loadPage('pages/segnala.php')">Segnala un incendio</a></li>
        <li><a onclick="loadPage('pages/profile.php')">Profilo Personale</a></li>
    </ul>
</nav>

<script>
    function toggleMenu() {
        document.getElementById("sideMenu").classList.toggle("active");
    }

    function loadPage(page) {
        fetch(page)
        .then(response => response.text())
        .then(data => {
            document.getElementById("content").innerHTML = data;
        })
        .catch(error => console.error('Errore nel caricamento della pagina:', error));
    }

    // Carica la home di default senza header
    window.onload = function() {
        loadPage('pages/home.php');
    };
</script>
