<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'gestionale_cv');
if ($conn->connect_error) { die("F"); }
$result = $conn->query("SELECT * FROM dashboard_sections");
$out = "ID | TITLE | ACTIVE | ROUTE\n";
while($row = $result->fetch_assoc()) {
    $out .= "{$row['id']} | {$row['title']} | {$row['is_active']} | {$row['route']}\n";
}
file_put_contents('dump_sections_simple.txt', $out);
echo "OK";
$conn->close();
