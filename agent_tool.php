<?php
// agent_tool.php - Strumento di gestione file per l'assistente AI
// ATTENZIONE: Questo file è potente e dovrebbe essere protetto o rimosso in produzione.

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$key = $_GET['key'] ?? '';

// Simple security key (hardcoded for this session, User can change it)
if ($key !== 'AntigravitySecureKey123') {
    http_response_code(403);
    echo json_encode(['error' => 'Access Denied']);
    exit;
}

$baseDir = __DIR__;

try {
    switch ($action) {
        case 'list':
            $dir = $_GET['dir'] ?? '.';
            $path = realpath($baseDir . '/' . $dir);
            if (!$path || strpos($path, $baseDir) !== 0) throw new Exception("Invalid path");
            
            $files = scandir($path);
            $result = [];
            foreach ($files as $f) {
                if ($f == '.' || $f == '..') continue;
                $result[] = [
                    'name' => $f,
                    'type' => is_dir($path . '/' . $f) ? 'dir' : 'file',
                    'size' => is_file($path . '/' . $f) ? filesize($path . '/' . $f) : 0
                ];
            }
            echo json_encode(['data' => $result]);
            break;

        case 'read':
            $file = $_GET['file'] ?? '';
            $path = realpath($baseDir . '/' . $file);
            if (!$path || strpos($path, $baseDir) !== 0 || !is_file($path)) throw new Exception("Invalid file");
            echo json_encode(['content' => file_get_contents($path)]);
            break;

        case 'write':
            $input = json_decode(file_get_contents('php://input'), true);
            $file = $input['file'] ?? '';
            $content = $input['content'] ?? '';
            $path = $baseDir . '/' . $file;
            // Basic path traversal check
            if (strpos($path, '..') !== false) throw new Exception("Invalid path");
            
            $dir = dirname($path);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            
            if (file_put_contents($path, $content) !== false) {
                echo json_encode(['status' => 'success']);
            } else {
                throw new Exception("Write failed");
            }
            break;
            
        case 'delete':
            $file = $_GET['file'] ?? '';
            $path = realpath($baseDir . '/' . $file);
            if (!$path || strpos($path, $baseDir) !== 0) throw new Exception("Invalid path");
            
            if (is_file($path)) unlink($path);
            elseif (is_dir($path)) rmdir($path); // Only empty dirs
            
            echo json_encode(['status' => 'success']);
            break;

        default:
            echo json_encode(['error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
