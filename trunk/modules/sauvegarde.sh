#!/bin/bash

# Configuration de la sauvegarde.
WWW=/home/www/
ARCHIVE=/home/batch/
USERSQL=sauvegarde
PASSSQL=sauv4
XHAMSQL=terminal_xham.`date +%w`.sql
TUV2SQL=terminal_tuv2.`date +%w`.sql
CCAMSQL=terminal_ccam.`date +%w`.sql
HOST=192.168.2.5
REPD=/home/arh/
USER=user
PASS=pass

# Positionnement dans le bon répertoire.
cd $ARCHIVE

# Extraction du contenu des bases.
mysqldump terminal_xham -u $USERSQL -p$PASSSQL > $XHAMSQL
mysqldump terminal_tuv2 -u $USERSQL -p$PASSSQL > $TUV2SQL
mysqldump terminal_ccam -u $USERSQL -p$PASSSQL > $CCAMSQL

# Compression du code source et des exports MySQL.
NOM_WWW=terminurg.`date +%w`.tgz
tar zcf $NOM_WWW $WWW $XHAMSQL $TUV2SQL $CCAMSQL

# Transfert FTP de l'archive compressée.
ftp -v -n $HOST <<EOF
user $USER $PASS
cd $REPD
bin
prompt
put $NOM_WWW
quit
EOF
