#!/bin/sh

# Exécution des scripts qui se trouvent dans le répertoire du script courant.
# Le premier des scripts à exécuté est mentionné dans le fichier de configuration. Si non défini, tous sont exécutés.
# Les scripts exécutés doivent se nommer : BLScriptUpdateVersion_XX.php (ex: BLScriptUpdateVersion_01.php, BLScriptUpdateVersion_02.php, BLScriptUpdateVersion_01.1.php)
# Ils sont exécutés dans l'ordre alpha-numérique.
# S'il existe qu'un seul script, il doit quand-même être numéroté (ex: BLScriptUpdateVersion_01.php)
# Il faut multiplier les scripts si entre 2 scripts le contexte ou du cache doivent être réinitialisés.

echo "====> Montée de version des données..."

. $(dirname $0)/busbl_updatedata.cfg

TODOLIST_FILEPATH="/tmp/todolist.txt"

if [ -f $TODOLIST_FILEPATH ]; then
    rm $TODOLIST_FILEPATH;
fi

for script in $(find $(dirname $0)/ -name "BLScriptUpdateVersion*.php" -type f | sort) ;
do
    if [ ! "$(basename $script)" \< "$script_premier" ]; then
        echo "--> Exécution du script : $script"
        sudo -H -u www-data /usr/bin/php "$script" todolist_filepath=$TODOLIST_FILEPATH
    fi
done

if [ -f $TODOLIST_FILEPATH ]; then
    echo ""
    echo "====> Paramétrages complémentaires à effectuer :"
    cat $TODOLIST_FILEPATH
    echo ""
fi

