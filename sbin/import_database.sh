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

function create_dump() {
    dump_name="$(date -u +%Y-%m-%dT%H%M%SZ).sql.gz"
    dump_path="${ROOT_PATH}/src/db-assets"
    dump_file="${dump_path}/${dump_name}"

    mkdir -p "${dump_path}"
    ssh ${TARGET_HOST} -T "php ${TARGET_PATH}/release/console.php database:export" | gzip > "${dump_file}"
    [[ ! -s "${dump_file}" ]] && rm ${dump_file} && output "Database dump is empty. Exiting." ${ERROR} >&2 && exit 1

    echo "${dump_file}"
}

function import_dump() {
    dump_file="$1"
    [[ -z ${dump_file} ]] && output "Please specify a dump file for import with DDEV." ${ERROR} >&2 && exit 1

    ddev import-db --src "${dump_file}"
    rm ${dump_file}
}

# Dump database and store it locally
output "Dumping from remote..." ${ACTION} 0
dump=$(create_dump)
output " Done." ${SUCCESS}

# Import database
output "Importing dump with DDEV..." ${ACTION}
import_dump "$dump"

print_success_message
