<?php
// Ce fichier doit étre appellé é l'installation et apres chaque mise é jour
//


/*
 * Fonction d'affichage des tests
 * @param string $s texte descriptif du test
 * @param variant $result soit un tab soit un boolean, résultat à afficher
 * @param boolean $error indique si $result est un tab (true) ou un boolean (false)
 * @param boolean $orange indique si en cas de KO il faut écrire en orange ou en rouge
 * @param boolean $die indique si il faut appeler die en cas de KO
 * @return boolean
 */
function affichage($s, $result, $error = false, $orange = false, $die = false) {
    echo $s . " : ";
    if ($error) {
	$b = $result[0];
	$txtError = $result[1];
    }
    else {
	$b = $result;
	$txtError = "";
    }
    if ($b) {
	echo "<font color=\"green\">OK " . $txtError . "</font><br />";
	return true;
    }

    $color = $orange ? "orange" : "red" ;
    echo "<font color=\"" . $color . "\">KO " . $txtError . "</font><br />";
    if ($die) die;
    return false;

}



// Appel du fichier de configuration.
include_once ( "config.php" ) ;
if (! file_exists("urlMaj.txt")) {
    $a = "http://www.orupaca.fr/ressources/tu/repository" ;
    $file = fopen("urlMaj.txt", "w+");
    fwrite($file, $a);
    fclose($file);
    define('URL_MAJ', $a);
}

//parametres apache/php par default
if ( ! file_exists(URLLOCAL.'.htaccess'))
    copy(URLLOCAL.'default.htaccess',URLLOCAL. '.htacess');
if ( ! file_exists('define.xml.php'))
    copy(URLLOCAL.'define.xml.default',URLLOCAL. 'define.xml.php');

if ( ! file_exists(URLLOCAL.'queries_int/getHistorique.qry'))
    copy(URLLOCAL.'queries_int/getHistorique.qry.base',URLLOCAL.'queries_int/getHistorique.qry');

if ( ! file_exists(URLLOCAL.'queries_int/CCAM_CoraCCAM.qry'))
    copy(URLLOCAL.'queries_int/CCAM_CoraCCAM.qry.base',URLLOCAL.'queries_int/CCAM_CoraCCAM.qry');

if ( ! file_exists(URLLOCAL.'queries_int/getHistoriqueDocs.qry'))
    copy(URLLOCAL.'queries_int/getHistoriqueDocs.qry.base',URLLOCAL.'queries_int/getHistoriqueDocs.qry');

if ( ! file_exists(URLLOCAL.'queries_int/getHistoriqueDoc.qry') AND file_exists(URLLOCAL.'queries_int/getHistoriqueDoc.qry.base'))
    copy(URLLOCAL.'queries_int/getHistoriqueDoc.qry.base',URLLOCAL.'queries_int/getHistoriqueDoc.qry');



echo "<h3>Procédure de vérification</h3><hr />" ;

/*
 * TEST DE LA CONFIGURATION
 */
echo "<h4>Configuration php basique</h4>" ;

$version = "5.1.0";
$mem = "512M";
affichage("Test version de PHP > " . $version, clUpdater::checkPHPVersion($version));
affichage("Safe mode non activé", clUpdater::testSafeMode());
affichage(
    "Test de la désactivation de la limite temporelle d'exécution du script",
    clUpdater::testLimiteTempo());
affichage(
    "Test de l'augmentation de la mémoire allouée à " . $mem,
    clUpdater::testNoNoNoNoNoNoThereIsNoLimit($mem)
);



/*
-disable-cgi --enable-soap
            --with-libxml-dir --with-xsl --enable-track-vars --with-xml --enable-ftp --with-mysql
            --with-calendar --with-gd --enable-calendar --with-zlib-dir --with-jpeg-dir
            --enable-sigchild --with-freetype-dir --enable-mbstring --enable-sockets --with-zip
*/

/*
 * TEST DES MODULES PHP
 */

echo "<br /><hr /><h4>Modules php nécessaires</h4>" ;
$modules = array("soap", "xsl", "xml", "ftp", "mysql", "calendar","gd","zlib","ftp","mbstring","sockets");
foreach ($modules as $module) {
    affichage("Test de la présence du module PHP " . $module, clUpdater::testModule($module), false, false, false);
}

echo "<h4>Modules php pour fonctionalités étendues</h4>" ;
$modules = array("curl","openssl");
foreach ($modules as $module) {
    affichage("Test de la présence du module PHP " . $module, clUpdater::testModule($module), false, true, false);
}


/*
 * TESTS SUR LES REPERTOIRES
 */
echo "<br /><hr/><h4>Vérification des répertoires</h4>" ;

$dirs = array(
    URLCACHE, URLDOCS, URLLOCAL.'hprim/', URLLOCAL.'hprim/ok/', URLLOCAL.'hprim/xml/',
    URLLOCAL.'rpu/', URLLOCAL.'rpu/ok/', URLLOCAL.'rpu/logs/', URLLOCAL.'var/',
    URLLOCAL.'var/maj/', URLLOCAL.'temp/', URLLOCAL.'var/dist/'
);
foreach ($dirs as $dir) {
    affichage(
	"Test du droit d'écriture sur le dossier " . $dir,
	clUpdater::testEcritureDossier($dir),
	true, false, true
    );
}


/*
 * TESTS DES FICHIERS .cfg
 */
echo "<br /><hr/><h4>Création des fichiers de configuration MySQL</h4>" ;

affichage(
    "Creation du fichier " . URLLOCAL. "queries_int/config_xham.cfg",
    clUpdater::genResultQueryConfigFile(URLLOCAL. "queries_int/config_xham.cfg",MYSQL_HOST,BASEXHAM,MYSQL_USER,MYSQL_PASS)
);
affichage(
    "Creation du fichier " . URLLOCAL. "queries_int/config_ccam.cfg",
    clUpdater::genResultQueryConfigFile(URLLOCAL. "queries_int/config_ccam.cfg",MYSQL_HOST,CCAM_BDD,MYSQL_USER,MYSQL_PASS)
);
affichage(
    "Creation du fichier " . URLLOCAL. "queries_int/config_terminal.cfg",
    clUpdater::genResultQueryConfigFile(URLLOCAL. "queries_int/config_terminal.cfg",MYSQL_HOST,BDD,MYSQL_USER,MYSQL_PASS)
);
affichage(
    "Creation du fichier " . URLLOCAL. "queries_int/config_formx.cfg",
    clUpdater::genResultQueryConfigFile(URLLOCAL. "queries_int/config_formx.cfg",MYSQL_HOST,(defined('FX_BDD')?FX_BDD:BDD),MYSQL_USER,MYSQL_PASS)
);


/*
 * TEST DE CONNEXION AUX BASES
 */
echo "<br /><hr /><h4>Connexions aux bases</h4>" ;

affichage(
    "Connexion au serveur MySQL '".MYSQL_USER."@".MYSQL_HOST." (using password: ".(MYSQL_PASS?'YES':'NO').")'",
    mysql_pconnect ( MYSQL_HOST, MYSQL_USER, MYSQL_PASS ),
    false, false, true
);

$bases = array(BASEXHAM, BDD, CCAM_BDD);
foreach ($bases as $base) {
    affichage(
	"Connexion à la base '" . $base . "'",
	mysql_select_db ( $base ),
	false, false, true
    );

    affichage(
	"Test des privilèges CREATE ALTER DROP",
	clUpdater::testGrantOnBase( MYSQL_HOST, MYSQL_USER, MYSQL_PASS,$base),
	false, false, true
    );
}




//Installation des bases si vides
clUpdater::installBase(BASEXHAM,URLLOCAL.'meta/install/tuv2_xham.sql','logs',MYSQL_USER,MYSQL_PASS,MYSQL_HOST);ob_flush();flush();
clUpdater::installBase(BDD,URLLOCAL.'meta/install/tuv2_tuv2.sql','patients_presents',MYSQL_USER,MYSQL_PASS,MYSQL_HOST);ob_flush();flush();
clUpdater::installBase(CCAM_BDD,URLLOCAL.'meta/install/tuv2_ccam.sql','ccam_liste',MYSQL_USER,MYSQL_PASS,MYSQL_HOST);ob_flush();flush();

$b = clUpdater::mysql_table_exists('R_ACTE_IVITE_PHASE',CCAM_BDD);
if (!$b) $erreur = "</font><font><br />sous Debian: Rajoutez lower_case_table_names = 1 dans /etc/mysql/my.cnf";
else $erreur = "";
affichage(
    "Test du parametre mysql server 'lower_case_table_names = 1'",
    array($b, $erreur), true
);


/*
 * TESTS DE COMMUNICATION VERS L'EXTERIEUR
 */
echo "<br /><hr /><h4>Communication</h4>" ;

//Test connexion FTP vers serveur de veille
ob_flush();flush();

$ftp_server = 'www.veille-arh-paca.com' ;
$ftp_user_name = 'importsrv' ;
$ftp_user_pass = '4dS#3!b';
affichage(
    "Test de connexion FTP vers serveur de veille  (ftp://www.veille-arh-paca.com)",
    clUpdater::testDepotFTP($ftp_server, $ftp_user_name, $ftp_user_pass),
    true
);


$isSrvMaj = affichage(
    "Test de connexion au serveur de mises à jour (www.orupaca.fr:80)",
    false !==   strpos(XhamUpdater::getUrlContents('http://www.orupaca.fr/test_tu.html'), 'ok'  ),
    false, false, false
);


affichage(
    "Test de cryptage avec la clef publique ARH",
    clUpdater::clefARH(), true
);



/*
 * MAJ BASES
 */
echo "<br /><hr /><h4>Mise a jour des bases de données</h4>" ;

clUpdater::applyPatchs(IDSITE) == 0;


/*
 * MAJ TU
 */
if ( $isSrvMaj ) {
//    clUpdater::updateTU(URL_MAJ);
    echo "<br /><hr /><h4>Mise a jour de l'application</h4>" ;
    $tabMatches = array();
    preg_match('/_maj_(.*)_hash_(.*)_/', XhamUpdater::getUrlContents(URL_MAJ . '/last_version_'.BRANCHE.'.html?nocacheteweak='.rand(1,10000)),$tabMatches) ;
    $lastVersion = $tabMatches[1];
    $currentVersion = str_replace("\n",'', file_get_contents(URLLOCAL.'version.txt'));
    $currentVersion = str_replace("\r",'', $currentVersion);
    $hash = $tabMatches[2];

    //print strlen($currentVersion).'*'.$currentVersion.'*'.$lastVersion.'*'.strlen($lastVersion);

    if ( version_compare($lastVersion,$currentVersion,'>')) {
	echo "<br />Une nouvelle version:  $lastVersion est disponible. <br />Téléchargement dans ".URLLOCAL."var/dist/... <br />" ;
	ob_flush() ; flush() ;
	$hashvide = md5('') ;
	$nomFic = PREFIXEARCHIVE.'.maj.'.$lastVersion.'.tgz';
	$ficArchive = URLLOCAL.'var/dist/'.$nomFic ;
	//print 'http://www.orupaca.fr/ressources/tu/repository/'.$nomFic;

	$messageKo ='' ;
	XhamUpdater::downloadFile(URL_MAJ .'/'.$nomFic, $ficArchive, $messageKo);

	$hashrecu = md5(file_get_contents($ficArchive)) ;
	if( $hashrecu == $hash ) {
	    echo "<font color=\"green\">CHECKSUM $hash OK</font> <a href='install.php?release=$lastVersion'>Installer la nouvelle version</a><br /><br />";
	}
	else {
	    if( $hashvide == $hashrecu )
		$messageKo .= " Fichier reçu vide" ;
	    unlink($ficArchive);
	    print "<font color=\"red\">KO (problème lors du téléchargement) hash attendu $hash , hash reçu $hashrecu  $messageKo</font>";
	}
    }
    else {
	print "<font color=\"green\">Votre TU est à jour.</font>";
    }

}

?>
