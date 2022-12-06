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

ADA_VERSION=$1

cd "$THIS_SCRIPT_REAL_PATH"
rm -rf ../../../.build/launcher/macos/
mkdir -p ../../../.build/launcher/macos/

cp -r ./ ../../../.build/launcher/macos/
cp ../agrifuture-desktop-agent.sh ../../../.build/launcher/macos/AgrifutureDesktopAgent/Contents/MacOS/agrifuture-desktop-agent.sh

cd ../../../.build/launcher/macos/

sed -i "s/{{ ADA_VERSION }}/$ADA_VERSION/g" ./AgrifutureDesktopAgent/Contents/MacOS/agrifuture-desktop-agent.sh
sed -i "s/{{ ADA_VERSION }}/$ADA_VERSION/g" ./AgrifutureDesktopAgent/Contents/Info.plist

mv ./AgrifutureDesktopAgent ./AgrifutureDesktopAgent.app

create-dmg --overwrite ./AgrifutureDesktopAgent.app ./
mv "Agrifuture Desktop Agent ${ADA_VERSION}.dmg" ./AgrifutureDesktopAgent.dmg

unset THIS_SCRIPT_REAL_PATH
