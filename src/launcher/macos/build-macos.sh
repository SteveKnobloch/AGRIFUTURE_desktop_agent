#!/bin/bash

# ***********************************************
# @version 0.0.1
# ***********************************************

# ******************
# check requirements
if [ "$BASH" = "" ]; then echo "Error: you are not running this script within the bash."; exit 1; fi
# npm install --global create-dmg
if [ ! -x "$(command -v create-dmg)" ]; then echo "Error: create-dmg is not installed / executable."; exit 1; fi
THIS_SCRIPT_REAL_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd "$THIS_SCRIPT_REAL_PATH"

rm -rf ./AgrifutureDesktopAgent.app
cp -r ./AgrifutureDesktopAgent ./AgrifutureDesktopAgent.app
cp ../agrifuture-desktop-agent.sh ./AgrifutureDesktopAgent.app/Contents/MacOS/agrifuture-desktop-agent.sh

create-dmg --overwrite ./AgrifutureDesktopAgent.app ./
mv "Agrifuture Desktop Agent 1.0.dmg" ./AgrifutureDesktopAgent.dmg

unset THIS_SCRIPT_REAL_PATH