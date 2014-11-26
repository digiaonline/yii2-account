#!/bin/bash

# download the package lists from the repositories
sudo apt-get update

# --- miscellaneous ----

sudo apt-get install -y python-software-properties
sudo apt-get install -y curl
sudo apt-get install -y git-core
sudo apt-get install -y screen
sudo apt-get install -y vim

# --- apache ---

# install packages
sudo apt-get install -y apache2

# enable apache modules
sudo a2enmod rewrite
sudo a2enmod setenvif
sudo a2enmod autoindex
sudo a2enmod deflate
sudo a2enmod filter
sudo a2enmod headers
sudo a2enmod expires

# --- php ---

# install php 5.4
sudo add-apt-repository ppa:ondrej/php5-oldstable
sudo apt-get update
sudo apt-get install -y php5

# install php packages
sudo apt-get install -y php5-curl
sudo apt-get install -y php5-mcrypt
sudo apt-get install -y php5-mysql

# --- mysql ---

# configure the installer
echo mysql-server mysql-server/root_password select "root" | debconf-set-selections
echo mysql-server mysql-server/root_password_again select "root" | debconf-set-selections

# install packages
sudo apt-get install -y mysql-server-5.5

# create database
mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS account_test CHARACTER SET utf8;"

# --- composer ---

cd /vagrant && sudo curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer global require "fxp/composer-asset-plugin:1.0.0-beta4"

# install dependencies through composer
composer install --dev --prefer-source

# --- codeception ---

cd /vagrant/tests && sudo wget http://codeception.com/codecept.phar
chmod +x codecept.phar
mv codecept.phar /usr/local/bin/codecept

# --- configure apache ---

# remove default webroot
sudo rm -rf /var/www

# symlink project as webroot
sudo ln -fs /vagrant/tests/app/web /var/www

# change apache to run under the 'vagrant' user
sudo rm -rf /var/lock/apache2
sudo sed -i 's/www-data/vagrant/g' /etc/apache2/envvars

# setup hosts
sudo cp /vagrant/scripts/assets/apache-sites /etc/apache2/sites-available/default

# restart apache
sudo service apache2 restart
