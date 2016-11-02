#!/usr/bin/env bash
yum -y update

# Install nano editor
yum -y install nano

# Install default web server (apache2)
yum -y install httpd

# Add epel packages to allow installation of PHP 5.6
rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
yum -y install php56w php56w-opcache
yum -y install php56w-xml
yum -y install php56w-pdo
# Steps needed to install Xdebug
yum -y install php56w-devel gcc gcc-c++ autoconf automake
yum -y install php-pear
pecl install Xdebug
cat > /etc/php.d/xdebug.ini << EOF
zend_extension=xdebug.so
xdebug.remote_enable=1
xdebug.remote_host=10.0.2.2
xdebug.remote_port=9000
EOF

# Give php.ini a timezone to stop php configuration moans
sed -i "s/;date.timezone =/date.timezone = Europe\/Berlin/g" /etc/php.ini

# Set up virtual hosts directories for apache
mkdir /etc/httpd/sites-available
mkdir /etc/httpd/sites-enabled

# Add basic virtual host file for our restaurant search
cat > /etc/httpd/sites-available/meetupeventsmap.local.conf << EOF
<VirtualHost *:80>
    ServerName meetupeventsmap.local
    ServerAlias meetupeventsmap.local

    DocumentRoot /vagrant/web
    <Directory /vagrant/web>
        # enable the .htaccess rewrites
        AllowOverride All
        Require all granted
    </Directory>

    # uncomment the following lines if you install assets as symlinks
    # or run into problems when compiling LESS/Sass/CoffeScript assets
    # <Directory /var/www/meetupeventsmap.local/html>
    #    Option FollowSymlinks
    # </Directory>

    ErrorLog /var/log/httpd/meetupeventsmap.local_error.log
    CustomLog /var/log/httpd/meetupeventsmap.local_access.log combined
</VirtualHost>
EOF

# Link available virtual host to enabled virtual host
ln -s /etc/httpd/sites-available/meetupeventsmap.local.conf /etc/httpd/sites-enabled/meetupeventsmap.local.conf

# Tell httpd.conf to look for config files in sites-enabled
echo "IncludeOptional sites-enabled/*.conf" >> /etc/httpd/conf/httpd.conf

# Disabling SELinux because the alternative is to spend a long time working out the appropriate policies
# and extensive experimentation like this: https://github.com/mitchellh/vagrant/issues/6970
# for our NFS directory.
setenforce 0
sed -i "s/SELINUX=enforcing/SELINUX=disabled/g" /etc/sysconfig/selinux

# Install and run database
yum -y install mariadb-server mariadb
systemctl start mariadb.service
systemctl enable mariadb.service

# Make a meetupeventsmap user + db and set root db password
mysql -u root -e "CREATE USER 'meetupeventsmap'@'localhost' IDENTIFIED BY 'meetupeventsmap';"
mysql -u root -e "GRANT USAGE ON *.* TO 'meetupeventsmap'@'localhost' IDENTIFIED BY 'meetupeventsmap' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;"
mysql -u root -e "CREATE DATABASE IF NOT EXISTS meetupeventsmap;"
mysql -u root -e "GRANT ALL PRIVILEGES ON meetupeventsmap.* TO 'meetupeventsmap'@'localhost';"
mysql -u root -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('devpassword');"

# Install phpmyadmin
yum -y install phpmyadmin

# Allow phpmyadmin to be accessed from any ip and not just localhost, for dev purposes
sed -i "s/Require ip 127.0.0.1/# Require ip 127.0.0.1/g" /etc/httpd/conf.d/phpMyAdmin.conf
sed -i "s/Require ip ::1/# Require ip ::1\\n       Require all granted/g" /etc/httpd/conf.d/phpMyAdmin.conf

# Allow the app/cache and app/logs to be writeable by both apache and the vagrant user
setfacl -R -m u:apache:rwX -m u:vagrant:rwX /vagrant/var/cache /vagrant/var/logs /vagrant/var/sessions
setfacl -dR -m u:apache:rwX -m u:vagrant:rwX /vagrant/var/cache /vagrant/var/logs /vagrant/var/sessions

# Create tables
# php /vagrant/app/console doctrine:schema:update --force

# Add apache to boot and start
systemctl enable httpd.service
service httpd start
