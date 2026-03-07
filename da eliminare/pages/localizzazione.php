<!-- Nomefile: localizzazione.php -->
<div class="form-section">
    <label>Provincia:</label>
    <select name="provincia">
        <option>Catanzaro</option>
        <option>Cosenza</option>
        <option>Crotone</option>
        <option>Reggio Calabria</option>
        <option>Vibo Valentia</option>
    </select>

    <label>Comune:</label>
    <input type="text" name="comune" required>

    <label>Località:</label>
    <input type="text" name="localita" required>

    <label>Quota sul livello del mare (m):</label>
    <input type="number" id="altitudine" name="altitudine" step="0.1">
    
    <label>Coordinate geografiche (DMS):</label>
    <div class="coord-group">
        <input type="number" name="latDeg" placeholder="Gradi">°
        <input type="number" name="latMin" placeholder="Minuti">' 
        <input type="number" name="latSec" placeholder="Secondi">'' 
    </div>
    <div class="coord-group">
        <input type="number" name="lonDeg" placeholder="Gradi">° 
        <input type="number" name="lonMin" placeholder="Minuti">' 
        <input type="number" name="lonSec" placeholder="Secondi">'' 
    </div>
</div>
