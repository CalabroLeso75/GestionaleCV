<fieldset>
  <legend>Altre Informazioni</legend>

  <label for="input-fonte-idrica">Fonte idrica</label>
  <select id="input-fonte-idrica">
    <option>Vasca</option>    
    <option>Mare</option>
    <option>Fiume</option>
    <option>Lago</option>
    <option>Diga</option>
    <option>Altro</option>
  </select>

  <label for="input-infrastrutture">Infrastrutture</label>
  <select id="input-infrastrutture" class="input-primario" onchange="gestisciInfrastrutture()">
    <option>Nessuna</option>
    <option>Industrie</option>
    <option>Serbatoi di Gas</option>
    <option>Serbatoi di Carburante</option>
    <option>Polveriere</option>
    <option>Discariche</option>
  </select>

  <div id="prossimita-infrastrutture" class="input-secondario" style="display: none; margin-top: 10px;">
    <label for="input-prossimita-infrastrutture">Prossimità incendio alle infrastrutture</label>
    <select id="input-prossimita-infrastrutture" class="input-secondario">
      <option value="">-- Seleziona --</option>
      <option value="vicino">A ridosso dell'infrastruttura</option>
      <option value="medio">Possibile raggiungimento in breve tempo</option>
      <option value="lontano">Attualmente distante</option>
    </select>
  </div>

  <label for="input-insediamenti">Insediamenti</label>
  <select id="input-insediamenti" class="input-primario" onchange="gestisciInsediamenti()">
    <option value="nessuno">Nessuno</option>
    <option value="abitazioni">Abitazioni Sparse</option>
    <option value="campeggi">Campeggi</option>
    <option value="centri">Centri Abitati</option>
    <option value="ricettive">Strutture Ricettive</option>
    <option value="sanitarie">Strutture Sanitarie</option>
    <option value="altro">Altro</option>
  </select>

  <div id="dettagli-insediamento" style="display: none; margin-top: 10px;">
    <label for="tipo-insediamento">Tipologia specifica</label>
    <select id="tipo-insediamento" class="input-secondario" name="tipo-insediamento" required></select>

    <div id="presenza-persone" class="input-secondario" style="margin-top: 10px;">
  <label style="display: block; margin-bottom: 6px;"><strong>Presenza persone</strong></label>
  <label style="display: inline-flex; align-items: center; margin-right: 20px;">
    <input type="radio" name="presenza-persone" value="certa" required>
    <span style="margin-left: 6px;">Certa</span>
  </label>
  <label style="display: inline-flex; align-items: center;">
    <input type="radio" name="presenza-persone" value="incerta">
    <span style="margin-left: 6px;">Incerta</span>
  </label>
</div>


    <div id="motivo-incerto" style="display: none; margin-top: 10px;">
      <label for="motivo-presenza-incerta">Motivo dell'incertezza</label>
      <input type="text" id="motivo-presenza-incerta" class="input-terziario" name="motivo-presenza-incerta" style="width: 100%;">
      <div style="margin-top: 4px;">
        <small style="color: darkred;">
          ❗ Specifica cosa ti fa ritenere possibile la presenza di persone e perché non hai potuto verificarla direttamente.
        </small>
      </div>
    </div>
  </div>

  <label for="input-ostacoli">Ostacoli</label>
  <select id="input-ostacoli" class="input-primario" onchange="gestisciOstacoli()">
    <option>Nessuno</option>
    <option>Antenne</option>
    <option>Fili a Sbalzo</option>
    <option>Funivie</option>
    <option>Tralicci</option>
    <option>Altro</option>
  </select>

  <div id="prossimita-ostacoli" style="display: none; margin-top: 10px;">
    <label for="input-prossimita-ostacoli">Prossimità incendio agli ostacoli</label>
    <select id="input-prossimita-ostacoli" class="input-secondario">
      <option value="">-- Seleziona --</option>
      <option value="vicino">In prossimità</option>
      <option value="medio">A distanza ma in traiettoria</option>
      <option value="lontano">Fuori area operativa immediata</option>
    </select>
  </div>

  <label for="input-elettrodotti">Elettrodotti</label>
  <select id="input-elettrodotti" class="input-primario">
    <option>Nessuno</option>
    <option>A distanza di sicurezza</option>
    <option>Attivi</option>
    <option>In disattivazione</option>
    <option>Non attivi</option>
  </select>

  <div id="operativita-elettrodotti" style="margin-top: 10px;">
    <label for="input-operativita-elettrodotti">Valutazione operatività in presenza di elettrodotti</label>
    <select id="input-operativita-elettrodotti" class="input-secondario">
      <option value="">-- Seleziona --</option>
      <option>Il mezzo può operare mantenendo distanza di sicurezza</option>
      <option>Il mezzo potrà operare non appena l'incendio supererà l'area degli elettrodotti</option>
      <option>L'incendio si sta avvicinando e potrebbe compromettere l’operatività</option>
    </select>
  </div>

  <label for="input-radio">Frequenza radio</label>
  <select id="input-radio">
    <option>122.150</option>
    <option>118.525</option>
    <option>122.350</option>
    <option>141.100</option>
  </select>

  <label for="input-ritardante">Uso ritardante</label>
  <select id="input-ritardante">
    <option>No</option>
    <option>Sì, solo alla prima sortita</option>
    <option>Sì, per tutte le sortite</option>
  </select>

  <label for="input-richiesta">Richiesta per</label>
  <select id="input-richiesta">
    <option>Soppressione</option>
    <option>Bonifica</option>
    <option>Contenimento</option>
    <option>Ricognizione Arm.</option>
  </select>
</fieldset>
