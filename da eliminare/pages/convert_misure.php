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
        <p>Seleziona le unità di misura e inserisci un valore per effettuare la conversione.</p>

        <!-- Form per la conversione -->
        <div class="conversion-container">
            <label for="fromUnit">Converti da:</label>
            <select id="fromUnit" onchange="updateToUnit()" required>
                <option value="" disabled selected>Seleziona un'unità</option>
                <optgroup label="Distanza">
                    <option value="m">Metri (m)</option>
                    <option value="km">Chilometri (km)</option>
                    <option value="mi">Miglia (mi)</option>
                    <option value="nmi">Miglia nautiche (nmi)</option>
                </optgroup>
                <optgroup label="Superficie">
                    <option value="m2">Metri quadrati (m²)</option>
                    <option value="km2">Chilometri quadrati (km²)</option>
                    <option value="ha">Ettari (ha)</option>
                    <option value="a">Are (a)</option>
                    <option value="ac">Acri (ac)</option>
                </optgroup>
            </select>

            <label for="toUnit">A:</label>
            <select id="toUnit" disabled required></select>

            <label for="value">Inserisci valore:</label>
            <input type="number" id="value" placeholder="Inserisci il valore" min="0" step="any">

            <button onclick="convert()">Converti</button>

            <h3>Risultato: <span id="result">-</span></h3>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2024 Strumenti DOS - Azienda Calabria Verde</p>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var menu = document.getElementById("menu");
        var toggleButton = document.querySelector(".menu-toggle");
        var hideMenuButton = document.querySelector(".hide-menu");

        toggleButton.addEventListener("click", function () {
            menu.style.display = "block";
            toggleButton.style.display = "none";
        });

        hideMenuButton.addEventListener("click", function () {
            menu.style.display = "none";
            toggleButton.style.display = "block";
        });
    });

    const distanceUnits = {
        "m": "Metri (m)",
        "km": "Chilometri (km)",
        "mi": "Miglia (mi)",
        "nmi": "Miglia nautiche (nmi)"
    };

    const areaUnits = {
        "m2": "Metri quadrati (m²)",
        "km2": "Chilometri quadrati (km²)",
        "ha": "Ettari (ha)",
        "a": "Are (a)",
        "ac": "Acri (ac)"
    };

    function updateToUnit() {
        const fromUnit = document.getElementById("fromUnit").value;
        const toUnit = document.getElementById("toUnit");

        toUnit.innerHTML = "";
        toUnit.disabled = false;

        let units = fromUnit in distanceUnits ? distanceUnits : areaUnits;

        for (const key in units) {
            let option = document.createElement("option");
            option.value = key;
            option.textContent = units[key];
            toUnit.appendChild(option);
        }
    }

    function convert() {
        var fromUnit = document.getElementById("fromUnit").value;
        var toUnit = document.getElementById("toUnit").value;
        var value = parseFloat(document.getElementById("value").value);

        if (!fromUnit || !toUnit || isNaN(value)) {
            document.getElementById("result").textContent = "Compila tutti i campi.";
            return;
        }

        var conversionFactors = {
            "m": 1, "km": 0.001, "mi": 0.000621371, "nmi": 0.000539957,
            "m2": 1, "km2": 0.000001, "ha": 0.0001, "a": 0.01, "ac": 0.000247105
        };

        var baseValue = value / conversionFactors[fromUnit];  
        var convertedValue = baseValue * conversionFactors[toUnit]; 

        document.getElementById("result").textContent = formatResult(convertedValue) + " " + toUnit;
    }

    function formatResult(value) {
        return value % 1 === 0 ? value.toFixed(0) : value.toFixed(3);
    }

    function scrollToContent() {
        document.getElementById("content").scrollIntoView();
    }
</script>

<style>
    body, h1, h2, h3, p {
        text-align: center;
    }

    .conversion-container {
        max-width: 400px;
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        text-align: center;
    }

    .conversion-container label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
        text-align: center;
    }

    .conversion-container select, .conversion-container input {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        text-align: center;
    }

    .conversion-container button {
        margin-top: 15px;
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

    h3 {
        margin-top: 15px;
        font-size: 18px;
        color: #333;
    }

    footer {
        text-align: center;
    }
</style>

</body>
</html>
