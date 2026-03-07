<?php
function getSMTPHost($email) {
    $dominio = substr(strrchr($email, "@"), 1); // es: calabriaverde.eu
    $db = new SQLite3('../data/email_providers.db');
    $stmt = $db->prepare("SELECT smtp_host FROM provider_email WHERE dominio = :dominio");
    $stmt->bindValue(':dominio', $dominio, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    return $row ? $row['smtp_host'] : 'smtps.aruba.it'; // default Aruba
}

function getSMTPPort($email) {
    $dominio = substr(strrchr($email, "@"), 1);
    $db = new SQLite3('../data/email_providers.db');
    $stmt = $db->prepare("SELECT smtp_porta FROM provider_email WHERE dominio = :dominio");
    $stmt->bindValue(':dominio', $dominio, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    return $row ? intval($row['smtp_porta']) : 465; // default porta SSL
}
?>
