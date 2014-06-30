#!/bin/sh
# ATTENTION : utiliser les sauts de ligne LINUX et pas WINDOWS

echo [$(date +%d-%m-%Y\ %H\:%M\:%S)] Demarrage $0
if [ ! -z $(find /tmp -maxdepth 1 -iname '*.lock' -print -quit) ]
then
    echo Application en cours de maintenance.
else
    sudo -H -u www-data /usr/bin/php /var/www/pastell/bl-core-extension/batch/bl-action-connecteur-global.php id_connecteur=signaturebl action=update-cache-circuits
fi
echo [$(date +%d-%m-%Y\ %H\:%M\:%S)] Terminaison $0
