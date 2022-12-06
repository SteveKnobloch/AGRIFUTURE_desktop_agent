Name "AgrifutureDesktopAgent"
OutFile "AgrifutureDesktopAgent\AgrifutureDesktopAgent.exe"
RequestExecutionLevel user

Icon "AgrifutureDesktopAgent\AppIcon.ico"

Section
  SetSilent silent
  SetAutoClose true

  !include x64.nsh
  ${DisableX64FSRedirection}
  Exec '".\agrifuture-desktop-agent.bat"'
  ${EnableX64FSRedirection}
SectionEnd
