FROM php:5.6-fpm-alpine

RUN apk --no-cache add curl git

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV COMPOSER_VERSION 1.3.2
ENV DB_HOST localhost
ENV DB_PORT 3306
ENV DB_NAME formbuilder
ENV DB_USER formbuilder
ENV DB_PASSWORD password
ENV MAIL_HOST 127.0.0.1
ENV MAIL_USER null
ENV MAIL_PASSWORD null
ENV SECRET randomstrings


RUN echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/memory-limit.ini" \
 && echo "date.timezone=${PHP_TIMEZONE:-UTC}" > "$PHP_INI_DIR/conf.d/date_timezone.ini"

RUN curl -s -f -L -o /tmp/installer.php https://raw.githubusercontent.com/composer/getcomposer.org/5fd32f776359b8714e2647ab4cd8a7bed5f3714d/web/installer \
 && php -r " \
    \$signature = '55d6ead61b29c7bdee5cccfb50076874187bd9f21f65d8991d46ec5cc90518f447387fb9f76ebae1fbbacf329e583e30'; \
    \$hash = hash('SHA384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
        unlink('/tmp/installer.php'); \
        echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
        exit(1); \
    }" \
 && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
 && rm /tmp/installer.php \
 && composer --ansi --version --no-interaction

ADD . /app

WORKDIR /app

RUN cp app/config/parameters.yml.template app/config/parameters.yml \
  && composer install

ENTRYPOINT ["scripts/docker-entrypoint.sh"]

CMD ["php-fpm"]
