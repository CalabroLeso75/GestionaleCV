<?php
session_start();
require_once 'menu.php'; // Include il menu
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - Step 1</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <!-- HEADER -->
    <header>
        <?php echo $menu; ?> <!-- Includi il menu qui -->
    </header>

    <!-- AREA CENTRALE -->
    <div class="conversion-container">
        <h2>Step 1: Dati Personali</h2>
        <?php if (isset($errors) && !empty($errors)): ?>
            <ul class="error">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <form method="post">
            <label for="firstName">Nome:</label>
            <input type="text" id="firstName" name="firstName" required>

            <label for="lastName">Cognome:</label>
            <input type="text" id="lastName" name="lastName" required>

            <label for="birthDate">Data di Nascita (YYYY-MM-DD):</label>
            <input type="date" id="birthDate" name="birthDate" required>

            <label for="countryOfBirth">Paese di Nascita:</label>
            <input type="text" id="countryOfBirth" name="countryOfBirth" required>

            <label>Sono straniero:</label>
            <input type="checkbox" id="isForeigner" name="isForeigner">

            <div id="italianDetails" style="display:none;">
                <label for="provinceOfBirth">Provincia di Nascita:</label>
                <input type="text" id="provinceOfBirth" name="provinceOfBirth">

                <label for="cityOfBirth">Comune di Nascita:</label>
                <input type="text" id="cityOfBirth" name="cityOfBirth">
            </div>

            <script src="../js/scripts.js"></script>
            <button type="submit">Avanti</button>
        </form>
    </div>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2023 Calabria Verde. Tutti i diritti riservati.</p>
    </footer>

</body>
</html>