#!/bin/bash

# **********************************************
# @version 1.0.0
# **********************************************

if [ "$ADA_RUN_AGENT" == "1" ]; then
    exec supervisord -c /etc/supervisord.conf --nodaemon
fi
