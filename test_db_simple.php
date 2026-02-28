<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'gestionale_cv');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully\n";

$sql = "SHOW TABLES";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_row($result)) {
    echo $row[0] . "\n";
}
mysqli_close($conn);
