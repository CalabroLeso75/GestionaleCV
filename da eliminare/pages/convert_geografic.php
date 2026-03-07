<?php include("header.php"); ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body onload="scrollToContent()">
<?php include("menubar.php"); ?>
<header>
    <h1><?php echo $title; ?></h1>
</header>



<!-- CONTENUTO DELLA PAGINA -->
<main id="content">
    <section>
        <p>Seleziona il tipo di coordinate da convertire e inserisci i dati.</p>

        <div class="conversion-container">
            <label for="fromType">Converti da:</label>
            <select id="fromType" onchange="updateToType()">
                <option value="" disabled selected>Seleziona un formato</option>
                <option value="dms">DMS (Gradi, Minuti, Secondi)</option>
                <option value="dd">DD (Decimali)</option>
            </select>

            <label for="toType">A:</label>
            <select id="toType" disabled></select>

            <div id="coordinateInputs"></div>

            <button onclick="convert()">Converti</button>

            <h3>Risultato: <span id="result">-</span></h3>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2024 Strumenti DOS - Azienda Calabria Verde</p>
</footer>

<script>
    const coordinateTypes = ["dms", "dd"];

    function updateToType() {
        const fromType = document.getElementById("fromType").value;
        const toType = document.getElementById("toType");

        toType.innerHTML = "";
        toType.disabled = false;

        coordinateTypes.forEach(type => {
            if (type !== fromType) {
                let option = document.createElement("option");
                option.value = type;
                option.textContent = type.toUpperCase();
                toType.appendChild(option);
            }
        });

        updateInputs(fromType);
    }

    function updateInputs(type) {
        const container = document.getElementById("coordinateInputs");
        container.innerHTML = "";

        if (type === "dms") {
            container.innerHTML = `
                <label>Latitudine:</label>
                <input type="number" id="latDeg" placeholder="Gradi">
                <input type="number" id="latMin" placeholder="Minuti" oninput="validateMinutesSeconds(this, 59)">
                <input type="number" id="latSec" placeholder="Secondi" step="0.0001" oninput="validateMinutesSeconds(this, 59.9999)">

                <label>Longitudine:</label>
                <input type="number" id="lonDeg" placeholder="Gradi">
                <input type="number" id="lonMin" placeholder="Minuti" oninput="validateMinutesSeconds(this, 59)">
                <input type="number" id="lonSec" placeholder="Secondi" step="0.0001" oninput="validateMinutesSeconds(this, 59.9999)">
            `;
        } else if (type === "dd") {
            container.innerHTML = `
                <label>Latitudine:</label>
                <input type="number" id="latDd" placeholder="Latitudine" step="0.000001">

                <label>Longitudine:</label>
                <input type="number" id="lonDd" placeholder="Longitudine" step="0.000001">
            `;
        }
    }

    function validateMinutesSeconds(input, maxValue) {
        if (parseFloat(input.value) >= maxValue) {
            input.value = maxValue;
        }
    }

    function convert() {
        const fromType = document.getElementById("fromType").value;
        const toType = document.getElementById("toType").value;
        const resultElement = document.getElementById("result");
        let result = "";

        if (fromType === "dms" && toType === "dd") {
            let lat = dmsToDd(
                parseFloat(document.getElementById("latDeg").value),
                parseFloat(document.getElementById("latMin").value),
                parseFloat(document.getElementById("latSec").value)
            );
            let lon = dmsToDd(
                parseFloat(document.getElementById("lonDeg").value),
                parseFloat(document.getElementById("lonMin").value),
                parseFloat(document.getElementById("lonSec").value)
            );
            result = `${lat.toFixed(6)} N, ${lon.toFixed(6)} E`;
        } else if (fromType === "dd" && toType === "dms") {
            let latDms = ddToDms(parseFloat(document.getElementById("latDd").value));
            let lonDms = ddToDms(parseFloat(document.getElementById("lonDd").value));
            result = `${latDms} N, ${lonDms} E`;
        } else {
            result = "Conversione non ancora implementata.";
        }

        resultElement.textContent = result;
    }

    function dmsToDd(deg, min, sec) {
        return deg + min / 60 + sec / 3600;
    }

    function ddToDms(dd) {
        let deg = Math.floor(dd);
        let min = Math.floor((dd - deg) * 60);
        let sec = ((dd - deg - min / 60) * 3600).toFixed(4);
        return `${deg}° ${min}' ${sec}"`;
    }

    function scrollToContent() {
        document.getElementById("content").scrollIntoView();
    }
	document.addEventListener("DOMContentLoaded", function () {
    const menu = document.getElementById("menu");
    const toggleButton = document.querySelector(".menu-toggle");
    const hideMenuButton = document.querySelector(".hide-menu");

    // Mostra il menu quando si clicca sull'hamburger
    toggleButton.addEventListener("click", function () {
        menu.style.display = "block"; // Mostra il menu
        toggleButton.style.display = "none"; // Nasconde l'hamburger
    });

    // Nasconde il menu quando si clicca su "Nascondi barra dei menu"
    hideMenuButton.addEventListener("click", function () {
        menu.style.display = "none"; // Nasconde il menu
        toggleButton.style.display = "block"; // Mostra di nuovo l'hamburger
    });
});

</script>

<style>
    /* Testo centrale per tutti gli elementi */
    body, h1, h2, h3, p, label, select, input, button {
        text-align: center;
    }

    .conversion-container {
        max-width: 400px;
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .conversion-container label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
    }

    .conversion-container select, .conversion-container input {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    .conversion-container button {
        margin-top: 20px;
        padding: 10px;
        width: 100%;
        background: #ff5722;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
    }

    .conversion-container button:hover {
        background: #e64a19;
    }

    footer {
        text-align: center;
    }
	
</style>

</body>
</html>
