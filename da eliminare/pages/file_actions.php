<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['ruolo'] !== 'admin') {
    die("Accesso negato.");
}

$baseDir = realpath(__DIR__ . "/../uploads");
$relativePath = $_POST['path'] ?? '';
$targetPath = realpath($baseDir . '/' . $relativePath);

if (!$targetPath || strpos($targetPath, $baseDir) !== 0) {
    die("Percorso non valido.");
}

$action = $_POST['action'];

switch ($action) {
  case 'create_folder':
    $folderName = basename($_POST['folder_name']);
    mkdir($targetPath . '/' . $folderName, 0775, true);
    break;

  case 'upload_file':
    $file = $_FILES['file'];
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $filename = basename($file['name']);
        $dest = $targetPath . '/' . $filename;
        move_uploaded_file($file['tmp_name'], $dest);
    }
    break;

  case 'delete_folder':
    $folderName = basename($_POST['folder_name']);
    $folderPath = $targetPath . '/' . $folderName;
    if (is_dir($folderPath)) {
        array_map('unlink', glob("$folderPath/*.*"));
        rmdir($folderPath);
    }
    break;

  case 'delete_file':
    $fileName = basename($_POST['file_name']);
    $filePath = $targetPath . '/' . $fileName;
    if (is_file($filePath)) {
        unlink($filePath);
    }
    break;
}

header("Location: file_manager.php?path=" . urlencode($relativePath));
exit();
