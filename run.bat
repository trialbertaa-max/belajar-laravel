@echo off
title Hyu PACS & MCU Workstation Launcher
echo ===================================================================
echo     HYU PACS ^& MCU WORKSTATION - SYSTEM LAUNCHER
echo ===================================================================
echo.
echo Setting up PHP 8.3 environment from Laragon...
set PATH=D:\laragon\bin\php\php-8.3.33-Win32-vs16-x64;%PATH%

echo Checking PHP version:
php -v
echo.

echo Launching web browser to Start Screen (http://127.0.0.1:8000/start)...
start http://127.0.0.1:8000/start

echo.
echo Starting Laravel Artisan Server on http://127.0.0.1:8000...
echo (Press Ctrl+C to stop the server)
echo ===================================================================
php artisan serve --host=127.0.0.1 --port=8000
pause

