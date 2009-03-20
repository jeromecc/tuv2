#!/bin/bash
echo -n "Damien Borel, do you remind have updated the version.txt file ? (y/n)" 
read rep
if [[ $rep != 'y' ]]
then
exit ;
fi


location=/home/www/xham/terminal_urgences/
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
cp -frL ${location}index.php    ${locationtmp}
cp -frL ${location}define.php    ${locationtmp}
cp -frL ${location}config.php    ${locationtmp}
cp -frL ${location}version.txt    ${locationtmp}
cp -frL ${location}ajax.js    ${locationtmp}
cp -frL ${location}scripts.js    ${locationtmp}
cp -frL ${location}templates_int/    ${locationtmp}
cp -frL ${location}templates_gen/    ${locationtmp}
cp -frL ${location}classes_int/    ${locationtmp}
cp -frL ${location}classes_gen/    ${locationtmp}
cp -frL ${location}formx.js    ${locationtmp}
cp -frL ${location}formx_scripts.js    ${locationtmp}
cp -frL ${location}css/    ${locationtmp}
cp -frL ${location}images/    ${locationtmp}
mkdir ${locationtmp}modules
cp -frL ${location}modules/CCAM    ${locationtmp}modules/
cp -frL ${location}modules/mouvements    ${locationtmp}modules/
cp -frL ${location}modules/wz_dragdrop.js ${locationtmp}modules/

#copie formulaires et fonctions formx
mkdir ${locationtmp}formx
mkdir ${locationtmp}formx/functions
cp -frL ${location}formx/functions/GetDateE3.php    ${locationtmp}formx/functions/
cp -frL ${location}formx/functions/getAge2.php    ${locationtmp}formx/functions/

cp -frL ${location}formx/Formulaire_Radio_Partie_*    ${locationtmp}formx/


#Suppressions des plugins de mouvements spécifiques en dev
rm -f ${locationtmp}modules/mouvements/enabled/*

#Sauf pour ceux ci qui sont des plugins globaux
cp -fL ${location}modules/mouvements/avalaible/sortie_enquetes.php ${locationtmp}modules/mouvements/enabled/

#pour que les droits, ce ne soit plus notre problème:
chown -R www $locationtmp
chgrp -R www $locationtmp
chmod -R 777 $locationtmp



cd  $locationtmp

#On enleve les liens subversion
find -name '.svn' -exec rm -fr {} \;

version=`cat ${location}version.txt`

#On crée l'archive
tar zcvf ${location}tu.maj.$version.tgz *

#on nettoie
rm -fr ${locationtmp}

