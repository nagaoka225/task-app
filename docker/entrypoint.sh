#!/bin/sh
set -e

# php-fpm をバックグラウンドで起動
php-fpm -D

# nginx の設定テスト
nginx -t

# nginx をフォアグラウンドで起動
exec nginx -g 'daemon off;'
