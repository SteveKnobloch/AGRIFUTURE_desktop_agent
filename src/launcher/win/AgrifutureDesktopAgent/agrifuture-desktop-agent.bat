@echo off

wsl -d Ubuntu bash --version >nul 2>&1 && (
     echo.
) || (
    echo.Error
    echo.

    echo.[DEBUG] wsl status:
    echo.
    wsl --status

    echo.
    echo.[DEBUG] bash:
    echo.
    wsl -d Ubuntu bash --version
    timeout /T 10
    exit 1
)

wsl -d Ubuntu docker --version >nul 2>&1 && (
     echo.
) || (
    echo.Error
    echo.
    echo.[DEBUG] docker:
    echo.
    wsl -d Ubuntu docker --version
    timeout /T 10
    exit 1
)

wsl -u agrifutureAutoInstall -d Ubuntu bash -c './agrifuture-desktop-agent.sh'
