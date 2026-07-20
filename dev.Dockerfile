FROM hyperf/hyperf:8.4-alpine-v3.21-swoole

ARG UID=1000
ARG GID=1000

ENV APP_ENV=dev \
    SCAN_CACHEABLE=(false) \
    TZ=UTC

RUN addgroup -g ${GID} application && \
    adduser -S -D -u ${UID} -G application -s /bin/ash -h /home/application application

USER application

WORKDIR /opt/www

COPY . /opt/www
RUN composer install --no-scripts

EXPOSE 9501

ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "start"]
