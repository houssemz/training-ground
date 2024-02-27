#!/bin/sh
set -eu

GITHUB_TOKEN=${GITHUB_TOKEN:-}

if [ ! -z "${GITHUB_TOKEN}" ]; then
    composer config --global github-oauth.github.com "${GITHUB_TOKEN}";
fi
