#!/bin/sh

# Exécution des scripts qui se trouvent dans le répertoire du script courant
# Les scripts exécutés doivent se nommer : BLScriptUpdateVersion_XX.php (ex: BLScriptUpdateVersion_01.php, BLScriptUpdateVersion_02.php)
# Ils sont exécutés dans l'ordre de leur numéro
# S'il existe qu'un seul script, il doit quand-même être numéroté (ex: BLScriptUpdateVersion_01.php)

echo "====> Montée de version des données..."

for i in $(find $(dirname $0)/ -name BLScriptUpdateVersion*.php -type f | sort) ;
do
    if [ -f "$i" ]; then
        echo "--> Execution du script : $i"
        sudo -H -u www-data /usr/bin/php "$i"
    fi
done
