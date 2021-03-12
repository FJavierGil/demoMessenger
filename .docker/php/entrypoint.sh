#!/usr/bin/env bash
set -e

cd /home/wwwroot/aos/
composer update -n

exec "$@"