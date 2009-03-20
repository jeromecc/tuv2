<?php
/*
 * Created on 7 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
class XhamVariables {

	static function getVar($var) {
		global $xham ;
		if(! defined('TABLEGLOB'))
			define('TABLEGLOB','variables');		
		$param['table']= TABLEGLOB;
		$param['cw']="WHERE nom='$var' AND idapplication = ".IDAPPLICATION;
		$res = $xham -> Execute ( "Fichier", "getGenXHAM", $param, "ResultQuery" ) ; 
		if( $res['INDIC_SVC'][2] == 0)
			return false;
		return $res['valeur'][0];
	}

	static function setVar($var,$val){
		global $xham ;
		if(! defined('TABLEGLOB'))
			define('TABLEGLOB','variables');
		$param = array();
		$param['valeur']= $val;
		$param['nom']= $var;
		$param['idapplication']= IDAPPLICATION;
		$xham->newRequete(BASEXHAM,TABLEGLOB,$param);
		$xham->uoiRecord(" nom='$var' AND idapplication='".IDAPPLICATION."'");
	}
}
?>
