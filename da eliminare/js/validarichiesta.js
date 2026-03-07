// === Funzione per mostrare o nascondere il flag di responsabilità in base al vento ===
function aggiornaResponsabilitaVento() {
  try {
    const vento = document.getElementById("input-vento")?.value?.toLowerCase() || "";
    const flagResponsabilita = document.getElementById("input-dos-responsabile")?.parentElement;
    const containerVento = document.getElementById("container-opzioni-vento");
    const selectVento = document.getElementById("input-opzioni-vento");

    const richiedeResponsabilita = vento.includes("moderato") || vento.includes("forte");

    if (richiedeResponsabilita) {
      if (flagResponsabilita) flagResponsabilita.style.display = "block";
      if (containerVento) containerVento.style.display = "block";
      if (selectVento) selectVento.required = true;
      const flag = document.getElementById("input-dos-responsabile");
      if (flag) flag.required = true;
    } else {
      if (flagResponsabilita) flagResponsabilita.style.display = "none";
      const flag = document.getElementById("input-dos-responsabile");
      if (flag) {
        flag.required = false;
        flag.checked = false;
      }
      if (containerVento) containerVento.style.display = "none";
      if (selectVento) {
        selectVento.required = false;
        selectVento.value = "";
      }
    }
  } catch (error) {
    console.error("Errore nella funzione aggiornaResponsabilitaVento:", error);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  try {
    const ventoSelect = document.getElementById("input-vento");
    if (ventoSelect) {
      ventoSelect.addEventListener("change", aggiornaResponsabilitaVento);
      aggiornaResponsabilitaVento();
    }
  } catch (error) {
    console.error("Errore durante l'inizializzazione DOMContentLoaded:", error);
  }
});

// === Validazione finale prima invio ===
function validaCampiRichiesta() {
  try {
    // Località
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

    // Comune e Provincia
    const comune = document.getElementById("input-comune").value.trim().toLowerCase();
    const provincia = document.getElementById("input-provincia").value.trim().toLowerCase();
    const comuneObj = typeof comuniData !== 'undefined' ? comuniData.find(c => c.comune === comune) : null;

    if (comuneObj) {
      if (comuneObj.provincia !== provincia) {
        alert("Attenzione: il comune selezionato risulta appartenere alla provincia: " + comuneObj.provincia.toUpperCase());
        document.getElementById("input-provincia").focus();
        return false;
      }
    } else {
      alert("Comune non riconosciuto. Verifica che sia stato scritto correttamente.");
      document.getElementById("input-comune").focus();
      return false;
    }

    // Coordinate DMS obbligatorie
    const latGradi = document.getElementById("lat-gradi").value;
    const latMinuti = document.getElementById("lat-minuti").value;
    const latSecondi = document.getElementById("lat-secondi").value;
    const lonGradi = document.getElementById("lon-gradi").value;
    const lonMinuti = document.getElementById("lon-minuti").value;
    const lonSecondi = document.getElementById("lon-secondi").value;

    if (!latGradi || !latMinuti || !latSecondi) {
      alert("Inserisci tutti i valori della latitudine (gradi, minuti, secondi).");
      document.getElementById("lat-gradi").focus();
      return false;
    }
    if (!lonGradi || !lonMinuti || !lonSecondi) {
      alert("Inserisci tutti i valori della longitudine (gradi, minuti, secondi).");
      document.getElementById("lon-gradi").focus();
      return false;
    }

    // Quota
    const quota = document.getElementById("input-quota").value;
    if (!quota || isNaN(quota) || parseInt(quota) < 0) {
      alert("Inserisci una quota valida.");
      document.getElementById("input-quota").focus();
      return false;
    }

    // Superficie e rischio
    const supBruciata = document.getElementById("input-superficie-bruciata").value;
    const supRischio = document.getElementById("input-superficie-rischio").value;
    if (!supBruciata || isNaN(supBruciata) || parseFloat(supBruciata) < 0) {
      alert("Inserisci una superficie bruciata valida.");
      document.getElementById("input-superficie-bruciata").focus();
      return false;
    }
    if (!supRischio || isNaN(supRischio) || parseFloat(supRischio) < 0) {
      alert("Inserisci una superficie a rischio valida.");
      document.getElementById("input-superficie-rischio").focus();
      return false;
    }

    // Fronti e lunghezza
    const fronti = document.getElementById("input-fronti").value;
    const lunghezzaFronti = document.getElementById("input-lunghezza-fronti").value;
    if (!fronti || isNaN(fronti) || parseInt(fronti) < 1) {
      alert("Inserisci un numero di fronti valido.");
      document.getElementById("input-fronti").focus();
      return false;
    }
    if (!lunghezzaFronti || isNaN(lunghezzaFronti) || parseInt(lunghezzaFronti) <= 0) {
      alert("Inserisci una lunghezza fronti valida.");
      document.getElementById("input-lunghezza-fronti").focus();
      return false;
    }

    return true;
  } catch (error) {
    console.error("Errore durante la validazione dei campi:", error);
    alert("Errore interno durante la validazione. Verifica i dati inseriti o contatta l'assistenza.");
    return false;
  }
}

function generaCommentoCriticita(dati) {
  let punteggio = 10;
  let commento = "";
  if (dati.insediamento && dati.insediamento !== "nessuno") {
    commento += `Insediamento rilevato: ${dati.tipoInsediamento || dati.insediamento}. `;
    punteggio -= 2;
    if (dati.presenzaPersone === "certa") {
      commento += "Presenza certa di persone. ";
      punteggio -= 2;
    } else if (dati.presenzaPersone === "incerta") {
      commento += `Presenza incerta di persone. Motivo: ${dati.motivoIncerta}. `;
      punteggio -= 1;
    }
  }
  if (dati.infrastruttura && dati.infrastruttura !== "Nessuna") {
    commento += `Infrastrutture: ${dati.infrastruttura} (${dati.prossInfrastruttura}). `;
    if (dati.prossInfrastruttura === "vicino") punteggio -= 2;
    else if (dati.prossInfrastruttura === "medio") punteggio -= 1;
  }
  if (dati.ostacolo && dati.ostacolo !== "Nessuno") {
    commento += `Ostacoli: ${dati.ostacolo} (${dati.prossOstacolo}). `;
    if (dati.prossOstacolo === "vicino") punteggio -= 2;
    else if (dati.prossOstacolo === "medio") punteggio -= 1;
  }
  if (dati.elettrodotto === "Attivi" || dati.elettrodotto === "In disattivazione") {
    commento += `Elettrodotti: ${dati.elettrodotto}. `;
    punteggio -= (dati.elettrodotto === "Attivi" ? 2 : 1);
  }
  if (dati.vento?.toLowerCase().includes("forte")) {
    commento += "Vento forte rilevato. ";
    punteggio -= 2;
  } else if (dati.vento?.toLowerCase().includes("moderato")) {
    commento += "Vento moderato rilevato. ";
    punteggio -= 1;
  }
  if (dati.impervia) {
    commento += "Area impervia. ";
    punteggio -= 1;
  }
  if (punteggio < 1) punteggio = 1;
  if (punteggio > 10) punteggio = 10;
  commento += `\n\nLivello indicativo di criticità: ${punteggio}\n(1 = massima criticità, 10 = minima)`;
  const campoLivello = document.getElementById("input-livello-criticita");
  if (campoLivello) campoLivello.value = punteggio;
  console.log("✅ Criticità salvata:", punteggio);
  return commento;
}

function preparePDFData(event) {
  event.preventDefault();

  if (!validaCampiRichiesta()) {
    return;
  }

  const prioritaSelezionata = document.querySelector('input[name="input-priorita"]:checked');
  if (!prioritaSelezionata) {
    alert("Seleziona una priorità prima di inviare la richiesta.");
    return;
  }

  const notaAutomatica = document.getElementById("nota-tecnica-auto")?.value || document.getElementById("nota-tecnica-auto")?.textContent || "";
  const livelloCriticita = document.getElementById("input-livello-criticita")?.value || "--/10";
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
    "livello_criticita": livelloCriticita,
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
    "input-dos-responsabile": document.getElementById("input-dos-responsabile")?.checked || false,
    "input-squadre": document.getElementById("input-squadre")?.value || "",
    "input-numero-persone": document.getElementById("input-numero-persone")?.value || "",
    "input-elicotteri": document.getElementById("input-elicotteri")?.checked || false,
    "input-fonte-idrica": document.getElementById("input-fonte-idrica")?.value || "",
    "input-infrastrutture": document.getElementById("input-infrastrutture")?.value || "",
    "input-insediamenti": document.getElementById("input-insediamenti")?.value || "",
    "input-ostacoli": document.getElementById("input-ostacoli")?.value || "",
    "input-elettrodotti": document.getElementById("input-elettrodotti")?.value || "",
    "input-radio": document.getElementById("input-radio")?.value || "",
    "input-ritardante": document.getElementById("input-ritardante")?.value || "",
    "input-richiesta": document.getElementById("input-richiesta")?.value || "",
    "input-prossimita-infrastrutture": document.getElementById("input-prossimita-infrastrutture")?.value || "",
    "input-prossimita-ostacoli": document.getElementById("input-prossimita-ostacoli")?.value || ""
  };

  document.getElementById("formData").value = JSON.stringify(formData);
  document.getElementById("pdfForm").submit();
}
