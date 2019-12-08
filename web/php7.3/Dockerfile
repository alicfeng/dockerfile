FROM alpine:3.10
LABEL Maintainer="AlicFeng <a@samego.com>" \
      Description="NP container with Nginx 1.16 & PHP-FPM 7.3 based on Alpine Linux"

# env list
ENV TIMEZONE=Asia/Shanghai \
    PHPREDIS_VERSION=4.3.0

# crontab
COPY crontab/default /var/spool/cron/crontabs/
# create container entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

# install dependent app including php,nginx,composer
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories && \
    apk update && \
    # install evn dependence
    apk --no-cache add --virtual .build-deps \
        tzdata \
        gcc g++  \
        libc-dev \
        make automake autoconf \
        openssl-dev \
        pcre-dev \
        zlib-dev \
        linux-headers \
        curl curl-dev \
        git \
        gnupg \
        libxslt-dev \
        geoip-dev \
        pcre-dev \
        zlib-dev \
        freetype \
        libpng \
        libjpeg-turbo \
        freetype-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        gd-dev \
        gd \
        php7-dev \
    # install php nginx supervisor
    && apk --no-cache add php7 php7-fpm \
        php7-mysqli php7-json php7-openssl php7-curl \
        php7-zlib php7-xml php7-phar php7-intl php7-dom \
        php7-xmlreader php7-ctype php7-session php7-mbstring \
        php7-gd php7-pdo \
        nginx \
        supervisor  \
    # install composer
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod a+x /usr/local/bin/composer \
    # install phpredis
    && git clone --depth=1 -b ${PHPREDIS_VERSION} https://github.com/phpredis/phpredis.git /tmp/php-redis \
    && cd /tmp/php-redis \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && echo 'extension=redis.so' >> /etc/php7/conf.d/redis.ini \
    # timezone
    && ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo $TIMEZONE > /etc/timezone \
    # create www.www and configuration for application
    && addgroup -S www \
    && adduser -D -S -h /var/cache/www -s /sbin/nologin -G www www \
    && mkdir -p /var/www/html /run/nginx \
    && chown -R www.www /run \
    && chown -R www.www /var/lib/nginx \
    && chown -R www.www /var/tmp/nginx \
    && chown -R www.www /var/log/nginx \
    && cat /var/spool/cron/crontabs/default >> /var/spool/cron/crontabs/root \
    && mkdir -p /var/log/cron \
    && touch /var/log/cron/cron.log \
    # clean
    && apk del .build-deps -f \
    && rm -rf /tmp/* /var/cache/apk/*

# configure php-fpm
COPY php/php-fpm.d/www.conf /etc/php7/php-fpm.d/www.conf
COPY php/conf.d/php.ini /etc/php7/conf.d/php.ini
# configure supervisord
COPY supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
# configure nginx
COPY nginx/conf.d/ /etc/nginx/conf.d/
# add application
COPY --chown=www src/ /var/www/

WORKDIR /var/www/html
EXPOSE 80 443
CMD ["docker-entrypoint.sh"]
