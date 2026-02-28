<?php
$ch = curl_init('https://api.github.com/user');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: PHP', 'Authorization: token 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']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
file_put_contents('gh_user.json', curl_exec($ch));
