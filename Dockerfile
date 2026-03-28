FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    curl \
    vim \
    unzip \
    nginx \
    supervisor \
    dcron \
    nodejs \
    npm \
    libzip-dev \
    libxml2-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    pango \
    linux-headers \
    python3 \
    py3-pip \
    py3-cffi \
    py3-brotli \
    && pip3 install --break-system-packages WeasyPrint

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        sockets \
        pcntl \
        zip \
        exif \
        bcmath \
        intl \
        gd

RUN apk add --no-cache --virtual .build-deps autoconf g++ make \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

COPY . /app
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --prefer-dist --no-interaction

RUN npm install
RUN npm run build

RUN mkdir -p /tmp/public/ \
    && cp -r /app/public/* /tmp/public/

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php-fpm.ini /usr/local/etc/php/conf.d/zzz-fpm-overrides.ini

RUN /usr/bin/crontab /app/docker/crontab

ENTRYPOINT ["sh", "/app/docker/entrypoint.sh"]
