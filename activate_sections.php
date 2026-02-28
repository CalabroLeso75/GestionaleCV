<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'gestionale_cv');
if ($conn->connect_error) { die("F"); }
$conn->query("UPDATE dashboard_sections SET is_active = 1");
echo "OK: " . $conn->affected_rows;
$conn->close();
