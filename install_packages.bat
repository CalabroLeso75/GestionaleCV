@echo off
echo ===================================================
echo   INSTALLAZIONE MANUALE SPATIE PERMISSION
echo ===================================================
echo.
echo Sto installando il pacchetto spatie/laravel-permission...
echo Attendi qualche minuto (dipende dalla tua connessione).
echo.

cd laravel
"c:\xampp\php\php.exe" ..\composer.phar require spatie/laravel-permission

echo.
echo ===================================================
if %errorlevel% neq 0 (
    echo [ERRORE] Qualcosa e' andato storto. Controlla l'output sopra.
) else (
    echo [ACCESSO] Installazione eompletata con successo!
    echo Ora puoi tornare al browser e rieseguire run_seeders.php
)
echo ===================================================
pause
