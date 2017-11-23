FROM wordpress:4-fpm-alpine

RUN apk --update upgrade -q -U -a && rm -rf /var/cache/apk/*

RUN apk --update add \
    curl \
    && \
    rm -rf /var/cache/apk/*

RUN docker-php-ext-install pdo pdo_mysql

# installation scripts
COPY scripts/install.sh                 /usr/local/bin/
COPY scripts/docker-entrypoint.sh       /usr/local/bin/
COPY scripts/install.php                /tmp/

# plugins
COPY plugins/                           /tmp/plugins/

ENTRYPOINT ["install.sh"]
CMD ["php-fpm"]
