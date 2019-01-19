#!/bin/bash

# Exit on first error
set -e

# Set execution path
exec_dir="$(pwd "$(dirname "$0")")"

# Get variables
set -a
source "$exec_dir/production.env"
set +a

# Define default variables
TARGET_HOST=${TARGET_HOST}
TARGET_PATH=${TARGET_PATH}

# Exit if required variables are not set
[[ -z "${TARGET_HOST}" ]] && echo "TARGET_HOST not set. Exiting." && exit 1
[[ -z "${TARGET_PATH}" ]] && echo "TARGET_HOST not set. Exiting." && exit 1

# Exit if there are unstaged files
[[ -n "$(git status --porcelain)" ]] && echo "Working directory is not clean. Exiting." && exit 1

# Get current Git revision
revision=$(git --git-dir="${exec_dir}/.git" log --pretty="%h" -n1 HEAD)

# Install dependencies
echo "Install dependencies via Composer..."
composer install

# Create directory structure on production
echo "Create directory structure on production..."
ssh ${TARGET_HOST} -T "mkdir -p ${TARGET_PATH}/{cache,local,release}"

# Transfer files to cache on production
echo "Transfer files to production cache..."
rsync -ar --delete --delete-excluded "${exec_dir}"/ ${TARGET_HOST}:${TARGET_PATH}/cache \
    --exclude /composer.json \
    --exclude /composer.lock \
    --exclude /.git \
    --exclude /.gitignore \
    --exclude /*.env \
    --exclude /production.env.dist \
    --exclude /.ddev \
    --exclude /sbin \
    --exclude /.idea

# Set new release on production
ssh ${TARGET_HOST} -T << __EOF
    set -e

    # Create revision file
    echo "${revision}" >| ${TARGET_PATH}/cache/REVISION

    echo "Production: Update live system with new release..."
    rsync -ar --delete ${TARGET_PATH}/cache/ ${TARGET_PATH}/release/

    echo "Production: Overlay release with local directory..."
    rsync -rl ${TARGET_PATH}/local/ ${TARGET_PATH}/release/

    echo "Production: Update database..."
    php ${TARGET_PATH}/release/console.php database:schema update
__EOF

echo "Done."
