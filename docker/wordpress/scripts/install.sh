#!/bin/bash

docker-entrypoint.sh "$@"

# install wordpress
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
php wp-cli.phar core install --path=/var/www/html --url=http://redirection-io.dev:8000 --title=redirectionio --admin_user=admin --admin_password=password --admin_email=admin@redirection.io --allow-root && \
rm wp-cli.phar

# install wordpress plugin
rm -rf /var/www/html/wp-content/plugins/
mv /tmp/plugins/ /var/www/html/wp-content/
php /tmp/install.php
rm /tmp/install.php

exec "$@"
