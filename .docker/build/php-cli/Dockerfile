FROM php:5.6-cli-alpine

# Setup the Composer installer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }"

# Install Composer
RUN php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot && rm -rf /tmp/composer-setup.php
RUN composer global require hirak/prestissimo

RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql

RUN printf '[php]\ndate.timezone = "UTC"\n' > /usr/local/etc/php/conf.d/tzone.ini

WORKDIR "/var/www"

ARG LOCAL_USER_ID=1000
ARG LOCAL_GROUP_ID=1000

RUN set -xe \
    && addgroup -g $LOCAL_USER_ID user \
    && adduser -u $LOCAL_GROUP_ID -G user -h /home/user -s /bin/bash -D -g "" user
