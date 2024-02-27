#!/bin/sh
set -eu

if [ ! -d "${APP_DIR}/vendor" ]; then
    cd "${APP_DIR}" && composer install --no-interaction --no-progress;
fi
