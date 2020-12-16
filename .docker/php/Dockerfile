FROM php:7.4-fpm

RUN apt-get update -yqq \
    && apt-get install -yqq --no-install-recommends vim curl debconf git apt-transport-https apt-utils \
    net-tools build-essential locales acl mailutils wget zip unzip \
    # gnupg gnupg1 gnupg2 \
    # install sshd
    # openssh-server \
    # install ping and netcat (for debugging xdebug connectivity)
    # iputils-ping netcat \
    # RabbitMQ
    librabbitmq-dev \
;

#RUN docker-php-ext-install \
#    # mbstring \
#    mysqli \
#    pdo pdo_mysql \
#;

RUN apt-get -y update \
    && apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN apt -qy install $PHPIZE_DEPS \
    && pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug

RUN pecl install amqp \
    && docker-php-ext-enable amqp

COPY php.ini /usr/local/etc/php/php.ini
COPY php-fpm-pool.conf /usr/local/etc/php/pool.d/www.conf
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

RUN groupadd dev -g 999
RUN useradd dev -g dev -d /home/dev -m
RUN passwd -d dev

RUN rm -rf /var/lib/apt/lists/*
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "es_ES.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

RUN echo "dev ALL=(ALL) ALL" > /etc/sudoers

WORKDIR /home/wwwroot/

EXPOSE 9000
CMD ["php-fpm"]
