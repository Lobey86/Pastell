#!/bin/sh
# ATTENTION : utiliser les sauts de ligne LINUX et pas WINDOWS

# attente en secondes. 30 min = 1800 sec
ATTENTE=1800

echo "[$(date +%d-%m-%Y\ %H\:%M\:%S)] Demarrage $0"

PROCHAINE=$(date -d "+$ATTENTE seconds" +%d-%m-%Y\ %H\:%M\:%S)
START=$(date +%s)
if [ ! -z $(find /tmp -maxdepth 1 -iname '*.lock' -print -quit) ]
then
    echo Application en cours de maintenance.
else
    sudo -H -u www-data /usr/bin/php /var/www/pastell/bl-core-extension/batch/bl-action-auto.php
fi
END=$(date +%s)
DIFF=$(( $END - $START ))

echo -n "[$(date +%d-%m-%Y\ %H\:%M\:%S)] Duree execution : $DIFF"s " - Prochaine : "

if [ "$DIFF" -lt "$ATTENTE" ]
then
  echo $PROCHAINE
  sleep $(( $ATTENTE - $DIFF ))
else
  echo "maintenant"
fi
