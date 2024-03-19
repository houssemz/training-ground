ARG BUILD_MODE=production
ARG PHP_VERSION=8.3.2
ARG ALPINE_VERSION=3.19
ARG COMPOSER_VERSION=2.6.6
ARG APP_DIR=/srv/app
ARG PHP_BUILDER_OUTPUT_DIR=/php-builder
ARG COMPOSER_HOME=/home/www-data
ARG OPENRESTY_VERSION=1.21.4.1
ARG HOST_UID=1000
ARG HOST_GID=1000

#------------------------------------------------------------------------------
# COMPOSER
#------------------------------------------------------------------------------
FROM composer:${COMPOSER_VERSION} AS composer

ARG APP_DIR
ARG COMPOSER_HOME

ENV COMPOSER_HOME=${COMPOSER_HOME} \
    PATH="${PATH}:${COMPOSER_HOME}/vendor/bin"

WORKDIR ${APP_DIR}
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# PHP BUIDLER
#------------------------------------------------------------------------------
FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} as php-builder

ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    mkdir -p \
        ${PHP_BUILDER_OUTPUT_DIR} \
        ${PHP_BUILDER_OUTPUT_DIR}/bin \
    ; \
    apk add --no-cache \
        $PHPIZE_DEPS \
        coreutils \
        curl \
        linux-headers \
        openssl-dev \
    ;
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BUILD ext-apcu
#------------------------------------------------------------------------------
FROM php-builder AS ext-apcu

ARG PHP_BUILDER_OUTPUT_DIR
ARG APCU_VERSION=5.1.23

RUN set -eux; \
    pecl install \
        apcu-${APCU_VERSION} \
    ; \
    pecl clear-cache; \
    docker-php-ext-enable \
        apcu \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/apcu.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-apcu.ini ${PHP_BUILDER_OUTPUT_DIR};
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BUILD ext-intl
#------------------------------------------------------------------------------
FROM php-builder AS ext-intl

ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        icu-dev \
    ; \
    docker-php-ext-install -j$(nproc) \
        intl \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/intl.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-intl.ini ${PHP_BUILDER_OUTPUT_DIR}; \
    apk del .build-deps;
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BUILD ext-opcache
#------------------------------------------------------------------------------
FROM php-builder AS ext-opcache

ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    docker-php-ext-install -j$(nproc) \
        opcache \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/opcache.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini ${PHP_BUILDER_OUTPUT_DIR};
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BUILD ext-zip
#------------------------------------------------------------------------------
FROM php-builder AS ext-zip

ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        libzip-dev \
        zlib-dev \
    ; \
    docker-php-ext-install -j$(nproc) \
        zip \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/zip.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-zip.ini ${PHP_BUILDER_OUTPUT_DIR}; \
    apk del .build-deps;
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BUILD ext-bcmath
#------------------------------------------------------------------------------
FROM php-builder AS ext-bcmath

ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    docker-php-ext-install -j$(nproc) \
        bcmath \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/bcmath.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-bcmath.ini ${PHP_BUILDER_OUTPUT_DIR};
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BUILD ext-pdo_pgsql
#------------------------------------------------------------------------------
FROM php-builder AS ext-pdo_pgsql

ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        postgresql-dev \
    ; \
    docker-php-ext-configure pdo_pgsql --with-pdo-pgsql; \
    docker-php-ext-install \
        pdo_pgsql \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/pdo_pgsql.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-pdo_pgsql.ini ${PHP_BUILDER_OUTPUT_DIR}; \
    apk del .build-deps;
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BUILD ext-pdo_mysql
#------------------------------------------------------------------------------
FROM php-builder AS ext-pdo_mysql

ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    docker-php-ext-install \
        pdo_mysql \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/pdo_mysql.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-pdo_mysql.ini ${PHP_BUILDER_OUTPUT_DIR};
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BUILD ext-amqp
#------------------------------------------------------------------------------
FROM php-builder AS ext-amqp

ARG PHP_BUILDER_OUTPUT_DIR
ARG AMQP_VERSION=2.1.1

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        rabbitmq-c-dev \
    ; \
    pecl install \
        amqp-${AMQP_VERSION} \
    ; \
    pecl clear-cache; \
    docker-php-ext-enable \
        amqp \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/amqp.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-amqp.ini ${PHP_BUILDER_OUTPUT_DIR}; \
    apk del .build-deps;
#------------------------------------------------------------------------------


#------------------------------------------------------------------------------
# BUILD ext-redis
#------------------------------------------------------------------------------
FROM php-builder AS ext-redis

ARG PHP_BUILDER_OUTPUT_DIR
ARG REDIS_VERSION=6.0.2

RUN set -eux; \
    pecl install \
        redis-${REDIS_VERSION} \
    ; \
    pecl clear-cache; \
    docker-php-ext-enable \
        redis \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/redis.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-redis.ini ${PHP_BUILDER_OUTPUT_DIR};
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BUILD ext-igbinary
#------------------------------------------------------------------------------
FROM php-builder AS ext-igbinary

ARG PHP_BUILDER_OUTPUT_DIR
ARG IGBINARY_VERSION=3.2.15

RUN set -eux; \
    pecl install \
        igbinary-${IGBINARY_VERSION} \
    ; \
    pecl clear-cache; \
    docker-php-ext-enable \
        igbinary \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/igbinary.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-igbinary.ini ${PHP_BUILDER_OUTPUT_DIR};
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BUILD ext-sockets
#------------------------------------------------------------------------------
FROM php-builder AS ext-sockets

ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    docker-php-ext-install -j$(nproc) \
        sockets \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/sockets.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-sockets.ini ${PHP_BUILDER_OUTPUT_DIR};
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# PHP EXTENSIONS
#------------------------------------------------------------------------------
FROM php-builder as php-extensions

ARG PHP_BUILDER_OUTPUT_DIR

COPY --from=ext-amqp ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-apcu ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-bcmath ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-igbinary ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-intl ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-opcache ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-pdo_mysql ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-pdo_pgsql ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-redis ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-sockets ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-zip ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BASE PHP
#------------------------------------------------------------------------------
FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} as php-base

ARG PHP_BUILDER_OUTPUT_DIR
ARG APP_DIR
ARG COMPOSER_HOME

ARG PHP_BUILDER_OUTPUT_DIR
ARG APP_DIR
ARG COMPOSER_HOME

WORKDIR ${APP_DIR}

ENV APP_DEBUG=0 \
    APP_DIR=${APP_DIR} \
    APP_ENV=prod \
    COMPOSER_HOME=${COMPOSER_HOME} \
    LD_PRELOAD=/usr/lib/preloadable_libiconv.so \
    PATH=${APP_DIR}/vendor/bin:${COMPOSER_HOME}/vendor/bin:${PATH} \
    PHP_APC_ENABLE_CLI=0 \
    PHP_APC_ENABLED=1 \
    PHP_DATE_TIMEZONE=UTC \
    PHP_FPM_PHP_ADMIN_VALUE_MEMORY_LIMIT=128M \
    PHP_FPM_PM_MAX_CHILDREN=5 \
    PHP_FPM_PM_MAX_REQUESTS=500 \
    PHP_FPM_PM_MAX_SPARE_SERVERS=3 \
    PHP_FPM_PM_MIN_SPARE_SERVERS=1 \
    PHP_FPM_PM_START_SERVERS=2 \
    PHP_MEMORY_LIMIT=128M \
    PHP_OPCACHE_ENABLE=On \
    PHP_OPCACHE_ENABLE_CLI=On \
    PHP_OPCACHE_FILE_CACHE=/tmp/opcache \
    PHP_OPCACHE_FILE_CACHE_ONLY=0 \
    PHP_OPCACHE_INTERNED_STRINGS_BUFFER=16 \
    PHP_OPCACHE_JIT_BUFFER_SIZE=0 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=30000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=256 \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=On \
    PHP_POST_MAX_SIZE=128M \
    PHP_REALPATH_CACHE_SIZE=4096K \
    PHP_REALPATH_CACHE_TTL=600 \
    PHP_SESSION_AUTO_START=Off \
    PHP_SHORT_OPEN_TAG=Off \
    PHP_UPLOAD_MAX_FILESIZE=128M

## General configuration & runtime packages installation
RUN set -eux; \
    mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"; \
    apk add --no-cache \
        bash \
        bash-completion \
        curl \
        fcgi \
        file \
        gettext \
		tini \
		tzdata \
    ;

## Install dependencies from other layers
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
## Install configs files & scripts
COPY docker/base/php/rootfs /

## Install php extensions
COPY --from=php-extensions ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
RUN set -eux; \
    cp -a ${PHP_BUILDER_OUTPUT_DIR}/*.so $(php -r "echo ini_get ('extension_dir');"); \
    cp -a ${PHP_BUILDER_OUTPUT_DIR}/*.ini $PHP_INI_DIR/conf.d/; \
    cp -a ${PHP_BUILDER_OUTPUT_DIR}/bin/* /usr/local/bin/ && chmod a+x /usr/local/bin/*; \
    rm -rf ${PHP_BUILDER_OUTPUT_DIR}; \
    runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )"; \
    apk add --update --no-cache $runDeps; \
    mkdir -p ${PHP_OPCACHE_FILE_CACHE}; \
    chown -R www-data:www-data /usr/local/etc $PHP_INI_DIR ${APP_DIR} ${PHP_OPCACHE_FILE_CACHE};

STOPSIGNAL SIGQUIT

EXPOSE 9000

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

ENTRYPOINT ["docker-entrypoint"]

CMD ["php-fpm"]
#------------------------------------------------------------------------------

## ----------------------------------------------------------------------------
## BLACKFIRE
## ----------------------------------------------------------------------------
FROM php-builder as blackfire

ARG BLACKFIRE_VERSION=2.23.0
ARG BLACKFIRE_PHP_VERSION=1.92.0
ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && architecture=$(case $(uname -m) in i386 | i686 | x86) echo "i386" ;; x86_64 | amd64) echo "amd64" ;; aarch64 | arm64 | armv8) echo "arm64" ;; *) echo "amd64" ;; esac) \
    && curl -A "Docker" -o $(php -r "echo ini_get ('extension_dir');")/blackfire.so -D - -L -s https://packages.blackfire.io/binaries/blackfire-php/${BLACKFIRE_PHP_VERSION}/blackfire-php-alpine_${architecture}-php-${version}.so \
    && curl -A "Docker" -o ${PHP_BUILDER_OUTPUT_DIR}/bin/blackfire -D - -L -s https://packages.blackfire.io/binaries/blackfire/${BLACKFIRE_VERSION}/blackfire-linux_${architecture}; \
    docker-php-ext-enable \
        blackfire \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/blackfire.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-blackfire.ini ${PHP_BUILDER_OUTPUT_DIR}
## ----------------------------------------------------------------------------

## ----------------------------------------------------------------------------
## PCOV EXTENSION
## ----------------------------------------------------------------------------
FROM php-builder as ext-pcov

ARG PCOV_VERSION=1.0.11
ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    pecl install \
        pcov-${PCOV_VERSION} \
    ; \
    pecl clear-cache; \
    docker-php-ext-enable \
        pcov \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/pcov.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-pcov.ini ${PHP_BUILDER_OUTPUT_DIR}
## ----------------------------------------------------------------------------

## ----------------------------------------------------------------------------
## XDEBUG EXTENSION
## ----------------------------------------------------------------------------
FROM php-builder as ext-xdebug

ARG XDEBUG_VERSION=3.3.1
ARG PHP_BUILDER_OUTPUT_DIR

RUN set -eux; \
    pecl install \
        xdebug-${XDEBUG_VERSION} \
    ; \
    pecl clear-cache; \
    docker-php-ext-enable \
        xdebug \
    ; \
    cp -p $(php -r "echo ini_get ('extension_dir');")/xdebug.so ${PHP_BUILDER_OUTPUT_DIR}; \
    cp -p $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini ${PHP_BUILDER_OUTPUT_DIR}
## ----------------------------------------------------------------------------

#------------------------------------------------------------------------------
# PHP DEVELOPMENT/DEBUG EXTENSIONS
#------------------------------------------------------------------------------
FROM php-builder as php-debug-extensions

ARG PHP_BUILDER_OUTPUT_DIR

COPY --from=blackfire ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-pcov ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
COPY --from=ext-xdebug ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# BASE PHP WITH DEVELOPMENT/DEBUG TOOLS
#------------------------------------------------------------------------------
FROM php-base as php-debug

ARG APP_DIR
ARG COMPOSER_REQUIRE_CHECKER_VERSION=4.7.1
ARG DEPTRAC_VERSION=1.0.2
ARG HOST_GID
ARG HOST_UID
ARG PHP_BUILDER_OUTPUT_DIR
ARG PHP_CS_FIXER_VERSION=3.50.0
ARG PHP_SECURITY_CHECKER_VERSION=2.0.6

## Install php development/debug config & extensions
COPY --from=php-debug-extensions ${PHP_BUILDER_OUTPUT_DIR} ${PHP_BUILDER_OUTPUT_DIR}

## General configuration & runtime packages installation
RUN set -eux; \
    mv "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/php.ini"; \
    apk add --no-cache \
        binutils \
        git \
        graphviz \
        jq \
        openssl \
        shadow \
        tree \
        util-linux \
    	vim \
    	yamllint \
    ; \
    # Change www-data UID / GID according to ARGS
    usermod -u "${HOST_UID}" www-data; \
    existing_group=$(getent group "${HOST_GID}" | cut -d: -f1); \
    if [[ -n "${existing_group}" ]]; then delgroup "${existing_group}"; fi; \
    groupmod -g "${HOST_GID}" www-data; \
    \
    cp -a ${PHP_BUILDER_OUTPUT_DIR}/*.so $(php -r "echo ini_get ('extension_dir');"); \
    cp -a ${PHP_BUILDER_OUTPUT_DIR}/*.ini $PHP_INI_DIR/conf.d/; \
    cp -a ${PHP_BUILDER_OUTPUT_DIR}/bin/* /usr/local/bin/ && chmod a+x /usr/local/bin/*; \
    rm -rf ${PHP_BUILDER_OUTPUT_DIR}; \
    runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )"; \
    apk add --update --no-cache $runDeps; \
    curl -L -s \
            -o /usr/local/bin/local-php-security-checker \
            "https://github.com/fabpot/local-php-security-checker/releases/download/v${PHP_SECURITY_CHECKER_VERSION}/local-php-security-checker_${PHP_SECURITY_CHECKER_VERSION}_linux_amd64"; \
        chmod a+x /usr/local/bin/local-php-security-checker; \
        local-php-security-checker --update-cache \
    ; \
  	curl -L -s \
            -o /usr/local/bin/deptrac \
    		"https://github.com/qossmic/deptrac/releases/download/${DEPTRAC_VERSION}/deptrac.phar"; \
        chmod a+x /usr/local/bin/deptrac \
    ; \
    curl -L -s \
            -o /usr/local/bin/php-cs-fixer \
    		"https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v${PHP_CS_FIXER_VERSION}/php-cs-fixer.phar"; \
        chmod a+x /usr/local/bin/php-cs-fixer \
    ; \
    curl -L -s \
            -o /usr/local/bin/composer-require-checker \
    		"https://github.com/maglnet/ComposerRequireChecker/releases/download/${COMPOSER_REQUIRE_CHECKER_VERSION}/composer-require-checker.phar"; \
        chmod a+x /usr/local/bin/composer-require-checker \
    ; \
    sh -c "$(curl --location https://taskfile.dev/install.sh)" -- -d -b /usr/local/bin;
#------------------------------------------------------------------------------

### PRODUCTION ################################################################

#------------------------------------------------------------------------------
# SOURCE CODE
#------------------------------------------------------------------------------
FROM composer as source-code

ARG APP_DIR

WORKDIR ${APP_DIR}

COPY srv/app/composer.json srv/app/composer.lock srv/app/symfony.lock ./

RUN set -eux; \
    composer install -n --no-progress --ignore-platform-reqs --no-dev --prefer-dist --no-scripts --no-autoloader; \
    composer clear-cache; \
    mkdir -p var/cache var/log;

## Copy source code
COPY srv/app/.env ./
COPY srv/app/bin bin
COPY srv/app/config config
COPY srv/app/public public
COPY srv/app/src src

#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# APP PRODUCTION
#------------------------------------------------------------------------------
FROM php-base as app_production

ARG APP_DIR

COPY docker/prod/php/rootfs /

RUN set -eux; \
    chown -R www-data:www-data /home/www-data /usr/local/etc $PHP_INI_DIR ${APP_DIR};

USER www-data

ENV PHP_OPCACHE_PRELOAD=${APP_DIR}/config/preload.php \
	PHP_OPCACHE_PRELOAD_USER=www-data \
	PHP_OPCACHE_VALIDATE_TIMESTAMPS=0

## Copy source code
COPY --chown=www-data:www-data --from=source-code ${APP_DIR} ${APP_DIR}

RUN set -eux; \
    composer dump-autoload --classmap-authoritative --no-dev; \
    composer clear-cache; \
    chmod +x bin/console; \
    php -d memory_limit=-1 bin/console cache:warmup;
#------------------------------------------------------------------------------

### DEVELOPMENT ###############################################################

#------------------------------------------------------------------------------
# APP SERVER WITH DEVELOPMENT/DEBUG TOOLING
#------------------------------------------------------------------------------
FROM php-debug as app_development

ARG APP_DIR
ARG IMAGE_SOURCE

ENV APP_DEBUG=1 \
	APP_ENV=dev \
    BLACKFIRE_SOCKET=tcp://blackfire:8307 \
    PHP_IDE_CONFIG=serverName=localhost \
    PHP_MEMORY_LIMIT=256M \
    XDEBUG_MODE=off \
    XDEBUG_SESSION=PHPSTORM \
# At this time phpcsfixer is not fully compatible with PHP8.2 (it will be soon). Upgrade phpcsfixer version and remove this env
    PHP_CS_FIXER_IGNORE_ENV=1

COPY docker/dev/php/rootfs /

RUN set -eux; \
    sh -c "$(curl --location https://taskfile.dev/install.sh)" -- -d -b /usr/local/bin; \
    chown -R www-data:www-data /home/www-data /usr/local $PHP_INI_DIR ${APP_DIR};

USER www-data

# Copy source code
COPY --chown=www-data:www-data --from=source-code ${APP_DIR} ${APP_DIR}
COPY --chown=www-data:www-data ${APP_DIR}/.env.* ${APP_DIR}/.php-cs-fixer.* ${APP_DIR}/.phpstan.* ${APP_DIR}/phpunit.* ${APP_DIR}/phpspec.* ${APP_DIR}/.yamllint.* ${APP_DIR}/Taskfile.* ${APP_DIR}/.composer-require-checker.* ${APP_DIR}/.deptrac.yaml ${APP_DIR}/
COPY --chown=www-data:www-data ${APP_DIR}/tests ${APP_DIR}/tests

RUN set -eux; \
    composer install -n --prefer-dist --no-scripts --no-progress --ignore-platform-reqs; \
    composer clear-cache;

LABEL org.opencontainers.image.source=${IMAGE_SOURCE}
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# HTTP server
#------------------------------------------------------------------------------
FROM openresty/openresty:${OPENRESTY_VERSION}-alpine AS nginx

ARG APP_DIR
ENV APP_DIR="${APP_DIR}"

RUN apk --no-cache update \
 && apk --no-cache upgrade

WORKDIR ${APP_DIR}

COPY docker/base/nginx/rootfs /
COPY --from=source-code ${APP_DIR}/public ${APP_DIR}/public/
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# DEFAULT TARGET (depends on 'BUILD_MODE' ARG)
#------------------------------------------------------------------------------
FROM app_${BUILD_MODE} as app
#------------------------------------------------------------------------------

#------------------------------------------------------------------------------
# WORKER TARGET (depends on 'BUILD_MODE' ARG)
#------------------------------------------------------------------------------
FROM app as worker

HEALTHCHECK NONE

CMD ["sh", "-c", "bin/console messenger:consume $WORKER_TRANSPORT -n --sleep $WORKER_IDLE_TIME"]
#------------------------------------------------------------------------------

