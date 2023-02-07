#!/bin/bash
set -eo pipefail

# ***********************************************
# @version 0.0.1
#
# Use this script to build the container.
#
# Parameters:
#
#   DSL_IMAGE_NAMESPACE: The image namespace (default: ghcr.io/steveknobloch/agrifuture_desktop_agent)
#   DSL_IMAGE_TAG: The image tag (default: latest)
#   DSL_TARGET_STAGE: The build target (default: production)
#
# Examples:
#
#   ./build.sh
#   DSL_IMAGE_NAMESPACE=ghcr.io/steveknobloch/agrifuture_desktop_agent ./build.sh
#   DSL_IMAGE_NAMESPACE=ghcr.io/steveknobloch/agrifuture_desktop_agent DSL_IMAGE_TAG=namespace ./build.sh
# ***********************************************

# ******************
# check requirements
if [ "$BASH" = "" ]; then echo "Error: you are not running this script within the bash."; exit 1; fi
if [ ! -x "$(command -v docker)" ]; then echo "Error: docker is not installed / executable."; exit 1; fi
THIS_SCRIPT_REAL_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

DSL_IMAGE_NAMESPACE=${DSL_IMAGE_NAMESPACE:-ghcr.io/steveknobloch/agrifuture_desktop_agent}
DSL_IMAGE_TAG=${DSL_IMAGE_TAG:-latest}
DSL_TARGET_STAGE=${DSL_TARGET_STAGE:-production}

# remove trailing slashes
DSL_IMAGE_NAMESPACE=${DSL_IMAGE_NAMESPACE%/}

echo "build image ${DSL_IMAGE_NAMESPACE}:${DSL_IMAGE_TAG}"
docker build --pull --no-cache --tag ${DSL_IMAGE_NAMESPACE}:${DSL_IMAGE_TAG} --target ${DSL_TARGET_STAGE} ${THIS_SCRIPT_REAL_PATH}/buildfiles/

unset THIS_SCRIPT_REAL_PATH
unset DSL_IMAGE_NAMESPACE
unset DSL_IMAGE_TAG
