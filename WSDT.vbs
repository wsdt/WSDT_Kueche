'Since version 4 no update checker needed
WScript.Quit()
CreateObject("Wscript.Shell").Run "C:\xampp\htdocs\WSDT_Kueche\WSDT.bat", 0, False
CreateObject("Wscript.Shell").Run "C:\xampp\apache_start.bat", 0, False

Wscript.Sleep 500

DIM fso,x
Set fso = CreateObject("Scripting.FileSystemObject")

If (fso.FileExists("C:\xampp\htdocs\WSDT_Kueche\update_wsdt.vbs")) Then
  x=msgbox ("UPDATE fuer WSDT verfuegbar!"+chr(13)+chr(13)+"UPDATE jetzt durchführen?"+chr(13)+chr(13)+"..oder führe das Installationsskript in M:\_Speiseplaene\install_wsdt\install_wsdt.bat manuell neu aus!",vbYesNo+vbInformation, "UPDATE AVAILABLE")
  fso.DeleteFile("C:\xampp\htdocs\WSDT_Kueche\update_wsdt.vbs")
  If x = vbYes Then CreateObject("Wscript.Shell").Run "M:\Kuechensekr\_Speiseplaene\install_wsdt\install_wsdt.bat", 1, False 
End If




