@echo off
echo ========================================
echo   Menjalankan Server UD Medan
echo ========================================
echo.

echo [1/2] Menjalankan FastAPI di port 8001...
start "FastAPI Server" cmd /k "cd /d c:\Users\tiffany\programkode && venv\Scripts\uvicorn.exe main:app --host 127.0.0.1 --port 8001 --reload"

timeout /t 2 /nobreak >nul

echo [2/2] Menjalankan Laravel di port 8000...
start "Laravel Server" cmd /k "cd /d c:\Users\tiffany\programkode && php artisan serve --port=8000"

echo.
echo ========================================
echo   Kedua server sudah berjalan!
echo   Laravel  : http://127.0.0.1:8000
echo   FastAPI  : http://127.0.0.1:8001
echo ========================================
pause
