<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "gestionale_cv");

if ($mysqli->connect_errno) {
    echo "SQL_CONN_ERROR: " . $mysqli->connect_error . "\n";
    exit();
}

$check = $mysqli->query("SHOW COLUMNS FROM vehicles LIKE 'ultima_revisione'");
if ($check->num_rows == 0) {
    if ($mysqli->query("ALTER TABLE vehicles ADD ultima_revisione DATE NULL AFTER immatricolazione_anno")) {
        echo "SQL_SUCCESS: Column 'ultima_revisione' added.\n";
    } else {
        echo "SQL_ALTER_ERROR: " . $mysqli->error . "\n";
    }
} else {
    echo "SQL_SKIP: Column 'ultima_revisione' already exists.\n";
}

$mysqli->close();
