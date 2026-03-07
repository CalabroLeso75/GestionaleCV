<fieldset>
  <legend>Credenziali Email per Invio</legend>

  <label for="dos-email">Email (Aruba)</label>
  <input id="dos-email" type="email"
         placeholder="nome@dominio.it"
         oninput="this.value = this.value.toLowerCase();"
         required
         style="width: 100%; height: 3.5em; font-size: calc(2vw + 1em);">

  <label for="dos-password">Password</label>
  <div style="position:relative;">
    <input id="dos-password" type="password"
           placeholder="Password"
           autocomplete="off"
           style="width: 100%; height: 3.5em; font-size: calc(2vw + 1em);"
           required>
    <span onclick="togglePassword()"
          style="position: absolute; right: 10px; top: 12px; cursor: pointer; font-size: 1.5em;">👁️</span>
  </div>

  <label>
    <input type="checkbox" id="remember-auth"> Ricorda credenziali su questo dispositivo
  </label>

  <small><br>
    I dati inseriti verranno utilizzati solo per l'invio della richiesta tramite la tua email Aruba e,
    se selezionato, saranno salvati unicamente nel tuo dispositivo per utilizzi futuri.
  </small>
</fieldset>


