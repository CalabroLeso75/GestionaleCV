<fieldset>
  <legend>Localizzazione</legend>

  <button class="orange-button" onclick="autoCompilaDati()" type="button">📍 Rileva posizione</button>
  <button type="button" class="orange-button" onclick="apriMappaPopup()">🗺️ Cerca sulla mappa</button>

  <label for="input-provincia">Provincia</label>
  <select id="input-provincia" required>
    <option value="">-- Seleziona provincia --</option>
    <option>Cosenza</option>
    <option>Catanzaro</option>
    <option>Crotone</option>
    <option>Vibo Valentia</option>
    <option>Reggio Calabria</option>
  </select>

<label for="input-comune">Comune</label>
<select id="input-comune" required>
  <option value="">-- Seleziona comune --</option>
</select>


  <label for="input-localita">Località</label>
  <input id="input-localita" type="text" placeholder="Inserisci località o punto di riferimento" oninput="this.value = this.value.toUpperCase();">

  <label for="lat-gradi">Latitudine (DMS)</label>
  <div class="coord-group">
    <input id="lat-gradi" type="number" placeholder="Gradi" max="90" min="0">
    <input id="lat-minuti" type="number" placeholder="Minuti" max="59" min="0">
    <input id="lat-secondi" type="number" placeholder="Secondi" step="0.01" max="59.99" min="0">
    <input type="text" value="N" readonly>
  </div>

  <label for="lon-gradi">Longitudine (DMS)</label>
  <div class="coord-group">
    <input id="lon-gradi" type="number" placeholder="Gradi" max="180" min="0">
    <input id="lon-minuti" type="number" placeholder="Minuti" max="59" min="0">
    <input id="lon-secondi" type="number" placeholder="Secondi" step="0.01" max="59.99" min="0">
    <input type="text" value="E" readonly>
  </div>

  <label for="input-quota">Quota (m slm)</label>
  <input id="input-quota" type="number" placeholder="Es. 350" min="0" step="1">
</fieldset>
