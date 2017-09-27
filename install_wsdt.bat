@echo off
cls&echo.&color a
echo **************** WSDT SETUP *********************
echo.
echo WSDT wird installiert. Bitte warten..
echo.
rem Seid Version 4 ohne Xampp
rem robocopy M:\Kuechensekr\_Speiseplaene\install_wsdt\xampp\ C:\xampp\ /E
xcopy /Y \\tilak.cc\share\daten01\Kuechensekr\_Speiseplaene\install_wsdt\WSDT.lnk H:\Desktop\
echo.
echo ################################################
echo ################################################
echo ########## INSTALLATION ABGESCHLOSSEN ##########
echo ################################################
echo ################################################

start H:\Desktop\WSDT.lnk

timeout 5
exit