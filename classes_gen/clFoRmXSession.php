<?php


class clFoRmXSession {
 //tableau de correspondancee droitsid/formulaires par parcours du repertoire local
 public $localDroits;	
 public $objXham;	
 public $infos;	
 public $tableVariables ;
 //mode iframe
 //droitgen 
 function clFoRmXSession() {
 	global $session;
 	global $xham;
 	$this->setDefaultEnv();
 	if(defined("VERSIONXHAM") && VERSIONXHAM == "2" ) { $session =  $xham  ; }
 	$this->objXham = $session ;
 	$this->getTabDroitsLocaux();
 	$this->setDefaultEnv();
 }
 /**
  *
  * @global <type> $formxSession
  * @return ClFoRmXSession
  */
 static function getInstance() 
  {
 	global $formxSession ;
 	if( ! is_object($formxSession))	
 		$formxSession = new clFoRmXSession();
 	return $formxSession ;
  }
 
 function getObjRequete($data='',$base='',$table='') {
	if( !$table ) $table = $this->tableInstances ;
	if( !$base ) $base = $this->baseInstances ;
 	if(defined("VERSIONXHAM") && VERSIONXHAM == "2" ) return new XhamRequete($base, $table , $data);
 	return new clRequete($base, $table , $data);
 }

 function getObjRequeteGlobals($data='') {
 	if(defined("VERSIONXHAM") && VERSIONXHAM == "2" ) return new XhamRequete($this->baseInstances, $this->tableVariables , $data);
 	return new clRequete($this->baseInstances, $this->tableVariables , $data);
 }

 function getObjResultQuery()
 {
     if ( defined("VERSIONXHAM") && VERSIONXHAM == "2" )
     {
	 $rq = new XhamQuery() ;
     }
     else
     {
	 $rq = new clResultQuery() ;
     }
     return $rq;
 }

 
 function setDefaultEnv() {
 	$this->tableInstances = defined('FX_INSTANCES')?FX_INSTANCES:FX_TABLEFORMX ;
 	$this->tableInstances = defined('TABLEFORMX')?TABLEFORMX:$this->tableInstances ;
 	$this->baseInstances = defined('FX_BDD')?FX_BDD:BDD ;
 	$this->tableVariables = defined('FX_GLOBVARS')?FX_GLOBVARS:$this->tableVariables ;
 	$this->tableVariables = defined('TABLEFORMXGLOBVARS')?TABLEFORMXGLOBVARS:$this->tableVariables ;
 	$this->tableVariables = defined('FX_TABLEFORMXGLOBVARS')?FX_TABLEFORMXGLOBVARS:$this->tableVariables ;

 	if(defined('FX_URLLOCAL')) 			$this->xmlLocation = FX_URLLOCAL ;
 	if(defined('FX_FORMX_LOCATION')) 	$this->xmlLocation = FX_FORMX_LOCATION ;
 	if(defined('FORMX_LOCATION')) 		$this->xmlLocation = FORMX_LOCATION ;

 	$this->droitGen = (defined('FX_DROITGENFORMX')?FX_DROITGENFORMX:DROITGENFORMX) ;
 	$this->droitGen = ($this->droitGen?$this->droitGen:'gest_formx');
 	$this->idApplication = (defined('FX_IDAPPLICATION')?FX_IDAPPLICATION:IDAPPLICATION) ;
 	$this->tableDyn = (defined('FX_TABLEFORMXDYNTAB')?FX_TABLEFORMXDYNTAB:FX_TABLEDYN);
 	$this->urlImglogo = FX_URLIMGLOGO ;
	$this->urlImglogoWeb = FX_URLIMGLOGOWEB ;
 	$this->url = FX_URL ;
	$this->urlCssWeb = FX_URL.'css/';
 	$this->urlCache = FX_URLCACHE ;
 	$this->urlCacheWeb = FX_URLCACHEWEB ;
 	$this->urlImgCal = FX_URLIMGCAL ;
 	$this->urlImgEdi = (defined('FX_URLIMGEDI')?FX_URLIMGEDI:$this->url."imprimer.png");
 	$this->urlImgRien = (defined('FX_URLIMGRIEN')?FX_URLIMGRIEN:$this->url."none.gif");
 	$this->urlImgClo = (defined('FX_URLIMGCLO')?FX_URLIMGCLO:$this->url."closepiti.gif"); 	
 	$this->droit = $this->droitGen ;//remplace def dans formx de $this->droit
 	$this->maxSizeUpload = (defined('FX_MAXSIZEUPLOAD')?FX_MAXSIZEUPLOAD:10) ;
 	$this->idApplication = IDAPPLICATION ;
 }


 function getLocalUrlTemplates()
 {
	return $this->xmlLocation.'templates/';
 }

 function getLocalUrlCache()
 {
	 return $this->urlCache ;
 }

 function getWebUrlCache()
 {
	 return $this->urlCacheWeb ;
 }

 function getWebUrl()
 {
	 return $this->url ;
 }



function getWebUrlLogo()
{
	return$this->urlImglogoWeb ;
}

function getWebUrlCss()
{
	return $this->urlCssWeb ;
}


 function getFxLocalPath() {
 	return 	$this->xmlLocation ;
 }

 function getFxTriggerPath() {
 	return 	$this->xmlLocation.'triggers/' ;
 }

 
 function getTable() {
 	return $this->tableInstances ;
 }


function getTableGlobals() {
 	return $this->tableVariables ;
 }
 
function getBase() {
	return $this->baseInstances ;
}

 function getDroit($opt,$c="r") {
  	return $this->objXham->getDroit($opt,$c);
 }
 
  function getOption($opt) {
  	if(defined("VERSIONXHAM") && VERSIONXHAM == "2" )  
 		return 	$this->objXham->getOption($opt); 
 	else {
 		global $options;
 		return 	$options->getOption($opt);
 	}
 }
 
 function getNullValues() {
 if(defined('FX_NULLVALUES'))
 	$this->nullValues =	utf8_encode(FX_NULLVALUES);
 else
 	$this->nullValues = utf8_encode("Champ Non Précisé.");
 return explode('|',$this->nullValues);	
 }
 
 function genNavi() {
 	$tabArgs = func_get_args();
 	//eko ("return \$session->genNavi(\"".implode('","',$tabArgs)."\");");
 	$ret = "" ;
 	eval ( "\$ret =  \$this->objXham->genNavi(\"".implode('","',$tabArgs)."\");") ;
	return $ret;
 }
 
 function getNavi($arg) {
 	return 	$this->objXham->getNavi($arg);
 }
 
 function getUser() {
 	if(defined("VERSIONXHAM") && VERSIONXHAM == "2" ) { 
 		return 	$this->objXham->user->getIdentite(); }
 	 return 	$this->objXham->getUser();
 }
 
 function addErreur($err) {
 global $errs; 
 if(defined("VERSIONXHAM") && VERSIONXHAM == "2" ) { return 	$this->objXham->errs-> addErreur($err); }
 return $errs-> addErreur($err);
 }
 
 //Prend en compte la mise à jour des droits dans les fichiers xml
//et les applique en base
function getTabDroitsLocaux() {
	$dos=opendir($this->xmlLocation);
	$liste = array();
	 while ($fich = readdir($dos)) {
		if (ereg("^.*\.xml$",$fich)) {
		//on ouvre le fichier pour en trouver les caracteristiques principales
		$xml =  simplexml_load_file($this->xmlLocation.$fich);
		if (! $xml) {
			eko("pb chargement de l'instance");
		} else {
			if($xml['access'])
				$liste[(string) $xml['id']] =  $xml['access'] ;
		}
	  }
	}
 $this->localDroits = $liste;
 }	
 
 
 function loadFunc($func) 
 {
	$location = $this->getFxLocalPath() ;
	if( class_exists('objPlugin'))
	{
	    $location= objPlugin::getAnEventualOtherLocationForFormxFunc($location, $func);
	    
	}
	if ( strpos($func,'/')) {
		$funcname = basename($func);
		require_once ($location.'functions/'.$func.'.php');
		return $funcname ;
	} else if (file_exists($location.'functions/'.$func.'.php'))  {
		require_once ($location.'functions/'.$func.'.php');
		return $func ;
	} else if (file_exists($location.'functions/helpers/'.$func.'.php'))  {
		require_once ($location.'functions/helpers/'.$func.'.php');
		return $func ;
	} else if (file_exists($location.'functions/getters/'.$func.'.php'))  {
		require_once ($location.'functions/getters/'.$func.'.php');
		return $func ;
	} else if (file_exists($location.'functions/setters/'.$func.'.php'))  {
		require_once ($location.'functions/setters/'.$func.'.php');
		return $func ;
	}
} 
 
 
	
}

