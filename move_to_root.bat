@echo off
echo ===================================================
echo   SPOSTAMENTO PROGETTO NELLA ROOT
echo ===================================================
echo.
echo Sto spostando tutti i file dalla cartella 'laravel' alla root 'GestionaleCV'...
echo.

REM 1. Backup existing root files that might conflict (optional/overwrite)
REM index.php is the main one. We will overwrite it with a new one or adapt.

REM Move contents of laravel to current directory
xcopy laravel\* . /E /Y /H

REM Remove empty laravel directory
rmdir laravel /S /Q

echo.
echo ===================================================
echo [OK] Spostamento completato.
echo Ora il progetto e' direttamente in c:\xampp\htdocs\GestionaleCV
echo.
echo Aggiorno index.php per puntare ai percorsi corretti...
echo.

REM Create new index.php pointing to public/index.php or bootstrapping app directly?
REM Standard Laravel public/index.php does require __DIR__.'/../storage'...
REM If we are in root, and public is /public...
REM Let's just point the root index.php to public/index.php logic to simulate a serve.

(
echo ^<?php
echo.
echo /**
echo  * Laravel - A PHP Framework For Web Artisans
echo  *
echo  * @package  Laravel
echo  * @author   Taylor Otwell ^<taylor@laravel.com^>
echo  */
echo.
echo $uri = urldecode(
echo     parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
echo );
echo.
echo // This file allows us to emulate Apache's "mod_rewrite" functionality from the
echo // built-in PHP web server. This provides a convenient way to test a Laravel
echo // application without having installed a "real" web server software here.
echo if ($uri !== '/' ^&^& file_exists(__DIR__.'/public'.$uri^)^) {
echo     return false;
echo }
echo.
echo require_once __DIR__.'/public/index.php';
) > index.php

echo [OK] index.php aggiornato.
echo ===================================================
pause
