#!/bin/bash

# Exit on first error
set -e

# Global variables
ROOT_PATH="$(pwd "$(dirname "$0")")"

# Get variables
set -a
source "$ROOT_PATH/production.env"
set +a

# Define default variables
TARGET_HOST=${TARGET_HOST}
TARGET_PATH=${TARGET_PATH}

# Exit if required variables are not set
[[ -z "${TARGET_HOST}" ]] && echo "TARGET_HOST not set. Exiting." && exit 1
[[ -z "${TARGET_PATH}" ]] && echo "TARGET_HOST not set. Exiting." && exit 1

function create_dump() {
    dump_name="$(date -u +%Y-%m-%dT%H%M%SZ).sql.gz"
    dump_path="${ROOT_PATH}/.ddev/import-db"
    dump_file="${dump_path}/${dump_name}"

    ssh ${TARGET_HOST} -T "php ${TARGET_PATH}/release/console.php database:export &" | gzip > "${dump_file}"
    [[ ! -s "${dump_file}" ]] && rm ${dump_file} && echo "Database dump is empty. Exiting." >&2 && exit 1

    echo "${dump_file}"
}

function import_dump() {
    dump_file="$1"
    [[ -z ${dump_file} ]] && echo "Please specify a dump file for import with DDEV." >&2 && exit 1

    ddev import-db --src "${dump_file}"
}

# Dump database and store it locally
echo "Dumping from remote..."
dump=$(create_dump)

# Import database
echo "Importing dump with DDEV..."
import_dump "$dump"

echo "Done."
