<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['email']) || $_SESSION['ruolo'] !== 'admin') {
    die("Accesso negato.");
}

$rootDir = realpath(__DIR__ . '/../');
$relPath = $_GET['path'] ?? '';
$fullPath = realpath($rootDir . '/' . $relPath);

if ($fullPath === false || strpos($fullPath, $rootDir) !== 0) {
    die("Percorso non valido.");
}

$action = $_GET['action'] ?? '';

function getReturnPath($relPath) {
    return $relPath === '' ? '' : dirname($relPath);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'create_folder':
            $newFolder = basename($_POST['new_folder']);
            $target = $fullPath . '/' . $newFolder;
            if (!file_exists($target)) {
                mkdir($target, 0775, true);
            }
            header('Location: file_manager.php?dir=' . urlencode($relPath));
            exit();

        case 'upload_file':
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $dest = $fullPath . '/' . basename($_FILES['file']['name']);
                move_uploaded_file($_FILES['file']['tmp_name'], $dest);
            }
            header('Location: file_manager.php?dir=' . urlencode($relPath));
            exit();

        case 'rename_file':
            $newName = basename($_POST['new_name']);
            $newPath = dirname($fullPath) . '/' . $newName;
            rename($fullPath, $newPath);
            header('Location: file_manager.php?dir=' . urlencode(getReturnPath($relPath)));
            exit();

        case 'move_file':
            $newDir = rtrim($_POST['new_dir'], '/');
            $targetDir = realpath($rootDir . '/' . $newDir);
            if ($targetDir && strpos($targetDir, $rootDir) === 0) {
                $newPath = $targetDir . '/' . basename($fullPath);
                rename($fullPath, $newPath);
                header('Location: file_manager.php?dir=' . urlencode($newDir));
                exit();
            } else {
                echo "Percorso di destinazione non valido.";
            }
            break;

        case 'change_permissions':
            $permessi = intval($_POST['permissions'], 8);
            if (chmod($fullPath, $permessi)) {
                header('Location: file_manager.php?dir=' . urlencode(getReturnPath($relPath)));
                exit();
            } else {
                echo "Errore nel cambio dei permessi.";
            }
            break;
    }
}

switch ($action) {
    case 'create_folder':
        echo '<form method="POST"><h3>➕ Crea nuova cartella</h3>
              <input type="text" name="new_folder" required>
              <button type="submit">Crea</button></form>';
        break;

    case 'upload_file':
        echo '<form method="POST" enctype="multipart/form-data"><h3>📤 Carica file</h3>
              <input type="file" name="file" required>
              <button type="submit">Carica</button></form>';
        break;

    case 'delete_folder':
        if (is_dir($fullPath)) {
            array_map('unlink', glob($fullPath . '/*.*'));
            rmdir($fullPath);
        }
        header('Location: file_manager.php?dir=' . urlencode(getReturnPath($relPath)));
        exit();

    case 'delete_file':
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
        header('Location: file_manager.php?dir=' . urlencode(getReturnPath($relPath)));
        exit();

    case 'rename_file':
        echo '<form method="POST"><h3>✏️ Rinomina file/cartella</h3>
              <input type="text" name="new_name" required>
              <button type="submit">Rinomina</button></form>';
        break;

    case 'move_file':
        echo '<form method="POST"><h3>📂 Sposta file/cartella</h3>
              <label>Nuovo percorso relativo da root:</label>
              <input type="text" name="new_dir" required>
              <button type="submit">Sposta</button></form>';
        break;

    case 'change_permissions':
        echo '<form method="POST"><h3>🔧 Cambia permessi</h3>
              <label>Inserisci permessi (es: 0755):</label>
              <input type="text" name="permissions" pattern="[0-7]{3,4}" required>
              <button type="submit">Applica</button></form>';
        break;

    default:
        echo "Azione non valida.";
        break;
}
?>