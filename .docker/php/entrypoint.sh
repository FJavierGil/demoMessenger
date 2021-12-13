#!/bin/bash
set -e

cd /home/wwwroot/aos/
composer update -n
chown -hR dev:dev /home/wwwroot/aos/
# chown -hR www-data:www-data /home/wwwroot/aos/var/

# set permissions for future files and folders
setfacl -dR -m u:"www-data":rwX -m u:"$(whoami)":rwX /home/wwwroot/aos/var/

# set permissions on the existing files and folders
setfacl -R -m u:"www-data":rwX -m u:"$(whoami)":rwX /home/wwwroot/aos/var/

exec "$@"