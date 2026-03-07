<?php
include("header.php");
require_once("../utils/auth_utils.php"); // ⬅️ aggiunto

if (!isUtenteAutorizzato()) {
    die("Accesso riservato esclusivamente a raffaele.cusano@calabriaverde.eu.");
}

include("menubar.php");

$rootDir = realpath(__DIR__ . '/../');
$currentDir = isset($_GET['dir']) ? realpath($rootDir . '/' . $_GET['dir']) : $rootDir;

if ($currentDir === false || strpos($currentDir, $rootDir) !== 0) {
    die("Percorso non valido.");
}

function isWritable($path) {
    return is_writable($path) ? '' : ' 🔒';
}

$relativePath = str_replace($rootDir, '', $currentDir);
$items = scandir($currentDir);
$breadcrumbs = explode('/', trim($relativePath, '/'));
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>File Manager Avanzato</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        a { text-decoration: none; color: blue; }
        .folder { font-weight: bold; }
        .actions {
            margin-right: 10px;
            font-size: 0.9em;
            display: inline-block;
            width: 130px;
            text-align: right;
            vertical-align: middle;
        }
        li { margin: 6px 0; }
    </style>
</head>
<body>
<h2>📁 Gestione File e Cartelle da root</h2>

<p><strong>Posizione:</strong>
<?php
echo '<a href="?dir=">📁 Root</a>';
if ($relativePath !== '') {
    $accPath = '';
    foreach ($breadcrumbs as $crumb) {
        if (!$crumb) continue;
        $accPath .= '/' . $crumb;
        echo ' / <a href="?dir=' . urlencode(ltrim($accPath, '/')) . '">' . htmlspecialchars($crumb) . '</a>';
    }
}
?>
</p>

<ul>
<?php if ($currentDir !== $rootDir): ?>
    <li>
        <span class="actions"></span>
        <a href="?dir=<?php echo urlencode(dirname($relativePath)); ?>">⬆️ ..</a>
    </li>
<?php else: ?>
    <li>
        <span class="actions">
            <a href="file_action_tree.php?action=create_folder&path=" title="Crea Cartella in Root">➕</a>
        </span>
        📁 Root
    </li>
<?php endif; ?>

<?php foreach ($items as $item):
    if ($item === '.' || $item === '..') continue;
    $fullPath = $currentDir . DIRECTORY_SEPARATOR . $item;
    $relPath = ltrim($relativePath . '/' . $item, '/');
    $writableIcon = is_dir($fullPath) ? isWritable($fullPath) : '';
    ?>
    <li>
        <?php if (is_dir($fullPath)): ?>
            <span class="actions">
                <a href="file_action_tree.php?action=create_folder&path=<?php echo urlencode($relPath); ?>" title="Crea Cartella">➕</a>
                <?php if ($fullPath !== $rootDir): ?>
                    <a href="file_action_tree.php?action=delete_folder&path=<?php echo urlencode($relPath); ?>" onclick="return confirm('Eliminare la cartella?')" title="Elimina Cartella">🗑️</a>
                <?php endif; ?>
                <a href="file_action_tree.php?action=upload_file&path=<?php echo urlencode($relPath); ?>" title="Carica File">📤</a>
                <a href="file_action_tree.php?action=change_permissions&path=<?php echo urlencode($relPath); ?>" title="Modifica Permessi">🔧</a>
            </span>
            📁 <a href="?dir=<?php echo urlencode($relPath); ?>"><?php echo htmlspecialchars($item); ?></a><?php echo $writableIcon; ?>
        <?php else: ?>
            <span class="actions">
                <a href="file_action_tree.php?action=rename_file&path=<?php echo urlencode($relPath); ?>" title="Rinomina File">✏️</a>
                <a href="file_action_tree.php?action=move_file&path=<?php echo urlencode($relPath); ?>" title="Sposta File">📂</a>
                <a href="file_action_tree.php?action=delete_file&path=<?php echo urlencode($relPath); ?>" onclick="return confirm('Eliminare il file?')" title="Elimina File">🗑️</a>
            </span>
            📄 <?php echo htmlspecialchars($item); ?>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
</body>
</html>
