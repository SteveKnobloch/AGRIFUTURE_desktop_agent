#!/bin/bash
set -eo pipefail

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

rm -rf ../../../.build/macos/app/
mkdir -p ../../../.build/macos/app/
rsync -rav ./ ../../../.build/macos/app/
cp ../agrifuture-desktop-agent.sh ../../../.build/macos/app/AgrifutureDesktopAgent/Contents/MacOS/agrifuture-desktop-agent.sh

cd ../../../.build/macos/app/
mv ./AgrifutureDesktopAgent ./AgrifutureDesktopAgent.app

create-dmg --overwrite ./AgrifutureDesktopAgent.app ../

unset THIS_SCRIPT_REAL_PATH
