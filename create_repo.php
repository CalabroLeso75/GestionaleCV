<?php
$token = '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';
$ch = curl_init('https://api.github.com/user/repos');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: PHP Script',
    'Authorization: token ' . $token,
    'Accept: application/vnd.github.v3+json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'name' => 'GestionaleCV',
    'private' => true
]));

$response = curl_exec($ch);
curl_close($ch);
echo $response;
?>
