#!/bin/bash

# configure codeception
erb tests/app/config/env.php.erb > tests/app/config/env.php
erb tests/codeception.yml.erb > tests/codeception.yml
erb tests/codeception/acceptance.suite.yml.erb > tests/codeception/acceptance.suite.yml

# install dependencies through composer
composer self-update && composer --version
composer global require "fxp/composer-asset-plugin:1.0.0-beta3"
composer install --no-interaction --prefer-source

# start the web server
php -S localhost:8000 -t tests/app/web/ > /dev/null 2>&1 &

# install codeception
wget http://codeception.com/codecept.phar

# create the database
mysql -e 'CREATE DATABASE account_test CHARACTER SET utf8;'
