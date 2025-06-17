#!/bin/bash
if [ "$EUID" -ne 0 ]
  then echo "Please run as root"
  exit
fi

apt-get install pkg-config libmagickwand-dev -y
cd /tmp
wget https://pecl.php.net/get/imagick-3.4.4.tgz
tar xvzf imagick-3.4.4.tgz
cd imagick-3.4.4
phpize
./configure
make install
rm -rf /tmp/imagick-3.4.4*
echo extension=imagick.so >> /etc/php/7.4/cli/php.ini
echo extension=imagick.so >> /etc/php/7.4/fpm/php.ini
service php7.4-fpm restart
service nginx restart