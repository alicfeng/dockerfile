FROM ubuntu:18.04
LABEL Maintainer="AlicFeng <a@samego.com>" \
      Description="PHP7.2+Nginx+ICE3.7.3+Supervisor container based on ubuntu:18.04 LTS"

# env list
ENV DEBIAN_FRONTEND=noninteractive \
    TIMEZONE=Asia/Shanghai \
    LANG=C.UTF-8

# alarm2(alarm package)
ADD package/alarm2.tar.gz /usr/
# tonyenc(php source encrypt)
ADD package/tonyenc.tar.gz /usr/local/src/
# redis
ADD package/redis-5.1.1.tgz /usr/local/src/
# composer (could choose curl ...)
COPY package/composer.phar /usr/local/bin/composer
# swoole
ADD package/swoole-src-4.3.1.tar.gz /usr/local/src/
# crontab
COPY crontab/cron /etc/cron.d/cron
# sources.list
COPY package/aliyun.sources.list /etc/apt/sources.list
# container start default script
COPY docker-entrypoint.sh /usr/local/bin/

RUN apt-get update \
    ## base dependence
    && apt-get install \
        curl \
        tzdata \
        jpegoptim \
        libjpeg-turbo-progs \
        pngcrush \
        optipng \
        apngopt \
        pngnq \
        pngquant \
        cron \
        gnupg2 -y \
    && apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv B6391CB2CFBA643D \
    && echo "deb http://download.zeroc.com/Ice/3.7/ubuntu18.04 stable main" >> /etc/apt/sources.list.d/ice.list \
    && apt-get update \

    # app tool
    && apt-get install \
        # supervisor
        supervisor \
        # nginx
        nginx \
        # php
        php7.2-cli \
        php7.2-fpm \
        php7.2-json \
        php7.2-intl \
        php7.2-curl \
        php7.2-mysql \
        php7.2-gd \
        php7.2-opcache \
        php7.2-soap \
        php7.2-zip \
        php7.2-mbstring \
        php7.2-bcmath \
        php7.2-xmlrpc \
        php7.2-xsl \
        php7.2-bz2 \
        php7.2-dev \
        php-pear \
        php-apcu \
        # php ice ext
        php-zeroc-ice \
#        git \
#        vim \
        -y \
    && pecl channel-update pecl.php.net \

    # install composer
    # && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer \

    # install swoole by source
    && cd /usr/local/src/swoole-src-4.3.1 \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && echo extension=swoole.so >> /etc/php/7.2/mods-available/swoole.ini \
    && ln -s /etc/php/7.2/mods-available/swoole.ini /etc/php/7.2/cli/conf.d/swoole.ini \
    && ln -s /etc/php/7.2/mods-available/swoole.ini /etc/php/7.2/fpm/conf.d/swoole.ini \

    # install tonyenc by source
    && cd /usr/local/src/tonyenc \
    && phpize \
    && ./configure \
    && make \
    && mv modules/tonyenc.so /usr/lib/php/20170718/tonyenc.so \
    && echo extension=tonyenc.so >> /etc/php/7.2/mods-available/tonyenc.ini \
    && ln -s /etc/php/7.2/mods-available/tonyenc.ini /etc/php/7.2/cli/conf.d/tonyenc.ini \
    && ln -s /etc/php/7.2/mods-available/tonyenc.ini /etc/php/7.2/fpm/conf.d/tonyenc.ini \

    # install redis by source
    && cd /usr/local/src/redis-5.1.1 \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && echo extension=redis.so >> /etc/php/7.2/mods-available/redis.ini \
  #  && ln -s /etc/php/7.2/mods-available/redis.ini /etc/php/7.2/cli/conf.d/redis.ini \
  #  && ln -s /etc/php/7.2/mods-available/redis.ini /etc/php/7.2/fpm/conf.d/redis.ini \


    # cron
    && chmod 0644 /etc/cron.d/cron \
    && crontab /etc/cron.d/cron \
    && touch /var/log/cron.log \

    # refresh load share library and env handler
    && ldconfig \
    && chmod +x /usr/local/bin/composer \
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
#    && echo "set fileencodings=utf-8" >> /etc/vim/vimrc \
#    && echo "set termencoding=utf-8" >> /etc/vim/vimrc \
#    && echo "set encoding=utf-8" >> /etc/vim/vimrc \

    # clean
    && apt-get auto-remove php7.2-dev curl tzdata -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/local/src/*

# supervisor
COPY supervisord/supervisord.conf /etc/supervisor/supervisord.conf
COPY supervisord/conf.d /etc/supervisor/conf.d
# nginx
COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/conf.d /etc/nginx/conf.d
COPY app/index.php /var/www/html/index.php
# php
COPY php/cli/php.ini /etc/php/7.2/cli/php.ini
COPY php/fpm/php.ini /etc/php/7.2/fpm/php.ini
COPY php/fpm/php-fpm.conf /etc/php/7.2/fpm/php-fpm.conf
COPY php/fpm/www.conf /etc/php/7.2/fpm/pool.d/www.conf

EXPOSE 80 443
WORKDIR /var/www/
CMD ["docker-entrypoint.sh"]

#ice-doc https://doc.zeroc.com/ice/3.7/release-notes/using-the-linux-binary-distributions#id-.UsingtheLinuxBinaryDistributionsv3.7-deb
