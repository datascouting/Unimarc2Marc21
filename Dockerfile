# syntax=docker/dockerfile:1

# Targets:
#   base        runtime only, application code is bind-mounted (used by docker-compose)
#   standalone  runtime + application code baked in (default target, for third-party use)

FROM php:8.5.12-apache AS base

LABEL maintainer="Iordanis Kostelidis <ikostelidis@datascouting.com>" \
      org.opencontainers.image.title="unimarc2marc21" \
      org.opencontainers.image.source="https://github.com/datascouting/Unimarc2Marc21" \
      org.opencontainers.image.licenses="MIT"

ARG APP_UID=1000
ARG APP_GID=1000
ARG TIMEZONE=Europe/Athens
ARG LOCALE=en_US.UTF-8
ARG PHP_INI_PROFILE=production
ARG DEBIAN_FRONTEND=noninteractive

ENV LANG=${LOCALE} \
    LANGUAGE=${LOCALE} \
    LC_ALL=${LOCALE} \
    TZ=${TIMEZONE}

# System packages, locale, timezone, apache/php setup and application user in a single layer
RUN apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y --no-install-recommends \
        locales \
        tzdata \
        yaz \
        xsltproc \
        libcap2-bin \
 # Generate only the required locale (instead of the ~250MB locales-all package) \
 && echo "${LOCALE} ${LOCALE#*.}" > /etc/locale.gen \
 && locale-gen \
 # Timezone (OS and PHP) \
 && ln -fs "/usr/share/zoneinfo/${TIMEZONE}" /etc/localtime \
 && echo "${TIMEZONE}" > /etc/timezone \
 && printf 'date.timezone = "%s"\n' "${TIMEZONE}" > /usr/local/etc/php/conf.d/timezone.ini \
 # Allow apache to bind port 80 without root, then drop the tooling \
 && setcap 'cap_net_bind_service=+ep' /usr/sbin/apache2 \
 && apt-get purge -y --auto-remove libcap2-bin \
 && a2enmod rewrite \
 && cp "/usr/local/etc/php/php.ini-${PHP_INI_PROFILE}" /usr/local/etc/php/php.ini \
 # Application user (-o tolerates ids that already exist in the image) \
 && groupadd -o -g "${APP_GID}" unimarc2marc21 \
 && useradd -o -l -m -s /bin/bash -u "${APP_UID}" -g unimarc2marc21 unimarc2marc21 \
 && usermod -a -G unimarc2marc21 www-data \
 # Clean up \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/* /tmp/* /var/tmp/*

USER unimarc2marc21

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -fsS -o /dev/null http://localhost/index.css || exit 1


# PHPUnit test runner on the same PHP/runtime as production
# Usage: docker build --target test -t unimarc2marc21:test . && docker run --rm unimarc2marc21:test
FROM base AS test

USER root

RUN apt-get update \
 && apt-get install -y --no-install-recommends unzip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
RUN chown unimarc2marc21:unimarc2marc21 /app

USER unimarc2marc21

COPY --chown=unimarc2marc21:unimarc2marc21 composer.json composer.lock* ./
RUN composer install --no-interaction --no-progress --prefer-dist

COPY --chown=unimarc2marc21:unimarc2marc21 phpunit.xml.dist ./
COPY --chown=unimarc2marc21:unimarc2marc21 tests ./tests
COPY --chown=unimarc2marc21:unimarc2marc21 www ./www

# Run the suite at build time; a failing test fails the build.
# The marker file is what the standalone stage depends on.
RUN vendor/bin/phpunit && touch /app/.tests-passed

HEALTHCHECK NONE
CMD ["vendor/bin/phpunit"]


FROM base AS standalone

# Force the test stage to run (BuildKit skips stages nothing depends on).
# The bind mount adds no files or layers to the final image.
RUN --mount=type=bind,from=test,source=/app/.tests-passed,target=/tmp/.tests-passed true

COPY --chown=unimarc2marc21:www-data ./www /var/www/html
