<fieldset>
  <legend>Dettagli Incendio</legend>
  <label for="input-area-impervia">
    <input id="input-area-impervia" type="checkbox"> Area impervia
  </label><br>

  <label for="input-superficie-bruciata">Superficie bruciata (ha)</label>
  <input id="input-superficie-bruciata" class="input-primario" type="number" step="0.01" placeholder="Es. 3.5">

  <label for="input-vegetazione-bruciata">Tipo vegetazione bruciata</label>
  <select id="input-vegetazione-bruciata" class="input-secondario">
    <option>I. Erba e Sterpaglia</option>
    <option>II. Arbusti</option>
    <option>III. Alta macchia</option>
    <option>IV. Bosco conifere alte</option>
    <option>In definizione</option>
  </select>

  <label for="input-valore-ambientale">Valore ambientale</label>
  <select id="input-valore-ambientale" class="input-secondario">
    <option>1. Basso</option>
    <option>2. Medio</option>
    <option>3. Alto</option>
    <option>4. Eccezionale</option>
    <option>In definizione</option>
  </select>

  <label for="input-superficie-rischio">Superficie a rischio (ha)</label>
  <input id="input-superficie-rischio" class="input-primario"  type="number" step="0.01" placeholder="Es. 5.0">

  <label for="input-vegetazione-rischio">Tipo vegetazione a rischio</label>
  <select id="input-vegetazione-rischio" class="input-secondario">
    <option>I. Erba e Sterpaglia</option>
    <option>II. Arbusti</option>
    <option>III. Alta macchia</option>
    <option>IV. Bosco conifere alte</option>
    <option>In definizione</option>
  </select>

  <label for="input-valore-rischio">Valore ambientale (a rischio)</label>
  <select id="input-valore-rischio" class="input-secondario">
    <option>1. Basso</option>
    <option>2. Medio</option>
    <option>3. Alto</option>
    <option>4. Eccezionale</option>
    <option>In definizione</option>
  </select>

  <label for="input-fronti">N. fronti del fuoco</label>
  <input id="input-fronti" class="input-primario" type="number" min="1" placeholder="Es. 2">

  <label for="input-lunghezza-fronti">Somma lunghezze fronti (m)</label>
  <input id="input-lunghezza-fronti" class="input-secondario" type="number" min="0" placeholder="Es. 1200">

  <label for="input-vento">Vento</label>
  <select id="input-vento">
    <option>Assente</option>
    <option>Debole</option>
	<option>Moderato</option>
    <option>Forte</option>
  </select>
  	<p id="info-vento" style="font-size: 0.85em; color: #444; margin-top: 5px;">
	  Il vento incide fortemente sulla sicurezza e l'efficacia dei mezzi aerei. 
	  Raffiche oltre i 50–60 km/h possono compromettere la stabilità in volo e la precisione del lancio. 
	  Gli elicotteri leggeri (Airbus H350 e H412) sono più sensibili, mentre il Canadair può operare in condizioni più sostenute, 
	  ma richiede traiettorie pulite e ampi spazi. L’Erickson S-64 mantiene maggiore stabilità, ma necessita comunque di una valutazione attenta. 
	  In caso di vento moderato o forte, è obbligatorio indicare una valutazione operativa per motivare la richiesta.
	</p>
<p style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; font-size: 0.85em; margin-top: 10px;">
  <strong>Attenzione:</strong> inviando la richiesta, il DOS dichiara che il mezzo aereo richiesto può operare in sicurezza e con efficacia minima garantita, 
  anche in presenza delle condizioni di vento moderato o forte. Una valutazione motivata è obbligatoria.
</p> 
<div id="sezione-responsabilita-dos" style="margin-top: 1em; display: none; display: inline-flex; align-items: center; gap: 0.5em;">
  <input type="checkbox" id="input-dos-responsabile" name="input-dos-responsabile" required>
    Confermo operatività in sicurezza e con efficacia.<br>
</div>
  <div id="blocco-opzioni-vento" style="display: none; margin-top: 15px;">
  <label for="input-opzioni-vento"><strong>Valutazione operativa in caso di vento sostenuto</strong></label>
  <select id="input-opzioni-vento" name="input-opzioni-vento" required style="width: 100%; margin-top: 5px;">
    <option value="">-- Seleziona una valutazione --</option>
    <option value="Condizioni accettabili per elicotteri leggeri - H350 o H412 operativi con cautela. Raffiche sotto soglia.">
      <strong>Condizioni accettabili per elicotteri leggeri</strong> - H350 o H412 operativi con cautela. Raffiche sotto soglia.
    </option>
    <option value="Solo Erickson consigliato - Stabilità elevata richiesta. Presenza di ostacoli gestita.">
      <strong>Solo Erickson consigliato</strong> - Stabilità elevata richiesta. Presenza di ostacoli gestita.
    </option>
    <option value="Canadair operabile ma lancio con limitazioni - Precisione ridotta per deriva da vento.">
      <strong>Canadair operabile ma lancio con limitazioni</strong> - Precisione ridotta per deriva da vento.
    </option>
    <option value="Canadair con traiettoria sicura valutata - Il DOS ha verificato efficacia del lancio.">
      <strong>Canadair con traiettoria sicura valutata</strong> - Il DOS ha verificato efficacia del lancio.
    </option>
    <option value="Lanci possibili solo con appoggio da terra - Efficacia parziale garantita.">
      <strong>Lanci possibili solo con appoggio da terra</strong> - Efficacia parziale garantita.
    </option>
    <option value="Attesa condizioni favorevoli prima di decollo - Il DOS ritarda per sicurezza.">
      <strong>Attesa condizioni favorevoli</strong> - Il DOS ritarda per sicurezza.
    </option>
    <option value="Intervento sospeso per vento critico - Nessun mezzo può operare in sicurezza.">
      <strong>Intervento sospeso per vento critico</strong> - Nessun mezzo può operare in sicurezza.
    </option>
  </select>





</div>

</fieldset>
