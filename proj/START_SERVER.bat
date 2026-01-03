@echo off
chcp 65001 >nul
echo ========================================
echo Starting SmartClinic Chatbot Server
echo ========================================
echo.

cd /d "%~dp0"

echo Checking Python installation...
python --version
if errorlevel 1 (
    echo ERROR: Python is not installed or not in PATH!
    echo Please install Python 3.8 or higher.
    pause
    exit /b 1
)

echo.
echo Checking if requirements are installed...
python -c "import flask" 2>nul
if errorlevel 1 (
    echo Installing required packages...
    pip install -r requirements.txt
    if errorlevel 1 (
        echo ERROR: Failed to install requirements!
        pause
        exit /b 1
    )
)

echo.
echo ========================================
echo Starting Flask Server on http://localhost:5000
echo ========================================
echo Press Ctrl+C to stop the server
echo ========================================
echo.

python controller.py

pause











