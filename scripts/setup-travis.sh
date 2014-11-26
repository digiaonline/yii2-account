#!/bin/bash

# create the database
mysql -e 'CREATE DATABASE account_test CHARACTER SET utf8;'

# configure codeception
export DB_HOST=127.0.0.1 DB_USER=root DB_PASS=''
erb tests/app/config/env.php.erb > tests/app/config/env.php
erb tests/codeception.yml.erb > tests/codeception.yml
export BASE_URL='http://localhost:8000/'
erb tests/codeception/acceptance.suite.yml.erb > tests/codeception/acceptance.suite.yml

# install dependencies through composer
composer self-update
composer install --no-interaction --prefer-source

# start the web server
php -S localhost:8000 -t tests/app/web/ > /dev/null 2>&1 &

# install codeception
wget http://codeception.com/codecept.phar
