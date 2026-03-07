<script>
// Compila automaticamente i campi DOS se la sessione è attiva
document.addEventListener("DOMContentLoaded", function () {
  <?php if (isset($_SESSION['nome']) && isset($_SESSION['cognome'])): ?>
    document.getElementById("dos-nome").value = "<?php echo addslashes($_SESSION['nome'] . ' ' . $_SESSION['cognome']); ?>";
  <?php endif; ?>
  <?php if (isset($_SESSION['sigla'])): ?>
    document.getElementById("dos-sigla").value = "<?php echo addslashes($_SESSION['sigla']); ?>";
  <?php endif; ?>
  <?php if (isset($_SESSION['telefono'])): ?>
    document.getElementById("dos-telefono").value = "<?php echo addslashes($_SESSION['telefono']); ?>";
  <?php endif; ?>
  <?php if (isset($_SESSION['email'])): ?>
    const emailInput = document.getElementById("dos-email");
    if (emailInput) emailInput.value = "<?php echo addslashes($_SESSION['email']); ?>";
  <?php endif; ?>
});
</script>
