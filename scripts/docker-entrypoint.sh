#!/bin/sh
set -e

config=/app/app/config/parameters.yml

echo -ne "Configure database connection..."
if [ -f $config ]
then
    sed -Ei "s/database_host:.*/database_host: $DB_HOST/" $config
    sed -Ei "s/database_port:.*/database_port: $DB_PORT/" $config
    sed -Ei "s/database_name:.*/database_name: $DB_NAME/" $config
    sed -Ei "s/database_user:.*/database_user: $DB_USER/" $config
    sed -Ei "s/database_password:.*/database_password: $DB_PASSWORD/" $config
    sed -Ei "s/mailer_host:.*/mailer_host: $MAIL_HOST/" $config
    sed -Ei "s/mailer_user:.*/mailer_user: $MAIL_USER/" $config
    sed -Ei "s/mailer_password:.*/mailer_password: $MAIL_PASSWORD/" $config
    sed -Ei "s/secret:.*/secret: $secret/" $config
    echo OK
else
    echo "Not exist. SKIP"
fi

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm "$@"
fi

exec "$@"
