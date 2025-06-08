REM =============================================================================
REM 3. stop-all.bat - Detener todos los servicios
REM =============================================================================
@echo off
chcp 65001 > nul
echo 🛑 Deteniendo todos los microservicios...
echo.

echo 📦 Deteniendo servicios en puertos 8000, 8001, 8002...

REM Matar procesos por puerto
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000') do (
    echo Deteniendo proceso en puerto 8000 (PID: %%a)
    taskkill /f /pid %%a > nul 2>&1
)

for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8001') do (
    echo Deteniendo proceso en puerto 8001 (PID: %%a)
    taskkill /f /pid %%a > nul 2>&1
)

for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8002') do (
    echo Deteniendo proceso en puerto 8002 (PID: %%a)
    taskkill /f /pid %%a > nul 2>&1
)

echo.
echo ✅ Todos los servicios han sido detenidos
echo.
pause