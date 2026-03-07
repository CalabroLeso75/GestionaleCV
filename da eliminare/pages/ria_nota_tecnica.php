<?php //nomefile: ria_nota_tecnica.php ?>
<fieldset>
  <legend>Nota Tecnica</legend>

  <label for="nota-tecnica-auto">Nota tecnica generata dal sistema</label>
  <textarea id="nota-tecnica-auto" readonly rows="12" style="width: 100%; font-size: 0.7em; background-color: #f2f2f2; color: black; font-style: normal; line-height: 1.5em; border-left: 4px solid #005f2f; padding: 10px;"></textarea>
  <input type="hidden" id="input-livello-criticita" name="input-livello-criticita" value="--/10">

  <div id="descrizione-nota-tecnica" style="margin-top: 10px; font-size: 0.85em; line-height: 1.6em;">
    <p>
      La presente nota tecnica, generata automaticamente in base ai dati inseriti dal Direttore delle Operazioni di Spegnimento (DOS), fornisce una stima iniziale del livello di criticità dell'evento. Il contenuto non è modificabile e rappresenta un supporto informativo per la successiva valutazione operativa da parte della Sala Operativa in collaborazione con il DOS.
    </p>
    <p>
      Eventuali precisazioni o ulteriori elementi di rilievo devono essere indicati nel campo <strong>Note Aggiuntive</strong>.
    </p>
  </div>
</fieldset>