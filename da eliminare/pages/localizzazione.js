// nomefile: /pages/localizzazione.js
// Richiede: turf.js e toGeoJSON già caricati prima

let comuniGeoJson = null;
let provinceGeoJson = null;

// In questa funzione inizializzo i dati GeoJSON partendo dai file KML di comuni e province
export function inizializzaKML(callback) {
    Promise.all([
        fetch('Calabria Comuni.kml').then(res => res.text()),
        fetch('province.kml').then(res => res.text())
    ]).then(([comuniKmlText, provinceKmlText]) => {
        const comuniDom = new DOMParser().parseFromString(comuniKmlText, 'text/xml');
        const provinceDom = new DOMParser().parseFromString(provinceKmlText, 'text/xml');

        // Converto i KML in oggetti GeoJSON
        comuniGeoJson = toGeoJSON.kml(comuniDom);
        provinceGeoJson = toGeoJSON.kml(provinceDom);

        // Eseguo la funzione callback se fornita, al termine del caricamento
        if (callback) callback();
    }).catch(() => {
        // Log disattivato per evitare messaggi in console
    });
}

// In questa funzione, aggiorno automaticamente il campo Comune e Provincia partendo dalle coordinate
export function aggiornaComuneEProvincia(lat, lon) {
    const comuneInput = document.getElementById('comune-centroide');
    const provinciaSelect = document.getElementById('provincia-centroide');

    // Controllo che esistano gli elementi nel DOM e le coordinate
    if (!comuneInput || !provinciaSelect || !lat || !lon) {
        return;
    }

    const punto = turf.point([lon, lat]);

    let comune = "Comune non trovato";
    let provincia = "Provincia non trovata";

    // Cerco il comune che contiene il punto
    if (comuniGeoJson) {
        for (const feature of comuniGeoJson.features) {
            if (turf.booleanPointInPolygon(punto, feature)) {
                comune = feature.properties.name || feature.properties.NOME || comune;
                break;
            }
        }
    }

    // Cerco la provincia che contiene il punto
    if (provinceGeoJson) {
        for (const feature of provinceGeoJson.features) {
            if (turf.booleanPointInPolygon(punto, feature)) {
                const nomeProvincia = feature.properties.name || feature.properties.NOME || "";
                provincia = siglaProvinciaDaNome(nomeProvincia);
                break;
            }
        }
    }

    // Aggiorno il campo Comune solo se non è stato modificato manualmente
    if (comuneInput.dataset.auto !== "no") {
        comuneInput.value = comune;
    }

    // Imposto sempre il valore della provincia selezionata
    provinciaSelect.value = provincia;
}

// Funzione di utilità che converte un nome completo di provincia nella sua sigla
function siglaProvinciaDaNome(nome) {
    const norm = nome.toUpperCase();
    if (norm.includes("CROTONE")) return "KR";
    if (norm.includes("CATANZARO")) return "CZ";
    if (norm.includes("COSENZA")) return "CS";
    if (norm.includes("REGGIO")) return "RC";
    if (norm.includes("VIBO")) return "VV";
    return "";
}
