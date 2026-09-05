@echo off
title WhatsApp Gateway - PT Besmindo Materi Sewatama
color 0b
echo ========================================================
echo   WHATSAPP GATEWAY MANDIRI (PORT 3000)
echo   PT. BESMINDO MATERI SEWATAMA
echo ========================================================
echo.
cd /d "%~dp0whatsapp-gateway"

if not exist node_modules (
    echo [INFO] Mengunduh dependensi Node.js...
    call npm install
)

echo [INFO] Menjalankan server.js...
echo [INFO] Jangan tutup jendela ini selama reminder digunakan!
echo.
node server.js
if %errorlevel% neq 0 (
    echo.
    echo [ERROR] Server berhenti dengan error.
)
pause
