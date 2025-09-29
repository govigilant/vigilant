FROM dunglas/frankenphp:php8.4

RUN apt-get update && apt-get install -y \
    bash \
    git \
    libzip-dev \
    libxml2-dev \
    libicu-dev \
    supervisor \
    nodejs \
    npm \
    unzip \
    icu-devtools \
    curl \
    cron \
    vim \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    sockets \
    pcntl \
    zip \
    exif \
    bcmath \
    intl

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY . /app
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --prefer-dist --no-interaction

RUN npm install
RUN npm run build

RUN mkdir -p /tmp/public/ \
    && cp -r /app/public/* /tmp/public/

RUN yes | php artisan octane:install --server=frankenphp

RUN /usr/bin/crontab /app/docker/crontab

ENV OCTANE_SERVER=frankenphp

ENTRYPOINT ["sh", "/app/docker/entrypoint.sh"]

