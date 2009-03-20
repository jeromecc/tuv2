<?php
function getDateE3($formx) {

global $tool;
$idu = $formx->getVar('ids');

$req = new clResultQuery ;
$param = array();

$param['cw']="dt_naissance";
$param['idu']=$idu;
$res = $req -> Execute ( "Fichier", "getInfoPatientFromIDU", $param, "ResultQuery" ) ;
//eko($res);
$DateNPat=$res['dt_naissance'][0];
//eko($DateNPat);
$tab= explode(" ",$DateNPat);
$tab2= explode("-",$tab[0]);
//eko($tab2);

// Calcul sur les différentes dates.
    //$age = new clDate ( $DateNPat ) ;
    //$dateSimple = $age -> getDate ( "d-m-Y" ) ;
    //$dateComple = $age -> getDateText ( ) ;
    //$duree = new clDuree ( ) ;
    //eko($dateComple);
    
return "".utf8_encode($tab2[2])."/".utf8_encode($tab2[1])."/".utf8_encode($tab2[0])."";


}

?>
