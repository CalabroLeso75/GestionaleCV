<div class="form-section">
    <label>Direttore delle Operazioni di Spegnimento (DOS):</label>
    <input type="text" name="nome_dos" placeholder="Nome" required>
    <input type="text" name="cognome_dos" placeholder="Cognome" required>
    <input type="text" name="sigla_radio" placeholder="Sigla Radio" pattern="[A-Z0-9]+" title="Solo lettere maiuscole e numeri, senza spazi o caratteri speciali." required>
    <input type="tel" name="telefono1" placeholder="Numero di telefono 1" required>
    <input type="tel" name="telefono2" placeholder="Numero di telefono 2 (opzionale)">

    <label>Frequenza Radio TBT:</label>
    <select name="frequenza_tbt">
        <option>122.150</option>
        <option>118.525</option>
        <option>122.350</option>
        <option>141.100</option>
    </select>

    <label>Squadre a terra:</label>
    <select id="squadre_terra" name="squadre_terra" onchange="mostraNumeroSquadre()">
        <option>No</option>
        <option>Si</option>
    </select>
    <div id="numero_squadre" style="display: none;">
        <label>Numero di squadre:</label>
        <input type="number" name="numero_squadre">
    </div>

    <label>Presenza elicottero regionale:</label>
    <input type="checkbox" name="elicottero_regionale">
</div>
