#!/bin/sh
set -eux

APP_DISABLED_EXTENSIONS=${APP_DISABLED_EXTENSIONS:-}

if [ ! -z "${APP_DISABLED_EXTENSIONS}" ]; then
    for EXTENSION_TO_DISABLE in "${APP_DISABLED_EXTENSIONS}"
    do
      if [ $(php -r "echo extension_loaded('${EXTENSION_TO_DISABLE}');") ]; then docker-php-ext-disable "${EXTENSION_TO_DISABLE}"; fi
    done
fi
