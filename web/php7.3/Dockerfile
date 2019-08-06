FROM alpine:3.10
LABEL Maintainer="AlicFeng <a@samego.com>" \
      Description="NP container with Nginx 1.16 & PHP-FPM 7.3 based on Alpine Linux"

# config env
ENV TIMEZONE=Asia/Shanghai \
    PHP_MEMORY_LIMIT=512M \
    MAX_UPLOAD=50M \
    PHP_MAX_FILE_UPLOAD=200 \
    PHP_MAX_POST=100M \
    PHPREDIS_VERSION=4.3.0

# install dependent app including php,nginx,composer
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories && \
    apk update && \

    # install evn dependence
    apk --no-cache add --virtual .build-deps \
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
    gd-dev gd && \

    # install php
    apk --no-cache add php7 php7-fpm \
    php7-mysqli php7-json php7-openssl php7-curl \
    php7-zlib php7-xml php7-phar php7-intl php7-dom \
    php7-xmlreader php7-ctype php7-session php7-mbstring \
    php7-gd php7-pdo php7-dev \

    # install nginx
    nginx \

    # install supervisor
    supervisor && \

    # install composer
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer  && \
    chmod a+x /usr/local/bin/composer && \

    # install phpredis
    git clone --depth=1 -b ${PHPREDIS_VERSION} https://github.com/phpredis/phpredis.git /tmp/php-redis && \
    cd /tmp/php-redis && \
    phpize &&  ./configure && make && make install && \
    echo 'extension=redis.so' >> /etc/php7/conf.d/redis.ini && \

    # clean
    apk del tzdata gcc g++ make automake autoconf git -f && \
    rm -rf /tmp/* /var/cache/apk/* && \

    # create www.www and configuration for application
    addgroup -S www && \
    adduser -D -S -h /var/cache/www -s /sbin/nologin -G www www && \
    mkdir -p /var/www/html /run/nginx && \
    chown -R www.www /run && \
    chown -R www.www /var/lib/nginx && \
    chown -R www.www /var/tmp/nginx && \
    chown -R www.www /var/log/nginx && \
    sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php7/php.ini && \
    sed -i "s|;date.timezone =.*|date.timezone = ${TIMEZONE}|" /etc/php7/php.ini && \
    sed -i "s|memory_limit =.*|memory_limit = ${PHP_MEMORY_LIMIT}|" /etc/php7/php.ini && \
    sed -i "s|upload_max_filesize =.*|upload_max_filesize = ${MAX_UPLOAD}|" /etc/php7/php.ini && \
    sed -i "s|max_file_uploads =.*|max_file_uploads = ${PHP_MAX_FILE_UPLOAD}|" /etc/php7/php.ini && \
    sed -i "s|post_max_size =.*|max_file_uploads = ${PHP_MAX_POST}|" /etc/php7/php.ini

# crontab
COPY crontab/default /var/spool/cron/crontabs/
RUN cat /var/spool/cron/crontabs/default >> /var/spool/cron/crontabs/root && \
    mkdir -p /var/log/cron && \
    touch /var/log/cron/cron.log

# configure php-fpm
COPY php/www.conf /etc/php7/php-fpm.d/www.conf
COPY php/php.ini /etc/php7/conf.d/php.ini

# configure supervisord
COPY supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# configure nginx
COPY nginx/conf.d/ /etc/nginx/conf.d/

# Add application
WORKDIR /var/www/html
COPY --chown=www src/ /var/www/

# create container start shell script
COPY start.sh /start.sh

# expose the port nginx | default 80 443
EXPOSE 80 443

# start nginx and php-fpm by supervisord
CMD ["/start.sh"]
