#!/usr/bin/env bash


function box_out()
{
  clear

  local s=("$@") b w
  for l in "${s[@]}"; do
    ((w<${#l})) && { b="$l"; w="${#l}"; }
  done
  tput setaf 2
  echo " -${b//?/-}-
| ${b//?/ } |"
  for l in "${s[@]}"; do
    printf '| %s%*s%s |\n' "$(tput setaf 2)" "-$w" "$l" "$(tput setaf 2)"
  done
  echo "| ${b//?/ } |
 -${b//?/-}-"
  tput sgr 0

  sleep 2
}


setenforce 0


box_out "Omaya v3 - Installer - Built 2022 - Rocky Linux"

rocky_version=$(cat /etc/rocky-release | awk '{print $4}' | cut -d '.' -f 1)

if [ "$rocky_version" != "8" ]; then


  clear

  echo "This installer only works for Rocky Linux release 8"
  echo "Terminated"

  exit


fi


box_out "Remove automatic update package if installed.."

dnf remove dnf-automatic -y


box_out "Installing basic tools.."

dnf update -y

sleep 3

dnf install net-snmp python2 epel-release vim nmap-ncat net-tools tuned dos2unix curl -y

dnf module install perl:5.26 -y


box_out "Install git .."

dnf install git -y

mkdir -p /var/www/

git clone https://work.amiruladib30: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@github.com/synchroweb/omayav3.git /var/www/omaya
# git clone https://work.amiruladib30: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@github.com/work-amiruladib30/omaya3-encoded.git /var/www/omaya

box_out "Update package repository list.."

cp /var/www/omaya/installer/packages/Omaya.repo /etc/yum.repos.d/

dnf update -y


box_out "Installing PHP and REDIS package.."

dnf module reset php redis -y

dnf module install php:remi-7.4 redis:remi-5.0 -y

dnf install php-pecl-radius php-pecl-swoole4 php-pecl-ssh2 php-ldap php-bcmath php-gd php-mysqlnd php-pecl-zip php-pecl-redis5 php-snmp tmux rsync -y


yes | cp /var/www/omaya/installer/packages/omaya-config/etc/php.d/10-ioncube.ini /etc/php.d/10-ioncube.ini
sleep 1
yes | cp /var/www/omaya/installer/packages/omaya-config/etc/php-zts.d/10-ioncube.ini /etc/php-zts.d/10-ioncube.ini
sleep 1

yes | cp /var/www/omaya/installer/packages/omaya-config/usr/lib64/php/modules/ioncube_loader_lin_7.4.so /usr/lib64/php/modules/
sleep 1

yes | cp /var/www/omaya/installer/packages/omaya-config/usr/lib64/php-zts/modules/ioncube_loader_lin_7.4_ts.so /usr/lib64/php-zts/modules/
sleep 1


box_out "Installing NGINX package.."

dnf install nginx -y


box_out "Installing Mariadb Server and Percona Toolkit package.."

dnf install MariaDB-server screen htop -y

dnf install /var/www/omaya/installer/packages/percona-toolkit-3.1.0-2.el8.x86_64.rpm -y



box_out "Installing and other configuration for MQTT"

dnf install php-pear -y
dnf --enablerepo=powertools install libedit-devel -y
pecl channel-update pecl.php.net

dnf install php-devel -y
dnf install mosquitto -y
dnf install php74-php-pecl-mosquitto php-pecl-mosquitto.x86_64 -y



box_out "Change performance profile.."

tuned-adm profile throughput-performance


box_out "Remove SWAP partition to avoid database performance issue.."

sed -i '/-swap/d' /etc/fstab


box_out "Apply default setting for performance.."

tar xfz /var/www/omaya/installer/packages/omaya-config.tgz -C /

yes | cp -rf /var/www/omaya/installer/packages/nginx.conf /etc/nginx/
yes | cp -rf /var/www/omaya/installer/packages/default.conf /etc/nginx/conf.d/
yes | cp -rf /var/www/omaya/installer/packages/my.cnf /etc/



yes | cp /var/www/omaya/installer/packages/omaya-config/etc/php.d/10-ioncube.ini /etc/php.d/10-ioncube.ini
sleep 1
yes | cp /var/www/omaya/installer/packages/omaya-config/etc/php-zts.d/10-ioncube.ini /etc/php-zts.d/10-ioncube.ini
sleep 1

yes | cp /var/www/omaya/installer/packages/omaya-config/usr/lib64/php/modules/ioncube_loader_lin_7.4.so /usr/lib64/php/modules/
sleep 1

yes | cp /var/www/omaya/installer/packages/omaya-config/usr/lib64/php-zts/modules/ioncube_loader_lin_7.4_ts.so /usr/lib64/php-zts/modules/
sleep 1

dnf install @minimal-environment -y

box_out "Installing Omaya core system.."

systemctl start mariadb


sleep 2


mysql -u root -e "CREATE DATABASE omaya;"


mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql



box_out "Enable service to be run during boot time.."

chmod -x /var/www/omaya/system/services/*

cp -rf /var/www/omaya/system/services/* /usr/lib/systemd/system/


sleep 2


# update daemon to remove private /tmp directory

sed -i 's/PrivateTmp=true/PrivateTmp=false/' /usr/lib/systemd/system/mariadb.service
sed -i 's/PrivateTmp=true/PrivateTmp=false/' /usr/lib/systemd/system/php-fpm.service
sed -i 's/PrivateTmp=true/PrivateTmp=false/' /usr/lib/systemd/system/redis.service
sed -i 's/PrivateTmp=yes/PrivateTmp=no/' /usr/lib/systemd/system/chronyd.service


systemctl daemon-reload

systemctl enable mariadb redis php-fpm nginx omaya_service omaya_extract omaya_prereport_processor mosquitto omaya_job omaya_agent_huawei_ble omaya_agent_huawei_wifi omaya_workspace_service




box_out "Change timezone to UTC.."

rm -rf /etc/localtime

ln -sf /usr/share/zoneinfo/UTC /etc/localtime



box_out "Open port for http, https communication.."

firewall-cmd --permanent --add-service={http,https}
firewall-cmd --permanent --add-port=9000/udp
firewall-cmd --permanent --add-port=9001/udp
firewall-cmd --permanent --add-port=3306/tcp


# update some config such installed time for default

systemctl restart redis mariadb mosquitto nginx php-fpm

sleep 5



box_out "Change file permission to allow appropriate service running.."


chown nginx:nginx -R /var/lib/php
chmod 755 -R /var/lib/php


dos2unix /var/www/omaya/system/schedule/scheduler.cron
crontab /var/www/omaya/system/schedule/scheduler.cron

box_out "Install and config Composer .."
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php

sleep 2

php -r "unlink('composer-setup.php');"

sleep 1

mv composer.phar /usr/bin/composer
export COMPOSER_ALLOW_SUPERUSER=1;


box_out "Install Package for Omaya .."
composer install --working-dir=/var/www/omaya/


box_out "Config Omaya env .."
cp /var/www/omaya/.env.example /var/www/omaya/.env



box_out "Database migration for first install .."


mysql -u root -e "CREATE USER 'omaya_main'@'localhost' IDENTIFIED BY PASSWORD '';"
mysql -u root -e "GRANT ALL PRIVILEGES ON omaya.* TO 'omaya_main'@'%';"


php /var/www/omaya/artisan migrate:fresh --seed

mysql omaya < /var/www/omaya/installer/packages/omaya_oui_standards.sql

#sudo chown -R root:root /var/www/omaya/
#sudo chown -R nginx:nginx /var/www/omaya/storage/
#sudo chmod -R 755 /var/www/omaya/storage/
#sudo chown -R nginx:nginx /var/www/omaya/bootstrap/



box_out "Complete. Reboot.."

sleep 3

reboot
