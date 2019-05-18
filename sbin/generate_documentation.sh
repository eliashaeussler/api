#!/bin/bash

# Global variables
SECONDS=0
SCRIPT_PATH="$(dirname "$0")"
ROOT_PATH="$(pwd "$SCRIPT_PATH")"

# Get scripts
set -a
source "$SCRIPT_PATH/shared/console.sh"
set +a

# Global configuration
SAMI="$ROOT_PATH/vendor/bin/sami.php"
CONFIG_FILE="$ROOT_PATH/.sami_config.php"
CURRENT_BRANCH="$(git rev-parse --abbrev-ref HEAD)"

# Ensure Sami is installed
if [[ ! -f "$SAMI" ]]; then
    output "Installing Sami..." ${ACTION} 0
    composer install --dev --quiet
    output " Done." ${SUCCESS}
fi

# Run Sami
output "Building documentation..." ${ACTION}
php ${SAMI} update ${CONFIG_FILE} --force $@
output "Done." ${SUCCESS}

# Go back to last branch
output "Going back to initial branch..." ${ACTION} 0
git checkout --quiet "$CURRENT_BRANCH"
output " Done." ${SUCCESS}

# Apply generated documentation
output "Applying generated documentation..." ${ACTION} 0
cp -r "$ROOT_PATH/docs/__build__/" "$ROOT_PATH/docs/php/" && rm -r "$ROOT_PATH/docs/__build__"
output " Done." ${SUCCESS}

print_success_message
