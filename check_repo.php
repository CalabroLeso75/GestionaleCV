<?php
$token = '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';
$ch = curl_init('https://api.github.com/repos/CalabroLeso75/GestionaleCV/commits');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: PHP Script',
    'Authorization: token ' . $token
]);
$res = curl_exec($ch);
file_put_contents('gh_commits.json', $res);
