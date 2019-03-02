#!/bin/bash

export NORMAL="\033[0m"
export ERROR="\033[1;31m"
export SUCCESS="\033[1;32m"
export WARNING="\033[1;33m"
export ACTION="\033[1;34m"

function output() {
    local message="$1"
    local color="${2-$NORMAL}"
    local lineBreak="${3-1}"

    printf "${color}${message}${NORMAL}"
    [[ ${lineBreak} == 0 ]] || printf "\n"
}

function print_success_message() {
    local duration="$SECONDS second"$([[ $SECONDS != 1 ]] && echo "s")
    local defaultMessage="Done in %s."
    local message=$(echo "${1-$defaultMessage}" | sed "s/%s/$duration/g")

    output "$message" ${SUCCESS}
}
