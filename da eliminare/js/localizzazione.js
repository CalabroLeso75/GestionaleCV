// nomefile: /pages/localizzazione.js
// Richiede: turf.js e toGeoJSON già caricati prima

let comuniGeoJson = null;
let provinceGeoJson = null;

// Inizializzo i dati da file KML dei comuni e delle province
export function inizializzaKML(callback) {
    Promise.all([
        fetch('Calabria Comuni.kml').then(res => res.text()),
        fetch('province.kml').then(res => res.text())
    ]).then(([comuniKmlText, provinceKmlText]) => {
        const comuniDom = new DOMParser().parseFromString(comuniKmlText, 'text/xml');
        const provinceDom = new DOMParser().parseFromString(provinceKmlText, 'text/xml');

        comuniGeoJson = toGeoJSON.kml(comuniDom);
        provinceGeoJson = toGeoJSON.kml(provinceDom);

        // Richiamo la funzione di callback dopo aver caricato tutto
        if (callback) callback();
    }).catch(error => {
        // Se i file KML non sono disponibili o hanno errori, non faccio nulla
    });
}

// Aggiorno i campi di comune e provincia in base al punto fornito (lat/lon)
export function aggiornaComuneEProvincia(lat, lon) {
    const comuneInput = document.getElementById('comune-centroide');
    const provinciaSelect = document.getElementById('provincia-centroide');

    if (!comuneInput || !provinciaSelect || !lat || !lon) {
        return; // Esco se mancano elementi o coordinate
    }

    const punto = turf.point([lon, lat]);

    let comune = "Comune non trovato";
    let provincia = "Provincia non trovata";

    // Verifico se il punto ricade all'interno di un comune
    if (comuniGeoJson) {
        for (const feature of comuniGeoJson.features) {
            if (turf.booleanPointInPolygon(punto, feature)) {
                comune = feature.properties.name || feature.properties.NOME || comune;
                break;
            }
        }
    }

    // Verifico se il punto ricade all'interno di una provincia
    if (provinceGeoJson) {
        for (const feature of provinceGeoJson.features) {
            if (turf.booleanPointInPolygon(punto, feature)) {
                const nomeProvincia = feature.properties.name || feature.properties.NOME || "";
                provincia = siglaProvinciaDaNome(nomeProvincia);
                break;
            }
        }
    }

    // Solo se il campo non è stato modificato manualmente
    if (comuneInput.dataset.auto !== "no") {
        comuneInput.value = comune;
    }

    provinciaSelect.value = provincia;
}

// Converte un nome provincia nel relativo codice (es. Catanzaro → CZ)
function siglaProvinciaDaNome(nome) {
    const norm = nome.toUpperCase();
    if (norm.includes("CROTONE")) return "KR";
    if (norm.includes("CATANZARO")) return "CZ";
    if (norm.includes("COSENZA")) return "CS";
    if (norm.includes("REGGIO")) return "RC";
    if (norm.includes("VIBO")) return "VV";
    return "";
}