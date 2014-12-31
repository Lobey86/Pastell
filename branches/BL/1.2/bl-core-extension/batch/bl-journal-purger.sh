#!/bin/bash

# éviter les erreurs "No entry for terminal type "unknown";" lors de l'exécution par cron
if [ ! $TERM ]; then
    export TERM=xterm
fi

echo "[$(date +%d-%m-%Y\ %H\:%M\:%S)] $0 - démarrage"

sudo -H -u www-data /usr/bin/php /var/www/pastell/bl-core-extension/batch/bl-journal-purger.php nb_jours_conservation=60

echo "[$(date +%d-%m-%Y\ %H\:%M\:%S)] $0 - fin"

