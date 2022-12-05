Name "AgrifutureDesktopAgent"
OutFile "AgrifutureDesktopAgent\AgrifutureDesktopAgent.exe"
RequestExecutionLevel user

Icon "AgrifutureDesktopAgent\AppIcon.ico"

Section
  SetSilent silent
  SetAutoClose true

  ExpandEnvStrings $0 %COMSPEC%
  Exec '"$0" /C ".\agrifuture-desktop-agent.bat"'
SectionEnd
