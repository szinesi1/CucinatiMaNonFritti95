@echo off
setlocal
cd /d "%~dp0"

py -3.12 --version >nul 2>&1
if %errorlevel%==0 (
    py -3.12 avvia.py
) else (
    python avvia.py
)

if not %errorlevel%==0 pause
endlocal
