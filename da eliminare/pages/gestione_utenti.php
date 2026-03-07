<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("header.php");
require_once("../utils/auth_utils.php"); // <--- aggiunta

if (!isUtenteAutorizzato()) { // <--- controllo centralizzato
    header("Location: index.php");
    exit();
}

include("menubar.php");

// Connessione DB
$db = new SQLite3("../data/gestione_utenti.db");

// Inserimento nuovo utente
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add'])) {
    $stmt = $db->prepare("INSERT INTO utenti (sigla, nome, cognome, telefono, email, organizzazione, provider_email, ruolo) 
                          VALUES (:sigla, :nome, :cognome, :telefono, :email, :organizzazione, :provider, :ruolo)");
    $stmt->bindValue(":sigla", $_POST["sigla"]);
    $stmt->bindValue(":nome", $_POST["nome"]);
    $stmt->bindValue(":cognome", $_POST["cognome"]);
    $stmt->bindValue(":telefono", $_POST["telefono"]);
    $stmt->bindValue(":email", $_POST["email"]);
    $stmt->bindValue(":organizzazione", $_POST["organizzazione"]);
    $stmt->bindValue(":provider", $_POST["provider"]);
    $stmt->bindValue(":ruolo", $_POST["ruolo"]);
    $stmt->execute();
}

// Estrazione elenco utenti
$result = $db->query("SELECT id, nome, cognome, sigla FROM utenti ORDER BY cognome, nome");
?>

<main class="conversion-container">
  <h2>➕ Aggiungi Nuovo Utente</h2>
  <form method="POST">
    <input type="hidden" name="add" value="1">
    <label>Nome:</label><input type="text" name="nome" required>
    <label>Cognome:</label><input type="text" name="cognome" required>
    <label>Sigla:</label><input type="text" name="sigla" required>
    <label>Telefono:</label><input type="text" name="telefono" required>
    <label>Email:</label><input type="email" name="email" required>
    <label>Organizzazione:</label><input type="text" name="organizzazione" required>
    <label>Provider Email:</label><input type="text" name="provider" required>
    <label>Ruolo:</label>
    <select name="ruolo" required>
      <option value="dos">DOS</option>
      <option value="admin">Amministratore</option>
	  <option value="operatore">Operatore di S.O.</option>
    </select>
    <button type="submit">➕ Aggiungi Utente</button>
  </form>

  <hr>
  <h2>📋 Elenco Utenti</h2>
  <ul>
    <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
      <li>
        <a href="modifica_utente.php?id=<?php echo $row['id']; ?>">
          <?php echo htmlspecialchars($row['sigla'] . " - " . $row['cognome'] . " " . $row['nome']); ?>
        </a>
      </li>
    <?php endwhile; ?>
  </ul>
</main>

<?php include("../includes/footer.php"); ?>
