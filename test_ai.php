<?php
/**
 * Test Script for Local AI (Ollama)
 * This script attempts to communicate with the Ollama server running on localhost.
 */

header('Content-Type: text/plain; charset=utf-8');

$url = "http://localhost:11434/api/generate";
$model = "llama3"; // Change to "mistral" or "gemma" if downloaded

$data = [
    "model" => $model,
    "prompt" => "Ciao! Sei un assistente IA locale. Conferma che ricevi questo messaggio e dimmi brevemente quali sono i tuoi vantaggi rispetto ad una IA in cloud.",
    "stream" => false
];

echo "Inviando richiesta a Ollama ($model) su $url...\n\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo "ERRORE CONNESSIONE: " . curl_error($ch) . "\n";
    echo "Assicurati che Ollama sia installato e avviato.\n";
} else {
    if ($httpCode == 200) {
        $result = json_decode($response, true);
        echo "RISPOSTA DALL'IA:\n";
        echo "----------------------------------------\n";
        echo $result['response'] ?? "Risposta vuota.";
        echo "\n----------------------------------------\n";
    } else {
        echo "ERRORE HTTP $httpCode: " . $response . "\n";
        echo "Controlla se il modello '$model' è stato scaricato con 'ollama run $model'.\n";
    }
}

curl_close($ch);
