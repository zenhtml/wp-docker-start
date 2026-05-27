FROM wordpress:latest

RUN apt-get update && apt-get install -y --no-install-recommends \
    less \
    mariadb-client \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

COPY php.ini /usr/local/etc/php/conf.d/custom.ini
