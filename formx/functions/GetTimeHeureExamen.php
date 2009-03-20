<?php
function GetTimeHeureExamen($formx) {
  
  global $session ;
  
  $id_instance = $formx->getIdInstance ( ) ;
	$idu         = $formx->getVar ( 'ids' ) ;
	$idpatient   = $session->getNavi ( 2 ) ;
	
	$req = new clResultQuery ;
	$param = array ( ) ;
	
	$param['cw'] = "where idpatient='".$idpatient."'" ;
	$param['table'] = PPRESENTS ;
	$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
	//$idpatient = $res['idpatient'][0] ;
	//eko ( $res ) ;
	
	
	$posted           =   explode(" ",$res['dt_examen'][0]);
	
	return $posted[1];
}
?>
