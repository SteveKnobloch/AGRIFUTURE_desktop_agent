#!/usr/bin/env bash

# It is free software; you can redistribute it and/or modify it under
# the terms of the GNU General Public License, either version 2
# of the License, or any later version.
#
# For the full copyright and license information, please read the
# LICENSE.txt file that was distributed with this source code.

ADA_VERSION=1.0.0

if [ "$BASH" = "" ]; then echo "Error: you are not running this script within the bash."; exit 1; fi
if [ ! -x "$(command -v docker)" ]; then echo "Error: docker is not available. Please install Docker according to these instructions: https://docs.docker.com/engine/install/ubuntu/"; exit 1; fi
if [ ! -x "$(command -v netstat)" ]; then echo "Error: netstat is not available. Please install netstat with the command 'sudo apt-get install net-tools'"; exit 1; fi

ADA_HOST_DIRECTORY="$(pwd)"
ADA_CONTAINER="code.tritum.de:5555/senckenberg/agrifuture_desktop_agent:latest"
#ADA_CONTAINER=senckenberg/agrifuture_desktop_agent:latest
ADA_CONTAINER_ID=""

function shutdown() {
    rm -f "${ADA_CMD_FILE}"

    if [ ! -z ${ADA_CONTAINER_ID+x} ]; then
        docker kill $ADA_CONTAINER_ID &>/dev/null
    fi

    unset ADA_VERSION
    unset ADA_HOST_DIRECTORY
    unset ADA_CONTAINER
    unset ADA_CONTAINER_ID
    unset ADA_DATA_DIR
    unset ADA_CMD_FILE

    clear
}

function setup() {
    ADA_DATA_DIR="${XDG_DATA_HOME:=${HOME}/.local/share}/agrifuture"
    ADA_CMD_FILE="${ADA_DATA_DIR}/cmd"

    mkdir -p "${ADA_DATA_DIR}"
    rm -f "${ADA_CMD_FILE}"

    echo "Check for updates..."
    docker pull $ADA_CONTAINER

    trap 'shutdown' EXIT
}

is_port_used()
{
    netstat -t4uln | grep LISTEN | awk '{print $4}' | cut -d':' -f2 | grep -e "^$1\$" &>/dev/null
}

function main() {
    if [[ "$1" == "-v" ]]; then
        printf 'RAPiD Pipeline Desktop Agent - launcher\n'
        printf 'Version %s\n' "$ADA_VERSION"
        printf '\n'
        printf 'It is free software; you can redistribute it and/or modify it under\n'
        printf 'the terms of the GNU General Public License, either version 2\n'
        printf 'of the License, or any later version.\n'
        printf '\n'
        printf 'For the full copyright and license information, please read the\n'
        printf 'LICENSE.txt file that was distributed with this source code.\n'
        exit
    fi

    local ADA_RAW_CMD
    local ADA_CMD
    local ADA_HOST_DIRECTORIES
    local ADA_SKIP_LANGUAGE_SETUP
    local ADA_RUNNING_CONTAINERS
    local ADA_PORT=8041

    ADA_SKIP_LANGUAGE_SETUP=0

    for ((;;)); {
        ADA_HOST_DIRECTORIES=$(ls -d1 "$ADA_HOST_DIRECTORY"/.*/ "$ADA_HOST_DIRECTORY"/*/ 2>/dev/null)
        #docker run --rm -e ADA_RUN_SETUP=1 -e ADA_SKIP_LANGUAGE_SETUP=$ADA_SKIP_LANGUAGE_SETUP -v "${ADA_DATA_DIR}":/home/ada/.local/share/ada -v $(pwd)/src/docker/build_context/usr/local/bin/ada-setup:/usr/local/bin/ada-setup -it $ADA_CONTAINER $ADA_HOST_DIRECTORIES
        docker run --rm -it -e ADA_RUN_SETUP=1 -e ADA_SKIP_LANGUAGE_SETUP=$ADA_SKIP_LANGUAGE_SETUP -v "${ADA_DATA_DIR}":/home/ada/.local/share/ada $ADA_CONTAINER $ADA_HOST_DIRECTORIES

        if [ ! -f "${ADA_CMD_FILE}" ]; then
            exit 0
        fi

        ADA_RAW_CMD=$(cat "$ADA_CMD_FILE" | tr -d '\n')

        if [  -z "${ADA_RAW_CMD}" ]; then
            echo "Error! No command. (code: 1661092995)"
            exit 1
        fi
        ADA_CMD=($ADA_RAW_CMD)

        case "${ADA_CMD[0]}" in
            "chdir")
                if [ ! -d "${ADA_CMD[1]}" ]; then
                    echo "Error! The folder '${ADA_CMD[1]}' does not exists. (code: 1661092998)"
                else
                    ADA_HOST_DIRECTORY="$( cd "$( realpath "${ADA_CMD[1]}" )" && pwd )"
                fi
            ;;
            "usedir")
                if [ ! -d "${ADA_CMD[1]}" ]; then
                    echo "Error! The folder '${ADA_CMD[1]}' does not exists. (code: 1661092999)"
                else
                    ADA_HOST_DIRECTORY="$( cd "$( realpath "${ADA_CMD[1]}" )" && pwd )"
                fi
                break
            ;;
            *)
                echo "Error! Unsupported command '${ADA_CMD[0]}'. (code: 1661092997)"
                exit 1
            ;;
        esac

        ADA_SKIP_LANGUAGE_SETUP=1

        # Exit if there is no longer a terminal attached.
        [[ -t 1 ]] || exit 1
    }

    rm -f "${ADA_CMD_FILE}"
    clear

    while is_port_used $ADA_PORT
    do
      let ADA_PORT=ADA_PORT+1
    done

    docker run --rm -e ADA_TRANSLATE=1 -v "${ADA_DATA_DIR}":/home/ada/.local/share/ada $ADA_CONTAINER "app.launch" "ada-setup"
    ADA_RUNNING_CONTAINERS=$(docker ps --filter "label=de.senckenberg.agrifuture=agrifuture_desktop_agent1" | tail -n +2 | wc -l)
    ADA_CONTAINER_ID=$(docker run --rm -d --label de.senckenberg.agrifuture=agrifuture_desktop_agent -e ADA_RUN_AGENT=1 -e ADA_RUNNING_CONTAINERS=$ADA_RUNNING_CONTAINERS -e ADA_HOST_DIRECTORY=$ADA_HOST_DIRECTORY -v "${ADA_DATA_DIR}":/home/ada/.local/share/ada -v "$ADA_HOST_DIRECTORY":/data:ro -p "127.0.0.1:$ADA_PORT:80" $ADA_CONTAINER)
    sleep 5
    docker run --rm -e ADA_TRANSLATE=1 -v "${ADA_DATA_DIR}":/home/ada/.local/share/ada $ADA_CONTAINER "app.launch_information" "ada-setup" "http://127.0.0.1:$ADA_PORT"
    xdg-open "http://127.0.0.1:$ADA_PORT" &>/dev/null

    for ((;;)); {
        nop
    }
}

setup
main "$@"
shutdown
