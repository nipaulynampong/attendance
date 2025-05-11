@echo off
echo Creating Barangay Malinta Application Package...

:: Create directories
mkdir BarangayMalintaApp
mkdir BarangayMalintaApp\www
mkdir BarangayMalintaApp\php
mkdir BarangayMalintaApp\www\Styles
mkdir BarangayMalintaApp\www\qrcodes
mkdir BarangayMalintaApp\www\tcpdf
mkdir BarangayMalintaApp\www\temp

:: Copy PHP Desktop files (user needs to download these)
echo Please download PHP Desktop from https://github.com/cztomczak/phpdesktop
echo and place the following files in this directory:
echo - phpdesktop-chrome.exe
echo - settings.json
echo - php folder contents
echo - www folder contents

:: Copy application files
xcopy /E /I /Y *.php BarangayMalintaApp\www\
xcopy /E /I /Y Styles\* BarangayMalintaApp\www\Styles\
xcopy /E /I /Y qrcodes\* BarangayMalintaApp\www\qrcodes\
xcopy /E /I /Y tcpdf\* BarangayMalintaApp\www\tcpdf\
xcopy /E /I /Y temp\* BarangayMalintaApp\www\temp\

echo.
echo Package created in BarangayMalintaApp folder!
echo Please follow these steps to complete the packaging:
echo 1. Download PHP Desktop from https://github.com/cztomczak/phpdesktop
echo 2. Copy phpdesktop-chrome.exe to BarangayMalintaApp folder and rename it to BarangayMalinta.exe
echo 3. Copy settings.json to BarangayMalintaApp folder
echo 4. Copy contents of php folder to BarangayMalintaApp\php
echo 5. Copy contents of www folder to BarangayMalintaApp\www
echo.
pause 