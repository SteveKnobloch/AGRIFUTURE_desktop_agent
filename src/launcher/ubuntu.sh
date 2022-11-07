#!/usr/bin/env bash

# It is free software; you can redistribute it and/or modify it under
# the terms of the GNU General Public License, either version 2
# of the License, or any later version.
#
# For the full copyright and license information, please read the
# LICENSE.txt file that was distributed with this source code.

ADA_VERSION=1.0.0

if grep -qiE '(microsoft|mingw64)' /proc/version; then
    ADA_IS_WIN=1
    if [ -x "$(command -v cygcheck)" ]; then
        ADA_IS_GYGWIN=1;
    else
        ADA_IS_GYGWIN=0;
    fi
else
    ADA_IS_WIN=0
fi

if [ "$BASH" = "" ]; then echo "Error: you are not running this script within the bash."; exit 1; fi
if [ ! -x "$(command -v docker)" ]; then echo "Error: docker is not available. Please install Docker according to these instructions: https://docs.docker.com/engine/install/ubuntu/"; exit 1; fi
if [ ! -x "$(command -v netstat)" ]; then echo -e "Error: netstat is not available. Please install netstat with the command\nsudo apt-get install net-tools"; exit 1; fi
if [ $ADA_IS_WIN -eq 0 ]; then if [ ! -x "$(command -v xdg-open)" ]; then echo -e "Error: xdg-open is not available. Please install xdg-open with the command\nsudo apt-get install xdg-utils"; exit 1; fi fi

ADA_HOST_DIRECTORY="$(pwd)"
ADA_CONTAINER="code.tritum.de:5555/senckenberg/agrifuture_desktop_agent:latest"
#ADA_CONTAINER=senckenberg/agrifuture_desktop_agent:latest
ADA_CONTAINER_ID=""
ADA_DOCKER_COMMAND_PREFIX=""
ADA_DATA_DIR=""
ADA_CMD_FILE=""

function shutdown()
{
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
    unset ADA_IS_WIN
    unset ADA_IS_GYGWIN
    unset ADA_DOCKER_COMMAND_PREFIX

#    clear
}

function setup()
{
    ADA_DATA_DIR="${XDG_DATA_HOME:=${HOME}/.local/share}/agrifuture"
    ADA_CMD_FILE="${ADA_DATA_DIR}/cmd"

    if [ $ADA_IS_GYGWIN -eq 1 ] && [ -x "$(command -v winpty)" ]; then
        ADA_DOCKER_COMMAND_PREFIX="winpty";
    fi

    mkdir -p "${ADA_DATA_DIR}"
    rm -f "${ADA_CMD_FILE}"

    if [ $ADA_IS_GYGWIN -eq 1 ]; then
        ADA_DATA_DIR="/$ADA_DATA_DIR";
    fi

    echo "Check for updates..."
    docker pull $ADA_CONTAINER

    trap 'shutdown' EXIT
}

function is_port_free()
{
    local ADA_IS_USED=0

    if [ $ADA_IS_GYGWIN -eq 1 ]; then
        ADA_IS_USED=$(netstat -an -p TCP | awk '{print $3}' | cut -d':' -f2 | grep -e "^$1\$" | wc -l)
    else
        ADA_IS_USED=$(netstat -t4uln | grep LISTEN | awk '{print $4}' | cut -d':' -f2 | grep -e "^$1\$" | wc -l)
    fi

    if [ $ADA_IS_USED -eq 0 ]; then
        ADA_IS_USED=$(docker ps -aq --filter "publish=$1" | wc -l)
    fi

    return $ADA_IS_USED
}

function main()
{
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
        #$ADA_DOCKER_COMMAND_PREFIX docker run --rm -e ADA_RUN_SETUP=1 -e ADA_SKIP_LANGUAGE_SETUP=$ADA_SKIP_LANGUAGE_SETUP -v "$ADA_DATA_DIR":/home/ada/.local/share/ada -v $(pwd)/src/docker/buildfiles/usr/local/bin/ada-setup:/usr/local/bin/ada-setup -it $ADA_CONTAINER $ADA_HOST_DIRECTORIES
        $ADA_DOCKER_COMMAND_PREFIX docker run --rm -it -e ADA_RUN_SETUP=1 -e ADA_SKIP_LANGUAGE_SETUP=$ADA_SKIP_LANGUAGE_SETUP -v "$ADA_DATA_DIR":/home/ada/.local/share/ada $ADA_CONTAINER "$ADA_HOST_DIRECTORIES"

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

    if [ $ADA_IS_GYGWIN -eq 1 ]; then
        ADA_HOST_DIRECTORY="/$ADA_HOST_DIRECTORY";
    fi

    $ADA_DOCKER_COMMAND_PREFIX docker run --rm -e ADA_TRANSLATE=1 -v "${ADA_DATA_DIR}":/home/ada/.local/share/ada $ADA_CONTAINER "app.launch" "ada-setup"

    while ! is_port_free $ADA_PORT
    do
      let ADA_PORT=ADA_PORT+1
    done

    ADA_RUNNING_CONTAINERS=$(docker ps --filter "label=de.senckenberg.agrifuture=agrifuture_desktop_agent1" | tail -n +2 | wc -l)
    ADA_CONTAINER_ID=$(docker run --rm -d --label de.senckenberg.agrifuture=agrifuture_desktop_agent -e ADA_RUN_AGENT=1 -e ADA_RUNNING_CONTAINERS=$ADA_RUNNING_CONTAINERS -e ADA_HOST_DIRECTORY=$ADA_HOST_DIRECTORY -v "$ADA_DATA_DIR":/home/ada/.local/share/ada -v "$ADA_HOST_DIRECTORY":/data:ro -p "127.0.0.1:$ADA_PORT:80" $ADA_CONTAINER)
    sleep 5
    $ADA_DOCKER_COMMAND_PREFIX docker run --rm -e ADA_TRANSLATE=1 -v "${ADA_DATA_DIR}":/home/ada/.local/share/ada $ADA_CONTAINER "app.launch_information" "ada-setup" "http://127.0.0.1:$ADA_PORT"

    if [ $ADA_IS_WIN -eq 1 ]; then
        powershell.exe -c start "http://127.0.0.1:$ADA_PORT"
    else
        xdg-open "http://127.0.0.1:$ADA_PORT" &>/dev/null
    fi

    sleep infinity
}

setup
main "$@"
shutdown
