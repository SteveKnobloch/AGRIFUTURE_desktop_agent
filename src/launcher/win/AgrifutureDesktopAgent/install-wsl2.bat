@echo off

curl.exe -L -o "%TEMP%\wsl_update_x64.msi" "https://wslstorestorage.blob.core.windows.net/wslblob/wsl_update_x64.msi"
%TEMP%\wsl_update_x64.msi /quiet
wsl --set-default-version 2
wsl --install -d Ubuntu
