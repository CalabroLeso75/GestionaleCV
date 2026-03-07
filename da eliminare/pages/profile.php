<?php
include("header.php");
require_once("../utils/auth_utils.php"); // <--- aggiunto per usare isUtenteAutorizzato()
?>
<?php include("menubar.php"); ?>
<main class="conversion-container">
  <h2>👤 Profilo Utente</h2>

  <p><strong>Nome:</strong> <?php echo $_SESSION['nome']; ?></p>
  <p><strong>Cognome:</strong> <?php echo $_SESSION['cognome']; ?></p>
  <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
  <p><strong>Sigla:</strong> <?php echo $_SESSION['sigla']; ?></p>
  <p><strong>Telefono:</strong> <?php echo $_SESSION['telefono']; ?></p>
  <p><strong>Organizzazione:</strong> <?php echo $_SESSION['organizzazione']; ?></p>
  <p><strong>Provider Email:</strong> <?php echo $_SESSION['provider']; ?></p>
  <p><strong>Ruolo:</strong> <?php echo ($_SESSION['ruolo'] === 'admin') ? 'Amministratore' : 'Utente DOS'; ?></p>

  <?php if (isUtenteAutorizzato()): ?> <!-- SOLO se autorizzato -->
    <hr>
    <a href="gestione_utenti.php" class="orange-button">🔧 Gestione Utenti</a>
    <br><br>
    <a href="file_manager.php" class="orange-button">📂 Gestione File e Cartelle</a>
  <?php endif; ?>
</main>

<?php include("footer.php"); ?>
