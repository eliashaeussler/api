#!/bin/bash

# Exit on first error
set -e

# Global variables
SECONDS=0
SCRIPT_PATH="$(dirname "$0")"
ROOT_PATH="$(pwd "$SCRIPT_PATH")"
SAMI="$ROOT_PATH/vendor/bin/sami.php"
CONFIG_FILE="$ROOT_PATH/.sami_config.php"

# Get scripts and variables
set -a
source "$SCRIPT_PATH/shared/console.sh"
set +a

# Ensure Sami is installed and executable
[[ ! -f "$SAMI" ]] && composer install --dev --quiet

# Run Sami
output "Building documentation" ${ACTION} 0
php ${SAMI} --quiet update ${CONFIG_FILE} $@
output " Done." ${SUCCESS}

print_success_message
