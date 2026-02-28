<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'gestionale_cv');
if ($conn->connect_error) die("Conn failed");
$res = $conn->query("SELECT * FROM system_areas");
$out = "ID | SLUG | NAME | ACTIVE\n";
while($row = $res->fetch_assoc()) {
    $out .= "{$row['id']} | {$row['slug']} | {$row['name']} | {$row['is_active']}\n";
}
file_put_contents('db_dump_areas.txt', $out);
echo "Dumped to db_dump_areas.txt";
$conn->close();
