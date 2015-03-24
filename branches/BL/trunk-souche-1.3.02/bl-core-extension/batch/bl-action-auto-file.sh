#!/bin/sh
# ATTENTION : utiliser les sauts de ligne LINUX et pas WINDOWS

echo "[$(date +%d-%m-%Y\ %H\:%M\:%S)] Demarrage $0"

# éviter les erreurs "No entry for terminal type "unknown";" lors de l'exécution par cron
if [ ! $TERM ]; then
    export TERM=xterm
fi

if [ ! -z $(find /tmp -maxdepth 1 -iname '*.lock' -print -quit) ]
then
    echo Application en cours de maintenance.
else
    /usr/bin/php /var/www/pastell/bl-core-extension/batch/bl-action-auto-file.php file=$1 duree_attente=$2
fi
