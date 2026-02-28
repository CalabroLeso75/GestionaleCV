@echo off
set GIT_TERMINAL_PROMPT=0
git config --local credential.helper ""
git remote remove origin
git remote add origin https://oauth2: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@github.com/CalabroLeso75/GestionaleCV.git
git branch -M main
git push -u origin main > git_push_log.txt 2>&1
echo DONE >> git_push_log.txt
