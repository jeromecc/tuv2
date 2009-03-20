<?php


class clVariables {

static function getVar($var) {
if(! defined('TABLEGLOB'))
	define('TABLEGLOB','variables');		
$param['table']= TABLEGLOB;
$param['cw']="WHERE nom='$var' AND idapplication = ".IDAPPLICATION;
$req = new clResultQuery ;
$res = $req -> Execute ( "Fichier", "getGenXHAM", $param, "ResultQuery" ) ; 
if( $res['INDIC_SVC'][2] == 0)
	return false;
return $res['valeur'][0];
}

static function setVar($var,$val){
if(! defined('TABLEGLOB'))
	define('TABLEGLOB','variables');
$param = array();
$param['valeur']= $val;
$param['nom']= $var;
$param['idapplication']= IDAPPLICATION;
$requete = new clRequete(BASEXHAM,TABLEGLOB,$param);
$requete->uoiRecord(" nom='$var' AND idapplication='".IDAPPLICATION."'");
}
}
?>



