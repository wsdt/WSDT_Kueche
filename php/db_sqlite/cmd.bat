@echo off
sqlite3 
rem attach "kueche.db" as kueche
rem .databases

:new
set /p command=%cd%: 
%command%
goto new