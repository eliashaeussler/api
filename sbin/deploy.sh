#!/bin/bash

# Exit on first error
set -e

# Global variables
SECONDS=0
SCRIPT_PATH="$(dirname "$0")"
ROOT_PATH="$(pwd "$SCRIPT_PATH")"

# Get scripts and variables
set -a
source "$SCRIPT_PATH/shared/console.sh"
source "$ROOT_PATH/remote.env"
set +a

# Define default variables
TARGET_HOST=${TARGET_HOST}
TARGET_PATH=${TARGET_PATH}
TARGET_PORT=${TARGET_PORT-22}

# Exit if required variables are not set
[[ -z "${TARGET_HOST}" ]] && output "TARGET_HOST not set. Exiting." ${ERROR} && exit 1
[[ -z "${TARGET_PATH}" ]] && output "TARGET_PATH not set. Exiting." ${ERROR} && exit 1
[[ -z "${TARGET_PORT}" ]] && output "TARGET_PORT not set. Exiting." ${ERROR} && exit 1

# Exit if there are unstaged files
[[ -n "$(git status --porcelain)" ]] && output "Working directory is not clean. Exiting." ${ERROR} && exit 1

# Get current Git revision and version
revision="$(git --git-dir="${ROOT_PATH}/.git" log --pretty="%h" -n1 HEAD)"
version="$(git --git-dir="${ROOT_PATH}/.git" describe --tags)"

# Install dependencies
output "Install dependencies (no dev) via Composer..." ${ACTION} 0
composer install --no-dev --quiet --classmap-authoritative
output " Done." ${SUCCESS}

# Create directory structure on remote
output "Create directory structure on remote..." ${ACTION} 0
ssh ${TARGET_HOST} -p ${TARGET_PORT} -T "mkdir -p ${TARGET_PATH}/{cache,local,release}"
output " Done." ${SUCCESS}

# Transfer files to cache on remote
output "Transfer files to remote cache..." ${ACTION} 0
rsync -arq --delete --delete-excluded --port ${TARGET_PORT} "${ROOT_PATH}"/ ${TARGET_HOST}:${TARGET_PATH}/cache \
    --exclude /composer.json \
    --exclude /composer.lock \
    --exclude /.git \
    --exclude /.gitignore \
    --exclude /*.env \
    --exclude /remote.env.dist \
    --exclude /.ddev \
    --exclude /sbin \
    --exclude /.idea \
    --exclude /src/db-assets \
    --exclude /temp \
    --exclude /.php_cs* \
    --exclude /.sami_config.php \
    --exclude /docs/cache \
    --exclude /docs/__build__ \
    --exclude /.gitlab-ci.yml
output " Done." ${SUCCESS}

# Set new release on remote
ssh ${TARGET_HOST} -p ${TARGET_PORT} -T << __EOF
    typeset -f output
    set -e

    # Create revision and version file
    echo "${revision}" >| ${TARGET_PATH}/cache/REVISION
    echo "${version}" >| ${TARGET_PATH}/cache/VERSION

    printf "$(output "Remote: Update live system with new release..." ${ACTION} 0)"
    rsync -ar --delete ${TARGET_PATH}/cache/ ${TARGET_PATH}/release/
    echo "$(output " Done." ${SUCCESS})"

    printf "$(output "Remote: Overlay release with local directory..." ${ACTION} 0)"
    rsync -rl ${TARGET_PATH}/local/ ${TARGET_PATH}/release/
    echo "$(output " Done." ${SUCCESS})"

    printf "$(output "Remote: Update database..." ${ACTION} 0)"
    php ${TARGET_PATH}/release/console.php database:schema update > /dev/null
    echo "$(output " Done." ${SUCCESS})"
__EOF

# Re-install dev dependencies
output "Re-install dev-dependencies via Composer..." ${ACTION} 0
composer install --dev --quiet
output " Done." ${SUCCESS}

print_success_message "Deployed version ${version} in %s."
