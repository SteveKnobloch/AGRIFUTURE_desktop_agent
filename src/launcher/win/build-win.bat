@echo off

REM @version 0.0.1
REM Install https://nsis.sourceforge.io/Download
REM choco install nsis

set ADA_VERSION=%1

rd /s /q ..\..\..\.build\launcher\win\
md ..\..\..\.build\launcher\win\

xcopy /s /y /q . ..\..\..\.build\launcher\win\
copy ..\agrifuture-desktop-agent.sh ..\..\..\.build\launcher\win\AgrifutureDesktopAgent\agrifuture-desktop-agent.sh

cd ..\..\..\.build\launcher\win\

powershell.exe -command "& {((Get-Content .\AgrifutureDesktopAgent\agrifuture-desktop-agent.sh).replace('{{ ADA_VERSION }}', '%ADA_VERSION%') -join \"`n\") + \"`n\" | Set-Content -NoNewline .\AgrifutureDesktopAgent\agrifuture-desktop-agent.sh}"

"%ProgramFiles(x86)%\NSIS\makensis.exe" .\AgrifutureDesktopAgent.nsi
"%ProgramFiles(x86)%\NSIS\makensis.exe" .\AgrifutureDesktopAgent-Install.nsi
