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


/**
 *
 * TODO
 * copier coller d'un script
 *
 */
	function vrac()
	{


		
	}



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


static function testModule($module)
{
	print "<br />Test de la présence du module PHP $module  ";

	if( extension_loaded($module) )
		print "<font color=\"green\">OK</font>" ;
	else
	{
		print "<font color=\"red\">KO</font>" ;
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
 		} else if ($requete['base'] == 'stats' ) {
 			$h = MYSQL_XARH_HOST ;
			$u = MYSQL_XARH_USER ;
			$p = MYSQL_XARH_PASS ;
			$b = MYSQL_XARH_BDD ;
 		}
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







}
?>
