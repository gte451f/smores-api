#!/bin/bash
# A shell script used to setup the smores database with fake data
# assumes a local mysql server is running
# passwords stored in config file
#
# Usage: ./init-data.sh
# ---------------------------------------------------------------------

echo "Reading config/base.cfg"
source config/base.cfg
echo "Server:" $DEVSRV
echo "DB Name:" $DEVDB

# backup production
echo "Backup database...$DEVDB"
mysqldump --host=$DEVSRV --user=$DEVUSER  --password=$DEVPASS $DEVDB > /tmp/smore-backup.sql 

# rebuild database from scratch
SQL='DROP DATABASE IF EXISTS `'$DEVDB'`;'
echo "$SQL"
echo $SQL > /tmp/tmp.sql

# create DEV database
SQL='CREATE DATABASE `'$DEVDB'`;'
echo "$SQL"
echo $SQL >> /tmp/tmp.sql
#execute command
mysql --host=$DEVSRV --user=$DEVUSER --password=$DEVPASS $DEVDB < /tmp/tmp.sql


# build schema
echo "Rebuild Schema w/ demo data"
mysql --host=$DEVSRV --user=$DEVUSER --password=$DEVPASS $DEVDB < sql/smores-demo.sql


echo "Make last minute adjustments"
mysql --host=$DEVSRV --user=$DEVUSER --password=$DEVPASS $DEVDB < sql/adjustments.sql



