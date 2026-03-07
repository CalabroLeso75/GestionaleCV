document.addEventListener("DOMContentLoaded", function () {
  const fieldsToWatch = [
    "input-insediamenti",
    "tipo-insediamento",
    "presenza-persone",
    "motivo-presenza-incerta",
    "input-infrastrutture",
    "input-prossimita-infrastrutture",
    "input-ostacoli",
    "input-prossimita-ostacoli",
    "input-elettrodotti",
    "input-vento",
    "input-area-impervia",
    "input-note",
    "input-opzioni-vento",
    "input-assunzione-responsabilita"
  ];

  fieldsToWatch.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener("change", aggiornaNotaTecnica);
      el.addEventListener("input", aggiornaNotaTecnica);
    }
  });

  function aggiornaNotaTecnica() {
    const dati = {
      insediamento: document.getElementById("input-insediamenti")?.value,
      tipoInsediamento: document.getElementById("tipo-insediamento")?.value,
      presenzaPersone: document.querySelector('input[name="presenza-persone"]:checked')?.value,
      motivoIncerta: document.getElementById("motivo-presenza-incerta")?.value,
      infrastruttura: document.getElementById("input-infrastrutture")?.value,
      prossInfrastruttura: document.getElementById("input-prossimita-infrastrutture")?.value,
      ostacolo: document.getElementById("input-ostacoli")?.value,
      prossOstacolo: document.getElementById("input-prossimita-ostacoli")?.value,
      elettrodotto: document.getElementById("input-elettrodotti")?.value,
      vento: document.getElementById("input-vento")?.value,
      impervia: document.getElementById("input-area-impervia")?.checked,
      notaDos: document.getElementById("input-note")?.value,
      valutazioneVento: document.getElementById("input-opzioni-vento")?.value,
      responsabilita: document.getElementById("input-assunzione-responsabilita")?.checked
    };

    let testo = "";
    let punteggio = 10;

    if (dati.insediamento && dati.insediamento !== "nessuno") {
      testo += `È presente un insediamento del tipo \"${dati.tipoInsediamento || dati.insediamento}\", `;
      punteggio -= 2;
      if (dati.presenzaPersone === "certa") {
        testo += `con presenza certa di persone. `;
        punteggio -= 2;
      } else if (dati.presenzaPersone === "incerta") {
        testo += `con presenza incerta di persone`;
        if (dati.motivoIncerta) {
          testo += `, come indicato: \"${dati.motivoIncerta}\"`;
        }
        testo += `. `;
        punteggio -= 1;
      } else {
        testo += `la presenza di persone non è stata specificata. `;
      }
    }

    if (dati.infrastruttura && dati.infrastruttura !== "Nessuna") {
      testo += `In prossimità si trovano infrastrutture del tipo \"${dati.infrastruttura}\"`;
      if (dati.prossInfrastruttura === "vicino") {
        testo += ", adiacenti all’area interessata";
        punteggio -= 2;
      } else if (dati.prossInfrastruttura === "medio") {
        testo += ", raggiungibili in breve tempo";
        punteggio -= 1;
      } else if (dati.prossInfrastruttura === "lontano") {
        testo += ", ma attualmente distanti dall’area attiva";
      }
      testo += ". ";
    }

    if (dati.ostacolo && dati.ostacolo !== "Nessuno") {
      testo += `Sono presenti ostacoli come \"${dati.ostacolo}\"`;
      if (dati.prossOstacolo === "vicino") {
        testo += ", in prossimità diretta dell'incendio";
        punteggio -= 2;
      } else if (dati.prossOstacolo === "medio") {
        testo += ", in area potenzialmente interferente";
        punteggio -= 1;
      } else if (dati.prossOstacolo === "lontano") {
        testo += ", ma lontani dalla zona operativa immediata";
      }
      testo += ". ";
    }

    if (dati.elettrodotto === "Attivi" || dati.elettrodotto === "In disattivazione") {
      testo += `Presenza di elettrodotti ${dati.elettrodotto.toLowerCase()}, il DOS ha dichiarato che il mezzo può operare con attenzione alla distanza di sicurezza. `;
      punteggio -= (dati.elettrodotto === "Attivi") ? 2 : 1;
    } else if (dati.elettrodotto === "A distanza di sicurezza") {
      testo += "Elettrodotti presenti ma già distanti dalla zona attiva. ";
    }

    if (dati.vento?.toLowerCase().includes("forte")) {
      testo += "Il vento forte potrebbe compromettere la precisione dei lanci. ";
      punteggio -= 2;
    } else if (dati.vento?.toLowerCase().includes("moderato")) {
      testo += "Il vento moderato richiede una valutazione attenta delle traiettorie. ";
      punteggio -= 1;
    } else if (dati.vento) {
      testo += "Il vento è al momento contenuto e non condiziona significativamente le operazioni. ";
    }
    if (dati.valutazioneVento && (dati.vento.toLowerCase().includes("moderato") || dati.vento.toLowerCase().includes("forte"))) {
      testo += `\n\nIn merito al vento, il DOS ha selezionato: \"${dati.valutazioneVento}\".`;
    }

    if (dati.responsabilita && (dati.vento.toLowerCase().includes("moderato") || dati.vento.toLowerCase().includes("forte"))) {
      testo += `\nIl DOS ha dichiarato di assumersi la responsabilità dell’impiego del mezzo in queste condizioni di vento.`;
    }
    if (dati.impervia) {
      testo += "L'area è segnalata come impervia, con potenziali difficoltà di accesso per squadre a terra. ";
      punteggio -= 1;
    }

    if (dati.presenzaPersone === "incerta" && dati.notaDos) {
      testo += `\n\nIl DOS riferisce: \"${dati.notaDos}\"`;
    }

    if (punteggio < 1) punteggio = 1;
    if (punteggio > 10) punteggio = 10;

    let colore = "green";
    if (punteggio <= 3) colore = "darkred";
    else if (punteggio <= 7) colore = "darkorange";

    testo += `\n\nLivello indicativo di criticità: ${punteggio}`;
    testo += `\n(1 = massima criticità, 10 = minima)`;

    const blocco = document.getElementById("nota-tecnica-auto");
    if (blocco) {
      blocco.textContent = testo;
      blocco.style.color = colore;
    }

    const campoLivello = document.getElementById("input-livello-criticita");
    if (campoLivello) {
      campoLivello.value = punteggio;
	  console.log("✅ Criticità salvata:", punteggio);

    }
  }

  aggiornaNotaTecnica();
});

window.aggiornaNotaTecnica = aggiornaNotaTecnica;
