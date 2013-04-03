Form Builder
========================

This app is used for generating a survey form from a question bank.

## Setup

0. Checkout the source from Github.
1. Get the php package manager, Composer, by running `curl -sS https://getcomposer.org/installer | php` in the Form Builder directory.
2. Install all the dependencies using Composer: `php composer.phar install`
3. Setup a web server to serve out of `formbuilder/web/`, ensure that the web server user has write permissions to `app/cache/` and `app/logs/`.
4. Configure the database credentials, either using http://localhost/config.php or editing `app/config/parameters.yml` directly.
5. Create the database using `php app/console doctrine:database:create`
6. Create the database tables using `php app/console doctrine:schema:update --force`
7. The site should now be accessible.

## TODO

* Unhardcode `/app_dev.php/*` from all the API calls.
* Find out why going to `/app_dev.php` doesn't work.

## Database Encoding

Note that symfony's ORM engine, doctrine, doesn't provide a way to configure charset and collation on the databases it creates. The database default character encodings is used. So if we want UTF8 encoded databases, it has to be configured as mysql defaults (e.g.: collation\_server, character\_set\_server options).

