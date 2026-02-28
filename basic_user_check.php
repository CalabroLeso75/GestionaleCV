<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'gestionale_cv');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT id, name, email FROM users WHERE name LIKE '%raffaele%' OR name LIKE '%cusano%'");

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - Name: " . $row["name"]. " - Email: " . $row["email"]. "\n";
    }
} else {
    echo "0 results";
}
$conn->close();
