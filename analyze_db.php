<?php
/**
 * AI-Driven Database Audit
 * This script sends the database schema to the local Ollama instance for analysis.
 */

header('Content-Type: text/plain; charset=utf-8');

$schemaUrl = "http://localhost/GestionaleCV/dump_schema_direct.php";
echo "Recuperando lo schema live da $schemaUrl...\n";
$schema = file_get_contents($schemaUrl);

if (!$schema) {
    die("Errore: Impossibile recuperare lo schema dal server web.\n");
}
$url = "http://localhost:11434/api/generate";
$model = "llama3";

$prompt = "Sei un esperto Database Administrator e Software Architect. Analizza lo schema del database fornito qui sotto in formato JSON. 
Lo schema appartiene a un 'Gestionale' per un'organizzazione forestale chiamato 'Calabria Verde'.

Compiti:
1. Identifica potenziali problemi di normalizzazione.
2. Suggerisci indici (INDEX) mancanti per ottimizzare le ricerche comuni (es. per codice fiscale, email, stato).
3. Evidenzia eventuali incongruenze nei tipi di dato (es. lunghezze stringhe, tipi per date/ora).
4. Suggerisci miglioramenti per le relazioni tra le tabelle principali come users, internal_employees, external_employees e organizations.

SCHEMA:\n$schema\n\nRispondi in italiano con un report tecnico dettagliato ma leggibile.";

$data = [
    "model" => $model,
    "prompt" => $prompt,
    "stream" => false
];

echo "Inviando lo schema a Ollama per l'audit di sistema...\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutes for complex analysis

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo "ERRORE: " . curl_error($ch) . "\n";
} else {
    if ($httpCode == 200) {
        $result = json_decode($response, true);
        echo "\nREPORT AUDIT IA LOCALE:\n";
        echo "========================================\n";
        echo $result['response'] ?? "Nessun report generato.";
        echo "\n========================================\n";
    } else {
        echo "ERRORE HTTP $httpCode: " . $response . "\n";
    }
}

curl_close($ch);
