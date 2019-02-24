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

# Exit if required variables are not set
[[ -z "${TARGET_HOST}" ]] && output "TARGET_HOST not set. Exiting." ${ERROR} && exit 1
[[ -z "${TARGET_PATH}" ]] && output "TARGET_PATH not set. Exiting." ${ERROR} && exit 1

# Exit if there are unstaged files
[[ -n "$(git status --porcelain)" ]] && output "Working directory is not clean. Exiting." ${ERROR} && exit 1

# Get current Git revision and version
revision="$(git --git-dir="${ROOT_PATH}/.git" log --pretty="%h" -n1 HEAD)"
version="$(git --git-dir="${ROOT_PATH}/.git" describe --tags)"

# Install dependencies
output "Install dependencies via Composer..." ${ACTION}
composer install

# Create directory structure on remote
output "Create directory structure on remote..." ${ACTION}
ssh ${TARGET_HOST} -T "mkdir -p ${TARGET_PATH}/{cache,local,release}"

# Transfer files to cache on remote
output "Transfer files to remote cache..." ${ACTION}
rsync -ar --delete --delete-excluded "${ROOT_PATH}"/ ${TARGET_HOST}:${TARGET_PATH}/cache \
    --exclude /composer.json \
    --exclude /composer.lock \
    --exclude /.git \
    --exclude /.gitignore \
    --exclude /*.env \
    --exclude /remote.env.dist \
    --exclude /.ddev \
    --exclude /sbin \
    --exclude /.idea \
    --exclude /src/db-assets

# Set new release on remote
ssh ${TARGET_HOST} -T << __EOF
    $(typeset -f output)

    set -e

    # Create revision and version file
    echo "${revision}" >| ${TARGET_PATH}/cache/REVISION
    echo "${version}" >| ${TARGET_PATH}/cache/VERSION

    printf "$(output "Remote: Update live system with new release..." ${ACTION})"
    rsync -ar --delete ${TARGET_PATH}/cache/ ${TARGET_PATH}/release/
    printf "$(output " Done.\n" ${SUCCESS})"

    printf "$(output "Remote: Overlay release with local directory..." ${ACTION})"
    rsync -rl ${TARGET_PATH}/local/ ${TARGET_PATH}/release/
    printf "$(output " Done.\n" ${SUCCESS})"

    echo "$(output "Remote: Update database..." ${ACTION})"
    php ${TARGET_PATH}/release/console.php database:schema update
__EOF

print_success_message
