#!/bin/bash
echo -n "Damien Borel, do you remind have updated the version.txt file ? (y/n)"
read rep
if [[ $rep != 'y' ]]
then
exit ;
fi

#Ca c'est de la commande bash.
absoluteScriptUrl="$(cd "${0%/*}" 2>/dev/null; echo "$PWD"/"${0##*/}")"

location=`dirname ${absoluteScriptUrl}`/
version=`cat ${location}version.txt`
locationtmp=${location}install/


if [ -d $locationtmp ]
then
	rm -fr $locationtmp
fi
mkdir $locationtmp

#Ajouts
cp -frL ${location}patchs.txt    ${locationtmp}
cp -frL ${location}index.php    ${locationtmp}
cp -frL ${location}install.php    ${locationtmp}
cp -frL ${location}index.test.php    ${locationtmp}
cp -frL ${location}define.php    ${locationtmp}
cp -frL ${location}define.xml.default    ${locationtmp}
cp -frL ${location}config.php    ${locationtmp}
cp -frL ${location}version.txt    ${locationtmp}
cp -frL ${location}ajax.js    ${locationtmp}
cp -frL ${location}default.htaccess    ${locationtmp}default.htaccess
cp -frL ${location}scripts.js    ${locationtmp}
cp -frL ${location}templates_int/    ${locationtmp}
cp -frL ${location}meta/    ${locationtmp}
cp -frL ${location}queries_int/    ${locationtmp}
cp -frL ${location}queries_gen/    ${locationtmp}
cp -frL ${location}templates_gen/    ${locationtmp}
cp -frL ${location}classes_int/    ${locationtmp}
cp -frL ${location}classes_gen/    ${locationtmp}
cp -frL ${location}classes_ext/    ${locationtmp}
cp -frL ${location}formx.js    ${locationtmp}
cp -frL ${location}formx_scripts.js    ${locationtmp}
cp -frL ${location}css/    ${locationtmp}
cp -frL ${location}images/    ${locationtmp}
mkdir ${locationtmp}modules
cp -frL ${location}modules/CCAM    ${locationtmp}modules/
cp -frL ${location}modules/mouvements    ${locationtmp}modules/
cp -frL ${location}modules/wz_dragdrop.js ${locationtmp}modules/

#duplication d'un fichier pour éviter un bug
cp -frL ${location}classes_gen/clListeRadios.php    ${locationtmp}classes_int/clListeRadios.php

#copie formulaires et fonctions formx
mkdir ${locationtmp}formx
mkdir ${locationtmp}formx/functions
cp -frL ${location}formx/functions/GetDateE3.php    ${locationtmp}formx/functions/
cp -frL ${location}formx/functions/getAge2.php    ${locationtmp}formx/functions/

cp -frL ${location}formx/Formulaire_Radio_Partie_*    ${locationtmp}formx/

cp -frL ${location}formx/Formulaire_Bio.xml    ${locationtmp}formx/
cp -frL ${location}formx/Formulaire_Radio.xml    ${locationtmp}formx/
cp -frL ${location}formx/Formulaire_Consultation_Specialisee.xml    ${locationtmp}formx/

#enquetes
mkdir ${locationtmp}formx/triggers
mkdir ${locationtmp}formx/enquetes
cp -frL ${location}formx/triggers/2009_avc.xml    ${locationtmp}formx/triggers/
cp -frL ${location}formx/triggers/2009_qualite_diag_sfmu.xml    ${locationtmp}formx/triggers/
cp -frL ${location}formx/triggers/2009_transfu_patient.xml    ${locationtmp}formx/triggers/
cp -frL ${location}formx/enquetes/*.xml    ${locationtmp}formx/enquetes/

#scripts AVC
mkdir ${locationtmp}formx/functions/specifique
mkdir ${locationtmp}formx/functions/specifique/avc
cp -frL ${location}formx/functions/specifique/avc/*    ${locationtmp}formx/functions/specifique/avc/

#fonctions utilitaires FX-TU
mkdir ${locationtmp}formx/functions/helpers
mkdir ${locationtmp}formx/functions/getters
mkdir ${locationtmp}formx/functions/setters

cp -frL ${location}formx/functions/helpers/*    ${locationtmp}formx/functions/helpers/
cp -frL ${location}formx/functions/getters/*    ${locationtmp}formx/functions/getters/
cp -frL ${location}formx/functions/setters/*    ${locationtmp}formx/functions/setters/


#ce fichier peut être spécialisé sur les sites
mv ${locationtmp}queries_int/getHistorique.qry  ${locationtmp}queries_int/getHistorique.qry.base
mv ${locationtmp}queries_int/CCAM_Cora.qry  ${locationtmp}queries_int/CCAM_Cora.qry.base

#Suppressions des fichiers de config queries gen
rm -f ${locationtmp}queries_gen/*.cfg

#Suppressions des plugins de mouvements spécifiques en dev
rm -f ${locationtmp}modules/mouvements/enabled/*

#Sauf pour ceux ci qui sont des plugins globaux
cp -fL ${location}modules/mouvements/avalaible/sortie_enquetes.php ${locationtmp}modules/mouvements/enabled/

#suppressions dev ponctuels  à ne pas diffuser
rm -f ${locationtmp}meta/tools/*

#pour que les droits, ce ne soit plus notre problème:
chown -R www $locationtmp
chgrp -R www $locationtmp
chmod -R 777 $locationtmp



cd  $locationtmp

#On enleve les liens subversion
find -name '.svn' -exec rm -fr {} \;

version=`cat ${location}version.txt`

#On crée l'archive
tar zcvf ${location}var/dist/tu.maj.$version.tgz *

md5=`md5sum ${location}var/dist/tu.maj.$version.tgz | cut -f1 -d\ `



#publication dans le repository stable
if [[ $1 == 'publishstable' ]]
then
    HOST='www.orupaca.fr'
    USER='orupaca'
    PASS=`cat ${location}pass.ftp.repository`
    REP='ressources/tu/repository'
    cd ${location}var/dist/
    echo _maj_${version}_hash_${md5}_ > ${location}var/dist/last_version_stable.html
    ftp -v -n $HOST <<EOF
user $USER $PASS
cd $REP
bin
prompt
put tu.maj.$version.tgz
put last_version_stable.html
quit
EOF

fi

#on nettoie
rm -fr ${locationtmp}
