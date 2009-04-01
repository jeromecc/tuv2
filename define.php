<?php

// Titre  : Déclaration des constantes (pour la configuration...).
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 05 Janvier 2005

// Description : 

// Des constantes sont définies ici, puis utilisées dans le site.
// Ca permet d'externaliser les parties en dur dans le code pour
// une configuration plus facile.

$xml = simplexml_load_file ( 'define.xml.php' ) ;

// Liste des paramètres qui changent d'un établissement sur l'autre.
define ( 'ADMINLOGIN', 		(string)$xml->adminlogin ) ;
define ( 'ADMINPASSWORD', 	(string)$xml->adminpasswd ) ;
define ( 'ADMINACTIF', 		(string)$xml->adminactif ) ;
define ( 'DATELANCEMENT',   (string)$xml->datelancement ) ;
define ( 'DATESTATS', 		(string)$xml->datestats ) ;
define ( 'NOMAPPLICATION', 	(string)$xml->nomapplication ) ;
define ( 'URLIMGLOGO',  	"images/villes/".(string)$xml->nomlogo ) ;
define ( 'URL',         	(string)$xml->url ) ;
define ( 'URLLOCAL',    realpath(dirname(__FILE__)).'/' 	) ;
define ( 'MYSQL_HOST', 		(string)$xml->mysqlhost ) ;
define ( 'MYSQL_USER', 		(string)$xml->mysqluser ) ;
define ( 'MYSQL_PASS', 		(string)$xml->mysqlpass ) ;
define ( 'BDD', 			(string)$xml->basetuv2 ) ;
define ( 'BASEXHAM', 		(string)$xml->basexham ) ;
define ( 'CCAM_BDD',		(string)$xml->baseccam ) ;


define ( 'Erreurs_Actif', 	(string)$xml->erreursactives ) ;
define ( 'Erreurs_MailApp', (string)$xml->erreursfrom ) ;
define ( 'Erreurs_Mail', 	(string)$xml->erreursto ) ;
if ( (string)$xml->baseformx ) 
	define ( 'FX_BDD',      (string)$xml->baseformx ) ;

if ( (string)$xml->uniqIdSiteInstallation )
	define ( 'IDSITE',      (string)$xml->uniqIdSiteInstallation ) ;
else
	define ( 'IDSITE','0') ;

if ( (string)$xml->branche )
	define ( 'BRANCHE',      (string)$xml->branche ) ;
else
	define ( 'BRANCHE','stable') ;


// Versions
define ( 'CCAM_VERSION',"CCAM V11") ;
define ( 'VERSIONAPPLICATION', file_get_contents( "version.txt" ) ) ;

// Activation du débugage.
define ( 'FORMX_DEBUG', "0" ) ;
define ( 'FORMX_DEBUG_EKO', "0" ) ;
define ( 'DEBUGSQL', "0" ) ;
define ( 'NONOTICE', "1" ) ;
define ( 'DEBUGDATE', "0" ) ;
define ( 'PRINT_ERRORS', "0" ) ;
define ( 'DEBUGOPTION', "0" ) ;
define ( 'DEBUGLISTES', "0" ) ;
define ( 'DEBUGSESSION', "0" ) ;
define ( 'DEBUGPATIENT', "0" ) ;
define ( 'DEBUGPOSITIONS', "0" ) ;
define ( 'DEBUGLOGSESSION', "0" ) ;
define ( 'DEBUGLISTESPATIENTS', "0" ) ;
define ( 'DEBUGAUTHENTIFICATION', "0" ) ;
define ( 'BCCERREURS', "terminal@ch-hyeres.fr" ) ;

// Divers
define ( 'IDAPPLICATION', "1" ) ;
define ( 'ENCODERURL', "1" ) ;
define ( 'VIDEDEFAUT', "--" ) ;
define ( 'LISTEVIDE', "Aucune valeur dans cette liste." ) ;
define ( 'SELECTLISTE', "--" ) ;
define ( 'AnneeMin', "1750" ) ;

// Définition des URL.
define ( 'URLNAVI',     URL."index.php?navi=" ) ;
define ( 'URLINDEX',    URL."index.php" ) ;
define ( 'URLDOCS',     URLLOCAL."editions/" ) ;
define ( 'URLDOCSWEB',  URL."editions/" ) ;
define ( 'URLCACHE',    URLLOCAL."cache/" ) ;
define ( 'URLACCESLOCALDOCFORMS',   URLDOCS ) ;
define ( 'URLACCESEXTDOCFORMS',   URLDOCSWEB ) ;
define ( 'URLCACHEWEB', URL."cache/" ) ;
define ( 'URLIMG',      URL."images/" ) ;
define ( 'URLLOGO',     URLIMG."villes/" ) ;
define ( 'URLIMGMOD',   URLIMG."modifier.png" ) ;
define ( 'URLIMGMO3',   URLIMG."modifier3.gif" ) ;
define ( 'URLIMGVAL',   URLIMG."valider.gif"  ) ;
define ( 'URLIMGANU',   URLIMG."annuler.gif"  ) ;
define ( 'URLIMGANN',   URLIMG."annuler.gif"  ) ;
define ( 'URLIMGAJO',   URLIMG."ajouter.png"  ) ;
define ( 'URLIMGAJ2',   URLIMG."ajouter2.gif" ) ;
define ( 'URLIMGREP',   URLIMG."reparer.gif"  ) ;
define ( 'URLIMGPDF',   URLIMG."pdf.png"      ) ;
define ( 'URLIMGDOC',   URLIMG."doc.png"      ) ;
define ( 'URLIMGHOR',   URLIMG."horloge.png"  ) ;
define ( 'URLIMGXCL',   URLIMG."xclock.png"   ) ;
define ( 'URLIMGMAS',   URLIMG."masquer.gif"  ) ;
define ( 'URLIMGAFF',   URLIMG."afficher.gif" ) ;
define ( 'URLIMGLOU',   URLIMG."loupe.png"    ) ;
define ( 'URLIMGFER',   URLIMG."fermer.gif"   ) ;
define ( 'URLIMGEDI',   URLIMG."imprimer.png" ) ;
define ( 'URLIMGSUP',   URLIMG."supprimerPetit.gif" ) ;
define ( 'URLIMGFH1',   URLIMG."fleche_haut.gif" ) ;
define ( 'URLIMGFH2',   URLIMG."fleche_haut2.gif" ) ;
define ( 'URLIMGFB1',   URLIMG."fleche_bas.gif" ) ;
define ( 'URLIMGFB2',   URLIMG."fleche_bas2.gif" ) ;
define ( 'URLIMGMAJ',   URLIMG."majProd.png" ) ;
define ( 'URLIMGRAD',   URLIMG."bmr.png" ) ;
define ( 'URLIMGOBS',   URLIMG."bulle.gif" ) ;
define ( 'URLIMGLOGO',  URLLOGO."LOGOhyeres.jpg" ) ;
define ( 'URLPATCHS',   URLLOCAL."patchs.txt" ) ;
define ( 'URLRPU',		URLLOCAL."rpu/" ) ;
define ( 'URLRPULOGS',	URLRPU."logs/" ) ;
define ( 'URLDLLCORA',  URLLOCAL."dllcora/" );

// Images de la colonne observations.
define ( 'IMGHISTO',     "<img src=\"".URLIMG."historique.gif\" alt=\"H\" />&nbsp;" ) ;
define ( 'IMGHISTODOCS', "<img src=\"".URLIMG."courrier.gif\" alt=\"D\" />&nbsp;" ) ;
define ( 'IMGDOCS',      "<img src=\"".URLIMG."messages.gif\" alt=\"M\" />&nbsp;" ) ;
define ( 'IMGALERTE',    "<img src=\"".URLIMG."cligne.gif\" alt=\"A\" />&nbsp;" ) ;

// Modules
define ( 'MODULE_CCAM', URLLOCAL."modules/CCAM/" ) ;
define ( 'CCAM_IDDOMAINE', 1 ) ;

// Informations sur la base de données.
define ( 'TABLELOGS', "logs" ) ;
define ( 'PPRESENTS', "patients_presents" ) ;
define ( 'PATTENDUS', "patients_attendus" ) ;
define ( 'PSORTIS', "patients_sortis" ) ;
define ( 'IMPORTS', "imports" ) ;
define ( 'TABLEOPTS', "options" ) ;
define ( 'TABLEDROITS', "droits" ) ;
define ( 'TABLESTATS', "sessions_statistiques" ) ;
define ( 'TABLESACTU', "sessions_actuelles" ) ;
define ( 'TABLESHIST', "sessions_historique" ) ;
define ( 'TABLEUSERS', "utilisateurs" ) ;
define ( 'TABLERELUG', "rel_utilisateur_groupe" ) ;
define ( 'TABLENOTES', "notes" ) ;
define ( 'TABLENAVI', "navigation" ) ;
define ( 'TABLERELAG', "rel_application_groupe" ) ;
define ( 'MAILSLOGS', "logs_mails" ) ;
define ( 'DOCSEDITES', "editions" ) ;
define ( 'URLQUERIES', URLLOCAL."queries/"  ) ;

/********  FoRmX *******/
define('TABLEFORMX',		'formx');
define('TABLEFORMXDYNTAB',	false);
define('TABLEFORMXGLOBVARS','formx_globvars');
define('FORMX_LOCATION',	URLLOCAL.'formx/');
define('DROITGENFORMX',		'formulaires'); 
define('FX_URL',			URL);
define('FX_URLCACHE',		URLCACHE);
define('FX_URLCACHEWEB',	URLCACHEWEB);
define('FX_URLIMGLOGO',		URLIMGLOGO);
define('FX_URLDOCS',		URLDOCS);
define('FX_URLDOCUMENT', "http://cypres.ch-hyeres.fr/xham/terminal_urgences/"."formx/docs/" );
define('FX_URLIMGVAL',		URLIMG."valider.gif"  ) ;
define('FX_URLIMGRIEN',		URLIMG."none.gif" ) ;
define('FX_URLCACHEWEB',	URL."cache/" ) ;
define('FX_URLIMGEDI',		URLIMG."imprimer.png" ) ;
define('FX_URLIMGANNMINI',	URLIMG."annuler3.gif"  ) ;
define('FX_URLIMGCLO',		URLIMG."closepiti.gif"  ) ;
define('FX_URLIMGPREV',		URLIMG."g.gif");
define('FX_URLIMGNEXT',		URLIMG."dw.gif");
define('FX_URLLOCAL',		URLLOCAL);
define('FX_MAXSIZEUPLOAD',	10);
define('FX_URLIMGCAL', 		URLIMG."horloge.png" ) ;
define('FX_NULLVALUES',   	utf8_encode("Champ Non Précisé.")."|".utf8_encode("Non")  );
define('URLACCESLOCALDOCFORMS', URLDOCS ) ;
define('URLACCESEXTDOCFORMS',   URLDOCSWEB ) ;


// Connexion à l'annuaire LDAP.
if ( file_exists ( "modules/ldap.php" ) ) include_once ( "modules/ldap.php" ) ;

// Configuration de l'envoi des mails d'erreurs
define ( 'Erreurs_NomApp', NOMAPPLICATION ) ;
define ( 'Erreurs_Bloquante', "Erreur bloquante détectée." ) ;
define ( 'Erreurs_Normale', "Erreur(s) détectée(s)." ) ;
define ( 'Erreurs_Nom', 	"" ) ;


// Messages d'erreurs.
define ( 'ERR_UHCD_NON2702'      , "Un patient dans un lit UHCD doit être en UF UHCD." ) ;
define ( 'ERR_SUP8_NON2702'      , "Le patient doit être en UF UHCD pour sortir." ) ;
define ( 'ERR_RECOURS_CATEGORIE' , "La catégorie de recours doit être renseignée." ) ;
define ( 'ERR_CODE_GRAVITE'      , "Le code gravité doit être renseigné." ) ;
define ( 'ERR_DATE_EXAMEN'       , "La date d'examen doit être renseignée." ) ;
define ( 'ERR_DEST_ATTENDUE'     , "La destination attendue doit être renseignée." ) ;
define ( 'ERR_DEST_SOUHAITEE'    , "La destination souhaitée doit être renseignée." ) ;
define ( 'ERR_IDE'               , "L'IDE doit être renseignée." ) ;
define ( 'ERR_MEDECIN'           , "Le médecin doit être renseigné." ) ;
define ( 'ERR_MOTIF_TRANSFERT'   , "En rapport avec la destination attendue, le motif de transfert doit être renseigné." ) ;
define ( 'ERR_MOYEN_TRANSPORT'   , "En rapport avec la destination attendue, le moyen de transport doit être renseigné." ) ;
define ( 'ERR_RECOURS'           , "Le recours doit être renseigné." ) ;
define ( 'ERR_SALLE_EXAMEN'      , "La salle d'examen doit être renseignée." ) ;
define ( 'ERR_ADRESSEUR'         , "L'adresseur doit être renseigné." ) ;
define ( 'ERR_MODE_ADMISSION'    , "Le mode d'admission doit être renseigné." ) ;
define ( 'ERR_CCMU'              , "Le code CCMU doit être renseigné." ) ;
define ( 'ERR_GEMSA'             , "Le GEMSA doit être renseigné." ) ;
define ( 'ERR_TRAUMATO'          , "La saisie de la case 'Traumato' est obligatoire." ) ;
define ( 'ERR_PROVENANCE'        , "La provenance doit être saisie." ) ;
define ( 'ERR_DEST_PMSI'         , "La destination PMSI doit être saisie." ) ;
define ( 'ERR_ORIENTATION'       , "L'orientation doit être saisie." ) ;
define ( 'ERR_TISS'              , "Le TISS doit être saisi." ) ;

$listeMois = Array ( "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ) ;

?>
