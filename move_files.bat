@echo off
echo Starting move operation > move_log.txt

if not exist laravel (
    echo Creating laravel directory... >> move_log.txt
    mkdir laravel >> move_log.txt 2>&1
)

echo Moving artisan... >> move_log.txt
move /Y artisan laravel\artisan >> move_log.txt 2>&1

echo Moving app folder... >> move_log.txt
robocopy app laravel\app /E /MOVE /IS /IT /NFL /NDL /NJH /NJS >> move_log.txt 2>&1

echo Operation completed. >> move_log.txt
