$url = "https://oauth2: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@github.com/CalabroLeso75/GestionaleCV.git"
git remote remove origin
git remote add origin $url
git branch -M main
git push -u origin main *>&1 | Tee-Object -FilePath "C:\xampp\htdocs\GestionaleCV\ps_log.txt"
