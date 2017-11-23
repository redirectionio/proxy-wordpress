FROM nginx:1.12-alpine

RUN apk --update upgrade -q -U -a && rm -rf /var/cache/apk/*

RUN apk --update add \
    bash \
    && \
    rm -rf /var/cache/apk/*

ARG RIO_UID=1000
RUN addgroup -g 1000 rio && \
    adduser -S -u $RIO_UID -h /home/rio -s /bin/bash rio rio

RUN mkdir /etc/nginx/sites-enabled

COPY conf/nginx.conf              /etc/nginx/
COPY conf/wordpress.conf          /etc/nginx/sites-enabled/
