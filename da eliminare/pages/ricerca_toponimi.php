<?php
// Imposta intestazioni per risposte JSON e CORS (se utile)
header('Content-Type: application/json');

// Recupera latitudine e longitudine passate via GET o POST
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;

if ($lat === null || $lon === null) {
  echo json_encode(["error" => "Coordinate mancanti"]);
  exit;
}

// Carica i toponimi da un file CSV generato una tantum (per prestazioni)
$csvFile = __DIR__ . '/../data/toponimi_calabria.csv';
if (!file_exists($csvFile)) {
  echo json_encode(["error" => "File dati non trovato"]);
  exit;
}

$toponimi = [];
if (($handle = fopen($csvFile, "r")) !== false) {
  fgetcsv($handle); // salta intestazione
  while (($data = fgetcsv($handle)) !== false) {
    $toponimi[] = [
      "name" => $data[0],
      "latitude" => floatval($data[1]),
      "longitude" => floatval($data[2])
    ];
  }
  fclose($handle);
}

// Calcolo distanza Haversine
function haversine($lat1, $lon1, $lat2, $lon2) {
  $R = 6371; // raggio terrestre in km
  $dLat = deg2rad($lat2 - $lat1);
  $dLon = deg2rad($lon2 - $lon1);
  $a = sin($dLat/2) * sin($dLat/2) +
       cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
       sin($dLon/2) * sin($dLon/2);
  $c = 2 * atan2(sqrt($a), sqrt(1-$a));
  return $R * $c;
}

// Calcola le distanze
foreach ($toponimi as &$t) {
  $t['distanza'] = haversine($lat, $lon, $t['latitude'], $t['longitude']);
}
unset($t);

// Ordina e prendi i 20 più vicini
usort($toponimi, fn($a, $b) => $a['distanza'] <=> $b['distanza']);
$vicini = array_slice($toponimi, 0, 20);

echo json_encode($vicini);
exit;
?>
