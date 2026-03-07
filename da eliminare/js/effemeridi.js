// nomefile: effemeridi.js

// Gestisco il menu responsive se presente
// Mostro o nascondo il menu quando clicco sull'icona

document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".menu-toggle");
    const menu = document.getElementById("menu");

    if (menuToggle && menu) {
        menuToggle.addEventListener("click", function () {
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        });
    }
});

// Funzione principale per calcolare le effemeridi e riempire la tabella
function calculateEphemerides() {
    const month = document.getElementById("month").value;
    const year = document.getElementById("year").value;
    const location = document.getElementById("location").value;
    const lat = parseFloat(document.getElementById("selectedLat").value);
    const lon = parseFloat(document.getElementById("selectedLon").value);

    if (!month || !year || !location || isNaN(lat) || isNaN(lon)) {
        alert("Compila tutti i campi richiesti.");
        return;
    }

    const mesi = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];
    document.getElementById("monthYearHeader").textContent = `${mesi[month - 1]} ${year}`.toUpperCase();
    document.getElementById("locationHeader").textContent = ` - ${location.toUpperCase()}`;

    const ephemeridesTable = document.getElementById("ephemerides");
    ephemeridesTable.innerHTML = "";

    const lastSundayMarch = getLastSunday(year, 3);
    const lastSundayOctober = getLastSunday(year, 10);
    const holidays = calculateHolidays(year);
    const daysInMonth = new Date(year, month, 0).getDate();

    const holidayLabels = {
        "1/1": "Capodanno",
        "6/1": "Epifania",
        "25/4": "Liberazione",
        "1/5": "Lavoro",
        "2/6": "Repubblica",
        "15/8": "Ferragosto",
        "1/11": "Ognissanti",
        "8/12": "Immacolata",
        "25/12": "Natale",
        "26/12": "S. Stefano",
        "16/7": "Patrono"
    };

    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month - 1, day);
        const weekday = date.toLocaleDateString("it-IT", { weekday: "long" });
        const holidayKey = `${day}/${month}`;

        let festName = capitalize(weekday);
        if (holidayLabels[holidayKey]) {
            festName = holidayLabels[holidayKey];
        } else if (holidayKey === calculateEaster(year)) {
            festName = "Pasqua";
        } else if (holidayKey === calculateEasterMonday(calculateEaster(year))) {
            festName = "Pasquetta";
        }

        const isHoliday = festName !== capitalize(weekday) || weekday.toLowerCase() === "domenica";

        let timezone = "Solare";
        if ((month > 3 && month < 10) || (month == 3 && day >= lastSundayMarch) || (month == 10 && day < lastSundayOctober)) {
            timezone = "Legale";
        }

        const sunriseTime = calculateSunrise(lat, lon, year, month, day);
        const sunsetTime = calculateSunset(lat, lon, year, month, day);

        const row = document.createElement("tr");
        if (isHoliday || weekday.toLowerCase() === "domenica") row.classList.add("festivo");
        row.innerHTML = `
            <td style="text-align: left; width: 40%;">${day} - ${festName}</td>
            <td style="text-align: center; width: 20%;">${sunriseTime}</td>
            <td style="text-align: center; width: 20%;">${sunsetTime}</td>
            <td style="text-align: center; width: 20%;">${timezone}</td>
        `;
        ephemeridesTable.appendChild(row);
    }
}

function calculateSunrise(lat, lon, year, month, day) {
    const times = SunCalc.getTimes(new Date(year, month - 1, day), lat, lon);
    return formatTime(times.sunrise);
}

function calculateSunset(lat, lon, year, month, day) {
    const times = SunCalc.getTimes(new Date(year, month - 1, day), lat, lon);
    return formatTime(times.sunset);
}

function formatTime(date) {
    if (!date) return "--:--";
    let hours = date.getHours();
    let minutes = date.getMinutes();
    return `${hours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}`;
}

function getLastSunday(year, month) {
    let date = new Date(year, month, 0);
    while (date.getDay() !== 0) {
        date.setDate(date.getDate() - 1);
    }
    return date.getDate();
}

function calculateHolidays(year) {
    const holidays = ["1/1", "6/1", "25/4", "1/5", "2/6", "15/8", "1/11", "8/12", "25/12", "26/12", "16/7"];
    const easter = calculateEaster(year);
    holidays.push(easter);
    holidays.push(calculateEasterMonday(easter));
    return holidays;
}

function calculateEaster(year) {
    const f = Math.floor, G = year % 19, C = f(year / 100), H = (C - f(C / 4) - f((8 * C + 13) / 25) + 19 * G + 15) % 30,
    I = H - f(H / 28) * (1 - f(29 / (H + 1)) * f((21 - G) / 11)), J = (year + f(year / 4) + I + 2 - C + f(C / 4)) % 7,
    L = I - J, month = 3 + f((L + 40) / 44), day = L + 28 - 31 * f(month / 4);
    return `${day}/${month}`;
}

function calculateEasterMonday(easter) {
    const [day, month] = easter.split("/").map(Number);
    return `${day + 1}/${month}`;
}

function updateCoordinates() {
    const locationSelect = document.getElementById("location");
    const selectedOption = locationSelect.options[locationSelect.selectedIndex];

    if (selectedOption.value !== "") {
        const lat = selectedOption.getAttribute("data-lat");
        const lon = selectedOption.getAttribute("data-lon");
        document.getElementById("selectedLat").value = lat;
        document.getElementById("selectedLon").value = lon;
    }
}

function printTable() {
    let printContents = `
        <div style="text-align:center;">
            <img src="../images/Int4.png" alt="Intestazione Calabria Verde" style="width:100%;max-width:1000px;margin-bottom:20px;">
        </div>
        ${document.getElementById("ephemeridesTable").outerHTML}
    `;

    let newWindow = window.open("", "", "width=800, height=600");
    newWindow.document.write(`
        <html>
            <head>
                <title>Stampa Effemeridi</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 10px; }
                    table { width: 100%; border-collapse: collapse; font-size: 0.75em; }
                    th { font-size: 1em; font-weight: bold; text-align: center; }
                    th, td { border: 1px solid #ccc; padding: 3px; }
                    td { text-align: center; }
                    td:first-child { text-align: left; }
                    .festivo { background-color: #ffe6e6; color: red; font-weight: bold; }
                    img { display: block; margin: auto; max-width: 100%; height: auto; }
                </style>
            </head>
            <body>${printContents}</body>
        </html>
    `);
    newWindow.document.close();
    newWindow.print();
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
