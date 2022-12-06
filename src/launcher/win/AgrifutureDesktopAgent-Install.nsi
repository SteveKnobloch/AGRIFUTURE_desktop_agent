!define NAME "Agrifuture Desktop Agent"
!define REGPATH_UNINSTSUBKEY "Software\Microsoft\Windows\CurrentVersion\Uninstall\AgrifutureDesktopAgent"

Name "${NAME}"
OutFile "AgrifutureDesktopAgent.exe"
Unicode True
RequestExecutionLevel User
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


Section "Program files (Required)"
  SectionIn Ro

  SetOutPath $INSTDIR

  File "AgrifutureDesktopAgent\AgrifutureDesktopAgent.exe"
  File "AgrifutureDesktopAgent\agrifuture-desktop-agent.bat"
  File "AgrifutureDesktopAgent\agrifuture-desktop-agent.sh"
  WriteUninstaller "$INSTDIR\Uninstall.exe"

  WriteRegStr HKCU "${REGPATH_UNINSTSUBKEY}" "DisplayName" "${NAME}"
  WriteRegStr HKCU "${REGPATH_UNINSTSUBKEY}" "DisplayIcon" "$INSTDIR\AgrifutureDesktopAgent.exe,0"
  WriteRegStr HKCU "${REGPATH_UNINSTSUBKEY}" "UninstallString" '"$INSTDIR\Uninstall.exe"'
  WriteRegStr HKCU "${REGPATH_UNINSTSUBKEY}" "QuietUninstallString" '"$INSTDIR\Uninstall.exe" /S'

  WriteRegDWORD HKCU "${REGPATH_UNINSTSUBKEY}" "NoModify" 1
  WriteRegDWORD HKCU "${REGPATH_UNINSTSUBKEY}" "NoRepair" 1
SectionEnd

Section "Start Menu shortcut"
  CreateDirectory "$SMPROGRAMS\AgrifutureDesktopAgent"
  CreateShortcut "$SMPROGRAMS\AgrifutureDesktopAgent\AgrifutureDesktopAgent.lnk" "$INSTDIR\AgrifutureDesktopAgent.exe"
  CreateShortcut "$SMPROGRAMS\AgrifutureDesktopAgent\Uninstall.lnk" "$INSTDIR\Uninstall.exe"
SectionEnd

Section "Desktop Shortcut"
    CreateShortcut "$DESKTOP\AgrifutureDesktopAgent.lnk" "$INSTDIR\AgrifutureDesktopAgent.exe"
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
  Delete "$INSTDIR\Uninstall.exe"

  RMDir "$INSTDIR"
  DeleteRegKey HKCU "${REGPATH_UNINSTSUBKEY}"

  ${UnpinShortcut} "$SMPrograms\AgrifutureDesktopAgent\AgrifutureDesktopAgent.lnk"
  ${UnpinShortcut} "$SMPrograms\AgrifutureDesktopAgent\Uninstall.lnk"
  Delete "$SMPrograms\AgrifutureDesktopAgent\*.lnk"
  Delete "$DESKTOP\AgrifutureDesktopAgent.lnk"
  RMDir "$SMPrograms\AgrifutureDesktopAgent"
SectionEnd
