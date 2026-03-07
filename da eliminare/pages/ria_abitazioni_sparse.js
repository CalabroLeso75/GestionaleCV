document.addEventListener('DOMContentLoaded', function () {
  const selectInsediamenti = document.getElementById("input-insediamenti");
  const divDettagli = document.getElementById("dettagli-insediamento");
  const selectTipologia = document.getElementById("tipo-insediamento");
  const radioPersone = document.getElementsByName("presenza-persone");
  const divMotivoIncerto = document.getElementById("motivo-incerto");

  const tipologiePerInsediamento = {
    abitazioni: [
      "Rudere (Bassa criticità)",
      "Casa rurale (Media criticità)",
      "Casale (Media criticità)",
      "Rustico (Media criticità)",
      "Abitazione isolata (Alta criticità)",
      "Casa colonica (Alta criticità)",
      "Villetta (Alta criticità)",
      "Villa con podere (Alta criticità)"
    ],
    campeggi: [
      "Campeggio attrezzato (Alta criticità)",
      "Area di sosta camper (Alta criticità)",
      "Zona bivacco (Media criticità)"
    ],
    centri: [
      "Borgata rurale (Media criticità)",
      "Centro urbano (Alta criticità)",
      "Area urbana periferica (Alta criticità)"
    ],
    ricettive: [
      "Hotel (Alta criticità)",
      "Agriturismo (Alta criticità)",
      "B&B (Media criticità)",
      "Ostello (Media criticità)",
      "Villaggio turistico (Alta criticità)"
    ],
    sanitarie: [
      "Ospedale (Massima criticità)",
      "Casa di riposo (Massima criticità)",
      "Clinica privata (Alta criticità)"
    ]
  };

  if (selectInsediamenti) {
    selectInsediamenti.addEventListener("change", gestisciInsediamenti);
  }

  function gestisciInsediamenti() {
    const valore = selectInsediamenti.value;

    if (valore === "nessuno") {
      divDettagli.style.display = "none";
      selectTipologia.innerHTML = "";
      return;
    }

    divDettagli.style.display = "block";

    if (valore === "altro") {
      selectTipologia.innerHTML = '<option value="altro">Specifica nel campo note</option>';
      selectTipologia.disabled = true;
    } else {
      selectTipologia.disabled = false;
      const opzioni = tipologiePerInsediamento[valore] || [];
      selectTipologia.innerHTML = '<option value="">-- Seleziona --</option>';
      opzioni.forEach(item => {
        const option = document.createElement("option");
        option.value = item;
        option.textContent = item;
        selectTipologia.appendChild(option);
      });
    }
  }

  // Gestione visibilità campo motivazione
  radioPersone.forEach(radio => {
    radio.addEventListener("change", function () {
      if (this.value === "incerta") {
        divMotivoIncerto.style.display = "block";
      } else {
        divMotivoIncerto.style.display = "none";
        document.getElementById("motivo-presenza-incerta").value = "";
      }
    });
  });
});
