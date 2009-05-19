<?php 
// Ce fichier doit être appellé à l'installation et apres chaque mise à jour
// 




// Appel du fichier de configuration.
include_once ( "config.php" ) ;

//parametres apache/php par default
if ( ! file_exists(URLLOCAL.'.htaccess'))
	copy(URLLOCAL.'default.htaccess',URLLOCAL. '.htacess');
if ( ! file_exists('define.xml.php'))
	copy(URLLOCAL.'define.xml.default',URLLOCAL. 'define.xml.php');

if ( ! file_exists(URLLOCAL.'queries_int/getHistorique.qry'))
    copy(URLLOCAL.'queries_int/getHistorique.qry.base',URLLOCAL.'queries_int/getHistorique.qry');

if ( ! file_exists(URLLOCAL.'queries_int/getHistoriqueDocs.qry'))
    copy(URLLOCAL.'queries_int/getHistoriqueDocs.qry.base',URLLOCAL.'queries_int/getHistoriqueDocs.qry');

if ( ! file_exists(URLLOCAL.'queries_int/getHistoriqueDoc.qry') AND file_exists(URLLOCAL.'queries_int/getHistoriqueDoc.qry.base'))
    copy(URLLOCAL.'queries_int/getHistoriqueDoc.qry.base',URLLOCAL.'queries_int/getHistoriqueDoc.qry');



print "<h3>Procédure de vérification</h3>" ;
print "<hr />" ;

print "<h4>Configuration php basique</h4>" ;

clUpdater::checkPHPVersion('5.1.0');
clUpdater::testSafeMode();

clUpdater::testNoNoNoNoNoNoThereIsNoLimit('512M');




/*
-disable-cgi --enable-soap
            --with-libxml-dir --with-xsl --enable-track-vars --with-xml --enable-ftp --with-mysql
            --with-calendar --with-gd --enable-calendar --with-zlib-dir --with-jpeg-dir
            --enable-sigchild --with-freetype-dir --enable-mbstring --enable-sockets --with-zip
*/



print "<hr />" ;

print "<h4>Modules php nécessaires</h4>" ;
clUpdater::testModule('soap');
clUpdater::testModule('xsl');
clUpdater::testModule('xml');
clUpdater::testModule('ftp');
clUpdater::testModule('mysql');
clUpdater::testModule('calendar');
clUpdater::testModule('gd');
clUpdater::testModule('zlib');
clUpdater::testModule('ftp');
clUpdater::testModule('mbstring');
clUpdater::testModule('sockets');
print "<hr/>";

print "<h4>Vérification des répertoires</h4>" ;


clUpdater::testEcritureDossier(URLCACHE);
clUpdater::testEcritureDossier(URLDOCS);
clUpdater::testEcritureDossier(URLLOCAL.'hprim/');
clUpdater::testEcritureDossier(URLLOCAL.'hprim/ok/');
clUpdater::testEcritureDossier(URLLOCAL.'hprim/xml/');
clUpdater::testEcritureDossier(URLLOCAL.'rpu/');
clUpdater::testEcritureDossier(URLLOCAL.'rpu/ok/');
clUpdater::testEcritureDossier(URLLOCAL.'rpu/logs/');
clUpdater::testEcritureDossier(URLLOCAL.'var/');
clUpdater::testEcritureDossier(URLLOCAL.'var/maj/');
clUpdater::testEcritureDossier(URLLOCAL.'temp/');
clUpdater::testEcritureDossier(URLLOCAL.'var/dist/');

print "<hr/>";

print "<h4>Création des fichiers de configuration MySQL</h4>" ;


clUpdater::genResultQueryConfigFile(URLLOCAL. "queries_int/config_xham.cfg",MYSQL_HOST,BASEXHAM,MYSQL_USER,MYSQL_PASS);
clUpdater::genResultQueryConfigFile(URLLOCAL. "queries_int/config_ccam.cfg",MYSQL_HOST,CCAM_BDD,MYSQL_USER,MYSQL_PASS);
clUpdater::genResultQueryConfigFile(URLLOCAL. "queries_int/config_terminal.cfg",MYSQL_HOST,BDD,MYSQL_USER,MYSQL_PASS);
clUpdater::genResultQueryConfigFile(URLLOCAL. "queries_int/config_formx.cfg",MYSQL_HOST,(defined('FX_BDD')?FX_BDD:BDD),MYSQL_USER,MYSQL_PASS);


print "<br><br><hr><h4>Connexions aux bases</h4>" ;

print "Connexion au serveur MySQL '".MYSQL_USER."@".MYSQL_HOST." (using password: ".(MYSQL_PASS?'YES':'NO').")' => " ;
$res = mysql_pconnect ( MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
if ( $res ) print "<font color=\"green\">OK</font>" ;
else  {  print "<font color=\"red\">KO</font>" ; die ; }

print "<br>Connexion à la base '".BASEXHAM."' => " ;
if ( mysql_select_db ( BASEXHAM ) ) print "<font color=\"green\">OK</font>" ;
else  {  print "<font color=\"red\">KO</font>" ; die ; }

clUpdater::testGrantOnBase( MYSQL_HOST, MYSQL_USER, MYSQL_PASS,BASEXHAM);

print "<br>Connexion à  la base '".BDD."' => " ;
if ( mysql_select_db ( BDD ) ) print "<font color=\"green\">OK</font>" ;
else {  print "<font color=\"red\">KO</font>" ; die ; }

clUpdater::testGrantOnBase( MYSQL_HOST, MYSQL_USER, MYSQL_PASS,BDD);

print "<br>Connexion à la base '".CCAM_BDD."' => " ;
if ( mysql_select_db ( CCAM_BDD ) ) print "<font color=\"green\">OK</font>" ;
else  {  print "<font color=\"red\">KO</font>" ; die ; }

clUpdater::testGrantOnBase( MYSQL_HOST, MYSQL_USER, MYSQL_PASS,CCAM_BDD);




//Installation des bases si vides
clUpdater::installBase(BASEXHAM,URLLOCAL.'meta/install/tuv2_xham.sql','logs',MYSQL_USER,MYSQL_PASS,MYSQL_HOST);ob_flush();flush();
clUpdater::installBase(BDD,URLLOCAL.'meta/install/tuv2_tuv2.sql','patients_presents',MYSQL_USER,MYSQL_PASS,MYSQL_HOST);ob_flush();flush();
clUpdater::installBase(CCAM_BDD,URLLOCAL.'meta/install/tuv2_ccam.sql','ccam_liste',MYSQL_USER,MYSQL_PASS,MYSQL_HOST);ob_flush();flush();

print "<br>Test du parametre mysql server 'lower_case_table_names = 1 ' ";
if (clUpdater::mysql_table_exists('R_ACTE_IVITE_PHASE',CCAM_BDD)) print "<font color=\"green\">OK</font>" ;
else  {  print "<font color=\"red\">KO</font>" ; print "<br />sous Debian: Rajoutez lower_case_table_names = 1 dans /etc/mysql/my.cnf"  ; die ; }



print "<br><br><hr><h4>Communication</h4>" ;


//Test connexion FTP vers serveur de veille
print "<br />Test de connexion FTP vers serveur de veille  (ftp://www.veille-arh-paca.com)  =>" ;ob_flush();flush();
$ftp_server = 'www.veille-arh-paca.com' ;
$ftp_user_name = 'importsrv' ;
$ftp_user_pass = '4dS#3!b';
$conn_id = ftp_connect($ftp_server,21,20);
if(! $conn_id) {
		 print "<font color=\"red\">Connexion impossible</font>";
} else {
	ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 20);
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	if(! $login_result) {
		 print "<font color=\"red\">Identification refusée</font>";
	} else {
		$fp = fopen(URLLOCAL.'index.php', 'r');
		if (ftp_fput($conn_id, 'test_tu.txt', $fp, FTP_BINARY)) {
			print "<font color=\"green\">OK</font>";
		} else {
			print "<font color=\"red\">Problème lors du transfert</font>";
		}
	}
}


print "<br />Test de connexion au serveur de mises à jour (www.orupaca.fr:80) => " ;ob_flush();flush();



if(false !== strpos(@file_get_contents(PROTO.'www.orupaca.fr/test_tu.html', false, $context), 'ok'  ))
{
	$isSrvMaj = true ;
	print "<font color=\"green\">OK</font>";
}
else
{
	$isSrvMaj = false ;
	print "<font color=\"red\">KO</font>";
}


print "<br />Test de cryptage avec la clé publique ARH => " ;
//$gpg = new gnuPG(PGPLOCATION,HOMEGPG);
if( file_exists( URLLOCAL.'index.php.gpg' ))
    unlink(URLLOCAL.'index.php.gpg');

$gpg = new gnuPG(false,GNUPG);
$gpg->EncryptFile('import@veille-arh-paca.com',URLLOCAL.'index.php');
$errors=$gpg->error;
if($errors) {
	print "<font color=\"red\">KO : $errors</font>";
	print "<br /><br /><code>Pour installer la clé publique, exécutez en tant que user apache: ";
	//print "<br />su ".$_ENV["APACHE_RUN_USER"];
	print "<br />gpg --import ".URLLOCAL."meta/import@veille-arh-paca.com.public.key";
	print "<br />gpg --edit-key import@veille-arh-paca.com";
	print "<br />     trust";
	print "<br />    choisir 'je donne une confiance ultime'";
	print "<br />    quit";
	print "<br /><br />Exécutez également en tant que root";
	print "<br />gpg --import ".URLLOCAL."meta/import@veille-arh-paca.com.public.key";
	print "<br />gpg --edit-key import@veille-arh-paca.com";
	print "<br />     trust";
	print "<br />    choisir 'je donne une confiance ultime'";
	print "<br />    quit</code>";

} else {
	print "<font color=\"green\">OK</font>";
}





print "<br><br><hr><h4>Mise a jour des bases de données</h4>" ;
	
clUpdater::applyPatchs(IDSITE);



if ( $isSrvMaj )
{
	print "<br><br><hr><h4>Mise a jour de l'application</h4>" ;
	$tabMatches = array();
	preg_match('/_maj_(.*)_hash_(.*)_/', file_get_contents('http://www.orupaca.fr/ressources/tu/repository/last_version_'.BRANCHE.'.html'),$tabMatches) ;
	$lastVersion = $tabMatches[1];
	$currentVersion = str_replace("\n",'', file_get_contents(URLLOCAL.'version.txt'));
	$currentVersion = str_replace("\r",'', $currentVersion);
	$hash = $tabMatches[2];

	//print strlen($currentVersion).'*'.$currentVersion.'*'.$lastVersion.'*'.strlen($lastVersion);

	if ( version_compare($lastVersion,$currentVersion,'>'))
	{
		print "<br />Une nouvelle version:  $lastVersion est disponible. <br />Téléchargement dans ".URLLOCAL."var/dist/   ..." ;
		ob_flush() ; flush() ;
        $hashvide = md5('') ;
        $nomFic = PREFIXEARCHIVE.'.maj.'.$lastVersion.'.tgz';
        $ficArchive = URLLOCAL.'var/dist/'.$nomFic ;
        //print 'http://www.orupaca.fr/ressources/tu/repository/'.$nomFic;

		

		$archive = @file_get_contents('http://www.orupaca.fr/ressources/tu/repository/'.$nomFic);
        $hashrecu = md5($archive) ;
        if( $hashrecu == $hash )
        {
            file_put_contents($ficArchive,$archive );
            print "<font color=\"green\">CHECKSUM $hash OK</font> <a href='install.php?release=$lastVersion'>Installer la nouvelle version (expérimental)</a><br /><br />";
        }
        else
        {
            if( $hashvide == $hashrecu )
                $plusInfos = "Fichier reçu vide" ;
            print "<font color=\"red\">KO (problème lors du téléchargement) hash attendu $hash , hash reçu $hashrecu  $plusInfos</font>";
        }
	}
    else
    {
        print "<font color=\"green\">Votre TU est à jour.</font>";
    }

}
















?>
