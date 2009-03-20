<?php

function Init_NetForce_Data_Base ( $formx ) {
	global $session ;
	$id_instance = $formx->getIdInstance ( ) ;
	//eko ($id_instance);
	$idu         = $formx->getVar ( 'ids' ) ;
	//eko ($idu);
	$idpatient   = $session->getNavi ( 2 ) ;
	//eko ($idpatient);
	
	$req = new clResultQuery ;
	$param = array ( ) ;
	
	$param['cw'] = "where idpatient='".$idpatient."'" ;
	$param['table'] = PPRESENTS ;
	$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
	//$idpatient = $res['idpatient'][0] ;
	//eko ( $res ) ;
	
	$param['idu'] = $idu ;
  $res = $req -> Execute ( "Fichier", "netForceDataBase_getAllFormulaire_ids",$param,"ResultQuery" );
	eko ($res[INDIC_SVC][15]);
	$liste = array();
	
	for ( $i=0 ; $i <=$res[INDIC_SVC][2] ; $i++ )
    {
    $liste[utf8_encode($res["id_instance"][$i])] = $res["dt_creation"][$i]." --- ".$res["libelle"][$i];
    } 
	//header ( 'Location:index.php?navi=TGlzdGVfUHJlc2VudHM=' ) ;
	eko ($liste);
	return $liste;
}
?>
