$token = "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"
$headers = @{
    "Authorization" = "token $token"
    "Accept"        = "application/vnd.github.v3+json"
}
$body = @{
    name = "GestionaleCV"
    private = $true
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri "https://api.github.com/user/repos" -Method Post -Headers $headers -Body $body
$response | ConvertTo-Json
