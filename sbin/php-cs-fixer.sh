#!/bin/bash

# Exit on first error
set -e

# Global variables
SCRIPT_PATH="$(dirname "$0")"
ROOT_PATH="$(pwd "$SCRIPT_PATH")"
PHP_CS_FIXER="$ROOT_PATH/vendor/bin/php-cs-fixer"
CONFIG_FILE="$ROOT_PATH/.php_cs"

# Ensure PHP-CS-Fixer is installed and executable
[[ ! -f "$PHP_CS_FIXER" ]] && composer install --dev --quiet

# Run PHP-CS-Fixer
${PHP_CS_FIXER} fix --config "${CONFIG_FILE}" "$@"
