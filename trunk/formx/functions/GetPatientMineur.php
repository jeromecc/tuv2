<?php
function getPatientMineur($formx) {

global $tool;
$idu = $formx->getVar('ids');

$req = new clResultQuery ;
$param = array();

$param['cw']="dt_naissance";
$param['idu']=$idu;
$res = $req -> Execute ( "Fichier", "getInfoPatientFromIDU", $param, "ResultQuery" ) ;

$DateNPat=$res['dt_naissance'][0];
//eko($DateNPat);

// Calcul sur les diff�rentes dates.
    $age = new clDate ( $DateNPat ) ;
    $dateSimple = $age -> getDate ( "d-m-Y" ) ;
    $dateComple = $age -> getDateText ( ) ;
    $duree = new clDuree ( ) ;

		$duree->getAgePrecis ( $age -> getTimestamp ( ) );
		if ($duree->getYears()<18)
		{ return "mineur"; }
		else
		{ return "majeur"; }
}

?>
