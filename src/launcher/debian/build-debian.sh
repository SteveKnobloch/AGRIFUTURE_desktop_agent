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

mv ../agrifuture-desktop-agent_*_all.deb ../AgrifutureDesktopAgent.deb

rm -rf ./debian/.debhelper/
rm -f ./debian/agrifuture-desktop-agent.substvars
rm -rf ./debian/agrifuture-desktop-agent/
rm -rf ./debian/debhelper-build-stamp
rm -rf ./debian/files
rm -f ../agrifuture-desktop-agent_*.dsc
rm -f ../agrifuture-desktop-agent_*.tar.xz
rm -f ../agrifuture-desktop-agent_*_amd64.buildinfo
rm -f ../agrifuture-desktop-agent_*_amd64.changes

unset THIS_SCRIPT_REAL_PATH
