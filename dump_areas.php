<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'gestionale_cv');
if ($conn->connect_error) { die("F"); }
$result = $conn->query("SELECT * FROM system_areas");
$out = "ID | NAME | SLUG | ACTIVE\n";
while($row = $result->fetch_assoc()) {
    $out .= "{$row['id']} | {$row['name']} | {$row['slug']} | {$row['is_active']}\n";
}
file_put_contents('dump_areas.txt', $out);
echo "OK";
$conn->close();
