@echo off
echo Starting Hospital Management System Services...

REM Check if MongoDB is running
echo Checking MongoDB status...
mongod --version >nul 2>&1
if %errorlevel% neq 0 (
    echo MongoDB is not installed or not in PATH
    echo Please install MongoDB and add it to PATH
    pause
    exit /b 1
)

REM Start MongoDB (if not already running)
echo Starting MongoDB...
start "MongoDB" mongod

REM Wait for MongoDB to start
timeout /t 5 /nobreak

REM Start Patient Service
echo Starting Patient Service...
cd patient-service
start "Patient Service" npm start
cd ..

REM Start Appointment Service
echo Starting Appointment Service...
cd appointment-service
start "Appointment Service" npm start
cd ..

REM Start User Service
echo Starting User Service...
cd user-service-node
start "User Service" npm start
cd ..

echo All services started!
echo.
echo Patient Service: http://localhost:3000
echo Appointment Service: http://localhost:3001
echo User Service: http://localhost:5000
echo.
echo Press any key to stop all services...
pause

REM Kill all Node.js processes and MongoDB
taskkill /F /IM node.exe >nul 2>&1
taskkill /F /IM mongod.exe >nul 2>&1

echo All services stopped.
pause 