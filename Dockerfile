FROM php:7.4-apache
MAINTAINER "Iordanis Kostelidis <ikostelidis@datascouting.com>"

ARG APP_UID=1000
ARG APP_GID=1000

ARG TIMEZONE=Europe/Athens

ENV LC_ALL=en_US.UTF-8
ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US.UTF-8

ENV DEBIAN_FRONTEND=noninteractive
ARG PHP_INI_PROFILE=production

# Update the base image, set timezone and locales
RUN apt-get update && apt-get upgrade -y \
 && apt-get -y install apt-utils \
 && apt-get -y install tzdata \
 && ln -fs /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
 && dpkg-reconfigure --frontend noninteractive tzdata \
 && apt-get install -y locales locales-all

# Install libcap (we need it to allow apache bind 80 port without root)
RUN apt-get -y install libcap2 libcap2-bin

# Install application dependencies
RUN apt-get -y install yaz xsltproc

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Setup apache and php
RUN setcap 'cap_net_bind_service=+ep' /usr/sbin/apache2 \
 && a2enmod rewrite \
 && service apache2 restart \
 && cp /usr/local/etc/php/php.ini-${PHP_INI_PROFILE} /usr/local/etc/php/php.ini

# Create application user
RUN groupadd -g "${APP_GID}" unimarc2marc21 \
 && useradd -u "${APP_UID}" -ms /bin/bash -g unimarc2marc21 unimarc2marc21 \
 && usermod -a -G unimarc2marc21 www-data

# Change default user
USER unimarc2marc21