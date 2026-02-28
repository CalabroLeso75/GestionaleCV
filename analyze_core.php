<?php
/**
 * Core Architecture AI Audit
 */

header('Content-Type: text/plain; charset=utf-8');

$schemaUrl = "http://localhost/GestionaleCV/dump_schema_direct.php";
$schema = file_get_contents($schemaUrl);
$data = json_decode($schema, true);

$url = "http://localhost:11434/api/generate";
$model = "llama3";

$prompt = "Analizza l'architettura delle tabelle principali del mio gestionale 'Calabria Verde'. Queste sono le tabelle core:

TABELLE:
1. Users (gestione accessi e ruoli)
2. Internal_employees (dipendenti interni)
3. External_employees (collaboratori esterni)
4. Organizations (enti/aziende esterne)
5. Activity_logs (tracciamento azioni)

SCHEMA COMPLETO:
$schema

DOMANDE PER L'AUDIT:
1. La relazione tra Users e Employees (Internal/External) è ottimale? Suggerisci come collegarli meglio se necessario (es. tramite ID univoci o chiavi esterne).
2. Come possiamo migliorare le performance delle tabelle di log che cresceranno velocemente?
3. Suggerisci indici avanzati per ricerche incrociate tra dipendenti e organizzazioni.
4. Identifica eventuali campi ridondanti o nomi di colonne inconsistenti.

Rispondi in italiano con un report tecnico professionale.";

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
curl_setopt($ch, CURLOPT_TIMEOUT, 180);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    $error = "ERRORE: " . curl_error($ch);
    echo $error;
    file_put_contents(__DIR__ . '/audit_report.txt', $error);
} else {
    $result = json_decode($response, true);
    $report = $result['response'] ?? "Nessuna risposta.";
    echo $report;
    file_put_contents(__DIR__ . '/audit_report.txt', $report);
}
curl_close($ch);
