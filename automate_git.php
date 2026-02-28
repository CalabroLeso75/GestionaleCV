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
$data = json_decode($response, true);

if (isset($data['clone_url'])) {
    $clone_url = str_replace('https://', 'https://' . $token . '@', $data['clone_url']);
    file_put_contents('gh_result.txt', "Repo created: " . $clone_url . "\n");
    $out1 = shell_exec("git remote add origin " . escapeshellarg($clone_url) . " 2>&1");
    $out2 = shell_exec("git branch -M main 2>&1");
    $out3 = shell_exec("git push -u origin main 2>&1");
    file_put_contents('gh_result.txt', "Reset origin: $out1 \n Branch: $out2 \n Push: $out3", FILE_APPEND);
} else {
    file_put_contents('gh_result.txt', "Failed to create repo: " . print_r($data, true));
}
