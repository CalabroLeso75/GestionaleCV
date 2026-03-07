<!-- menubar_index.php -->
<div class="menu-container">
    <button class="menu-toggle" onclick="toggleMenu()">☰ Mostra Menu</button>
    <div class="menu" id="mainMenu">
        <ul>
            <li><a href="index.php">🏠 Home page</a></li>
			<li><a href="pages/bussola.php">🧭 Bussola e orientamento dispositivo</a></li>			
            <li><a href="pages/distanzadeipunti.php">🔍 Cerca i 10 punti acqua più vicini</a></li>
            <li><a href="pages/map.php">🔥 Calcola Area e perimetro incendio</a></li>
            <li><a href="pages/effemeridi.php">🌙 Calcola effemeridi per mese, anno e comune</a></li>
            <li><a href="pages/foto.php">📷 Scatta una foto con logo aziendale e note</a></li>
            <li><a href="pages/convert_geografic.php">🧭 Converti coordinate geografiche DD ⇄ DMS</a></li>
            <li><a href="pages/convert_misure.php">📏 Converti unità di misura</a></li>
            <li><a href="pages/bussola.php">🧭 Bussola e orientamento dispositivo</a></li>
        </ul>
        <button class="hide-menu" onclick="toggleMenu()">✖ Nascondi barra dei menu</button>
    </div>
</div>

<script>
function toggleMenu() {
    const menu = document.getElementById('mainMenu');
    const toggleBtn = document.querySelector('.menu-toggle');
    if (menu.style.display === "block") {
        menu.style.display = "none";
        toggleBtn.style.display = "block";
    } else {
        menu.style.display = "block";
        toggleBtn.style.display = "none";
    }
}
</script>
