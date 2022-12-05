[![pipeline status](https://code.tritum.de/senckenberg/agrifuture_desktop_agent/badges/main/pipeline.svg)](https://code.tritum.de/senckenberg/agrifuture_desktop_agent/-/commits/main) 

Agrifuture Desktop Agent

# Development

Simulation eines gestarteten Agrifuture Desktop Agent via docker-compose.


```
docker-compose up -d
docker exec -it agrifuture_desktop_agent_app_1 composer install
```

Die vom Launchscript ermittelten Volumes werden im Testsetup auf feste Werte gestellt:

* ./volumes/fakehome: Ist das Hostverzeichnis in welches der Agrifuture Desktop Agent seine Datenbankdaten ablegt. Normalerweise `~/.local/share/agrifuture`
* ./volumes/data: Ist das Hostverzeichnis, welches Anwender:innen im Launchscript ausgewählt haben

Die Applikation liegt unter `./src/docker/buildfiles/opt/ada/app`

---

**WIP**

# Agrifuture Desktop Agent Image build

```
DSL_IMAGE_NAMESPACE=code.tritum.de:5555/senckenberg/agrifuture_desktop_agent DSL_IMAGE_TAG=latest ./src/docker/build.sh
DSL_IMAGE_NAMESPACE=code.tritum.de:5555/senckenberg/agrifuture_desktop_agent DSL_IMAGE_TAG=development DSL_TARGET_STAGE=development ./src/docker/build.sh
```

# install

## Linux

# run

## Windows

### System requirements

(partly from https://docs.docker.com/desktop/install/windows-install/#wsl-2-backend)

* install docker

* Windows 11 64-bit: Home or Pro version 21H2 or higher, or Enterprise or Education version 21H2 or higher.
* Windows 10 64-bit: Home or Pro 21H1 (build 19043) or higher, or Enterprise or Education 20H2 (build 19042) or higher.
* 64-bit processor with Second Level Address Translation (SLAT)
* 4GB system RAM
* BIOS-level hardware virtualization support must be enabled in the BIOS settings.

todo: note about https://docs.docker.com/network/proxy/

### Steps

* Install Docker Desktop on Windows - https://docs.docker.com/desktop/install/windows-install/
* Enable the WSL 2 backend on install: https://serversideup.net/open-source/spin/windows/docker-desktop-configuration.png
* Install the Linux kernel update package: https://serversideup.net/open-source/spin/windows/wsl-incomplete.png / https://learn.microsoft.com/de-de/windows/wsl/install-manual#step-4---download-the-linux-kernel-update-package
* Install Ubuntu: https://apps.microsoft.com/store/detail/ubuntu/9PDXGNCFSCZV?hl=de-de&gl=de
* Migrate Ubuntu WSL 1 to WSL 2: Open cmd -> wsl --set-version Ubuntu 2 - https://learn.microsoft.com/de-de/windows/wsl/install#upgrade-version-from-wsl-1-to-wsl-2
* Install Ubuntu deps: Open cmd -> wsl -d Ubuntu sudo apt-get update && sudo apt-get install net-tools wslu
* Enable Docker Desktop Ubuntu Integration: https://code.visualstudio.com/assets/blogs/2020/03/02/docker-resources-wsl-integration.png

## Linux

### System requirements

(partly from https://docs.docker.com/desktop/install/linux-install/#system-requirements)

* Ununtu 20.04
* 64-bit processor with Second Level Address Translation (SLAT)
* 4GB system RAM

### Steps

* goto some directory using the terminal (or right click a folder -> open in Terminal)
* 

todo: note about https://docs.docker.com/network/proxy/
