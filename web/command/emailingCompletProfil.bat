@echo off
cls
start schtasks /create /sc minute /mo 20 /tn "Hub3e-emailing" /tr "php ..\..\bin\console hub3e:emailing:completeProfil"
::set /P id=Do you want to launch the schedule?(y/n) :
::if "%id%" == "y"(start schtasks /create /sc minute /mo 20 /tn "Hub3e-emailing" /tr "php ..\..\bin\console hub3e:emailing:completeProfil")else (@echo aurevoir)
::pause