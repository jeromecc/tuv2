<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * XhamUpdater gere les mises à jour et update de l'appli
 * Attention, cette classe doit être appelée par des simples scripts,
 * Et pas seulement dans l'environnement Xham,
 * Ne pas utiliser d'objets globaux instanciés par xham
 *
 * @author ecervetti
 */
class XhamUpdater {



static function testSafeMode()
{
	print "<br />Safe mode non activé   ";
	if( ! ini_get('safe_mode') )
		print "<font color=\"green\">OK</font>" ;
	else
	{
		print "<font color=\"red\">KO</font>" ;
				die ;
	}
}

static function testNoNoNoNoNoNoThereIsNoLimit($memory)
{
	print "<br />Test de la désactivation de la limite temporelle d'exécution du script  ";
	set_time_limit(0);
	if(  ini_get('max_execution_time') == 0  )
		print "<font color=\"green\">OK</font>" ;
	else
	{
		print "<font color=\"red\">KO</font>" ;
				die ;
	}
	print "<br />Test de l'augmentation de la mémoire allouée à  $memory ";
	ini_set('memory_limit', $memory);
	if( ! ini_get('memory_limit') || (int) ini_get('memory_limit') >= (int) $memory  )
		print "<font color=\"green\">OK</font>" ;
	else
	{
		print "<font color=\"red\">KO</font>" ;
				die ;
	}
}


static function testModule($module,$bloquant=true)
{
	print "<br />Test de la présence du module PHP $module  ";

	$couleur = 'rouge' ;

	if( ! $bloquant )
		$couleur = 'orange';

	if( extension_loaded($module) )
		print "<font color=\"green\">OK</font>" ;
	else
	{
		print "<font color=\"$couleur\">KO</font>" ;
			//	die ;
	}
}

static function checkPHPVersion($version)
{


	print "<br />Test version de PHP > $version  ";

	if (version_compare(PHP_VERSION,$version, '>')) {
		print "<font color=\"green\">OK</font>" ;
	} else
	{
		print "<font color=\"red\">KO</font>" ;
				die ;
	}
}


static function installBase($base,$file,$table,$login,$pass,$host)
 {

	$link = mysql_connect($host,$login,$pass);
	mysql_select_db($base,$link);

	 if( ! self::mysql_table_exists($table,$base,$link ) )
	 {
		print "<br /><font color=\"orange\">La base $base semble vide, installation...</font>" ;
		self::execSqlFile($link,$file);
		print "<font color=\"green\">OK</font>" ;
	 }

}

/**
 * Teste, crée si besoin, change les droits si besoin, d'un dossier nécessaire en écriture
 * @param string $dir dossier à tester
 * @return null
 */
static function testEcritureDossier($dir)
{
	print "<br />Test du droit d'écriture sur le dossier $dir : ";
	$precisionCree = '' ;
	if(! file_exists($dir))
	{
		if(! mkdir($dir) )
		{
			print "<font color=\"red\">KO (creation impossible)</font>" ;
			die ;
		}
		else
		{
			$precisionCree = '(Créé)' ;
		}
	}
	if(is_writable($dir))
	{
		 print "<font color=\"green\">OK $precisionCree</font>" ;
	}
	else
	{
		chmod ($dir,"u+rwx");
		if(  is_writable($dir) ) {	print "<font color=\"green\">OK $precisionCree (droits changés)</font>" ;	return ; }
		chmod ($dir,"g+rwx");
		if(  is_writable($dir) ) {	print "<font color=\"green\">OK $precisionCree (droits changés)</font>" ;	return ; }
		chmod ($dir,"o+rwx");
		if(  is_writable($dir) ) {	print "<font color=\"green\">OK $precisionCree (droits changés)</font>" ;	return ; }
		print "<font color=\"red\">KO $precisionCree (droits non suffisants)</font>" ;
		die ;
	}
}

static function testEcritureFichier($file,$contenuInitial='') {
	print "<br />Test du droit d'écriture sur le fichier $file : ";
	if(  is_writable($file) ) {
		print "<font color=\"green\">OK </font>" ;
		return ;
	}
	if( ! file_exists($file) && is_writable(dirname ($file)))
	{
		 print "<font color=\"green\">OK (non existant mais dossier ok)</font>" ;
		 if($contenuInitial) file_put_contents($file, $contenuInitial) ;
	} else {
		chmod ($file,"u+rwx");
		if(  is_writable($file) ) {	print "<font color=\"green\">OK (droits changés)</font>" ;	if($contenuInitial) file_put_contents($file, $contenuInitial) ; return ; }
		chmod ($file,"g+rwx");
		if(  is_writable($file) ) {	print "<font color=\"green\">OK (droits changés)</font>" ;	if($contenuInitial) file_put_contents($file, $contenuInitial) ; return ; }
		chmod ($file,"o+rwx");
		if(  is_writable($file) ) {	print "<font color=\"green\">OK (droits changés)</font>" ;	if($contenuInitial) file_put_contents($file, $contenuInitial) ; return ; }
		print "<font color=\"red\">KO</font>" ;
		die ;
	}
}

function mysql_table_exists($table , $db) {
	$requete = 'SHOW TABLES FROM '.$db.' LIKE \''.$table.'\'';
	$exec = mysql_query($requete);
	return mysql_num_rows($exec);
}


static function execSqlFileFromConfig($h,$u,$p,$b,$file) {
    $rsql = mysql_connect($h,$u,$p);
	mysql_select_db($b,$rsql);
    self::execSqlFile($rsql,$file);
}

function execSqlFile($rsql,$file) {
	$requetes = '' ;
	$sql=file($file);
	foreach($sql as $l){ // on le lit
	if (substr(trim($l),0,2)!="--"){ // suppression des commentaires
		$requetes .= $l;
	}
	}
	$reqs = split(";[\n\r]+",$requetes);// on sépare les requêtes
	foreach($reqs as $req){	// et on les éxécute
        //print "<br />exécution de la requete ".$req;
		if (!mysql_query($req,$rsql) && trim($req)!=""){
			print("<br />ERROR : ".$req); // stop si erreur
			print  "<br /><span style='color:red;'>".mysql_errno() . ": " . mysql_error() . "</span>";
		}
	}
}

/**
 * teste si le user a les droits de création et de modification de la base
 * @param string $h
 * @param string $u
 * @param string $p
 * @param string $b
 * @return bool
 */
static function testGrantOnBase($h,$u,$p,$b) {
	print  "<br />Test des privilèges CREATE ALTER DROP ";
	$nocol = rand(1,9999);
	$requete_creation = "CREATE TABLE  IF NOT EXISTS `test_creation` ( `col1` VARCHAR( 1 ) NOT NULL ) ENGINE = MYISAM " ;
	$requete_modification =  "ALTER TABLE `test_creation` ADD `test_ncol_$nocol` VARCHAR( 1 ) NOT NULL ;";
	$requete_suppression =  "DROP TABLE `test_creation`" ;
	self::execRequete($h,$u,$p,$b,$requete_creation,true);
	self::execRequete($h,$u,$p,$b,$requete_modification,true);
	self::execRequete($h,$u,$p,$b,$requete_suppression,true);
	print "<font color=\"green\">OK </font>" ;
}


static function execRequete($h,$u,$p,$b,$req,$die='') {
	$bdd = mysql_connect($h,$u,$p);
	$rsql = mysql_select_db($b,$bdd);
	$res = mysql_query($req) ;
	if (! $res){
		print("<br />ERROR : ".$req); // stop si erreur
		print  "<br /><span style='color:red;'>".mysql_errno() . ": " . mysql_error() . "</span>";
		if($die) die ;
		return false ;
	}
	return $res ;
}

static function getRangForNavicat($navicat) {
	$requete = "SELECT MAX(rang) rg FROM navigation WHERE menuparent = '$navicat' ";
	$res = self::execRequete(MYSQL_HOST,MYSQL_USER,MYSQL_PASS,BASEXHAM,$requete,'1');
	$r = @mysql_fetch_array ( $res,MYSQL_ASSOC) ;
	 return 1 + (int) $r['rg'] ;
}


static function applyPatchs($idsite,$relFic='meta/update.xml',$varRelFic='var/maj/ok.list') {
	/*
MAJ DES PATCHS
*/
$isCcamUpdated = false ;
$xml = simplexml_load_file(URLLOCAL.$relFic);
if(file_exists(URLLOCAL.$varRelFic))
	$tabUpdateOk = explode(',',file_get_contents(URLLOCAL.$varRelFic));
else
	$tabUpdateOk = array();

foreach ($xml->update as $update) {
	//si déja appliquée
	if(in_array($update['id'],$tabUpdateOk))
		continue ;
	//si pour un site particulier
	if( $update['idsite'] && $idsite != $update['idsite'] )
		continue ;
	//sinon on applique les maj
	print "<br /><font color=\"orange\">Application du patch ".$update['id'].": ".utf8_decode((string) $update->description[0])."</font>" ;
	//Renommages de menu
	foreach($update->menu_rename as $menu_rename) {
		$requete = " UPDATE `navigation` SET `libelle` = '%s' WHERE `cle` = '%s'";
		$requete = sprintf($requete,utf8_decode((string) $menu_rename),$menu_rename['cle']);
		self::execRequete(MYSQL_HOST,MYSQL_USER,MYSQL_PASS,BASEXHAM,$requete,'1');
 	}
 	//creation de menu
 	foreach($update->menu_new as $menu_new ) {
 		$requete = " INSERT INTO `navigation` (  `idunique` , `idapplication` , `type` , `rang` , `menuparent` ,  `libelle` , `cle` , `cletotale` ,`noption` , `valeur` , `droit` , `etat` , `classe` , `arguments` , `code` , `lectureseule` , `noprecalc` ) VALUES ";
 		$requete .= " ( '%s', '%s', 'item', %s, '%s', '%s', '%s', '%s', '', '', '%s', '1', '%s', '%s', '', '0', '' )";
		$idUnique = $menu_new->cle['cle'].'_'.$update['id'];
		$idApplication = IDAPPLICATION ;
		$idMenuParent = $menu_new->sub['idUnique'];
		$rang = self::getRangForNavicat($idMenuParent);
		$libelle = utf8_decode(addslashes((string) $menu_new->libelle)) ;
		$cle = $menu_new->cle['cle'] ;
		$cletotale = $menu_new->cletotale['cle'] ;
		$droit = $menu_new->param['droit'];
		$classe = $menu_new->param['classe'];
		$arg = addslashes($menu_new->param['argument']);
		$requete = sprintf($requete,$idUnique,$idApplication,$rang,$idMenuParent,$libelle,$cle,$cletotale,$droit,$classe,$arg,$requete);
		self::execRequete(MYSQL_HOST,MYSQL_USER,MYSQL_PASS,BASEXHAM,$requete,'1');
 	}

 	//création d'option
 	foreach($update->option_new as $option_new ) {
 		$requete = " INSERT INTO `options` ( `idapplication`, `categorie`, `libelle`, `description`, `type`, `choix`, `valeur`, `administrateur`) VALUES ";
		$requete.= "( %s, '%s', '%s', '%s', '%s', '%s', '%s', '%s')";
		$idApplication = (string) IDAPPLICATION ;
		$categorie = utf8_decode((string) $option_new->categorie) ;
		$cle = (string) $option_new->cle ;
		$libelle = utf8_decode(addslashes((string) $option_new->libelle)) ;
		$type  =  (string) $option_new->type ? (string) $option_new->type : 'text';
		$choix =  (string) $option_new->choix ? (string) $option_new->choix : '';
		$defaultValue = utf8_decode(addslashes((string) $option_new->defaultValue)) ;
        $admin =  (string) $option_new->admin ? 1 : 0 ;
		$requete = sprintf($requete,$idApplication,$categorie,$cle,$libelle,$type,$choix,$defaultValue,$admin);
		self::execRequete(MYSQL_HOST,MYSQL_USER,MYSQL_PASS,BASEXHAM,$requete,'1');
 	}

    //update d'option
 	foreach($update->option_upd as $option_upd ) {
 		$requeted = " UPDATE `options` SET ";
		$requetef = " WHERE libelle='".$option_upd->cle."'" ;

		$set  = "libelle='".(string) $option_upd->cle."'" ;
		$set .= (string) $option_upd->libelle ? ",description='".utf8_decode(addslashes((string) $option_upd->libelle))."'" : '' ;
        $set .= (string) $option_upd->type ? ",type='".utf8_decode(addslashes((string) $option_upd->type))."'" : '' ;
		$set .= (string) $option_upd->choix ? ",choix='".utf8_decode(addslashes((string) $option_upd->choix))."'" : '' ;
		$set .= (string) $option_upd->value ? ",valeur='".utf8_decode(addslashes((string) $option_upd->value))."'" : '' ;
		$set .= (string) $option_upd->categorie ? ",categorie='".utf8_decode(addslashes((string) $option_upd->categorie))."'" : '' ;
		$set .= (string) $option_upd->administrateur ? ",administrateur='".((string) $option_upd->administrateur=='true'?1:0)."'" : '' ;

		$requete = $requeted.$set.$requetef ;
		self::execRequete(MYSQL_HOST,MYSQL_USER,MYSQL_PASS,BASEXHAM,$requete,'1');
 	}

 	//exécution de requete précise
 	foreach($update->requete as $requete ) {
		$h = MYSQL_HOST ;
		$u = MYSQL_USER ;
		$p = MYSQL_PASS ;
		$b = BDD ;
 		if ( $requete['base'] == 'xham' ) {
 			$h = MYSQL_HOST ;
			$u = MYSQL_USER ;
			$p = MYSQL_PASS ;
			$b = BASEXHAM ;
		} else if ( $requete['base'] == 'ccam' ) {
		 	$h = MYSQL_HOST ;
			$u = MYSQL_USER ;
			$p = MYSQL_PASS ;
			$b = CCAM_BDD ;
 		} else if ($requete['base'] == 'stats' ) {
 			$h = MYSQL_XARH_HOST ;
			$u = MYSQL_XARH_USER ;
			$p = MYSQL_XARH_PASS ;
			$b = MYSQL_XARH_BDD ;
 		}
		if( $requete['file'] )
			self::execSqlFileFromConfig($h,$u,$p,$b,URLLOCAL.$requete['file']);
		else
			self::execRequete($h,$u,$p,$b,utf8_decode((string) $requete),'1');
 	}

        //ccam ( spécifique tuv2 )
  	//exécution de requete précise
 	foreach($update->ccam as $ccam ) {
        if( ! $isCcamUpdated )
        {
            $h = MYSQL_HOST ;
            $u = MYSQL_USER ;
            $p = MYSQL_PASS ;
            $b = CCAM_BDD ;
            self::execRequete($h,$u,$p,$b,"DELETE FROM `ccam_liste` WHERE `categorie` LIKE 'Diagnostics'" );
            self::execSqlFileFromConfig($h,$u,$p,$b,URLLOCAL.'meta/refccam/ccam_liste.sql');
            self::execRequete($h,$u,$p,$b," TRUNCATE `ccam_actes_diagnostic` " );
            self::execRequete($h,$u,$p,$b," TRUNCATE `ccam_actes_domaine` " );
            self::execRequete($h,$u,$p,$b," TRUNCATE `ccam_actes_pack` " );
            self::execSqlFileFromConfig($h,$u,$p,$b,URLLOCAL.'meta/refccam/terminurg_zccam.sql');
            $h = MYSQL_HOST ;
            $u = MYSQL_USER ;
            $p = MYSQL_PASS ;
            $b = BASEXHAM ;
            self::execRequete($h,$u,$p,$b,"DELETE FROM `listes` WHERE `categorie` = 'recours' " );
            self::execSqlFileFromConfig($h,$u,$p,$b,URLLOCAL.'meta/refccam/motifs.sql');
            $isCcamUpdated = true ;
        }
 	}

	//enregistrement
  	$tabUpdateOk[] = $update['id'];
  	file_put_contents(URLLOCAL.$varRelFic,implode(',',$tabUpdateOk));
}





}

static function genResultQueryConfigFile($file,$host,$base,$user,$pass)
{
	$dom      = new DOMDocument ( '1.0', 'utf8' ) ;
	$result   = $dom->createElement ( 'result', '' ) ;
	$result  -> setAttribute ( 'num', '1' ) ;
	$element  = $dom->createElement ( 'element', '' ) ;
	$id       = $dom -> createElement ( 'id', 1 ) ;
	$nom      = $dom -> createElement ( 'nom', 'Local' ) ;
	$type     = $dom -> createElement ( 'type', 'MySQL' ) ;
	$host     = $dom -> createElement ( 'host', $host ) ;
	$login    = $dom -> createElement ( 'login', $user ) ;
	$password = $dom -> createElement ( 'password', $pass ) ;
	$db       = $dom -> createElement ( 'db', $base ) ;
	$env      = $dom -> createElement ( 'env', 'cfg' ) ;
	$element -> appendChild ( $id ) ;
	$element -> appendChild ( $nom ) ;
	$element -> appendChild ( $type ) ;
	$element -> appendChild ( $host ) ;
	$element -> appendChild ( $login ) ;
	$element -> appendChild ( $password ) ;
	$element -> appendChild ( $db ) ;
	$element -> appendChild ( $env ) ;
	$result  -> appendChild ( $element ) ;
	$dom     -> appendChild ( $result ) ;
	$FIC      = fopen ($file, "w" ) ;
	print "Creation du fichier '$file' => " ;
	if ( fwrite ( $FIC, $dom->saveXML ( ) ) ) print "<font color=\"green\">OK</font>" ;
	else print "<font color=\"red\">KO</font>" ;
	fclose ( $FIC ) ;
	print "<br />";
}


/**
 * Send post data to a URL, handle proxy authentification and anonymous proxies
 * if needed,  PROXY must be formated as  toto:titi@192.168.1.10:8080 or  192.168.1.10:8080
 * do not define PROXY in case of direct connection
 * @param string $url the url
 * @param array $tabData the array with data that must be sended
 * @return string the server body response ( without response headers )
 */
static function sendPostData($fullUrl,$tabDataPost)
{
	$tabMatch = array() ;
	//we parse the server and the relative url script
	if ( preg_match('/http:\/\/([^\/]+)(\/.*)/', $fullUrl,$tabMatch) )
	{
		$server = $tabMatch[1];
		$url = $tabMatch[2];
	}
	else
	{
		$server = '127.0.0.1';
		$url = $fullUrl ;
		//TODO: alternative port at the end of the URL handling
	}
	//proxy parsing
	$proxy_port = $proxy = $proxy_login = $proxy_pass = '' ;
	if( defined('PROXY') && PROXY )
	{
		self::getProxyParams($proxy,$proxy_port,$proxy_login,$proxy_pass);
		$serverTalker = $proxy ;
		$serverTalkerPort = $proxy_port ;
	}
	else
	{
		$serverTalker = $server ;
		$serverTalkerPort = 80 ;
	}
	//buidling of the headers to send
	$content = http_build_query($tabDataPost);
	$content_length = strlen($content);

	$headers= "POST http://$server$url HTTP/1.0\r\nContent-type: application/x-www-form-urlencoded\r\nHost: $server\r\nContent-length: $content_length\r\n";
	//$headers= "GET http://$server$url HTTP/1.0\r\nHost: $server\r\n";
	if( $proxy )
		$headers.='Proxy-Authorization: Basic '.base64_encode($proxy_login.':'.$proxy_pass)."\r\n";
	$headers.="\r\n";
	//opening the connection
	$fp = fsockopen($serverTalker, $serverTalkerPort, $errno, $errstr);
	if (!$fp)
	{
		throw new Exception("ERREUR : $errno - $errstr");
		return false;
	}
	//we send the data
	fputs($fp, $headers);
	fputs($fp, $content);
	//we retrieve the server headers
	$headerServer = '' ;
	$str = '_' ;
	while($str  )
	{
		$str =  trim(fgets($fp, 1024));
		$headerServer .= $str;
	}
	//we retrieve the body
	$ret = "";
	while (!feof($fp))
	{
		$str= fgets($fp, 1024);
		$ret.= $str ;
	}

	fclose($fp);
	return $ret ;
}


	private static function getProxyParams(&$proxy_host,&$proxy_port,&$proxy_login,&$proxy_pass)
	{
		if ( preg_match('/([^:]+):([^@]+)@([^:]+):([^:]+)/', PROXY,$tabMatch) )
		{
			$proxy_login = $tabMatch[1];
			$proxy_pass = $tabMatch[2];
			$proxy_host = $tabMatch[3];
			$proxy_port = $tabMatch[4];
		}
		else
		{
			list($proxy_host,$proxy_port) = explode(':',PROXY);
			$proxy_login = '';
			$proxy_pass = '';
		}
	}

/**
 *
 * @param <type> $url
 * @param <type> $fileAbsoluteLocalUrl
 * @param <type> $message
 * @return bool
 */
	static function downloadFile_curl ($url,$fileAbsoluteLocalUrl,&$message)
	{
		$ch = curl_init();
		if( defined('PROXY') && PROXY )
		{
				$proxy_port = $proxy_host = $proxy_login = $proxy_pass = '' ;
				self::getProxyParams($proxy_host,$proxy_port,$proxy_login,$proxy_pass);
				curl_setopt($ch, CURLOPT_PROXY, "$proxy_host:$proxy_port");
				if ( $proxy_login )
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, "$proxy_login:$proxy_pass");
		}
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		// Si le timeout est à 0, il n'y a pas de limite de temps de connexion et
		// le téléchargement ne devrait pas échouer en théorie...
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
		$data = curl_exec($ch);
		$message = curl_error($ch);
		$errorNumber = curl_errno($ch);
		curl_close($ch);
		file_put_contents($fileAbsoluteLocalUrl,$data);
		return (  $errorNumber== 0 );
	}




	static function downloadFile_wget($url,$fileAbsoluteLocalUrl,&$message)
	{
		$proxyOpts = '' ;
		$optionsEnv = null ;
		if( defined('PROXY') && PROXY )
		{
			$proxy_port = $proxy_host = $proxy_login = $proxy_pass = '' ;
			self::getProxyParams($proxy_host,$proxy_port,$proxy_login,$proxy_pass);
			$proxyOpts = "  " ;
			if ( $proxy_login )
				$proxyOpts .= " --proxy-user=$proxy_login --proxy-password=$proxy_pass "  ;
			$optionsEnv = array('http_proxy'=>"http://$proxy_host:$proxy_port");
		}	
		return XhamTools::_fork_process("wget $url $proxyOpts -O $fileAbsoluteLocalUrl", $message,$message,false,null,$optionsEnv);
	}


	static function downloadFile($url,$fileAbsoluteLocalUrl,&$message='')
	{
		switch(HTTP_DOWNLOAD_CLIENT)
		{
			case 'wget':
				return self::downloadFile_wget($url,$fileAbsoluteLocalUrl,$message);
			case 'php':
				return file_put_contents($fileAbsoluteLocalUrl, file_get_contents($url));
			case 'curl':
				return downloadFile_curl($url,$fileAbsoluteLocalUrl,$message);
			case 'xham':
			default :
				return file_put_contents($fileAbsoluteLocalUrl,self::sendPostData($url,array()));
		}
	}


	static function getUrlContents($url)
	{
		$message = '';
		$tmpFic = URLLOCAL.'temp/tmp'.rand(1,999999999999999);
		self::downloadFile($url,$tmpFic,$message);
		return file_get_contents($tmpFic);
	}


}
?>
