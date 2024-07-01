FROM dunglas/frankenphp:latest-php8.3-alpine

RUN apk add --no-cache bash git linux-headers libzip-dev libxml2-dev supervisor nodejs npm chromium

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install pdo pdo_mysql sockets pcntl zip exif bcmath

# Redis
RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
      && pecl install redis \
      && docker-php-ext-enable redis \
      && apk del pcre-dev ${PHPIZE_DEPS} \
      && rm -rf /tmp/pear

COPY . /app
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --prefer-dist --no-interaction

RUN npm install
RUN npm run build
RUN npm install -g lighthouse
RUN npm install -g @lhci/cli@0.7.x

RUN mkdir /tmp/public/
RUN cp -r /app/public/* /tmp/public/

RUN yes | php artisan octane:install --server=frankenphp
RUN /usr/bin/crontab /app/docker/crontab

ENV CHROME_PATH=/usr/bin/chromium
ENV OCTANE_SERVER=frankenphp

ENTRYPOINT ["sh", "/app/docker/entrypoint.sh"]
