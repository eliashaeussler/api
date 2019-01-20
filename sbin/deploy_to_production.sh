#!/bin/bash

# Exit on first error
set -e

# Global variables
ROOT_PATH="$(pwd "$(dirname "$0")")"

# Get variables
set -a
source "${ROOT_PATH}/remote.env"
set +a

# Define default variables
TARGET_HOST=${TARGET_HOST}
TARGET_PATH=${TARGET_PATH}

# Exit if required variables are not set
[[ -z "${TARGET_HOST}" ]] && echo "TARGET_HOST not set. Exiting." && exit 1
[[ -z "${TARGET_PATH}" ]] && echo "TARGET_HOST not set. Exiting." && exit 1

# Exit if there are unstaged files
[[ -n "$(git status --porcelain)" ]] && echo "Working directory is not clean. Exiting." && exit 1

# Get current Git revision and version
revision=$(git --git-dir="${ROOT_PATH}/.git" log --pretty="%h" -n1 HEAD)
version=$(git --git-dir="${ROOT_PATH}/.git" describe --tags)

# Install dependencies
echo "Install dependencies via Composer..."
composer install

# Create directory structure on remote
echo "Create directory structure on remote..."
ssh ${TARGET_HOST} -T "mkdir -p ${TARGET_PATH}/{cache,local,release}"

# Transfer files to cache on remote
echo "Transfer files to remote cache..."
rsync -ar --delete --delete-excluded "${ROOT_PATH}"/ ${TARGET_HOST}:${TARGET_PATH}/cache \
    --exclude /composer.json \
    --exclude /composer.lock \
    --exclude /.git \
    --exclude /.gitignore \
    --exclude /*.env \
    --exclude /remote.env.dist \
    --exclude /.ddev \
    --exclude /sbin \
    --exclude /.idea

# Set new release on remote
ssh ${TARGET_HOST} -T << __EOF
    set -e

    # Create revision and version file
    echo "${revision}" >| ${TARGET_PATH}/cache/REVISION
    echo "${version}" >| ${TARGET_PATH}/cache/VERSION

    echo "Remote: Update live system with new release..."
    rsync -ar --delete ${TARGET_PATH}/cache/ ${TARGET_PATH}/release/

    echo "Remote: Overlay release with local directory..."
    rsync -rl ${TARGET_PATH}/local/ ${TARGET_PATH}/release/

    echo "Remote: Update database..."
    php ${TARGET_PATH}/release/console.php database:schema update
__EOF

echo "Done."
