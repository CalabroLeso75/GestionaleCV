<?php
// Precompilazione automatica dei dati nella form richiesta aerea
$nome = isset($_SESSION['nome']) ? $_SESSION['nome'] : '';
$cognome = isset($_SESSION['cognome']) ? $_SESSION['cognome'] : '';
$sigla = isset($_SESSION['sigla']) ? $_SESSION['sigla'] : '';
$telefono = isset($_SESSION['telefono']) ? $_SESSION['telefono'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$organizzazione = isset($_SESSION['organizzazione']) ? $_SESSION['organizzazione'] : '';
$provider = isset($_SESSION['provider']) ? $_SESSION['provider'] : '';
?>
<script>
window.addEventListener('DOMContentLoaded', function () {
  document.getElementById('dos-nome').value = "<?php echo htmlspecialchars($nome . ' ' . $cognome); ?>";
  document.getElementById('dos-sigla').value = "<?php echo htmlspecialchars($sigla); ?>";
  document.getElementById('dos-telefono').value = "<?php echo htmlspecialchars($telefono); ?>";
  document.getElementById('dos-email').value = "<?php echo htmlspecialchars($email); ?>";
});
</script>
