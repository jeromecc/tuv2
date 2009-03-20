<?php

function NetForce_Data_Liste_Formulaire_Saisi ( $formx ) {
	global $session ;
	$id_instance = $formx->getIdInstance ( ) ;
	//eko ($id_instance);
	$idu         = $formx->getVar ( 'ids' ) ;
	//eko ($idu);
	$idpatient   = $session->getNavi ( 2 ) ;
	//eko ($idpatient);
	
	$param = array();
	
	$param['idu'] = $idu ;
  $res = $req -> Execute ( "Fichier", "netForceDataBase_getAllFormulaire_ids",$param,"ResultQuery" );
	
  $liste = array();
	
	for ( $i=0 ; $i <=$res[INDIC_SVC][2] ; $i++ )
    {
    $liste[$res["id_instance"][$i]] = utf8_encode($res["dt_creation"][$i]." --- ".$res["libelle"][$i]);
    } 
	//header ( 'Location:index.php?navi=TGlzdGVfUHJlc2VudHM=' ) ;

	return utf8_encode(liste);
}
?>
