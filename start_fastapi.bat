@echo off
title FastAPI Server UD Medan
echo ========================================
echo   Menjalankan Server FastAPI UD Medan
echo ========================================
cd /d %~dp0
echo Mengaktifkan uvicorn di http://127.0.0.1:8001 ...
venv\Scripts\uvicorn.exe main:app --host 127.0.0.1 --port 8001 --reload
pause
