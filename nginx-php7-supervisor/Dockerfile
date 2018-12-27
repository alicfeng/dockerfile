FROM alicfeng/centos:7-base

MAINTAINER AlicFeng <a@samego.com>

RUN groupadd www && useradd -M -g www -s /sbin/nologin www

WORKDIR /usr/local/src

ENV PHP_VERSION      7.1.4
ENV NGINX_VERSION    1.12.2
ENV OPENSSL_VERSION  1.1.0b
ENV PCRE_VERSION     8.41
ENV ZLIB_VERSION     1.2.11

COPY src/php-$PHP_VERSION.tar.gz php-$PHP_VERSION.tar.gz
COPY src/nginx-$NGINX_VERSION.tar.gz nginx-$NGINX_VERSION.tar.gz
COPY src/openssl-$OPENSSL_VERSION.tar.gz openssl-$OPENSSL_VERSION.tar.gz
COPY src/pcre-$PCRE_VERSION.tar.gz pcre-$PCRE_VERSION.tar.gz
COPY src/zlib-$ZLIB_VERSION.tar.gz zlib-$ZLIB_VERSION.tar.gz


RUN tar -xzvf php-$PHP_VERSION.tar.gz \
    && tar -zxvf pcre-$PCRE_VERSION.tar.gz \
    && tar -zxvf zlib-$ZLIB_VERSION.tar.gz \
    && tar -zxvf openssl-$OPENSSL_VERSION.tar.gz \
    && tar -zxvf nginx-$NGINX_VERSION.tar.gz

WORKDIR /usr/local/src/nginx-$NGINX_VERSION

# 编译安装nginx
RUN ./configure --prefix=/usr/local/nginx \
    --sbin-path=/usr/local/nginx/sbin/nginx \
    --conf-path=/usr/local/nginx/nginx.conf \
    --pid-path=/usr/local/nginx/nginx.pid \
    --user=www \
    --group=www \
    --with-http_ssl_module \
    --with-http_flv_module --with-http_mp4_module \
    --with-http_stub_status_module \
    --with-select_module \
    --with-poll_module \
    --error-log-path=/usr/local/nginx/logs/error.log \
    --http-log-path=/usr/local/nginx/logs/access.log \
    --with-pcre=/usr/local/src/pcre-$PCRE_VERSION \
    --with-zlib=/usr/local/src/zlib-$ZLIB_VERSION \
    --with-openssl=/usr/local/src/openssl-$OPENSSL_VERSION \
    --with-http_v2_module

RUN make && make install 
RUN ln -s /usr/local/nginx/sbin/nginx /usr/local/bin/nginx


WORKDIR /usr/local/src/php-$PHP_VERSION

# 编译安装php
RUN ./configure \ 
        --prefix=/usr/local/php \
        --with-curl \
        --with-freetype-dir \
        --with-gd \
        --with-jpeg-dir \
        --with-png-dir \
        --with-zlib-dir  \
        --with-freetype-dir \
        --with-gettext \
        --with-gmp \
        --with-gettext \
        --with-iconv-dir \
        --with-kerberos \
        --with-libdir=lib64 \
        --with-libxml-dir \
        --with-mysqli \
        --with-openssl \
        --with-pcre-regex \
        --with-pdo-mysql \
        --with-pdo-sqlite \
        --with-pear \
        --with-png-dir \
        --with-xmlrpc \
        --with-xsl \
        --with-zlib \
        --enable-fpm \
        --enable-bcmath \
        --enable-libxml \
        --enable-inline-optimization \
        --enable-gd-native-ttf \
        --enable-mbregex \
        --enable-mbstring \
        --enable-opcache \
        --enable-pcntl \
        --enable-shmop \
        --enable-soap \
        --enable-sockets \
        --enable-sysvsem \
        --enable-xml \
        --enable-zip \
    && make \
    # && make test \
    && make install


RUN cp php.ini-development /usr/local/php/lib/php.ini \
    && cp /usr/local/php/etc/php-fpm.conf.default /usr/local/php/etc/php-fpm.conf \
    && cp /usr/local/php/etc/php-fpm.d/www.conf.default /usr/local/php/etc/php-fpm.d/www.conf \
    && cp -R sapi/fpm/php-fpm /etc/init.d/php-fpm

RUN sed -i "s/nobody/www/g" /usr/local/php/etc/php-fpm.d/www.conf \
    && sed -i "s/nobody/www/g" /usr/local/nginx/nginx.conf

COPY index.php /www/default/index.php


# Installing supervisor
RUN yum install -y python-setuptools
RUN easy_install pip
RUN pip install supervisor

COPY supervisord.conf /etc/supervisor/supervisord.conf


WORKDIR /usr/local/src
RUN git clone https://github.com/phpredis/phpredis.git


WORKDIR /usr/local/src/phpredis
RUN /usr/local/php/bin/phpize
RUN ./configure --with-php-config=/usr/local/php/bin/php-config \
    && make && make install

ENV PATH /usr/local/nginx/sbin:$PATH
ENV PATH /usr/local/php/bin:$PATH

WORKDIR /
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod a+x /usr/local/bin/composer

# clean
RUN rm -rf /usr/local/src/*
RUN yum clean all && yum history new && rm -rf /tmp/*

RUN mkdir /usr/local/nginx/conf.d -p

EXPOSE 80 443

CMD ["/usr/bin/supervisord","-c", "/etc/supervisor/supervisord.conf"]