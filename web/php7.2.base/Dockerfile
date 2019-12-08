FROM alpine:3.8
LABEL Maintainer="AlicFeng <a@samego.com>" \
      Description="web container with Nginx 1.14 & PHP-FPM 7.2 based on Alpine Linux"

# crontab
COPY crontab/default /var/spool/cron/crontabs/

# config env
ENV TIMEZONE=Asia/Shanghai \
    PHP_MEMORY_LIMIT=512M \
    MAX_UPLOAD=50M \
    PHP_MAX_FILE_UPLOAD=200 \
    PHP_MAX_POST=100M

# install dependent app including php,nginx,composer
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories && \
    apk update && \
    # install php
    apk --no-cache add --virtual .build-deps \
        php7 php7-fpm \
        nginx \
        supervisor \
        tzdata  \
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
    && sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php7/php.ini \
    && sed -i "s|;date.timezone =.*|date.timezone = ${TIMEZONE}|" /etc/php7/php.ini \
    && sed -i "s|memory_limit =.*|memory_limit = ${PHP_MEMORY_LIMIT}|" /etc/php7/php.ini \
    && sed -i "s|upload_max_filesize =.*|upload_max_filesize = ${MAX_UPLOAD}|" /etc/php7/php.ini \
    && sed -i "s|max_file_uploads =.*|max_file_uploads = ${PHP_MAX_FILE_UPLOAD}|" /etc/php7/php.ini \
    && sed -i "s|post_max_size =.*|max_file_uploads = ${PHP_MAX_POST}|" /etc/php7/php.ini
    && cat /var/spool/cron/crontabs/default >> /var/spool/cron/crontabs/root \
    && mkdir -p /var/log/cron \
    && touch /var/log/cron/cron.log

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
