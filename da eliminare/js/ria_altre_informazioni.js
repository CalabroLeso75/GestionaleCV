function gestisciInsediamenti() {
  const select = document.getElementById("input-insediamenti");
  const divDettagli = document.getElementById("dettagli-insediamento");
  const selectTipologia = document.getElementById("tipo-insediamento");

  const valore = select.value;
  if (!select || !divDettagli || !selectTipologia) return;

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

function gestisciInfrastrutture() {
  const select = document.getElementById("input-infrastrutture");
  const div = document.getElementById("prossimita-infrastrutture");
  if (!select || !div) return;
  div.style.display = (select.value !== "Nessuna") ? "block" : "none";
}

function gestisciOstacoli() {
  const select = document.getElementById("input-ostacoli");
  const div = document.getElementById("prossimita-ostacoli");
  if (!select || !div) return;
  div.style.display = (select.value !== "Nessuno") ? "block" : "none";
}

//vento
document.addEventListener("DOMContentLoaded", function () {
  const campoVento = document.getElementById("input-vento");
  const bloccoOpzioni = document.getElementById("blocco-opzioni-vento");
  const selectOpzioni = document.getElementById("input-opzioni-vento");

  function controllaVento() {
    const valore = campoVento.value.toLowerCase();
    if (valore.includes("moderato") || valore.includes("forte")) {
      bloccoOpzioni.style.display = "block";
      selectOpzioni.setAttribute("required", "required");
    } else {
      bloccoOpzioni.style.display = "none";
      selectOpzioni.removeAttribute("required");
      selectOpzioni.value = "";
    }
  }

  campoVento.addEventListener("change", controllaVento);
  controllaVento(); // inizializza
});

function aggiornaNotaTecnica() {
  const dati = {
    'input-insediamenti': document.getElementById("input-insediamenti").value,
    'tipo-insediamento': document.getElementById("tipo-insediamento")?.value || '',
    'presenza-persone': document.querySelector('input[name="presenza-persone"]:checked')?.value || '',
    'input-infrastrutture': document.getElementById("input-infrastrutture").value,
    'input-prossimita-infrastrutture': document.getElementById("input-prossimita-infrastrutture")?.value || '',
    'input-ostacoli': document.getElementById("input-ostacoli").value,
    'input-prossimita-ostacoli': document.getElementById("input-prossimita-ostacoli")?.value || '',
    'input-elettrodotti': document.getElementById("input-elettrodotti").value,
    'input-vento': document.getElementById("input-vento")?.value || ''
  };

  const nota = generaCommentoCriticita(dati);
  const campoNotaTecnica = document.getElementById("contenuto-nota-tecnica");
  if (campoNotaTecnica) campoNotaTecnica.textContent = nota;
}

document.addEventListener('DOMContentLoaded', function () {
  const ins = document.getElementById("input-insediamenti");
  const radioPersone = document.getElementsByName("presenza-persone");
  const divMotivo = document.getElementById("motivo-incerto");

  window.tipologiePerInsediamento = {
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

  if (ins) ins.addEventListener("change", gestisciInsediamenti);
  const inf = document.getElementById("input-infrastrutture");
  const ost = document.getElementById("input-ostacoli");
  if (inf) inf.addEventListener("change", gestisciInfrastrutture);
  if (ost) ost.addEventListener("change", gestisciOstacoli);

  if (radioPersone.length > 0) {
    radioPersone.forEach(radio => {
      radio.addEventListener("change", function () {
        divMotivo.style.display = (this.value === "incerta") ? "block" : "none";
        if (this.value !== "incerta") {
          const motivoInput = document.getElementById("motivo-presenza-incerta");
          if (motivoInput) motivoInput.value = "";
        }
      });
    });
  }
});
