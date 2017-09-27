@echo off
REM Since version 4 no update check needed
exit
color a

REM VERSION EVALUATION
call "C:\xampp\htdocs\WSDT_Kueche\version.bat"
set /a curr_version = %version%

call "M:\Kuechensekr\_Speiseplaene\install_wsdt\xampp\htdocs\WSDT_Kueche\version.bat"

if %version% GTR %curr_version% echo update_available > C:\xampp\htdocs\WSDT_Kueche\update_wsdt.vbs

set chrome_path="C:\xampp\htdocs\WSDT_Kueche\GoogleChromePortable\GoogleChromePortable.exe"
%chrome_path% "localhost\WSDT_Kueche\index.php"
exit
