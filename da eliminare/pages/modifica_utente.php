<?php
// nomefile: /pages/modifica_utente.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ Attiva buffer per permettere i redirect
ob_start();

include("header.php");

if ($_SESSION['ruolo'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$db = new SQLite3("../data/gestione_utenti.db");
$id = $_GET['id'] ?? 0;
$utente = $db->querySingle("SELECT * FROM utenti WHERE id = $id", true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update'])) {
        $stmt = $db->prepare("UPDATE utenti SET sigla = :sigla, nome = :nome, cognome = :cognome, telefono = :telefono, email = :email, organizzazione = :organizzazione, provider_email = :provider, ruolo = :ruolo WHERE id = :id");
        $stmt->bindValue(":sigla", $_POST["sigla"]);
        $stmt->bindValue(":nome", $_POST["nome"]);
        $stmt->bindValue(":cognome", $_POST["cognome"]);
        $stmt->bindValue(":telefono", $_POST["telefono"]);
        $stmt->bindValue(":email", $_POST["email"]);
        $stmt->bindValue(":organizzazione", $_POST["organizzazione"]);
        $stmt->bindValue(":provider", $_POST["provider"]);
        $stmt->bindValue(":ruolo", $_POST["ruolo"]);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        header("Location: gestione_utenti.php");
        exit();
    } elseif (isset($_POST['delete'])) {
        $db->exec("DELETE FROM utenti WHERE id = $id");
        header("Location: gestione_utenti.php");
        exit();
    }
}

include("menubar.php");
?>

<main class="conversion-container">
  <h2>✏️ Modifica Utente</h2>
  <form method="POST">
    <label>Nome:</label><input type="text" name="nome" value="<?php echo htmlspecialchars($utente['nome']); ?>" required>
    <label>Cognome:</label><input type="text" name="cognome" value="<?php echo htmlspecialchars($utente['cognome']); ?>" required>
    <label>Sigla:</label><input type="text" name="sigla" value="<?php echo htmlspecialchars($utente['sigla']); ?>" required>
    <label>Telefono:</label><input type="text" name="telefono" value="<?php echo htmlspecialchars($utente['telefono']); ?>" required>
    <label>Email:</label><input type="email" name="email" value="<?php echo htmlspecialchars($utente['email']); ?>" required>
    <label>Organizzazione:</label><input type="text" name="organizzazione" value="<?php echo htmlspecialchars($utente['organizzazione']); ?>" required>
    <label>Provider Email:</label><input type="text" name="provider" value="<?php echo htmlspecialchars($utente['provider_email']); ?>" required>
    <label>Ruolo:</label>
    <select name="ruolo">
      <option value="dos" <?php if ($utente['ruolo'] === 'dos') echo 'selected'; ?>>DOS</option>
      <option value="admin" <?php if ($utente['ruolo'] === 'admin') echo 'selected'; ?>>Amministratore</option>
      <option value="operatore" <?php if ($utente['ruolo'] === 'operatore') echo 'selected'; ?>>Operatore di S.O.</option>
    </select>
    <button type="submit" name="update">💾 Salva Modifiche</button>
    <button type="submit" name="delete" onclick="return confirm('Sei sicuro di voler eliminare questo utente?');" style="background: #f44336;">🗑️ Elimina Utente</button>
    <a href="gestione_utenti.php" style="
      display: inline-block;
      padding: 10px 20px;
      background-color: #ff5722;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      margin-top: 20px;
    ">⬅️ Torna alla Gestione Utenti</a>
  </form>
</main>

<?php
include("../includes/footer.php");
ob_end_flush(); // ✅ chiusura del buffer di output
?>
