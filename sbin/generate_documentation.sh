#!/bin/bash

# Exit on first error
set -e

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
set +e
php ${SAMI} update ${CONFIG_FILE} $@
set -e
output "Done." ${SUCCESS}

# Go back to last branch
output "Going back to initial branch..." ${ACTION} 0
git add "$ROOT_PATH/docs" > /dev/null
git stash push --quiet --message "Updated code documentation" -- "$ROOT_PATH/docs"
git checkout --quiet "$CURRENT_BRANCH"
if [[ "$(git stash list)" ]]; then
    git checkout --quiet stash -- "$ROOT_PATH/docs"
    git stash drop --quiet
fi
output " Done." ${SUCCESS}

print_success_message
