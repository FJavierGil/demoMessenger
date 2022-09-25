FROM php:8.1-fpm

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
    && rm -rf /var/lib/apt/lists/* \
;

RUN apt-get --no-install-recommends -y update \
    && apt-get --no-install-recommends install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get --no-install-recommends -qy install $PHPIZE_DEPS \
    && pecl install xdebug-3.1.5 \
    && docker-php-ext-enable xdebug

RUN pecl install amqp \
    && docker-php-ext-enable amqp

COPY php.ini /usr/local/etc/php/php.ini
COPY php-fpm-pool.conf /usr/local/etc/php/pool.d/www.conf
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY docker-entrypoint.sh /docker-entrypoint.sh

SHELL ["/bin/bash", "-o", "pipefail", "-c"]
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer && chmod +x /usr/local/bin/composer

RUN groupadd dev -g 999
RUN useradd dev -g dev -d /home/dev -m
RUN passwd -d dev
RUN chmod +x /docker-entrypoint.sh

RUN apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "es_ES.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

RUN echo "dev ALL=(ALL) ALL" > /etc/sudoers

WORKDIR /home/wwwroot/

EXPOSE 9000
CMD ["php-fpm"]

ENTRYPOINT ["/docker-entrypoint.sh"]