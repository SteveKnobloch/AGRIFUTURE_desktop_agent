#!/bin/bash
set -eo pipefail

# **********************************************
# @version 1.0.0
# **********************************************

if [ "$ADA_RUN_SETUP" == "1" ]; then
    exec ada-setup "$@"
fi
