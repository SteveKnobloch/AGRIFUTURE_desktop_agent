@echo off

wsl -u root -d Ubuntu bash -c "apt-get update"
wsl -u root -d Ubuntu bash -c "apt-get install -y net-tools wslu"
wsl -u root -d Ubuntu bash -c "useradd -m -s /bin/bash agrifutureAutoInstall || echo 'user agrifutureAutoInstall exists. Skip'"
wsl -u root -d Ubuntu bash -c "groupadd -f docker"
wsl -u root -d Ubuntu bash -c "usermod -aG docker agrifutureAutoInstall"
