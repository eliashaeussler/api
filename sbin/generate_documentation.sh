#!/bin/bash

# Exit on first error
set -e

# Global variables
SECONDS=0
SCRIPT_PATH="$(dirname "$0")"
ROOT_PATH="$(pwd "$SCRIPT_PATH")"

SAMI_REMOTE_PHAR_FILE="https://github.com/blueend-ag/Sami/releases/latest/download/sami.phar"
SAMI_PHAR_FILE="$ROOT_PATH/vendor/bin/sami.phar"
SAMI_CONFIG_FILE="$ROOT_PATH/.sami_config.php"

# Get scripts and variables
set -a
source "$SCRIPT_PATH/shared/console.sh"
set +a

# Check if user wants to download the latest version of Sami
while getopts f opt; do
    case ${opt} in
        f) FORCE_DOWNLOAD=1; shift;;
    esac
done

# Download Sami
if [[ ! -f ${SAMI_PHAR_FILE} || ${FORCE_DOWNLOAD} ]]; then
    output "Downloading Sami..." ${ACTION} 0
    wget ${SAMI_REMOTE_PHAR_FILE} --quiet -O ${SAMI_PHAR_FILE}
    output " Done." ${SUCCESS}
fi

output "Building documentation..." ${ACTION} 0
php ${SAMI_PHAR_FILE} -q update ${SAMI_CONFIG_FILE} $@
output " Done." ${SUCCESS}

print_success_message
