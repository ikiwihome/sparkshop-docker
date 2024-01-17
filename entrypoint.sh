#!/bin/sh

nginx -t # 测试nginx配置文件
nginx # 启动nginx

redis-server "$@" & # 启动redis

echo "****Running on http://0.0.0.0:80"

php-fpm # 启动php-fpm