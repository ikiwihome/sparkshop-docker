#!/bin/sh

nginx -t # ����nginx�����ļ�
nginx # ����nginx

redis-server "$@" & # ����redis

echo "****Running on http://0.0.0.0:80"

php-fpm # ����php-fpm