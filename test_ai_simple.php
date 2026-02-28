<?php
/**
 * Simplified AI Diagnostic
 */

header('Content-Type: text/plain; charset=utf-8');

$schemaUrl = "http://localhost/GestionaleCV/dump_schema_direct.php";
$schema = file_get_contents($schemaUrl);
$data = json_decode($schema, true);
$usersSchema = json_encode($data['users'] ?? [], JSON_PRETTY_PRINT);

$url = "http://localhost:11434/api/generate";
$model = "llama3";

$prompt = "Analizza brevemente la tabella Users del mio database e suggeriscimi 2 indici necessari.\nSCHEMA:\n$usersSchema";

$postData = [
    "model" => $model,
    "prompt" => $prompt,
    "stream" => false
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo "ERRORE: " . curl_error($ch);
} else {
    $result = json_decode($response, true);
    echo $result['response'] ?? "Nessuna risposta.";
}
curl_close($ch);
