#!/usr/bin/env bash
echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' | tee /etc/apt/sources.list.d/newrelic.list

wget -O- https://download.newrelic.com/548C16BF.gpg | apt-key add -

apt-get update

apt-get install -y newrelic-php5
NR_INSTALL_SILENT=1 NR_INSTALL_KEY=351ecee7da11d3cb8b18bc89a9306c2e8aa03f54  newrelic-install install

VERSION=$1
if (( $VERSION == 5 ))
then
    sed -i -e 's/appname = "PHP Application"/appname = "SMORES Phalcon API"/g' /etc/php5/mods-available/newrelic.ini
    sed -i -e 's/appname = "PHP Application"/appname = "SMORES Phalcon API"/g' /etc/php5/cli/conf.d/20-newrelic.ini
    sed -i -e 's/appname = "PHP Application"/appname = "SMORES Phalcon API"/g' /etc/php5/fpm/conf.d/20-newrelic.ini
    sed -i -e 's/newrelic.license = ""/newrelic.license = "351ecee7da11d3cb8b18bc89a9306c2e8aa03f54"/g' /etc/php5/fpm/conf.d/20-newrelic.ini
    rm /etc/php5/cli/conf.d/newrelic.ini /etc/php5/fpm/conf.d/newrelic.ini #removing repeated files?
    service php5-fpm restart
else
    sed -i -e 's/appname = "PHP Application"/appname = "SMORES Phalcon API"/g' /etc/php/7.0/mods-available/newrelic.ini
    sed -i -e 's/appname = "PHP Application"/appname = "SMORES Phalcon API"/g' /etc/php/7.0/cli/conf.d/20-newrelic.ini
    sed -i -e 's/appname = "PHP Application"/appname = "SMORES Phalcon API"/g' /etc/php/7.0/fpm/conf.d/20-newrelic.ini
    sed -i -e 's/newrelic.license = ""/newrelic.license = "351ecee7da11d3cb8b18bc89a9306c2e8aa03f54"/g' /etc/php/7.0/fpm/conf.d/20-newrelic.ini
    rm /etc/php/7.0/cli/conf.d/newrelic.ini /etc/php/7.0/fpm/conf.d/newrelic.ini #removing repeated files?
    service php7.0-fpm restart
fi

# clear out any left over files
rm -rf /tmp/*