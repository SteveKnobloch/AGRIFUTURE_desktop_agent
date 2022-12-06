#!/bin/bash

# **********************************************
# @version 1.0.0
# **********************************************

setup_language() {
    if [ ! -f "/home/ada/.local/share/ada/lang" ]; then
        return 0
    fi

    local ADA_LANG=$(cat /home/ada/.local/share/ada/lang | tr -d '\n')

    if [ -z "$ADA_LANG" ]; then
        return 0
    fi

    if [ ! -z "$ADA_VERBOSE" ]; then echo "use language $ADA_LANG"; fi

    export LANG=$ADA_LANG
    export LANGUAGE=$ADA_LANG
    export LC_ALL=$ADA_LANG

    if [ "$ADA_LANG" == "en_US.UTF-8" ]; then
        local ADA_TIME=America/New_York
    elif [ "$ADA_LANG" == "de_DE.UTF-8" ]; then
        local ADA_TIME=Europe/Berlin
    fi

    if [ ! -z "ADA_TIME" ]; then
        if [ ! -z "$ADA_VERBOSE" ]; then echo "use timezone $ADA_TIME"; fi
        export TZ=$ADA_TIME

        echo $ADA_TIME > /etc/timezone
        if [ -f "/usr/share/zoneinfo/$ADA_TIME" ]; then
            cp "/usr/share/zoneinfo/$ADA_TIME" /etc/localtime
        fi
    fi
}

setup_language
