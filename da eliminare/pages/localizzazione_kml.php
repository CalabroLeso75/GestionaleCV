<?php
// File: localizzazione_kml.php
// Scopo: restituisce comune e provincia in cui ricade un punto (lat, lon) usando file KML locali

header('Content-Type: application/json');

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;

if ($lat === null || $lon === null) {
    echo json_encode(['error' => 'Coordinate non valide']);
    exit;
}

function parseCoordinates($coordStr) {
    $pairs = preg_split('/\s+/', trim($coordStr));
    $coords = [];
    foreach ($pairs as $pair) {
        list($lon, $lat) = explode(',', trim($pair));
        $coords[] = [floatval($lon), floatval($lat)];
    }
    return $coords;
}

function pointInPolygon($point, $polygon) {
    $x = $point[0];
    $y = $point[1];
    $inside = false;
    $n = count($polygon);
    for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
        $xi = $polygon[$i][0];
        $yi = $polygon[$i][1];
        $xj = $polygon[$j][0];
        $yj = $polygon[$j][1];
        $intersect = (($yi > $y) != ($yj > $y)) &&
                     ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-10) + $xi);
        if ($intersect) $inside = !$inside;
    }
    return $inside;
}

function trovaNomeKML($file, $point) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->load($file);
    $placemarks = $dom->getElementsByTagName('Placemark');

    foreach ($placemarks as $placemark) {
        $nameNode = $placemark->getElementsByTagName('name')->item(0);
        $coordNode = $placemark->getElementsByTagName('coordinates')->item(0);
        if (!$nameNode || !$coordNode) continue;

        $coords = parseCoordinates($coordNode->nodeValue);
        if (pointInPolygon($point, $coords)) {
            return $nameNode->nodeValue;
        }
    }
    return null;
}

$target = [$lon, $lat];

$comune = trovaNomeKML(__DIR__ . '/comuni.kml', $target);
$provincia = trovaNomeKML(__DIR__ . '/province.kml', $target);

$response = [
    'comune' => $comune ?: 'Comune non trovato',
    'provincia' => $provincia ?: 'Provincia non trovata'
];

echo json_encode($response);
exit;