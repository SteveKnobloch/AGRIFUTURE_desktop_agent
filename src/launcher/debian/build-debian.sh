#!/bin/bash
set -eo pipefail

# ***********************************************
# @version 0.0.1
# ***********************************************

# ******************
# check requirements
if [ "$BASH" = "" ]; then echo "Error: you are not running this script within the bash."; exit 1; fi
# sudo apt-get install debhelper fakeroot dpkg-dev
if [ ! -x "$(command -v dpkg-buildpackage)" ]; then echo "Error: dpkg-buildpackage is not installed / executable."; exit 1; fi
if [ ! -x "$(command -v dh)" ]; then echo "Error: dh is not installed / executable."; exit 1; fi
THIS_SCRIPT_REAL_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd "$THIS_SCRIPT_REAL_PATH"

cp ../agrifuture-desktop-agent.sh ./AgrifutureDesktopAgent/agrifuture-desktop-agent
cd ./AgrifutureDesktopAgent/

dpkg-buildpackage -uc -us

unset THIS_SCRIPT_REAL_PATH
