<?php
// verify_providers.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Definizione dei provider noti
$providers = [
    [
        'name' => 'Aruba',
        'hosts' => ['smtp.aruba.it', 'smtps.aruba.it'], // Host da testare
        'ports' => [587, 465], // Porte da testare
        'encryptions' => ['tls', 'ssl'] // Tipi di crittografia da testare
    ],
    [
        'name' => 'Gmail',
        'host' => 'smtp.gmail.com',
        'ports' => [587, 465],
        'encryptions' => ['tls', 'ssl']
    ],
    [
        'name' => 'Outlook',
        'host' => 'smtp-mail.outlook.com',
        'ports' => [587],
        'encryptions' => ['tls']
    ],
    [
        'name' => 'Yahoo',
        'host' => 'smtp.mail.yahoo.com',
        'ports' => [465],
        'encryptions' => ['ssl']
    ]
];

// File CSV per salvare i risultati
$dataFile = '../data/providers_log.csv';

// Funzione per testare la connessione SMTP
function testSMTPConnection($provider, $host, $port, $encryption) {
    $mail = new PHPMailer(true);

    try {
        // Configurazione SMTP
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = false; // Non usiamo credenziali per il test
        $mail->Port = $port;
        $mail->SMTPSecure = $encryption;

        // Tentativo di connessione
        $mail->smtpConnect();
        return 'Successo';
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}

// Verifica ciascun provider
$results = [];
foreach ($providers as $provider) {
    if (isset($provider['hosts'])) {
        foreach ($provider['hosts'] as $host) {
            foreach ($provider['ports'] as $port) {
                foreach ($provider['encryptions'] as $encryption) {
                    $status = testSMTPConnection($provider, $host, $port, $encryption);
                    $results[] = [
                        'name' => $provider['name'],
                        'host' => $host,
                        'port' => $port,
                        'encryption' => $encryption,
                        'status' => $status
                    ];
                }
            }
        }
    } else {
        foreach ($provider['ports'] as $port) {
            foreach ($provider['encryptions'] as $encryption) {
                $status = testSMTPConnection($provider, $provider['host'], $port, $encryption);
                $results[] = [
                    'name' => $provider['name'],
                    'host' => $provider['host'],
                    'port' => $port,
                    'encryption' => $encryption,
                    'status' => $status
                ];
            }
        }
    }
}

// Salva i risultati nel file CSV
if (!file_exists($dataFile)) {
    // Crea l'intestazione del file CSV se non esiste
    $header = ['Provider', 'Host', 'Porta', 'Crittografia', 'Stato'];
    file_put_contents($dataFile, implode(',', $header) . PHP_EOL);
}

foreach ($results as $result) {
    $row = [
        $result['name'],
        $result['host'],
        $result['port'],
        $result['encryption'],
        '"' . str_replace('"', '""', $result['status']) . '"'
    ];
    file_put_contents($dataFile, implode(',', $row) . PHP_EOL, FILE_APPEND);
}

// Mostra i risultati a schermo
echo "<h1>Risultati Verifica Provider</h1>";
echo "<table border='1'>";
echo "<tr><th>Provider</th><th>Host</th><th>Porta</th><th>Crittografia</th><th>Stato</th></tr>";
foreach ($results as $result) {
    echo "<tr>";
    echo "<td>{$result['name']}</td>";
    echo "<td>{$result['host']}</td>";
    echo "<td>{$result['port']}</td>";
    echo "<td>{$result['encryption']}</td>";
    echo "<td>" . htmlspecialchars($result['status']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>