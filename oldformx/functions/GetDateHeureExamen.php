<?php
function GetDateHeureExamen($formx) {
  
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
	
	
	$posted  = explode(" ",$res['dt_examen'][0]);
	$posted  = explode("-",$posted[0]);
	//eko ( $posted  ) ;
	return $posted[2]."-".$posted[1]."-".$posted[0];
	
	
}
?>
