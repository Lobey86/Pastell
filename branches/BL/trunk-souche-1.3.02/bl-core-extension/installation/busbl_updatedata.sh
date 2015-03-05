#!/bin/sh

# Exécution des scripts de la version.
#
# Paramètres :
#   $1 : version (ex : 1.7.0)
#
# La version est fournie en paramètre. Elle donne le nom du sous-répertoire contenant les scripts à exécuter.
# Les scripts son exécutés dans l'ordre alpha-numérique de leur nom (Ex : BLScriptUpdateVersion_01.php,  BLScriptUpdateVersion_01.1.php, BLScriptUpdateVersion_02.php). A noter : "8" sera après "10".
# Il faut multiplier les scripts si entre 2 scripts le contexte ou du cache doivent être réinitialisés.

echo "====> Montée de version des données..."

CURDIR=$(dirname $0)
TODOLIST_FILEPATH="/tmp/todolist.txt"
VERSION="$1"
VERSIONDIR="$CURDIR/$VERSION"

if [ -z "$VERSION" ]; then
    echo "ERREUR : paramètre \$1 non fourni (version)"
    exit 1
fi
if [ ! -d "$VERSIONDIR" ]; then
    echo "ERREUR : répertoire inexistant ($VERSIONDIR)"
    exit 1
fi

if [ -f $TODOLIST_FILEPATH ]; then
    rm $TODOLIST_FILEPATH;
fi

for script in $(find $VERSIONDIR/ -name "*ScriptUpdate*.php" -type f | sort); do
    echo "--> Exécution du script : $script"
    sudo -H -u www-data /usr/bin/php "$script" todolist_filepath=$TODOLIST_FILEPATH
done

if [ -f $TODOLIST_FILEPATH ]; then
    echo ""
    echo "====> Paramétrages complémentaires à effectuer :"
    cat $TODOLIST_FILEPATH
    echo ""
fi
