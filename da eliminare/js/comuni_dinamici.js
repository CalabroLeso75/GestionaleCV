document.addEventListener("DOMContentLoaded", () => {
  const selectProvincia = document.getElementById("input-provincia");
  const selectComune = document.getElementById("input-comune");

  function aggiornaComuni() {
    const provinciaSelezionata = selectProvincia.value.trim();
    selectComune.innerHTML = ""; // Pulisce i comuni esistenti

    let comuniDaMostrare = [];

    // Trova la chiave dell’oggetto corrispondente alla provincia selezionata
    const provinciaNormalizzata = Object.keys(comuniPerProvincia).find(
      p => p.toLowerCase() === provinciaSelezionata.toLowerCase()
    );

    if (provinciaNormalizzata) {
      comuniDaMostrare = comuniPerProvincia[provinciaNormalizzata];
    } else {
      // Nessuna provincia selezionata o non corrispondente: mostra tutti i comuni
      for (const listaComuni of Object.values(comuniPerProvincia)) {
        comuniDaMostrare = comuniDaMostrare.concat(listaComuni);
      }
    }

    // Aggiungi una voce di default
    const optionDefault = document.createElement("option");
    optionDefault.value = "";
    optionDefault.textContent = "-- Seleziona Comune --";
    selectComune.appendChild(optionDefault);

    // Ordina e inserisce
    comuniDaMostrare.sort().forEach(comune => {
      const option = document.createElement("option");
      option.value = comune;
      option.textContent = comune;
      selectComune.appendChild(option);
    });
  }

  // Quando si seleziona un comune, cerca di impostare la provincia corrispondente
  selectComune.addEventListener("change", () => {
    const comuneSelezionato = selectComune.value.trim().toLowerCase();
    for (const [provincia, comuni] of Object.entries(comuniPerProvincia)) {
      for (const comune of comuni) {
        if (comune.toLowerCase() === comuneSelezionato) {
          selectProvincia.value = provincia;
          return;
        }
      }
    }
  });

  selectProvincia.addEventListener("change", aggiornaComuni);

  aggiornaComuni(); // Popolamento iniziale
});
