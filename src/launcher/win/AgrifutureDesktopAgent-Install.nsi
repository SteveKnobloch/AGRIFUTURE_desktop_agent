!define NAME "Agrifuture Desktop Agent"
!define REGPATH_UNINSTSUBKEY "Software\Microsoft\Windows\CurrentVersion\Uninstall\AgrifutureDesktopAgent"

Name "${NAME}"
OutFile "AgrifutureDesktopAgent.exe"
Unicode True
RequestExecutionLevel admin
Icon "AgrifutureDesktopAgent\AppIcon.ico"
InstallDir "" ; Don't set a default $INSTDIR so we can detect /D= and InstallDirRegKey
InstallDirRegKey HKCU "${REGPATH_UNINSTSUBKEY}" "UninstallString"

!include LogicLib.nsh
!include WinCore.nsh
!include Integration.nsh

Page Directory
Page InstFiles

Uninstpage UninstConfirm
Uninstpage InstFiles


Function .onInit
  SetShellVarContext Current

  ${If} $INSTDIR == "" ; No /D= nor InstallDirRegKey?
    GetKnownFolderPath $INSTDIR ${FOLDERID_UserProgramFiles} ; This folder only exists on Win7+
    StrCmp $INSTDIR "" 0 +2 
    StrCpy $INSTDIR "$LocalAppData\Programs" ; Fallback directory

    StrCpy $INSTDIR "$INSTDIR\AgrifutureDesktopAgent"
  ${EndIf}
FunctionEnd

Function un.onInit
  SetShellVarContext Current
FunctionEnd

Section
  DetailPrint "Read After Reboot flag..."
  ReadRegStr $R0 HKCU "Software\Microsoft\Windows\CurrentVersion\" "${NAME}_afterreboot"
  StrCmp "$R0" "1" InstallStage2
  StrCmp "$R0" "2" InstallStage3

  DetailPrint "Run main installation:"
  DetailPrint "Copyng files, installing drivers, registring DLLs, etc."

  SetOutPath $INSTDIR

  File "AgrifutureDesktopAgent\AgrifutureDesktopAgent.exe"
  File "AgrifutureDesktopAgent\agrifuture-desktop-agent.bat"
  File "AgrifutureDesktopAgent\agrifuture-desktop-agent.sh"
  File "AgrifutureDesktopAgent\enable-wsl2.bat"
  File "AgrifutureDesktopAgent\install-wsl2.bat"
  File "AgrifutureDesktopAgent\install-docker.bat"
  File "AgrifutureDesktopAgent\configure-ubuntu.bat"
  WriteUninstaller "$INSTDIR\Uninstall.exe"

  WriteRegStr HKCU "${REGPATH_UNINSTSUBKEY}" "DisplayName" "${NAME}"
  WriteRegStr HKCU "${REGPATH_UNINSTSUBKEY}" "DisplayIcon" "$INSTDIR\AgrifutureDesktopAgent.exe,0"
  WriteRegStr HKCU "${REGPATH_UNINSTSUBKEY}" "UninstallString" '"$INSTDIR\Uninstall.exe"'
  WriteRegStr HKCU "${REGPATH_UNINSTSUBKEY}" "QuietUninstallString" '"$INSTDIR\Uninstall.exe" /S'

  WriteRegDWORD HKCU "${REGPATH_UNINSTSUBKEY}" "NoModify" 1
  WriteRegDWORD HKCU "${REGPATH_UNINSTSUBKEY}" "NoRepair" 1

  CreateDirectory "$SMPROGRAMS\AgrifutureDesktopAgent"
  CreateShortcut "$SMPROGRAMS\AgrifutureDesktopAgent\AgrifutureDesktopAgent.lnk" "$INSTDIR\AgrifutureDesktopAgent.exe"
  CreateShortcut "$SMPROGRAMS\AgrifutureDesktopAgent\Uninstall.lnk" "$INSTDIR\Uninstall.exe"

  CreateShortcut "$DESKTOP\AgrifutureDesktopAgent.lnk" "$INSTDIR\AgrifutureDesktopAgent.exe"

  !include x64.nsh
  ${DisableX64FSRedirection}
  ExecWait '"$INSTDIR\enable-wsl2.bat"'
  ${EnableX64FSRedirection}

  DetailPrint "Installer path: $EXEPATH"

  DetailPrint "Write in RunOnce Registry key..."
  WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\RunOnce" "${NAME}" "$EXEPATH"
  DetailPrint "Write After Reboot flag (stage 2) ..."
  WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\" "${NAME}_afterreboot" "1"

  DetailPrint "Reboot request."
  MessageBox MB_YESNO|MB_ICONINFORMATION "Installation will be continue after reboot. Press OK to reboot now." IDYES RebootNow
  DetailPrint "Installation continue if user restart system."
  Goto SecEnd

  RebootNow:
    DetailPrint "Rebooting..."
    Reboot
  InstallStage2:
    DetailPrint "Delete After Reboot flag (stage 2) ..."
    DeleteRegValue HKCU "Software\Microsoft\Windows\CurrentVersion\" "${NAME}_afterreboot"
    DetailPrint "Continue install after reboot..."			

    !include x64.nsh
    ${DisableX64FSRedirection}
    ExecWait '"$INSTDIR\install-wsl2.bat"'
    ExecWait '"$INSTDIR\install-docker.bat"'
    ${EnableX64FSRedirection}

    DetailPrint "Installer path: $EXEPATH"

    DetailPrint "Write in RunOnce Registry key..."
    WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\RunOnce" "${NAME}" "$EXEPATH"
    DetailPrint "Write After Reboot flag (stage 3) ..."
    WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\" "${NAME}_afterreboot" "2"

    DetailPrint "Reboot request."
    MessageBox MB_YESNO|MB_ICONINFORMATION "Installation will be finished after reboot. Press OK to reboot now." IDYES RebootNow
    DetailPrint "Installation finished if user restart system."
    Goto SecEnd

  InstallStage3:
    DetailPrint "Delete After Reboot flag (stage 3) ..."
    DeleteRegValue HKCU "Software\Microsoft\Windows\CurrentVersion\" "${NAME}_afterreboot"
    DetailPrint "Continue install after reboot..."			

    !include x64.nsh
    ${DisableX64FSRedirection}
    ExecWait '"$INSTDIR\configure-ubuntu.bat"'
    ${EnableX64FSRedirection}

    DetailPrint "Installation done."
    Goto SecEnd

  SecEnd:

SectionEnd

!macro DeleteFileOrAskAbort path
  ClearErrors
  Delete "${path}"
  IfErrors 0 +3
    MessageBox MB_ABORTRETRYIGNORE|MB_ICONSTOP 'Unable to delete "${path}"!' IDRETRY -3 IDIGNORE +2
    Abort "Aborted"
!macroend

Section -Uninstall
  !insertmacro DeleteFileOrAskAbort "$INSTDIR\AgrifutureDesktopAgent.exe"

  Delete "$INSTDIR\AgrifutureDesktopAgent.exe"
  Delete "$INSTDIR\agrifuture-desktop-agent.bat"
  Delete "$INSTDIR\agrifuture-desktop-agent.sh"
  Delete "$INSTDIR\enable-wsl2.bat"
  Delete "$INSTDIR\install-wsl2.bat"
  Delete "$INSTDIR\install-docker.bat"
  Delete "$INSTDIR\configure-ubuntu.bat"
  Delete "$INSTDIR\Uninstall.exe"

  RMDir "$INSTDIR"
  DeleteRegKey HKCU "${REGPATH_UNINSTSUBKEY}"

  ${UnpinShortcut} "$SMPrograms\AgrifutureDesktopAgent\AgrifutureDesktopAgent.lnk"
  ${UnpinShortcut} "$SMPrograms\AgrifutureDesktopAgent\Uninstall.lnk"
  Delete "$SMPrograms\AgrifutureDesktopAgent\*.lnk"
  Delete "$DESKTOP\AgrifutureDesktopAgent.lnk"
  RMDir "$SMPrograms\AgrifutureDesktopAgent"
SectionEnd
