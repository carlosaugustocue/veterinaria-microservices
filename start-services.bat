@echo off
chcp 65001 > nul
echo 🚀 Iniciando todos los microservicios de la veterinaria...

REM Crear directorio de logs
if not exist "logs" mkdir logs

REM Limpiar procesos existentes
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000') do taskkill /f /pid %%a > nul 2>&1
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8001') do taskkill /f /pid %%a > nul 2>&1
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8002') do taskkill /f /pid %%a > nul 2>&1

REM Iniciar servicios
echo 📦 Iniciando auth-service en puerto 8001...
cd auth-service
start /b /min cmd /c "php -S localhost:8001 -t public > ../logs/auth-service.log 2>&1"
cd ..
timeout /t 3 > nul

echo 📦 Iniciando pets-service en puerto 8002...
cd pets-service
start /b /min cmd /c "php -S localhost:8002 -t public > ../logs/pets-service.log 2>&1"
cd ..
timeout /t 3 > nul

echo 📦 Iniciando api-gateway en puerto 8000...
cd api-gateway
start /b /min cmd /c "php -S localhost:8000 -t public > ../logs/api-gateway.log 2>&1"
cd ..
timeout /t 3 > nul

echo ✅ Todos los servicios iniciados!
pause