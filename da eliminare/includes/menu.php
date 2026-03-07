<?php
// Determina il percorso base
$base_path = (basename($_SERVER['PHP_SELF']) == "index.php") ? "" : "../";
$current_page = basename($_SERVER['PHP_SELF']);

$menu_items = [
    "index.php" => "Home",
    "pages/convert_misure.php" => "Conversioni di Misure",
    "pages/convert_geografic.php" => "Conversione Coordinate",
    "pages/map.php" => "Strumenti su mappa",
    "pages/effemeridi.php" => "Ricerca Effemeridi",
    "pages/foto.php" => "Scatta una foto",
    "pages/segnala.php" => "Segnala un incendio",
    "pages/profile.php" => "Profilo Personale"
];

echo '<nav class="menu" id="sideMenu"><ul>';
foreach ($menu_items as $file => $label) {
    // Nasconde o disabilita il link della pagina corrente
    if (basename($file) === $current_page) {
        echo "<li><span style='color:gray; display:block; padding:10px; text-align:center;'>$label</span></li>";
    } else {
        echo "<li><a href='{$base_path}$file'>$label</a></li>";
    }
}
echo '</ul></nav>';
?>

<script>
    function toggleMenu() {
        const menu = document.getElementById("sideMenu");
        menu.style.left = menu.style.left === "0px" ? "-250px" : "0px";
    }
</script>
