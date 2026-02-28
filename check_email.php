<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'gestionale_cv');
if ($conn->connect_error) die("F");
$res = $conn->query("SELECT email, name, surname FROM users WHERE surname LIKE '%Cusano%'");
while($row = $res->fetch_assoc()) {
    echo "{$row['email']} | {$row['name']} | {$row['surname']}\n";
}
$conn->close();
