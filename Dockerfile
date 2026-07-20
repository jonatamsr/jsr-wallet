FROM hyperf/hyperf:8.4-alpine-v3.22-swoole

ENV APP_ENV=prod \
    SCAN_CACHEABLE=(true) \
    TZ=UTC

WORKDIR /opt/www

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts -o

COPY . .
RUN composer dump-autoload -o && php bin/hyperf.php

EXPOSE 9501

ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "start"]
