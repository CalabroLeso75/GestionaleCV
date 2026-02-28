<?php
$log = "Sync Log - " . date('Y-m-d H:i:s') . "\n";
try {
    $conn = new mysqli('127.0.0.1', 'root', '', 'gestionale_cv');
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $areas = [
        ['Sala Operativa Unificata Permanente (SOUP)', 'soup', 'Sala operativa centrale'],
        ['Centro Operativo Provinciale di Cosenza (COPCS)', 'cop-cosenza', 'COP Cosenza'],
        ['Centro Operativo Provinciale di Catanzaro (COPCZ)', 'cop-catanzaro', 'COP Catanzaro'],
        ['Centro Operativo Provinciale di Crotone (COPKR)', 'cop-crotone', 'COP Crotone'],
        ['Centro Operativo Provinciale di Vibo Valentia (COPVV)', 'cop-vibo-valentia', 'COP Vibo Valentia'],
        ['Centro Operativo Provinciale di Reggio Calabria (COPRC)', 'cop-reggio-calabria', 'COP Reggio Calabria'],
        ['Antincendio Boschivo', 'antincendio-boschivo', 'Prevenzione e lotta incendi boschivi'],
        ['Gestione delle Emergenze di Protezione Civile', 'emergenze-pc', 'Coordinamento emergenze'],
        ['Gestione dei Mezzi Aerei Regionali', 'mezzi-aerei', 'Flotta aerea regionale'],
        ['Gestione delle Squadre AIB e Supporto PC', 'squadre-aib-pc', 'Coordinamento squadre terra'],
        ['Gestione dei Mezzi di terra', 'mezzi-terra', 'Parco mezzi terrestre'],
        ['Gestione Utenze Telefoni e indirizzi di posta aziendali', 'utenze-aziendali', 'Gestione comunicazioni'],
        ['Gestione turnazioni personale AIB', 'turnazioni-aib', 'Turni e reperibilità'],
    ];

    foreach ($areas as $a) {
        $stmt = $conn->prepare("INSERT INTO system_areas (name, slug, description, is_active) VALUES (?, ?, ?, 1) 
                                ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), is_active = 1");
        $stmt->bind_param("sss", $a[0], $a[1], $a[2]);
        if ($stmt->execute()) {
            $log .= "Success: {$a[1]}\n";
        } else {
            $log .= "Error: {$a[1]} - " . $stmt->error . "\n";
        }
    }
    
    $conn->close();
    $log .= "Sync Finished Successfully\n";
} catch (Exception $e) {
    $log .= "Fatal Error: " . $e->getMessage() . "\n";
}
file_put_contents('force_sync_report.txt', $log);
echo "Report written to force_sync_report.txt";
