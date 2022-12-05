@echo off

REM @version 0.0.1
REM Install https://nsis.sourceforge.io/Download

copy ..\agrifuture-desktop-agent.sh .\AgrifutureDesktopAgent\agrifuture-desktop-agent.sh
"%ProgramFiles(x86)%\NSIS\makensis.exe" .\AgrifutureDesktopAgent.nsi
"%ProgramFiles(x86)%\NSIS\makensis.exe" .\AgrifutureDesktopAgent-Install.nsi
