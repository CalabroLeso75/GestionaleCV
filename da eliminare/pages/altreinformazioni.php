<div class="form-section">
    <label>Ritardante:</label>
    <select id="ritardante" name="ritardante" onchange="mostraNotaRitardante()">
        <option>No</option>
        <option>Si prima sortita</option>
        <option>Si tutte le sortite</option>
    </select>
    <div id="nota_ritardante" style="display: none;">
        <label>Motivazione dell'uso del ritardante:</label>
        <textarea name="nota_ritardante" required></textarea>
    </div>

    <label>Note:</label>
    <textarea name="note" rows="6" placeholder="Inserisci ulteriori dettagli..."></textarea>
</div>
