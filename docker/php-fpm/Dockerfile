FROM phpdockerio/php:8.1-fpm
WORKDIR "/var/www/html"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install sudo php8.1-mysql php8.1-gd php8.1-imagick php8.1-xml mysql-client php-pear php8.1-dev lsb-release git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install yarn
RUN curl -sL https://deb.nodesource.com/setup_18.x | sudo -E bash - \
    && sudo apt-get install -y nodejs \
    && sudo npm install -g yarn