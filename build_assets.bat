@echo off
echo Running build process... > build_status.txt

REM Try to find npm
set NPM_CMD=npm
if exist "C:\Program Files\nodejs\npm.cmd" (
    set NPM_CMD="C:\Program Files\nodejs\npm.cmd"
    echo Found npm at standard location. >> build_status.txt
) else (
    echo Using default npm command. >> build_status.txt
)

echo Using command: %NPM_CMD% >> build_status.txt

call %NPM_CMD% list bootstrap-italia > npm_list.txt 2>&1
echo NPM List done. >> build_status.txt

call %NPM_CMD% run build > build_output.txt 2>&1
echo Build done. >> build_status.txt

type build_output.txt
echo.
echo ==========================================
if exist "../build/manifest.json" (
    echo SUCCESS: Build completed and manifest found!
    echo Moving build folder...
    move /Y "../build" "../../build"
) else if exist "public/build/manifest.json" (
    echo SUCCESS: Build completed!
    echo Moving public/build to ../build...
    robocopy public/build ../../build /E /MOVE > nul
) else (
   echo WARNING: Manifest not found in expected locations.
   echo Check build_output.txt for details.
)
echo ==========================================
pause
