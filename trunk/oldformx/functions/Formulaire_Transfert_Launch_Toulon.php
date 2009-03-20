<?php

// Cette fonction n'est utilisable que dans le cas des transferts entre Toulon et La Seyne.

function Formulaire_Transfert_Launch ( $formx ) {
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
	eko ( $res ) ;
	
	$data['idpatient'] 				= $res['idpatient'][0] ;
	$data['idu'] 					= $res['idu'][0] ;
	$data['ilp'] 					= $res['ilp'][0] ;
	$data['nsej'] 					= $res['nsej'][0] ;
	$data['uf'] 					= $res['uf'][0] ;
	$data['nom'] 					= $res['nom'][0] ;
	$data['prenom'] 				= $res['prenom'][0] ;
	$data['sexe'] 					= $res['sexe'][0] ;
	$data['dt_naissance'] 			= $res['dt_naissance'][0] ;
	$data['adresse_libre'] 			= $res['adresse_libre'][0] ;
	$data['adresse_cp'] 			= $res['adresse_cp'][0] ;
	$data['adresse_ville'] 			= $res['adresse_ville'][0] ;
	$data['telephone'] 				= $res['telephone'][0] ;
	$data['prevenir'] 				= $res['prevenir'][0] ;
	$data['medecin_traitant'] 		= $res['medecin_traitant'][0] ;
	$data['dt_admission'] 			= $res['dt_admission'][0] ;
	$data['adresseur'] 				= $res['adresseur'][0] ;
	$data['mode_admission'] 		= $res['mode_admission'][0] ;
	$data['dt_examen'] 				= $res['dt_examen'][0] ;
	$data['medecin_urgences'] 		= $res['medecin_urgences'][0] ;
	$data['ide'] 					= $res['ide'][0] ;
	$data['salle_examen'] 			= $res['salle_examen'][0] ;
	$data['motif_recours']			= $res['motif_recours'][0] ;
	$data['code_gravite']			= $res['code_gravite'][0] ;
	$data['ccmu'] 					= $res['ccmu'][0] ;
	$data['gemsa'] 					= $res['gemsa'][0] ;
	$data['traumato'] 				= $res['traumato'][0] ;
	$data['dest_souhaitee'] 		= $res['dest_souhaitee'][0] ;
	$data['dest_attendue'] 			= $res['dest_attendue'][0] ;
	$data['moyen_transport'] 		= $res['moyen_transport'][0] ;
	$data['motif_transfert'] 		= $res['motif_transfert'][0] ;
	$data['dt_sortie'] 				= $res['dt_sortie'][0] ;
	$data['recours_code'] 			= $res['recours_code'][0] ;
	$data['recours_categorie'] 		= $res['recours_categorie'][0] ;
	$data['type_destination'] 		= $res['type_destination'][0] ;
	$data['diagnostic_categorie'] 	= $res['diagnostic_categorie'][0] ;
	$data['diagnostic_libelle'] 	= $res['diagnostic_libelle'][0] ;
	$data['diagnostic_code'] 		= $res['diagnostic_code'][0] ;
	$data['etatUHCD'] 				= $res['etatUHCD'][0] ;
	$data['dt_UHCD'] 				= $res['dt_UHCD'][0] ;
	$data['provenance'] 			= $res['provenance'][0] ;
	$data['dest_pmsi'] 				= $res['dest_pmsi'][0] ;
	$data['orientation'] 			= $res['orientation'][0] ;
	$data['iduser'] 				= 'Transfert' ;
	$data['manuel'] 				= $res['manuel'][0] ;
	
	
	if ( $res['uf'][0] == '6004' ) {
		$data['uf'] = '6006' ;
		$base = 'terminal2_tuv2' ;
	} elseif ( $res['uf'][0] == '6006' ) {
		$data['uf'] = '6004' ;
		$base = 'terminal_tuv2' ;		
	} elseif ( $res['uf'][0] == 'UHCD6004' ) {
                $data['uf'] = 'UHCD6006' ;
                $base = 'terminal2_tuv2' ;
	} elseif ( $res['uf'][0] == 'UHCD6006' ) {
                $data['uf'] = 'UHCD6004' ;
                $base = 'terminal_tuv2' ;
        } 

	
	
	$req = new clRequete ( $base, PPRESENTS, $data ) ;
	$req->addRecord ( ) ;
	
	
	$req = new clRequete ( BDD, PPRESENTS ) ;
	$req->delRecord ( "idpatient=$idpatient" ) ;
	
	header ( 'Location:index.php?navi=TGlzdGVfUHJlc2VudHM=' ) ;
	
	//eko ( $data ) ;
	
	/*
	$param = array();
	$param['etat'] = "a";
	$param['idpatient'] = $idpatient;
	$param['id_instance'] = $id_instance;
	$param['dt_creation'] = date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":00";
	//eko ($param);
	$req = new clRequete(BDD,"radios",$param);
	$req->addRecord();
	*/
	
	//$res = $req -> Execute ( "Fichier", "putFormulaireRadioData", $param, "ResultQuery" ) ;
	//eko($res);
	
	return "";
}
?>
