<fieldset>
  <legend>Dati Direttore delle Operazioni di Spegnimento (DOS)</legend>

  <label for="dos-nome">Nome e Cognome</label>
  <input id="dos-nome" type="text" placeholder="Mario Rossi" required
         oninput="this.value = this.value.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());">

  <label for="dos-sigla">Sigla DOS</label>
  <input id="dos-sigla" type="text" placeholder="ES: ABC123" required
         pattern="[A-Z0-9]{1,10}" maxlength="10"
         oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');">

  <label for="dos-telefono">Numero di Telefono</label>
  <input id="dos-telefono" type="text" placeholder="Es. 3331234567" required
         pattern="[0-9.]{5,15}"
         oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
</fieldset>
