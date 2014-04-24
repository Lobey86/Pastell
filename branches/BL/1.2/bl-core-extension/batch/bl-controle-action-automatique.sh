#!/bin/sh
# ATTENTION : utiliser les sauts de ligne LINUX et pas WINDOWS
#
# Ce script permet de vérifier que le script des actions automatiques se déclenche correctement.
# Envoi un mail si le fichier de log des actions automatiques n'a pas été modifié pendant plus de 30 minutes.
# A executer dans un cron.
#
# attente en secondes. 30 min = 1800 sec
ATTENTE=1800

START=$(date -r /var/log/upstart/bl-action-auto.log '+%s')

END=$(date '+%s')
DUREE_SECONDE=$(( $END - $START ))

h=$(($DUREE_SECONDE/3600))
m=$(($DUREE_SECONDE%3600/60))
s=$(($DUREE_SECONDE%60))

HOSTNAME=`hostname -s`

DUREE_LISIBLE=$(printf "%dh %dm %ds\n" $h $m $s)
if [ "$ATTENTE" -lt "$DUREE_SECONDE" ]
then    
mail -s "[ALERTE] $HOSTNAME : BusBL - Arret du script des actions automatiques" adminbus << MAIL_CORPS
Le script des actions automatiques semble ne plus s'exécuter sur le serveur $HOSTNAME : aucune écriture dans le fichier de log depuis plus de $DUREE_LISIBLE. 

Merci de vérifier son bon fonctionnement.

Cordialement.

--
Ce mail vous est envoyé automatiquement par le serveur $HOSTNAME.
MAIL_CORPS
fi