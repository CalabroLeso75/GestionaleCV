// nomefile: richiesta_mezzo.js
function aggiornaContatoreNote() {
  const note = document.getElementById("input-note").value;
  document.getElementById("note-counter").textContent = `${note.length} / 10.000`;
}

function scrollToContent() {
  window.scrollTo(0, 0);
}

function aggiornaDatiDaMappa(dati) {
  document.getElementById("input-comune").value = dati.comune;
  document.getElementById("input-provincia").value = dati.provincia;
  document.getElementById("lat-gradi").value = dati.latDeg;
  document.getElementById("lat-minuti").value = dati.latMin;
  document.getElementById("lat-secondi").value = dati.latSec;
  document.getElementById("lon-gradi").value = dati.lonDeg;
  document.getElementById("lon-minuti").value = dati.lonMin;
  document.getElementById("lon-secondi").value = dati.lonSec;

  if (dati.quota !== undefined && dati.quota !== '') {
    document.getElementById("input-quota").value = dati.quota;
  }
}

function apriMappaPopup() {
  window.open("ria_mappa.php", "Seleziona posizione", "width=900,height=700,resizable=yes,scrollbars=yes");
}

function toDMS(deg) {
  const d = Math.floor(deg);
  const minFloat = (deg - d) * 60;
  const m = Math.floor(minFloat);
  const s = ((minFloat - m) * 60).toFixed(2);
  return [d, m, s];
}

function trovaProvinciaDaCoordinate(lat, lon) {
  const point = L.latLng(lat, lon);
  for (const prov of provincePolygons) {
    if (prov.layer.getBounds().contains(point)) {
      return prov.name;
    }
  }
  return "Indefinita";
}

function autoCompilaDati() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(position => {
      const lat = position.coords.latitude;
      const lon = position.coords.longitude;
      const quota = position.coords.altitude;

      const [latDeg, latMin, latSec] = toDMS(lat);
      const [lonDeg, lonMin, lonSec] = toDMS(lon);

      document.getElementById("lat-gradi").value = latDeg;
      document.getElementById("lat-minuti").value = latMin;
      document.getElementById("lat-secondi").value = latSec;

      document.getElementById("lon-gradi").value = lonDeg;
      document.getElementById("lon-minuti").value = lonMin;
      document.getElementById("lon-secondi").value = lonSec;

      if (quota && !isNaN(quota)) {
        document.getElementById("input-quota").value = Math.round(quota);
      }

      fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
        .then(res => res.json())
        .then(data => {
          const comune = data.address.town || data.address.city || data.address.village || '';
          document.getElementById("input-comune").value = comune;

          const provinciaTrovata = trovaProvinciaDaCoordinate(lat, lon).toLowerCase();
          const mappaSigle = {
            "cosenza": "Cosenza",
            "catanzaro": "Catanzaro",
            "crotone": "Crotone",
            "vibo valentia": "Vibo Valentia",
            "reggio calabria": "Reggio Calabria",
            "reggio di calabria": "Reggio Calabria"
          };
          const nomeFormattato = mappaSigle[provinciaTrovata] || "";
          const provinciaSelect = document.getElementById("input-provincia");
          for (let i = 0; i < provinciaSelect.options.length; i++) {
            if (provinciaSelect.options[i].text.toLowerCase() === nomeFormattato.toLowerCase()) {
              provinciaSelect.selectedIndex = i;
              break;
            }
          }
        });
    }, err => {
      alert("Errore nel recupero posizione: " + err.message);
    }, { enableHighAccuracy: true });
  } else {
    alert("Geolocalizzazione non supportata dal browser.");
  }
}
function aggiornaResponsabilitaVento() {
  const vento = document.getElementById("input-vento").value.toLowerCase();
  const flagResponsabilita = document.getElementById("input-dos-responsabile")?.parentElement;
  const containerVento = document.getElementById("container-opzioni-vento");
  const selectVento = document.getElementById("input-opzioni-vento");

  const richiedeResponsabilita = vento.includes("moderato") || vento.includes("forte");

  if (richiedeResponsabilita) {
    if (flagResponsabilita) flagResponsabilita.style.display = "block";
    if (containerVento) containerVento.style.display = "block";
    if (selectVento) selectVento.required = true;
    document.getElementById("input-dos-responsabile").required = true;
  } else {
    if (flagResponsabilita) flagResponsabilita.style.display = "none";
    if (containerVento) containerVento.style.display = "none";
    if (selectVento) {
      selectVento.required = false;
      selectVento.value = "";
    }
    document.getElementById("input-dos-responsabile").required = false;
    document.getElementById("input-dos-responsabile").checked = false;
  }
}

function toggleOpzioniVento() {
  const vento = document.getElementById("input-vento").value.toLowerCase();
  const container = document.getElementById("container-opzioni-vento");
  const select = document.getElementById("input-opzioni-vento");

  if (vento.includes("moderato") || vento.includes("forte")) {
    container.style.display = "block";
    select.required = true;
  } else {
    container.style.display = "none";
    select.required = false;
    select.value = "";
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const ventoSelect = document.getElementById("input-vento");
  if (ventoSelect) {
    ventoSelect.addEventListener("change", aggiornaResponsabilitaVento);
    aggiornaResponsabilitaVento();
  }
});


function generaNotaCritica() {
  const insediamento = document.getElementById("tipo-insediamento")?.value || '';
  const presenza = document.querySelector('input[name="presenza-persone"]:checked')?.value || '';
  const infrastrutture = document.getElementById("input-infrastrutture")?.value || '';
  const prossInf = document.getElementById("input-prossimita-infrastrutture")?.value || '';
  const ostacoli = document.getElementById("input-ostacoli")?.value || '';
  const prossOst = document.getElementById("input-prossimita-ostacoli")?.value || '';
  const elettrodotti = document.getElementById("input-elettrodotti")?.value || '';
  const vento = document.getElementById("input-vento")?.value || '';
  const sceltaVento = document.getElementById("input-opzioni-vento")?.value || '';

  let nota = "In base alle scelte effettuate dal DOS, è stata rilevata la seguente situazione:\n\n";

  if (insediamento && insediamento !== "altro") {
    nota += `È presente un insediamento del tipo "${insediamento}", con presenza ${presenza || "non definita"} di persone.\n`;
  }

  if (infrastrutture !== "Nessuna") {
    const pross = prossInf === "vicino" ? "a ridosso" :
                  prossInf === "medio" ? "a distanza ravvicinata" :
                  prossInf === "lontano" ? "attualmente lontano" : "con prossimità non indicata";
    nota += `In prossimità dell’incendio sono localizzate infrastrutture del tipo "${infrastrutture}", ${pross}.\n`;
  }

  if (ostacoli !== "Nessuno") {
    const pross = prossOst === "vicino" ? "in prossimità" :
                  prossOst === "medio" ? "lungo la traiettoria" :
                  prossOst === "lontano" ? "fuori area operativa immediata" : "con prossimità non indicata";
    nota += `Sono presenti ostacoli del tipo "${ostacoli}", ${pross}.\n`;
  }

  if (elettrodotti !== "Nessuno") {
    nota += `È stata inoltre rilevata la presenza di elettrodotti "${elettrodotti}", per i quali il DOS ha valutato la possibilità di operare con mezzi aerei in sicurezza.\n`;
  }

  if (vento !== "Assente") {
    nota += `Il vento "${vento}" può influire sulle operazioni aeree; tuttavia il DOS ha ritenuto possibile e utile l’impiego del mezzo.\n`;
  }

  if (sceltaVento) {
    nota += `\nValutazione operativa in presenza di vento sostenuto: ${sceltaVento}`;
  }

  nota += `\n\nLivello indicativo di criticità: --/10\n\n`;
  nota += `Nota: Il livello di criticità indicato è frutto di una valutazione preliminare basata sulle informazioni fornite dal DOS. L’effettiva priorità e fattibilità dell'intervento verranno definite dal personale della Sala Operativa in collaborazione con il DOS stesso.`;

  return nota;
}

function validaCampiRichiesta() {
  const campoObbligatori = [
    { id: "input-provincia", nome: "Provincia" },
    { id: "input-comune", nome: "Comune" },
    { id: "lat-gradi", nome: "Latitudine (gradi)" },
    { id: "lat-minuti", nome: "Latitudine (minuti)" },
    { id: "lat-secondi", nome: "Latitudine (secondi)" },
    { id: "lon-gradi", nome: "Longitudine (gradi)" },
    { id: "lon-minuti", nome: "Longitudine (minuti)" },
    { id: "lon-secondi", nome: "Longitudine (secondi)" },
    { id: "input-quota", nome: "Quota" },
    { id: "input-superficie-bruciata", nome: "Superficie bruciata" },
    { id: "input-superficie-rischio", nome: "Superficie a rischio" },
    { id: "input-fronti", nome: "Numero fronti" },
    { id: "input-lunghezza-fronti", nome: "Lunghezza fronti" },
    { id: "input-vegetazione-bruciata", nome: "Tipo vegetazione bruciata" },
    { id: "input-valore-ambientale", nome: "Valore ambientale" },
    { id: "input-vegetazione-rischio", nome: "Tipo vegetazione a rischio" },
    { id: "input-valore-rischio", nome: "Valore ambientale a rischio" },
    { id: "input-radio", nome: "Frequenza radio" },
    { id: "input-fonte-idrica", nome: "Fonte idrica" },
    { id: "input-richiesta", nome: "Tipologia richiesta" },
    { id: "input-ritardante", nome: "Uso ritardante" },
    { id: "input-priorita", nome: "Priorità", tipo: "radio" }
  ];

  for (const campo of campoObbligatori) {
    if (campo.tipo === "radio") {
      const selezionato = document.querySelector(`input[name="${campo.id}"]:checked`);
      if (!selezionato) {
        alert(`Seleziona ${campo.nome}.`);
        const firstRadio = document.querySelector(`input[name="${campo.id}"]`);
        if (firstRadio) firstRadio.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
      }
    } else {
      const el = document.getElementById(campo.id);
      if (!el || el.value.trim() === "") {
        alert(`Compila il campo ${campo.nome}.`);
        el.focus();
        return false;
      }
    }
  }

  // Verifica campo località con conferma
  const localita = document.getElementById("input-localita");
  if (!localita.value.trim()) {
    const conferma = confirm("Il campo Località è vuoto. Vuoi inserire \"Non pervenuta\"?");
    if (conferma) {
      localita.value = "Non pervenuta";
    } else {
      localita.focus();
      return false;
    }
  }

  // Verifica insediamenti e presenza persone
  const insediamento = document.getElementById("input-insediamenti").value;
  const presenza = document.querySelector('input[name="presenza-persone"]:checked');
  if (insediamento !== "nessuno" && !presenza) {
    alert("Hai indicato un insediamento ma non hai selezionato la presenza di persone.");
    document.getElementById("input-insediamenti").scrollIntoView({ behavior: 'smooth', block: 'center' });
    return false;
  }

  // Verifica squadre e numero persone
  const squadre = document.getElementById("input-squadre").value;
  const numero = document.getElementById("input-numero-persone").value;
  if (squadre === "si" && (!numero || isNaN(numero))) {
    alert("Hai indicato la presenza di squadre ma non hai inserito il numero di persone.");
    document.getElementById("input-numero-persone").focus();
    return false;
  }

  // Vento moderato/forte: valutazione e flag
  const vento = document.getElementById("input-vento").value.toLowerCase();
  if (vento.includes("moderato") || vento.includes("forte")) {
    const opzione = document.getElementById("input-opzioni-vento").value;
    const conferma = document.getElementById("input-dos-responsabile").checked;
    if (!opzione) {
      alert("Se il vento è moderato o forte, devi indicare una valutazione operativa.");
      document.getElementById("input-opzioni-vento").focus();
      return false;
    }
    if (!conferma) {
      alert("Devi confermare che il mezzo aereo può operare in sicurezza ed efficacia.");
      document.getElementById("input-dos-responsabile").focus();
      return false;
    }
  }

  return true;
}


function preparePDFData(event) {
  event.preventDefault();
  if (typeof aggiornaNotaTecnica === "function") aggiornaNotaTecnica();

if (!validaCampiRichiesta()) {
  return;
}

  const prioritaSelezionata = document.querySelector('input[name="input-priorita"]:checked');
  if (!prioritaSelezionata) {
    alert("Seleziona una priorità prima di inviare la richiesta.");
    return;
  }

  const notaAutomatica = generaNotaCritica();
  const campoNote = document.getElementById("input-note");
  let noteManuale = "";
  if (campoNote && campoNote.value.trim()) {
    noteManuale = campoNote.value.trim();
  }

  const formData = {
    provincia: document.getElementById("input-provincia").value,
    comune: document.getElementById("input-comune").value,
    localita: document.getElementById("input-localita").value,
    latitudine: `${document.getElementById("lat-gradi").value}° ${document.getElementById("lat-minuti").value}' ${document.getElementById("lat-secondi").value}" N`,
    longitudine: `${document.getElementById("lon-gradi").value}° ${document.getElementById("lon-minuti").value}' ${document.getElementById("lon-secondi").value}" E`,
    priorita: prioritaSelezionata.value,
    quota: document.getElementById("input-quota").value,
    note: noteManuale,
    "nota-critica": notaAutomatica,
    "livello_criticita": document.getElementById("input-livello-criticita")?.value || "--/10",
    "input-superficie-bruciata": document.getElementById("input-superficie-bruciata").value,
    "input-vegetazione-bruciata": document.getElementById("input-vegetazione-bruciata").value,
    "input-valore-ambientale": document.getElementById("input-valore-ambientale").value,
    "input-superficie-rischio": document.getElementById("input-superficie-rischio").value,
    "input-vegetazione-rischio": document.getElementById("input-vegetazione-rischio").value,
    "input-valore-rischio": document.getElementById("input-valore-rischio").value,
    "input-fronti": document.getElementById("input-fronti").value,
    "input-lunghezza-fronti": document.getElementById("input-lunghezza-fronti").value,
    "input-area-impervia": document.getElementById("input-area-impervia").checked,
    "input-vento": document.getElementById("input-vento").value,
    "input-opzioni-vento": document.getElementById("input-opzioni-vento")?.value || "",
    "input-squadre": document.getElementById("input-squadre").value,
    "input-numero-persone": document.getElementById("input-numero-persone").value,
    "input-elicotteri": document.getElementById("input-elicotteri").checked,
    "input-fonte-idrica": document.getElementById("input-fonte-idrica").value,
    "input-infrastrutture": document.getElementById("input-infrastrutture").value,
    "input-insediamenti": document.getElementById("input-insediamenti").value,
    "input-ostacoli": document.getElementById("input-ostacoli").value,
    "input-elettrodotti": document.getElementById("input-elettrodotti").value,
    "input-radio": document.getElementById("input-radio").value,
    "input-ritardante": document.getElementById("input-ritardante").value,
    "input-richiesta": document.getElementById("input-richiesta").value,
    "input-prossimita-infrastrutture": document.getElementById("input-prossimita-infrastrutture").value,
    "input-prossimita-ostacoli": document.getElementById("input-prossimita-ostacoli").value,
	"input-dos-responsabile": document.getElementById("input-dos-responsabile")?.checked || false

  };

  document.getElementById("formData").value = JSON.stringify(formData);
  document.getElementById("pdfForm").submit();
}

let provincePolygons = [];
const map = L.map(document.createElement('div')).setView([39, 16], 7);
omnivore.kml('province.kml').on('ready', function () {
  this.eachLayer(layer => {
    const nomeProvincia = layer.feature.properties.name || layer.feature.properties.NOME || "Indefinita";
    provincePolygons.push({ name: nomeProvincia, layer: layer });
  });
});

document.getElementById("input-vento").addEventListener("change", function () {
  const valoreVento = this.value.toLowerCase();
  const flagResponsabilita = document.getElementById("input-dos-responsabile").parentElement;

  if (valoreVento.includes("moderato") || valoreVento.includes("forte")) {
    flagResponsabilita.style.display = "block";
    document.getElementById("input-dos-responsabile").required = true;
  } else {
    flagResponsabilita.style.display = "none";
    document.getElementById("input-dos-responsabile").required = false;
    document.getElementById("input-dos-responsabile").checked = false;
  }
});
