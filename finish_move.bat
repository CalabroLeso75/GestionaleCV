@echo off
echo Finish Moving Files > move_log_2.txt

REM Crea cartella laravel se non esiste (dovrebbe esserci)
if not exist laravel mkdir laravel

REM Sposta file singoli
move /Y .editorconfig laravel\.editorconfig >> move_log_2.txt 2>&1
move /Y .env laravel\.env >> move_log_2.txt 2>&1
move /Y .env.example laravel\.env.example >> move_log_2.txt 2>&1
move /Y .gitattributes laravel\.gitattributes >> move_log_2.txt 2>&1
move /Y .gitignore laravel\.gitignore >> move_log_2.txt 2>&1
move /Y composer.json laravel\composer.json >> move_log_2.txt 2>&1
move /Y composer.lock laravel\composer.lock >> move_log_2.txt 2>&1
move /Y package.json laravel\package.json >> move_log_2.txt 2>&1
move /Y phpunit.xml laravel\phpunit.xml >> move_log_2.txt 2>&1
move /Y README.md laravel\README.md >> move_log_2.txt 2>&1
move /Y vite.config.js laravel\vite.config.js >> move_log_2.txt 2>&1

REM Sposta cartelle usando robocopy (più robusto)
robocopy bootstrap laravel\bootstrap /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log_2.txt 2>&1
robocopy config laravel\config /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log_2.txt 2>&1
robocopy database laravel\database /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log_2.txt 2>&1
robocopy docs laravel\docs /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log_2.txt 2>&1
robocopy resources laravel\resources /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log_2.txt 2>&1
robocopy routes laravel\routes /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log_2.txt 2>&1
robocopy storage laravel\storage /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log_2.txt 2>&1
robocopy tests laravel\tests /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log_2.txt 2>&1
robocopy vendor laravel\vendor /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log_2.txt 2>&1

REM Sposta contenuto public in root
robocopy public . /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log_2.txt 2>&1
rmdir public >> move_log_2.txt 2>&1

echo Operation completed. >> move_log_2.txt
