<fieldset>
  <legend>Note Aggiuntive</legend>

  <label for="input-note">Note</label>
  <textarea id="input-note" rows="12" maxlength="10000"
    placeholder="Inserisci eventuali osservazioni, ostacoli, presenza di vento, insediamenti o infrastrutture..."
    style="width: 100%; font-size: calc(2vw + 1em);"
    oninput="aggiornaContatoreNote()"></textarea>

  <div style="text-align: right; font-size: 0.9em; margin-top: 5px;" id="note-counter">0 / 10.000</div>

  <small>
    Questo campo va compilato con particolare attenzione in presenza di ostacoli, insediamenti, infrastrutture o elettrodotti,
    nonché in caso di vento moderato o forte. È necessario segnalare se vi sono persone o cose a rischio incolumità.
    Specificare se l’intervento del mezzo aereo è indispensabile per spegnere l’incendio oppure se è richiesto per supportare
    le altre risorse impiegate nelle operazioni di spegnimento. Infine, indicare se il mezzo aereo potrà operare in condizioni
    di sicurezza, garantendo la protezione dell’equipaggio, la salvaguardia del mezzo e l’assenza di rischi per terzi.
  </small>
</fieldset>
