<?php

// Par Damien Borel <dborel@ch-hyeres.fr> le 06.09.2007

// Cette fonction n'est utilisable que dans le cas des transferts entre Toulon et La Seyne.
// 
// Tant que je n'aurai pas pu tester les liens symboliques dans un environnement de production, nous utiliserons 
// ce script lourd (recopie de toutes les informations d'un patient des différentes tables). A savoir que les transferts
// sont rares et que ce n'est pas bloquant du tout si la page est un peu longue (3 secondes).

function Formulaire_Transfert_Launch ( $formx ) {
	global $session ;
	
	
	// Configuration Toulon (1) / La seyne (1)
	/*
	$uf1 = '6006' ;
	$uf2 = '6004' ;
	$ufu1 = '6010' ;
	$ufu2 = '6005' ;
	$bdd1 = 'terminal_tuv2' ;
	$bddc1 = 'terminal_ccam' ;
	$bdd2 = 'terminal2_tuv2' ;
	$bddc2 = 'terminal2_ccam' ;
	*/
	
	// configuration Avignon Adultes / Gynéco
	
	$uf1 = '1103' ;
	$uf2 = '3251' ;
	$ufu1 = '1101' ;
	$ufu2 = '1101' ;
	$bdd1 = 'terminala_tuv2' ;
	$bddc1 = 'terminala_ccam' ; 
	$bdd2  = 'terminalg_tuv2' ; 
	$bddc2 = 'terminalg_ccam' ; 
	

		
	// Configuration de test
	/*
	$uf1 = '2701' ;
	$uf2 = '3000' ;
	$ufu1 = 'UHCD6004' ;
	$ufu2 = 'UHCD6006' ;
	$bdd1 = 'xham_tuv2' ;
	$bddc1 = 'xham_ccam' ;
	$bdd2 = 'terminal_tuv2' ;
	$bddc2 = 'terminal_ccam' ;
	*/

	// Récupération des informations globales du patient.
	$id_instance = $formx->getIdInstance ( ) ;
	$idu         = $formx->getVar ( 'ids' ) ;
	$idpatient   = $session->getNavi ( 2 ) ;

	// Récupération de toutes les informations du patient sur le terminal source.	
	$req = new clResultQuery ;
	$param = array ( ) ;
	$param['cw'] = "where idpatient='".$idpatient."'" ;
	$param['table'] = PPRESENTS ;
	$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
	// eko ( $res ) ;
	$idu = $res['idu'][0] ;
	// Préparation des données du patient.
	//$data['idpatient'] 				= $res['idpatient'][0] ;
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
//	$data['salle_examen'] 			= $res['salle_examen'][0] ;
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
	
	// En fonction de l'uf du patient, on détermine son uf de destination.
	// Par exemple, si l'UF du patient était celle des urgences de Toulon, alors
	// la nouvelle UF sera cette des urgences de La Seyne.
	// On détermine la table de destination aussi à cette étape.
	if ( $res['uf'][0] == $uf1 ) {
		$data['uf'] = $uf2 ;
		$base = $bdd2 ;
		$basec = $bddc2 ;
	} elseif ( $res['uf'][0] == $uf2 ) {
		$data['uf'] = $uf1 ;
		$base = $bdd1 ;
		$basec = $bddc1 ;		
	} elseif ( BDD == $bdd1 ) {
                $data['uf'] = $ufu2 ;
                $base = $bdd2 ;
                $basec = $bddc2 ;
	} elseif ( BDD == $bdd2 ) {
                $data['uf'] = $ufu1 ;
                $base = $bdd1 ;
                $basec = $bddc1 ;
    }	

	// On vérifie si le patient est déjà présent ou non.
	$param['cw'] = "where nsej='".$res['nsej'][0]."' UNION select * from patients_sortis where nsej='".$res['nsej'][0]."'" ;
	$param['table'] = PPRESENTS ;
	$resEx = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery", $base ) ;
	if ( $resEx['INDIC_SVC'][2] ) {
		$newidpatient = $resEx['idpatient'][0] ;
		$exist = 1 ;
	} else {
		// Création du patient sur le terminal de destination.
		$req = new clRequete ( $base, PPRESENTS, $data ) ;
		$sql = $req->addRecord ( ) ;
		$newidpatient = $sql['cur_id'] ;
		$exist = 0 ;
	}
	// Suppression du patient sur le terminal source.
	$req = new clRequete ( BDD, PPRESENTS ) ;
	$req->delRecord ( "idpatient=$idpatient" ) ;

	// Gestion des documents
	// Suppression des documents édités du patient sur le terminal de destination.
	$req = new clRequete ( $base, "editions" ) ;
	$req->delRecord ( "idpatient=$newidpatient" ) ;
	// Récupération des documents édités du patient sur le terminal source.
	$req = new clResultQuery ;
	$param['cw'] = "where idpatient='".$idpatient."'" ;
	$res = $req -> Execute ( "Fichier", "getDocumentsEdites", $param, "ResultQuery" ) ;
	// Création de chaque document du terminal source sur le terminal de destination.
	for ( $i = 0 ; isset ( $res['idedition'][$i]) ; $i++ ) {
		unset ( $data ) ;
		//$data['idedition']  = $res['idedition'][$i] ;
		$data['iddocument'] = $res['iddocument'][$i] ;
		$data['idpatient']  = $newidpatient ;
		$data['nomedition'] = $res['nomedition'][$i] ;
		$data['urledition'] = $res['urledition'][$i] ;
		$data['date']       = $res['date'][$i] ;
		$data['iduser']     = $res['iduser'][$i] ;
		$req = new clRequete ( $base, "editions", $data ) ;
		$req->addRecord ( ) ;
	}
	$req = new clRequete ( BDD, "editions" ) ;
	$req->delRecord ( "idpatient=$idpatient" ) ;
	
	// Gestion des messages d'alertes.
	// Suppression des messages du patient sur le terminal de destination.
	$req = new clRequete ( $base, "logs_mails" ) ;
	$req->delRecord ( "idpatient=$newidpatient" ) ;
	// Récupération des documents édités du patient sur le terminal source.
	$req = new clResultQuery ;
	$param['cw'] = "where idpatient='".$idpatient."'" ;
	$res = $req -> Execute ( "Fichier", "getMessages", $param, "ResultQuery" ) ;
	// Création de chaque message du terminal source sur le terminal de destination.
	for ( $i = 0 ; isset ( $res['idmail'][$i]) ; $i++ ) {
		unset ( $data ) ;
		//$data['idmail']    = $res['idmail'][$i] ;
		$data['idpatient'] = $newidpatient ;
		$data['dt_mail']   = $res['dt_mail'][$i] ;
		$data['contenu']   = $res['contenu'][$i] ;
		$data['nsej']      = $res['nsej'][$i] ;
		$data['type_mail'] = $res['type_mail'][$i] ;
		$data['traite']    = $res['traite'][$i] ;
		$data['erreur']    = $res['erreur'][$i] ;
		$data['positif']   = $res['positif'][$i] ;
		$req = new clRequete ( $base, "logs_mails", $data ) ;
		$req->addRecord ( ) ;
	}
	$req = new clRequete ( BDD, "logs_mails" ) ;
	$req->delRecord ( "idpatient=$idpatient" ) ;
	
	// Gestion des radios.
	// Suppression des radios du patient sur le terminal de destination.
	$req = new clRequete ( $base, "radios" ) ;
	$req->delRecord ( "idpatient=$newidpatient" ) ;
	// Récupération des documents édités du patient sur le terminal source.
	$req = new clResultQuery ;
	$param['cw'] = "where idpatient='".$idpatient."'" ;
	$res = $req -> Execute ( "Fichier", "getRadiosGlob", $param, "ResultQuery" ) ;
	// Création de chaque document du terminal source sur le terminal de destination.
	for ( $i = 0 ; isset ( $res['idradio'][$i]) ; $i++ ) {
		unset ( $data ) ;
		
		$data['idpatient']     = $newidpatient ;
		//$data['idradio']       = $res['idradio'][$i] ;
		$data['etat']          = $res['etat'][$i] ;
		$data['idapplication'] = $res['idapplication'][$i] ;
		$data['id_instance']   = $res['id_instance'][$i] ;
		$data['retour']        = $res['retour'][$i] ;
		$data['retourid']      = $res['retourid'][$i] ;
		$data['commentaire']   = $res['commentaire'][$i] ;
		$data['dt_retour']     = $res['dt_retour'][$i] ;
		$data['dt_creation']   = $res['dt_creation'][$i] ;
		$data['dt_pec']        = $res['dt_pec'][$i] ;
		$data['dt_encours']    = $res['dt_encours'][$i] ;
		$data['dt_fin']        = $res['dt_fin'][$i] ;
		$data['radiologue']    = $res['radiologue'][$i] ;
		$data['adeli']         = $res['adeli'][$i] ;
		$data['ccam']          = $res['ccam'][$i] ;
		
		$req = new clRequete ( $base, "radios", $data ) ;
		$req->addRecord ( ) ;
	}
	$req = new clRequete ( BDD, "radios" ) ;
	$req->delRecord ( "idpatient=$idpatient" ) ;
	
	/*
	// Gestion des formulaires.
	// Suppression des formulaires du patient sur le terminal de destination.
	$req = new clRequete ( $base, "formx" ) ;
	//eko ( $base ) ;
	if ( ! $exist ) $req->delRecord ( "ids='$idu'" ) ;
	// Récupération des documents édités du patient sur le terminal source.
	$req = new clResultQuery ;
	$param['table'] = 'formx' ;
	$param['cw'] = "where ids='$idu'" ;
	$res = $req -> Execute ( "Fichier", "FX_getGen", $param, "ResultQuery" ) ;
	// Création de chaque document du terminal source sur le terminal de destination.
	for ( $i = 0 ; isset ( $res['id_instance'][$i]) ; $i++ ) {
		unset ( $data ) ;
		$data['id_instance'] = $res['id_instance'][$i] ;
		$data['ids']         = $res['ids'][$i] ;
		$data['dt_creation'] = $res['dt_creation'][$i] ;
		$data['dt_modif']    = $res['dt_modif'][$i] ;
		$data['idformx']     = $res['idformx'][$i] ;
		$data['libelle']     = $res['libelle'][$i] ;
		$data['etape']       = $res['etape'][$i] ;
		$data['status']      = 'F' ;
		$data['data']	     = $res['data'][$i] ;
		$data['author']	     = $res['author'][$i] ;	
		$req = new clRequete ( $base, "formx", $data ) ;
		$req->addRecord ( ) ;
	}
	*/
	
	// Gestion des actes et diagnotics
	// Suppression des actes et diags du patient sur le terminal de destination.
	$req = new clRequete ( $basec, "ccam_cotation_actes" ) ;
	$req->delRecord ( "idEvent=$newidpatient" ) ;
	// Récupération des actes et des diags du patient sur le terminal source.
	$req = new clResultQuery ;
	$param['cw'] = "idEvent='".$idpatient."'" ;
	$res = $req -> Execute ( "Fichier", "CCAM_getActesDiagsCotation", $param, "ResultQuery" ) ;
	// Création de chaque document du terminal source sur le terminal de destination.
	for ( $i = 0 ; isset ( $res['identifiant'][$i]) ; $i++ ) {
		unset ( $data ) ;
		
		$data['idEvent']                    = $newidpatient ;
		$data['dateEvent']                  = $res['dateEvent'][$i] ;
		$data['idDomaine']                  = $res['idDomaine'][$i] ;
		$data['dtFinInterv']                = $res['dtFinInterv'][$i] ;
		$data['idu']                        = $res['idu'][$i] ;
		$data['ipp']                        = $res['ipp'][$i] ;
		$data['nomu']                       = $res['nomu'][$i] ;
		$data['pren']                       = $res['pren'][$i] ;
		$data['sexe']                       = $res['sexe'][$i] ;
		$data['dtnai']                      = $res['dtnai'][$i] ;
		$data['dateDemande']                = $res['dateDemande'][$i] ;
		$data['typeAdm']                    = $res['typeAdm'][$i] ;
		$data['lieuInterv']                 = $res['lieuInterv'][$i] ;
		$data['numUFexec']                  = $res['numUFexec'][$i] ;
		$data['Urgence']                    = $res['Urgence'][$i] ;
		$data['codeActe']                   = $res['codeActe'][$i] ;
		$data['libelleActe']                = $res['libelleActe'][$i] ;
		$data['cotationNGAP']               = $res['cotationNGAP'][$i] ;
		$data['codeActivite4']              = $res['codeActivite4'][$i] ;
		$data['modificateurs']              = $res['modificateurs'][$i] ;
		$data['type']                       = $res['type'][$i] ;
		$data['categorie']                  = $res['categorie'][$i] ;
		$data['extensionDoc']               = $res['extensionDoc'][$i] ;
		$data['matriculeIntervenant']       = $res['matriculeIntervenant'][$i] ;
		$data['nomIntervenant']             = $res['nomIntervenant'][$i] ;
		$data['numSejour']                  = $res['numSejour'][$i] ;
		$data['numUFdem']                   = $res['numUFdem'][$i] ;
		$data['validDefinitive']            = $res['validDefinitive'][$i] ;
		$data['quantite']                   = $res['quantite'][$i] ;
		$data['periodicite']                = $res['periodicite'][$i] ;
		$data['lesionMultiple']             = $res['lesionMultiple'][$i] ;
		$data['envoi_facturation']          = $res['envoi_facturation'][$i] ;
		$data['envoi_nomIntervenant']       = $res['envoi_nomIntervenant'][$i] ;
		$data['envoi_matriculeIntervenant'] = $res['envoi_matriculeIntervenant'][$i] ;

		$req = new clRequete ( $basec, "ccam_cotation_actes", $data ) ;
		$req->addRecord ( ) ;
	}
	$req = new clRequete ( CCAM_BDD, "ccam_cotation_actes" ) ;
	$req->delRecord ( "idEvent=$idpatient" ) ;
	
	
	header ( 'Location:index.php?navi=TGlzdGVfUHJlc2VudHM=' ) ;
	
	return "";
}
?>
