# syntax=docker/dockerfile:1

# Targets:
#   base        runtime only, application code is bind-mounted (used by docker-compose)
#   standalone  runtime + application code baked in (default target, for third-party use)

FROM php:8.5.11-apache AS base

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


FROM base AS standalone

COPY --chown=unimarc2marc21:www-data ./www /var/www/html
