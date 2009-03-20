<?php
function getAge2($formx) {

global $tool;
$idu = $formx->getVar('ids');

$req = new clResultQuery ;
$param = array();

$param['cw']="dt_naissance";
$param['idu']=$idu;
$res = $req -> Execute ( "Fichier", "getInfoPatientFromIDU", $param, "ResultQuery" ) ;

$DateNPat=$res['dt_naissance'][0];
//eko($DateNPat);

// Calcul sur les différentes dates.
    $age = new clDate ( $DateNPat ) ;
    $dateSimple = $age -> getDate ( "d-m-Y" ) ;
    $dateComple = $age -> getDateText ( ) ;
    $duree = new clDuree ( ) ;

return "".str_replace("<br>"," et ",$duree->getAgePrecis ( $age -> getTimestamp ( ) ) )."";


}

?>
