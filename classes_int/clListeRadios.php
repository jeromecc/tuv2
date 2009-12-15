<?php

// Titre  : Classe Liste Radios
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 29 Mai 2007

// Description : 
// 

class clListeRadios {

  // Attributs de la classe.


  	// Constructeur.
  	function __construct ( $ajax='' ) {
		global $session ;
		$this->ajax = $ajax ;
		if ( ( $_REQUEST['act_print'] OR $_REQUEST['act_print_x'] ) AND $_REQUEST['Formulaire2print'] ) {
    		// eko ( "Impression du formulaire blablabla" ) ;
    		if ( $_REQUEST['etape'] == 'a' AND $session->getDroit ( 'Liste_Radio', 'm' ) ) {
				/*
				$data['dt_pec'] = date ( 'Y-m-d H:i:s' ) ;
				$data['dt_encours'] = date ( 'Y-m-d H:i:s' ) ;
				$data['etat'] = 'b' ;
				$session->setLogSup ( "Radio : prise en charge" ) ;
				$requete = new clRequete ( BDD, "radios", $data ) ;
		    	$requete->updRecord ( "idradio='".$_REQUEST['idradio']."'" ) ;
		    	*/
    		}
    		$t = new clDemandeBons ( $session->getNavi(3), $session->getNavi(4) ) ;
    	}
    	if ( $session->getNavi(2) == 'valEnquete' ) {
    		$this->valEnquete ( $session->getNavi(3) ) ;
    	}
    	
		if ( $ajax ) {
			if ( $session->getNavi ( 1 ) == 'getRadios' )
				$this->af = $this->genListe ( ) ;
			elseif ( $session->getNavi ( 1 ) == 'reloadBandeau' )
				$this->genAffichage ( "bandeau" ) ;
			elseif ( $session->getNavi ( 1 ) == 'setRadiologue' )
				$this->setRadiologue ( ) ;
			elseif ( $session->getNavi ( 1 ) == 'setAnesthesie' )
				$this->setAnesthesie ( ) ;
			elseif ( $session->getNavi ( 1 ) == 'addActeRadio' )
				$this->listeActes ( ) ;
			elseif ( $session->getNavi ( 1 ) == 'delActeRadio' )
				$this->listeActes ( ) ;
			elseif ( $session->getNavi ( 1 ) == 'setModificateursRadio' ) 
				$this->updateModificateurs ( ) ;
			elseif ( $session->getNavi ( 1 ) == 'modRadiosEnquetes' )
				$this->modRadiosEnquetes ( ) ;
			elseif ( $session->getNavi ( 1 ) == 'setComRadio' )
				$this->setComRadio ( ) ;
			else $this->af = $this->genMod ( ) ;
			
		} else {
			//if ( $session->getNavi ( 0 ) == 'Radio' )
			$this->genAffichage ( ) ;
			global $pi ;
			//$pi->addMove ( 'mod', 'handMod' ) ;
		}
  	}

    // Enregistrement du commentaire de la radio
    function setComRadio ( ) {
        global $session ;
		if ( $session->getDroit ( "Liste_Radio", "m" ) ) {
			$data['commentaire_radio'] = $_REQUEST['note'] ;
			$requete = new clRequete ( BDD, "radios", $data ) ;
		    $requete->updRecord ( "idradio='".$_REQUEST['idradio']."'" ) ;
		}
    }

	// Validation du formulaire d'enquete.
	function valEnquete ( $idradio ) {
		global $session ;
		$data['libre1'] = $_POST['libre1'] ;
		$data['libre2'] = $_POST['libre2'] ;
		$data['libre3'] = $_POST['libre3'] ;
		$data['libre4'] = $_POST['libre4'] ;
		$data['libre5'] = $_POST['libre5'] ;
		$data['libre6'] = $_POST['libre6'] ;
		$data['libre7'] = $_POST['libre7'] ;
		$data['libre8'] = $_POST['libre8'] ;
		$data['libre9'] = $_POST['libre9'] ;
		$data['libre10'] = $_POST['libre10'] ;
		$data['radiologue'] = $_POST['radiologue'] ;
		$data['radiologueinterpretable'] = $_POST['radiologueinterpretable'] ;
		$data['radiologuediagradio'] = $_POST['radiologuediagradio'] ;
		$data['idUser'] = $session -> getUid ( ) ;
		if ( $_POST['libre1'] AND $_POST['libre2'] AND $_POST['libre3'] AND $_POST['libre4'] AND $_POST['libre5'] AND $_POST['libre6'] AND 
		$_POST['libre7'] AND $_POST['libre8'] AND $_POST['libre9'] AND $_POST['libre10'] AND $_POST['radiologue'] AND  
		$_POST['radiologueinterpretable'] AND $_POST['radiologuediagradio'] )
			$data['date'] = date ( 'Y-m-d H:i:s' ) ; else $data['date'] = '0000-00-00 00:00:00' ;
		$requete = new clRequete ( BDD, "radios_enquetes", $data ) ;
		$requete->updRecord ( "idradio='".$idradio."'" ) ;
	}
	
	// Formulaire de modification d'une enquête.
	function modRadiosEnquetes ( ) {
		global $session ;
		$rel = new clRequete ( BDD, '', '', MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
      	$ras = $rel -> exec_requete ( "select * from radios_enquetes where idradio=".$session->getNavi(2), 'resultquery' ) ;
      	$req = new clResultQuery ;
		$param['base'] = $this->getBaseFromIdRadio ( $session->getNavi(2) ) ;
		$param['cw'] = "where r.idpatient=p.idpatient and idradio=".$session->getNavi(2) ;
	   	$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
      	$mod = new ModeliXe ( "RadioEnquete.html" ) ;
	    $mod -> SetModeliXe ( ) ;
      	switch ( $ras['enquete'][0] ) {
      		case 'PoumonsFace':
      			$mod -> MxText ( 'nomEnquete', '"Poumons de face" pour le patient "'.$res['prenom'][0].' '.$res['nom'][0].'"' ) ;
      			$listeGen = new clListesGenerales ( "recup" ) ;
				$listeRadiologues = $listeGen -> getListeItemsV2 ( "Radiologues", "1", '', '1' ) ;
				$mod -> MxSelect ( "listeMedecins", "radiologue", $ras['radiologue'][0], $listeRadiologues, '', '', 'id="radiologue" style="width: 240px;" ' ) ;
				
				$mod -> MxText ( "question.question", "Les extrémités internes des clavicules sont symétriques par rapport à la ligne des apophyses épineuses vertébrales." ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "libre1", "oui", ($ras['libre1'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "libre1", "non", ($ras['libre1'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( "question.question", "Les bords internes des omoplates se projettent en dehors des côtes ou juste au niveau de leurs bords internes." ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "libre2", "oui", ($ras['libre2'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "libre2", "non", ($ras['libre2'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( "question.question", "Les champs pulmonaires sont vus en totalité." ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "libre3", "oui", ($ras['libre3'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "libre3", "non", ($ras['libre3'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( "question.question", "Le nobmre d'espaces intercostaux antérieurs est supérieur ou égal à 6." ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "libre4", "oui", ($ras['libre4'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "libre4", "non", ($ras['libre4'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( "question.question", "Le dôme diaphragmatique se projette au dessous de l'arc postérieur de la 9ième côte." ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "libre5", "oui", ($ras['libre5'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "libre5", "non", ($ras['libre5'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( "question.question", "La trame pulmonaire est visible jusqu'à 1cm de la périphérie." ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "libre6", "oui", ($ras['libre6'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "libre6", "non", ($ras['libre6'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( "question.question", "Absence de flou cinétique." ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "libre7", "oui", ($ras['libre7'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "libre7", "non", ($ras['libre7'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( "question.question", "Les rebords costaux ne sont pas dédoublés." ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "libre8", "oui", ($ras['libre8'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "libre8", "non", ($ras['libre8'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( "question.question", "Visibilité suffisante des vaisseaux en périphérie du poumon." ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "libre9", "oui", ($ras['libre9'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "libre9", "non", ($ras['libre9'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( "question.question", "Visualisation des gros vaisseaux à destinée lobaire inférieure et des vertèbres dorsales à travers le coeur." ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "libre10", "oui", ($ras['libre10'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "libre10", "non", ($ras['libre10'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( "question.question", "La radio est-elle interprétable ?" ) ;
				$mod -> MxText ( "question.width", "480" ) ;
				$mod -> MxCheckerField ( "question.oui", "radio", "radiologueinterpretable", "oui", ($ras['radiologueinterpretable'][0]=="oui"?1:0 ) ) ;
				$mod -> MxCheckerField ( "question.non", "radio", "radiologueinterpretable", "non", ($ras['radiologueinterpretable'][0]=="non"?1:0 ) ) ;
				$mod -> MxBloc ( 'question', 'loop' ) ;
				$mod -> MxText ( 'radiologuediagradio', $ras['radiologuediagradio'][0] ) ;
				$mod -> MxText ( 'indication', utf8_decode($ras['indication'][0]) ) ;
				$mod -> MxText ( 'recherche', utf8_decode($ras['recherche'][0]) ) ;
				$ns = '<span style="color:red">Pas encore saisi</span>' ;
				if ( ! $ras['qualite'][0] AND $ras['qualite'][0] != '0' ) $mod -> MxText ( 'qualite', $ns ) ;
				else $mod -> MxText ( 'qualite', $ras['qualite'][0].' / 10' ) ;
				if ( ! $ras['interpretable'][0] ) $mod -> MxText ( 'interpretable', $ns ) ;
				else $mod -> MxText ( 'interpretable', $ras['interpretable'][0] ) ;
				if ( ! $ras['impactdiag'][0] ) $mod -> MxText ( 'impactdiag', $ns ) ;
				else $mod -> MxText ( 'impactdiag', utf8_decode($ras['impactdiag'][0]) ) ;
				if ( ! $ras['impactthera'][0] ) $mod -> MxText ( 'impactthera', $ns ) ;
				else $mod -> MxText ( 'impactthera', utf8_decode($ras['impactthera'][0]) ) ;
				if ( ! $ras['diagradio'][0] ) $mod -> MxText ( 'diagradio', $ns ) ;
				else $mod -> MxText ( 'diagradio', utf8_decode($ras['diagradio'][0]) ) ;
				
      		break;
      	}
      	$mod -> MxHidden ( 'hidden', 'navi='.$session->genNavi('Radio','','valEnquete',$ras['idradio'][0]) ) ;
      	$this->af = $mod -> MxWrite ( "1" ) ;
	}
	

	// Affectation du radiologue.
	function setRadiologue ( ) {
		global $session ;
		if ( $session->getDroit ( "Liste_Radio", "m" ) ) {		
			$param['cw'] = "WHERE nomliste='Radiologues' AND libre='".$_REQUEST['adeli']."'" ;
	      	$req = new clResultQuery ;
	      	$res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ; 
			//print affTab ( $res ) ;
			if ( $res['INDIC_SVC'][2] )
				$data['radiologue'] = $res['nomitem'][0] ;
			else $data['radiologue'] = '' ;
			$data['adeli'] = $_REQUEST['adeli'] ;
			$requete = new clRequete ( BDD, "radios", $data ) ;
		    $requete->updRecord ( "idradio='".$session->getNavi(2)."'" ) ;
			if ( $_REQUEST['adeli'] AND $_REQUEST['adeli'] != '--' )
				$this->moduleCCAM ( ) ;
		}
	}

	// Affectation d'un acte d'anesthésie.
	function setAnesthesie ( ) {
		global $session ;
		global $options ;
		
		$req = new clResultQuery ;
		$param['base'] = $this->getBaseFromIdRadio ( $session->getNavi(2) ) ;
		$param['cw'] = "where r.idpatient=p.idpatient and idradio=".$session->getNavi(2) ;
	   	$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
		
		$requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes" ) ;
    	$r = $requete->delRecord ( "codeActe='".$options->getOption('RadioAnesthesie')."' AND idEvent=".$res['idpatient'][0] ) ;
    	//print affTab ( $r ) ;
		
		if ( $_REQUEST['anesthesie'] AND $_REQUEST['anesthesie'] != 'Non' ) {
		
			switch ( $_REQUEST['anesthesie'] ) {
				case '7'  : $modif = '7' ; break ;
				case '8'  : $modif = '8' ; break ;
				case '78' : $modif = '7~8' ; break ;
				default   : $modif = '' ; break;
			}
		
	  		$param['cw'] = " CODE='".$options->getOption('RadioAnesthesie')."'" ;
	  		$ris = $req -> Execute ( "Fichier", "CCAM_getActesCCAM", $param, "ResultQuery" ) ;
						
			//$data['identifiant'] = ;
			$data['idEvent'] = $res['idpatient'][0] ;
			$data['dateEvent'] = date ( 'Y-m-d H:i:s' ) ;
			$data['idDomaine'] = 1 ;
			$data['dtFinInterv'] = '' ;
			$data['idu'] = $res['idu'][0] ;
			$data['ipp'] = $res['ilp'][0] ;
			$data['nomu'] = $res['nom'][0] ;
			$data['pren'] = $res['prenom'][0] ;
			$data['sexe'] = $res['sexe'][0] ;
			$data['dtnai'] = substr($res['dt_naissance'][0],0,10) ;
			$data['dateDemande'] = $res['dt_admission'][0] ;
			$data['typeAdm'] = $res['type_destination'][0];
			$data['lieuInterv'] = $options->getOption ( 'RadioSalle' ) ;
			$data['numUFexec'] = $options -> getOption ( 'RadioUF' ) ;
			$data['Urgence'] = '' ;
			$data['codeActe'] = $options->getOption('RadioAnesthesie') ;
			$data['libelleActe'] = $ris['LIBELLE_COURT'][0];
			$data['cotationNGAP'] = '' ;
			$data['codeActivite4'] = '' ;
			$data['modificateurs'] = $modif ;
			$data['type'] = 'ACTE' ;
			$data['categorie'] = '' ;
			$data['extensionDoc'] = '' ;
			$data['matriculeIntervenant'] = $res['adeli'][0] ;
			$data['nomIntervenant'] = $res['radiologue'][0] ;
			$data['numSejour'] = $res['nsej'][0] ;
			$data['numUFdem'] = $res['uf'][0] ;
			$data['validDefinitive'] = '' ;
			$data['quantite'] = 1 ;
			$data['periodicite'] = 'aucune' ;
			$data['lesionMultiple'] = 'Non' ;
			//print affTab ( $data ) ;
			$requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes", $data ) ;
	    	$r = $requete->addRecord ( ) ;
	    	//print affTab ( $r ) ;
		}
	}

	// Affichage du module CCAM.
	function moduleCCAM ( $get='' ) {
		global $session ;
		global $options ;
		if ( $session->getDroit ( "Liste_Radio", "m" ) ) {		
			$mod = new ModeliXe ( "RadioCCAM.html" ) ;
	    	$mod -> SetModeliXe ( ) ;
	    	
	    	$req = new clResultQuery ;
	    	$param['base'] = $this->getBaseFromIdRadio ( $session->getNavi(2) ) ;
	    	$param['cw'] = 'where r.idpatient=p.idpatient and idradio='.$session->getNavi(2) ;
	      	$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
	
    		//$ran = $requete->delRecord ( "codeActe='".$options->getOption('RadioAnesthesie')."' AND idEvent=".$res['idpatient'][0]
			$param['cw'] = "codeActe='".$options->getOption('RadioAnesthesie')."' AND idEvent=".$res['idpatient'][0] ;
	      	$ran = $req -> Execute ( "Fichier", "CCAM_getActesDiagsCotation", $param, "ResultQuery" ) ;
			//print affTab ( $ran['INDIC_SVC'] ) ;
			$repAn = 'Oui' ;
			if ( ! $ran['INDIC_SVC'][2] ) $repAn = 'Non' ;
			else {
				$modi = explode ( '~', $ran['modificateurs'][0] ) ;
				//print affTab ( $mod ) ;
				while ( list ( $key, $val ) = each ( $modi ) ) {
					if ( $val == '8' ) {
						if ( $repAn == '7' OR $repAn == '78' ) $repAn = '78' ;
						else $repAn = '8' ;
					} elseif ( $val == '7' ) {
						if ( $repAn == '8' OR $repAn == '78' ) $repAn = '78' ;
						else $repAn = '7' ;
					}
				}
			}

			$aj = XhamTools::genAjax ( 'onChange', 'setAnesthesie', 'navi='.$session->genNavi ( 'Ajax', 'setAnesthesie', $session->getNavi(2) ) ) ;
			$listeAnesthesies = array ( 'Non'=>'Non', 'Oui'=>'Oui', '7'=>'Oui, avec le modificateur 7', '8'=>'Oui, avec le modificateur 8', '78'=>'Oui, avec les modificateurs 7 et 8') ;
			$mod -> MxSelect ( "listeAnesthesies", "anesthesie", $repAn, $listeAnesthesies, '', '', 'id="anesthesie" style="width: 240px;" '.$aj ) ;
	
	    	$param['cw'] = " idEvent=".$res['idpatient'][0]." and lieuInterv='".$options->getOption ( 'RadioSalle' )."'" ;
	      	$ris = $req -> Execute ( "Fichier", "CCAM_getActesDiagsCotation", $param, "ResultQuery" ) ;
	    	if ( $ris['INDIC_SVC'][2] OR $_REQUEST['addActe'] ) {
	   	   		$aj = XhamTools::genAjax ( 'onDblClick', 'addActeRadio', 'navi='.$session->genNavi ( 'Ajax', 'addActeRadio', $session->getNavi(2) ) ) ;
	    	} else $aj = XhamTools::genAjax ( 'onDblClick', 'addActeRadioBis', 'navi='.$session->genNavi ( 'Ajax', 'modRadios', $session->getNavi(2) ) ) ;
			
			/* Requête temporaire (pour démo) */
	    	$param['cw'] = " idDomaine=2" ;
	      	$res = $req -> Execute ( "Fichier", "CCAM_getActesDomaine2", $param, "ResultQuery" ) ;
	      	for ( $i = 0 ; isset ( $res['idActe'][$i] ) ; $i++ ) {
	      		global $pi ;
	      		$cle = $res['idActe'][$i].'" '.$pi->genInfoBulle($res['idActe'][$i].' : '.$res['libelleActe'][$i]).' title="'.$res['idActe'][$i].' : '.$res['libelleActe'][$i].'" style="' ;
	      		$listeCCAMRadio[$cle] = $res['idActe'][$i]." - ".strtolower($res['libelleActe'][$i]) ;
	      	}
	    	//print affTab ( $res['INDIC_SVC']) ;
	    	$mod -> MxSelect ( "listeCCAM", "acte", '', $listeCCAMRadio, '', '5', ' id="acte" '.$aj ) ;
	    	$mod -> MxText ( 'listeActes', $this->listeActes ( 'get' ) ) ;
	    	
			if ( $get ) return $mod -> MxWrite ( "1" ) ;
			else $this->af = $mod -> MxWrite ( "1" ) ;
		}
	}
	
	// Affichage de la liste des actes.
	function listeActes ( $get='' ) {
		global $options ;
		global $session ;
		if ( $session->getDroit ( "Liste_Radio", "m" ) ) {		
			$req = new clResultQuery ;
			$param['base'] = $this->getBaseFromIdRadio ( $session->getNavi(2) ) ;
			$param['cw'] = "where r.idpatient=p.idpatient and idradio=".$session->getNavi(2) ;
	      	$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
			if ( $_REQUEST['addActe'] ) {
				
				$param['cw'] = " idEvent=".$res['idpatient'][0]." and codeActe='".$_REQUEST['acte']."'" ;
	      		$rus = $req -> Execute ( "Fichier", "CCAM_getActesDiagsCotation", $param, "ResultQuery" ) ;
				if ( $rus['INDIC_SVC'][2] ) {
					$data['quantite'] = $rus['quantite'][0] + 1 ;
					$requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes", $data ) ;
		    		$requete->updRecord ( 'identifiant='.$rus['identifiant'][0] ) ;
				} else {
		      		$param['cw'] = " CODE='".$_REQUEST['acte']."'" ;
		      		$ris = $req -> Execute ( "Fichier", "CCAM_getActesCCAM", $param, "ResultQuery" ) ;
								
					//$data['identifiant'] = ;
					$data['idEvent'] = $res['idpatient'][0] ;
					$data['dateEvent'] = date ( 'Y-m-d H:i:s' ) ;
					$data['idDomaine'] = 1 ;
					$data['dtFinInterv'] = '' ;
					$data['idu'] = $res['idu'][0] ;
					$data['ipp'] = $res['ilp'][0] ;
					$data['nomu'] = $res['nom'][0] ;
					$data['pren'] = $res['prenom'][0] ;
					$data['sexe'] = $res['sexe'][0] ;
					$data['dtnai'] = substr($res['dt_naissance'][0],0,10) ;
					$data['dateDemande'] = $res['dt_admission'][0] ;
					$data['typeAdm'] = $res['type_destination'][0];
					$data['lieuInterv'] = $options->getOption ( 'RadioSalle' ) ;
					$data['numUFexec'] = $options -> getOption ( 'RadioUF' ) ;
					$data['Urgence'] = '' ;
					$data['codeActe'] = $_REQUEST['acte'] ;
					$data['libelleActe'] = $ris['LIBELLE_COURT'][0];
					$data['cotationNGAP'] = '' ;
					$data['codeActivite4'] = '' ;
					$data['modificateurs'] = '' ;
					$data['type'] = 'ACTE' ;
					$data['categorie'] = '' ;
					$data['extensionDoc'] = '' ;
					$data['matriculeIntervenant'] = $res['adeli'][0] ;
					$data['nomIntervenant'] = $res['radiologue'][0] ;
					$data['numSejour'] = $res['nsej'][0] ;
					$data['numUFdem'] = $res['uf'][0] ;
					$data['validDefinitive'] = '' ;
					$data['quantite'] = 1 ;
					$data['periodicite'] = 'aucune' ;
					$data['lesionMultiple'] = 'Non' ;
					//print affTab ( $data ) ;
					$requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes", $data ) ;
			    	$requete->addRecord ( ) ;
				}
			} elseif ( $session->getNavi ( 1 ) == "delActeRadio" ) {
				$param['cw'] = ' identifiant='.$session->getNavi(3) ;
	      		$ris = $req -> Execute ( "Fichier", "CCAM_getActesDiagsCotation", $param, "ResultQuery" ) ;
				if ( $ris['quantite'][0] > 1 ) {
					$data['quantite'] = $ris['quantite'][0] - 1 ;
					$requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes", $data ) ;
		    		$requete->updRecord ( 'identifiant='.$session->getNavi(3) ) ;
				} else {
					$requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes" ) ;
		    		$requete->delRecord ( 'identifiant='.$session->getNavi(3) ) ;
				}
			}
			$mod = new ModeliXe ( "RadioActes.html" ) ;
	    	$mod -> SetModeliXe ( ) ;
	    	
	    	$param['cw'] = " idEvent=".$res['idpatient'][0]." and lieuInterv='".$options->getOption ( 'RadioSalle' )."'" ;
	      	$ris = $req -> Execute ( "Fichier", "CCAM_getActesDiagsCotation", $param, "ResultQuery" ) ;
	      	
	      	//print affTab ( $ris['INDIC_SVC'] ) ;
	      	if ( $ris['INDIC_SVC'][2] ) {
	      		$dateE = new clDate ( ) ;
	      		global $pi ;
	      		for ( $i = 0 ; isset ( $ris['codeActe'][$i] ) ; $i++ ) {
	      			if ( $ris['codeActe'][$i] != $options->getOption('RadioAnesthesie') ) {
		      			if ( $ris['quantite'][$i] > 1 ) $q = ' ('.$ris['quantite'][$i].')' ; else $q = '' ;
		      			$mod -> MxText ( 'acte.acte', $ris['codeActe'][$i].$q ) ;
		      			$dateE -> setDate ( $ris['dateEvent'][$i] ) ;
		      			$mod -> MxText ( 'acte.date', $dateE -> getDate ( 'd/m/Y H:i:s' ) ) ;
		      			$mod -> MxText ( 'acte.med', $ris['nomIntervenant'][$i] ) ;
		      			$modif = explode ( '~', $ris['modificateurs'][$i] ) ;
		      			//$modif = explode ( '~', $tmp[0] ) ;
		      			$B = '' ; $C = '' ; $D = '' ; $E = '' ; $Y = '' ; $Z = '' ;
		      			while ( list ( $key, $val ) = each ( $modif ) ) {
		      				if ( $val )
		      					eval ( "\$$val=1;" ) ;
		      			}
		      			$ajR = XhamTools::genAjax ( 'onClick', 'setModificateursRadio', 'navi='.$session->genNavi ( 'Ajax', 'setModificateursRadio', $session->getNavi(2), $ris['identifiant'][$i] ) ) ;
		      			$ajR = 'onclick="request(\'index.php?navi='.$session->genNavi ( 'Ajax', 'setModificateursRadio', $session->getNavi(2), $ris['identifiant'][$i] ).'\','.$i.',\'setModificateursRadio\')"' ;
		      			
		      			$mod -> MxCheckerField ( "acte.B", "checkbox", "B$i", 1, (($B)?true:false) ,$pi->genInfoBulle('Radio réalisée au bloc opératoire, en unité de réanimation ou au lit du patient intransportable.')." id=\"B$i\" $ajR") ;
		      			$mod -> MxCheckerField ( "acte.C", "checkbox", "C$i", 1, (($C)?true:false) ,$pi->genInfoBulle('Réalisation d\'une radio comparative.')." id=\"C$i\" $ajR") ;
		      			$mod -> MxCheckerField ( "acte.D", "checkbox", "D$i", 1, (($D)?true:false) ,$pi->genInfoBulle('Acte de contrôle radiographique de segment de squelette immobilisé par contention rigide.')." id=\"D$i\" $ajR") ;
		      			$mod -> MxCheckerField ( "acte.E", "checkbox", "E$i", 1, (($E)?true:false) ,$pi->genInfoBulle('Acte de radiographie ou scanographie sur un patient de moins de 5 ans.')." id=\"E$i\" $ajR") ;
		      			$mod -> MxCheckerField ( "acte.Y", "checkbox", "Y$i", 1, (($Y)?true:false) ,$pi->genInfoBulle('Acte de radiographie réalisé par un pneumologue ou un rhumatologue.')." id=\"Y$i\" $ajR") ;
		      			$mod -> MxCheckerField ( "acte.Z", "checkbox", "Z$i", 1, (($Z)?true:false) ,$pi->genInfoBulle('Acte de radiographie réalisé par un radiologue.')." id=\"Z$i\" $ajR") ;
		    	      	$aj = XhamTools::genAjax ( 'onClick', 'delActeRadio', 'navi='.$session->genNavi ( 'Ajax', 'delActeRadio', $session->getNavi(2), $ris['identifiant'][$i] ) ) ;
		      			if ( $session->getDroit ( "Liste_Radio", "d" ) ) {		
		      				$mod -> MxText ( 'acte.action', "<img src=\"images/annuler.gif\" alt=\"Supprimer acte\" $aj />" ) ;
		      			}
		      			$mod -> MxBloc ( 'acte', 'loop' ) ;
	      			}
	      		}
	      	} else $mod -> MxBloc ( "acte", "replace", "<tr><td style=\"text-align: center;\" class=\"red\" colspan=9>Aucun acte saisi</td></tr>") ;
	    	
			if ( $get ) return $mod -> MxWrite ( "1" ) ;
			else $this->af = $mod -> MxWrite ( "1" ) ;
		}
	}
	
	// Mise à jour des modificateurs.
	function updateModificateurs ( ) {
		global $session ;
		if ( $session->getDroit ( "Liste_Radio", "m" ) ) {					
			
			$req = new clResultQuery ;
			$param['cw'] = " identifiant=".$session->getNavi(3) ;
	      	$rus = $req -> Execute ( "Fichier", "CCAM_getActesDiagsCotation", $param, "ResultQuery" ) ;
	      	//print affTab ( $rus['INDIC_SVC'] ) ;
	      	$modif = explode ( '~', $rus['modificateurs'][0] ) ;
	      	//$modif = explode ( '~', $tmp[0] ) ;
	      	$tam = array ( ) ;
	      	while ( list ( $key, $val ) = each ( $modif ) ) {
	      		if ( $val )
	      			//eval ( "\$$val=1;" ) ;
	      			$tam[$val] = $val ;
	      	}
			if ( $_REQUEST['B'] == 'true' ) $tam['B'] = 'B' ;
			elseif ( isset ( $tam['B'] ) ) unset ( $tam['B'] ) ;      	
			if ( $_REQUEST['C'] == 'true' ) $tam['C'] = 'C' ;
			elseif ( isset ( $tam['C'] ) ) unset ( $tam['C'] ) ;
			if ( $_REQUEST['D'] == 'true' ) $tam['D'] = 'D' ;
			elseif ( isset ( $tam['D'] ) ) unset ( $tam['D'] ) ;
			if ( $_REQUEST['E'] == 'true' ) $tam['E'] = 'E' ;
			elseif ( isset ( $tam['E'] ) ) unset ( $tam['E'] ) ;
			if ( $_REQUEST['Y'] == 'true' ) $tam['Y'] = 'Y' ;
			elseif ( isset ( $tam['Y'] ) ) unset ( $tam['Y'] ) ;
			if ( $_REQUEST['Z'] == 'true' ) $tam['Z'] = 'Z' ;
			elseif ( isset ( $tam['Z'] ) ) unset ( $tam['Z'] ) ;
	      	$data['modificateurs'] = implode ( '~', $tam ) ;
			//$data['modificateurs'] = $_REQUEST['adeli'] ;
			$requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes", $data ) ;
		    $r = $requete->updRecord ( "identifiant='".$session->getNavi(3)."'" ) ;
		    //print affTab ( $r ) ;
		}
	}

	// Cette fonction permet la saisie de retour d'informations 
	// sur les radios effectuées d'un patient qu'il ne soit
	// sorti du terminal.
	static function getRetour ( $idpatient, $idapp, $bdd='' ) {
		global $session ;
		$req = new clResultQuery ;
		$param['base'] = clListeRadios::getBaseFromIdApp ( $idapp ) ;
		$okVal = 1 ;
		if ( $bdd ) $base = $bdd ; else $base = BDD ;
		if ( $_REQUEST['validerRetourRadio'] == 'Valider' ) {
			$param['cw'] = "where r.idpatient=p.idpatient and (etat='d') and r.idpatient=$idpatient and retour='' and idapplication=$idapp" ;
      		$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
      		$rel = new clRequete ( $base, '', '', MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
      		$ras = $rel -> exec_requete ( "select * from radios_enquetes where idradio=".$res['idradio'][0], 'resultquery' ) ;
      		$erreurs = '' ;
      		if ( $ras['INDIC_SVC'][2] ) {
      			if ( $_POST['qualite'] == '#' OR ! $_POST['interpretable'] OR ! $_POST['impactdiag'] OR ! $_POST['impactthera'] OR ! $_POST['diagradio']) { 
      				$okVal = 0 ;
      				$erreurs = '<font color="red">Tous les items (sauf commentaires) sont obligatoires.</font><br/><br/>' ;
      			} else $okVal = 2 ;
      		}
      		if ( ! $_POST['anomalie'] ) { $okVal = 0 ; $erreurs = '<font color="red">Tous les items (sauf commentaires) sont obligatoires.</font><br/><br/>' ; }
      		
      		if ( $okVal ) {
				$idradio = $res['idradio'][0] ;
				$data['retourid'] = $session->getUid ( ) ;
				$data['retour'] = $_POST['anomalie'] ;
				$data['commentaire'] = $_POST['commentaire'] ;
				$data['dt_retour'] = date ( 'Y-m-d H:i:s' ) ;

				$requete = new clRequete ( $base, "radios", $data ) ;
		    	$requete->updRecord ( "idradio='".$idradio."'" ) ;
		    	
		    	if ( $okVal == 2 ) {
		    		unset ( $data ) ;
		    		$data['qualite'] = $_POST['qualite'] ;
		    		$data['interpretable'] = ($_POST['interpretable']=='oui'?true:false) ;
		    		$data['impactdiag'] = ($_POST['impactdiag']=='oui'?true:false) ;
		    		$data['impactthera'] = ($_POST['impactthera']=='oui'?true:false) ;
		    		$data['diagradio'] = $_POST['diagradio'] ; 
					$requete = new clRequete ( $base, "radios_enquetes", $data ) ;
			    	$requete->updRecord ( "idradio='".$idradio."'" ) ;
		    	}
      		}
		}
		$param['cw'] = "where r.idpatient=p.idpatient and (etat='d') and r.idpatient=$idpatient and retour='' and idapplication=$idapp" ;
      	$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
      	if ( $res['INDIC_SVC'][2] ) {
      		$mod = new ModeliXe ( "RadioRetour.html" ) ;
    		$mod -> SetModeliXe ( ) ;
    		if ( $okVal ) $mod -> MxText ( 'display', 'none' ) ;
    		$date = new clDate ( $res['dt_fin'][0] ) ;
    		$mod -> MxText ( "date", $date->getDate ( 'd/m/Y H:i' ) ) ;
    		$mod -> MxText ( "detail", clListeRadios::getDetailRadio ( $res, 0 ) ) ;
    		$rel = new clRequete ( $base, '', '', MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
      		$ras = $rel -> exec_requete ( "select * from radios_enquetes where idradio=".$res['idradio'][0], 'resultquery' ) ;
      		if ( $_POST['anomalie'] == 'Oui' ) $mod -> MxText ( 'ro', 'checked' ) ;
      		elseif ( $_POST['anomalie'] == 'Non' ) $mod -> MxText ( 'rn', 'checked' ) ;
      		elseif ( $_POST['anomalie'] == 'NR' ) $mod -> MxText ( 'rr', 'checked' ) ;
      		elseif ( $_POST['anomalie'] == 'NI' ) $mod -> MxText ( 'ri', 'checked' ) ;
      		
      		if ( $ras['INDIC_SVC'][2] ) {
    			$mod -> MxText ( 'enquete.typeEnquete', $ras['enquete'][0] ) ;
    			$mod -> MxText ( 'enquete.indication', $ras['indication'][0] ) ;
    			$mod -> MxText ( 'enquete.recherche', $ras['recherche'][0] ) ;
    			$listeQ['#'] = '--' ;
    			$listeQ[0] = '0' ;
    			$listeQ[1] = '1' ;
    			$listeQ[2] = '2' ;
    			$listeQ[3] = '3' ;
    			$listeQ[4] = '4' ;
    			$listeQ[5] = '5' ;
    			$listeQ[6] = '6' ;
    			$listeQ[7] = '7' ;
    			$listeQ[8] = '8' ;
    			$listeQ[9] = '9' ;
    			$listeQ[10] = '10' ;
    			$mod -> MxSelect ( "enquete.qualite", "qualite", $_POST['qualite'], $listeQ ) ;
    			if ( $_POST['interpretable'] == 'oui' ) $mod -> MxText ( 'enquete.ico', 'checked' ) ;
    			elseif ( $_POST['interpretable'] == 'non' ) $mod -> MxText ( 'enquete.icn', 'checked' ) ;
    			if ( $_POST['impactdiag'] == 'oui' ) $mod -> MxText ( 'enquete.idco', 'checked' ) ;
    			elseif ( $_POST['impactdiag'] == 'non' ) $mod -> MxText ( 'enquete.idcn', 'checked' ) ;
    			if ( $_POST['impactthera'] == 'oui' ) $mod -> MxText ( 'enquete.itco', 'checked' ) ;
    			elseif ( $_POST['impactthera'] == 'non' ) $mod -> MxText ( 'enquete.itcn', 'checked' ) ;
    			$mod -> MxText ( 'enquete.diagradio', $_POST['diagradio'] ) ;
    			$mod -> MxText ( "erreurs", $erreurs ) ;
      		} else $mod -> MxBloc ( 'enquete', 'delete' ) ;
    		$mod -> MxText ( 'commentaire', $_POST['commentaire'] ) ;
    		$mod -> MxText ( "erreurs", $erreurs ) ;
    		$mod -> MxHidden ( "hidden", "navi=".$session -> genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
    		return $mod -> MxWrite ( "1" ) ;
      	} else return '' ;
	} 

	static function getBaseFromIdRadio ( $idradio ) {
		$req = new clResultQuery ;
	    $param['cw'] = 'where idradio='.$idradio ;
		// Exécution de la requête.
      	$res = $req -> Execute ( "Fichier", "getRadiosGlob", $param, "ResultQuery" ) ;
      	return clListeRadios::getBaseFromIdApp ( $res['idapplication'][0] ) ;
	}

	static function getBaseFromIdApp ( $idapp ) {
		if ( IDAPPLICATION == 1 ) 
			return BDD ;
		else {
		switch ( $idapp ) {
			case '1':
			 	return "xham_tuv2" ;
			break ;
			case '24':
				return "xham_tc" ;
			break ;
			default:
				return "xham_tuv2" ;
			break ;
		}
		}
	}

	// Mod
	function genMod ( ) {
		global $session ;
		global $options ;
		if ( $session->getDroit ( "Liste_Radio", "m" ) ) {		
			if ( $session->getNavi(1) == "valDateRadios" ) {
				switch ( $_REQUEST['typeDate'] ) {
					case 'dateB':
						$data['dt_pec'] = $_REQUEST['valDate'] ;
						$data['dt_encours'] = $_REQUEST['valDate'] ;
						$data['etat'] = 'b' ;
						$session->setLogSup ( "Radio : prise en charge" ) ;
					break;
					/*
					case 'dateC':
						$data['dt_encours'] = $_REQUEST['valDate'] ;
						$session->setLogSup ( "Radio : en cours" ) ;
						$data['etat'] = 'c' ;
					break;
					*/
					case 'dateD':
						$data['dt_fin'] = $_REQUEST['valDate'] ;
						$session->setLogSup ( "Radio : terminée" ) ;
						$data['etat'] = 'd' ;
					break;
				}
				$requete = new clRequete ( BDD, "radios", $data ) ;
		    	$requete->updRecord ( "idradio='".$session->getNavi(2)."'" ) ;
			} elseif ( $session->getNavi(1) == "annDateRadios" ) {
				switch ( $_REQUEST['typeDate'] ) {
					case 'dateB':
						$data['dt_pec'] = '0000-00-00 00:00:00' ;
						$data['dt_encours'] = '0000-00-00 00:00:00' ;
						$session->setLogSup ( "Radio : annulation de la prise en charge" ) ;
						$data['etat'] = 'a' ;
					break;
					case 'dateC':
						/*
						$data['dt_encours'] = '0000-00-00 00:00:00' ;
						$session->setLogSup ( "Radio : annulation de 'en cours'" ) ;
						$data['etat'] = 'b' ;
						*/
					break;
					case 'dateD':
						$data['dt_fin'] = '0000-00-00 00:00:00' ;
						$session->setLogSup ( "Radio : annulation de 'terminée'" ) ;
						$data['etat'] = 'b' ;
					break;
				}
				$requete = new clRequete ( BDD, "radios", $data ) ;
		    	$requete->updRecord ( "idradio='".$session->getNavi(2)."'" ) ;
			}
			$_SESSION['typeModJS'] = '' ;
		
			$req = new clResultQuery ;
	    	$param['cw'] = 'where r.idpatient=p.idpatient and idradio='.$session->getNavi(2) ;
	    	$param['base'] = $this->getBaseFromIdRadio ( $session->getNavi(2) ) ;
			// Exécution de la requête.
	      	$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
	      	// print affTab ( $res['INDIC_SVC'] ) ;
			// Chargement du template de modeliXe.
	    	$mod = new ModeliXe ( "RadioMod.html" ) ;
	    	$mod -> SetModeliXe ( ) ;
	    	$mod -> MxText ( "patient", strtoupper($res['nom'][0]).' '.ucfirst(strtolower($res['prenom'][0])) ) ;
	    	
	    	$et = $res['etat'][0] ;
	    	
	    	
	    	// ************ //
	    	// * CREATION * //
	    	// ************ //
	    	$mod -> MxText ( "etape.nomEtape", "Création : " ) ;
	    	$date = new clDate ( $res['dt_creation'][0] ) ;
	    	if ( $res['dt_creation'][0] != '0000-00-00 00:00:00' )
	    		$dateA = $date -> getDate ( 'd/m/Y H:i' ) ;
	    	else $dateA = '<span class="red">Non renseignée</span>' ;
	    	$mod -> MxText ( "etape.dateEtape", $dateA ) ;
	    	$mod -> MxBloc ( "etape.formEtape", "delete" ) ;
	    	$mod -> MxBloc ( "etape.annulerEtape", "delete" ) ;
	    	$mod -> MxBloc ( "etape", "loop" ) ;
	    	
	     	// ******* //
	    	// * PEC * //
	    	// ******* //   	
	    	$mod -> MxText ( "etape.nomEtape", "Prise en charge : " ) ;
	    	$date = new clDate ( $res['dt_pec'][0] ) ;
	    	if ( $res['dt_pec'][0] != '0000-00-00 00:00:00' )
	    		$dateB = $date -> getDate ( 'd/m/Y H:i' ) ;
	    	else $dateB = '<span class="red">Non renseignée</span>' ;
	    	$mod -> MxText ( "etape.dateEtape", $dateB ) ;
	    	if ( $et == 'a' ) {
	    		$mod -> MxText ( "etape.dateEtape", '' ) ;
	    		$datePec = new clDate ( ) ;
	    		$dateMin = new clDate ( $res['dt_creation'][0] ) ;
	    		$initB = $datePec -> getDatetime ( ) ;
	    		for ( $i = 0 ; $datePec -> getTimestamp ( ) >= $dateMin -> getTimestamp ( ) AND $i < 3600 ; $datePec->addMinutes ( -1 ) ) {
	    			$listeB[$datePec->getDatetime ( )] = $datePec -> getDate ( 'd/m/Y H:i' ) ;
	    			$i++ ;
	    		}
	    		
	    		if ( $i == 0 ) {
            $datePec = new clDate ( $res['dt_creation'][0] ) ;
            $dateMin = new clDate ( ) ;
            $initB = $datePec -> getDatetime ( ) ;
            for ( $i = 0 ; $datePec -> getTimestamp ( ) >= $dateMin -> getTimestamp ( ) AND $i < 3600 ; $datePec->addMinutes ( -1 ) ) {
              $listeB[$datePec->getDatetime ( )] = $datePec -> getDate ( 'd/m/Y H:i' ) ;
              $i++ ;
            }
          }
          
          $mod -> MxSelect ( "etape.formEtape.valeurEtape", "date", $initB, $listeB ) ;
	    		$mod -> MxHidden ( "etape.formEtape.hidden", "typeDate=dateB" ) ;
	    		$_SESSION['typeModJS'] = 'dt_pec' ;
	    		$mod -> MxText ( "etape.formEtape.ajaxVal", XhamTools::genAjax ( 'onClick', 'valDateRadios', 'navi='.$session->genNavi ( 'Ajax', 'valDateRadios', $session->getNavi(2) ) ) ) ;
	    	} else $mod -> MxBloc ( "etape.formEtape", "delete" ) ;
	    	if ( $et == 'b' ) {
	    		$mod -> MxHidden ( "etape.annulerEtape.hidden", "typeDate=dateB" ) ;
	    		$mod -> MxText ( "etape.annulerEtape.ajaxAnn", XhamTools::genAjax ( 'onClick', 'annDateRadios', 'navi='.$session->genNavi ( 'Ajax', 'annDateRadios', $session->getNavi(2) ) ) ) ;
	    	} else $mod -> MxBloc ( "etape.annulerEtape", "delete" ) ;
	    	$mod -> MxBloc ( "etape", "loop" ) ;
	    	
	    	
	    	/*
	    	// ************ //
	    	// * EN COURS * //
	    	// ************ //
	    	$mod -> MxText ( "etape.nomEtape", "En cours : " ) ;
	    	$date = new clDate ( $res['dt_encours'][0] ) ;
	    	if ( $res['dt_encours'][0] != '0000-00-00 00:00:00' ) {
	    		$dateC = $date -> getDate ( 'd/m/Y H:i' ) ;
	    	} else $dateC = '<span class="red">Non renseignée</span>' ;
	    	$mod -> MxText ( "etape.dateEtape", $dateC ) ;
	    	if ( $et == 'b' ) {
	    		$mod -> MxText ( "etape.dateEtape", '' ) ;
	    		$datePec = new clDate ( ) ;
	    		$dateMin = new clDate ( $res['dt_pec'][0] ) ;
	    		$initC = $datePec -> getDatetime ( ) ;
	    		for ( $i = 0 ; $datePec -> getTimestamp ( ) >= $dateMin -> getTimestamp ( ) AND $i < 3600 ; $datePec->addMinutes ( -1 ) ) {
	    			$listeC[$datePec->getDatetime ( )] = $datePec -> getDate ( 'd/m/Y H:i' ) ;
	    			$i++ ;
	    		}
	    		$mod -> MxSelect ( "etape.formEtape.valeurEtape", "date", $initC, $listeC ) ;
	    		$mod -> MxHidden ( "etape.formEtape.hidden", "typeDate=dateC" ) ;
	    		$_SESSION['typeModJS'] = 'dt_encours' ;
	    		$mod -> MxText ( "etape.formEtape.ajaxVal", XhamTools::genAjax ( 'onClick', 'valDateRadios', 'navi='.$session->genNavi ( 'Ajax', 'valDateRadios', $session->getNavi(2) ) ) ) ;
	    	} else $mod -> MxBloc ( "etape.formEtape", "delete" ) ;
	    	if ( $et == 'c' ) {
	    		$mod -> MxHidden ( "etape.annulerEtape.hidden", "typeDate=dateC" ) ;
	    		$mod -> MxText ( "etape.annulerEtape.ajaxAnn", XhamTools::genAjax ( 'onClick', 'annDateRadios', 'navi='.$session->genNavi ( 'Ajax', 'annDateRadios', $session->getNavi(2) ) ) ) ;
	     	} else $mod -> MxBloc ( "etape.annulerEtape", "delete" ) ;
	    	$mod -> MxBloc ( "etape", "loop" ) ;
	    	*/
	    	
	    	// ******* //
	    	// * FIN * //
	    	// ******* //    	
	    	$mod -> MxText ( "etape.nomEtape", "Terminé : " ) ;
	    	$date = new clDate ( $res['dt_fin'][0] ) ;
	    	if ( $res['dt_fin'][0] != '0000-00-00 00:00:00' ) {
	    		$dateD = $date -> getDate ( 'd/m/Y H:i' ) ;
	    		$mod -> MxHidden ( "etape.annulerEtape.hidden", "typeDate=dateD" ) ;
	    		$mod -> MxText ( "etape.annulerEtape.ajaxAnn", XhamTools::genAjax ( 'onClick', 'annDateRadios', 'navi='.$session->genNavi ( 'Ajax', 'annDateRadios', $session->getNavi(2) ) ) ) ;
	     	} else { 
	    		$dateD = '<span class="red">Non renseignée</span>' ;
	    		$mod -> MxBloc ( "etape.annulerEtape", "delete" ) ;
	    	}
	    	if ( $et != 'b' )
	    		$mod -> MxBloc ( "etape.formEtape", "delete" ) ;
	    	else {
	    		$param['cw'] = " idEvent=".$res['idpatient'][0]." and lieuInterv='".$options->getOption ( 'RadioSalle' )."'" ;
	      		$ris = $req -> Execute ( "Fichier", "CCAM_getActesDiagsCotation", $param, "ResultQuery" ) ;
	    		//print affTab ( $ris['INDIC_SVC'] ) ;
	    		if ( $options -> getOption ( "RadioCCAM" ) AND ! $ris['INDIC_SVC'][2] AND ! $_REQUEST['addActe'] ) {
	    			$dateD = '<div id="CotationCCAMRadio" class="red">Cotation CCAM à saisir</span>' ;
	    			$mod -> MxBloc ( "etape.formEtape", "delete" ) ;
	    		} else {
	    			$dateD = '' ;
	    			$mod -> MxText ( "etape.dateEtape", '' ) ;
	    			$datePec = new clDate ( ) ;
	    			$dateMin = new clDate ( $res['dt_encours'][0] ) ;
	    			$initD = $datePec -> getDatetime ( ) ;
	    			for ( $i = 0 ; $datePec -> getTimestamp ( ) >= $dateMin -> getTimestamp ( ) AND $i < 3600 ; $datePec->addMinutes ( -1 ) ) {
	    				$listeD[$datePec->getDatetime ( )] = $datePec -> getDate ( 'd/m/Y H:i' ) ;
	    				$i++ ;
	    			}
	    			
	    			if ( $i == 0 ) {
            $datePec = new clDate ( $res['dt_creation'][0] ) ;
            $dateMin = new clDate ( ) ;
            $initB = $datePec -> getDatetime ( ) ;
            for ( $i = 0 ; $datePec -> getTimestamp ( ) >= $dateMin -> getTimestamp ( ) AND $i < 3600 ; $datePec->addMinutes ( -1 ) ) {
              $listeD[$datePec->getDatetime ( )] = $datePec -> getDate ( 'd/m/Y H:i' ) ;
              $i++ ;
              }
            }
	    			
	    			//1
	    			$mod -> MxSelect ( "etape.formEtape.valeurEtape", "date", $initD, $listeD ) ;
	    			$mod -> MxHidden ( "etape.formEtape.hidden", "typeDate=dateD" ) ;
	    			$_SESSION['typeModJS'] = 'dt_fin' ;
	    			$mod -> MxText ( "etape.formEtape.ajaxVal", 'onclick="'.XhamTools::genAjax ( '', 'valDateRadios', 'navi='.$session->genNavi ( 'Ajax', 'valDateRadios', $session->getNavi(2) ) ).';cache(\'mod\');cache(\'arbo\');"' ) ;
	    		}
	    	}
	    	$mod -> MxText ( "etape.dateEtape", $dateD ) ;
	   		$mod -> MxBloc ( "etape", "loop" ) ;

            $mod -> MxText ( 'idradio', $res['idradio'][0] ) ;
            $mod -> MxText ( 'commentaire_radio', $res['commentaire_radio'][0] ) ;

			if ( $options -> getOption ( "RadioCCAM" ) ) {
				$listeGen = new clListesGenerales ( "recup" ) ;
				$listeRadiologues = $listeGen -> getListeItemsV2 ( "Radiologues", "1", '', '1' ) ;
				$aj = XhamTools::genAjax ( 'onChange', 'setRadiologue', 'navi='.$session->genNavi ( 'Ajax', 'setRadiologue', $session->getNavi(2) ) ) ;
				$mod -> MxSelect ( "cotation.listeRadiologues", "radiologue", $res['adeli'][0], $listeRadiologues, '', '', 'id="radiologue" style="width: 240px;" '.$aj ) ;
				if ( $res['adeli'][0] ) {
					$mod -> MxText ( "cotation.moduleCCAM", $this->moduleCCAM ( 'get' ) ) ;
				}
			} else $mod -> MxBloc ( "cotation", "delete" ) ;
	
			return $mod -> MxWrite ( "1" ) ;
		}
	}

	// Retourne l'état actuel du patient.
	static function getEtatSalle ( $idpatient, $idapp='1' ) {
		$req = new clResultQuery ;
		$param['cw'] = "where r.idpatient=p.idpatient and (etat='a' or etat='b' or etat='c' or etat='d') and r.idpatient=$idpatient and idapplication=$idapp" ;
      	$param['base'] = clListeRadios::getBaseFromIdApp ( $idapp ) ;
      	$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
      	$l = '' ;
      	if ( $res['INDIC_SVC'][2] ) {
      		switch ( $res['etat'][0] ) {
      			case 'a': $text = '' ; break ;
      			case 'b': $text = ' (Radio)' ; break ;
      			case 'c': $text = ' (Radio)' ; break ;
      			case 'd': $text = '' ; break ;
      		}
      		return $text ;
      	} else return '' ;
	}

	// Retourne l'état actuel du patient.
	static function getEtat ( $idpatient, $idapp='1' ) {
		$req = new clResultQuery ;
		$param['cw'] = "where r.idpatient=p.idpatient and (etat='a' or etat='b' or etat='c' or etat='d') and r.idpatient=$idpatient and idapplication=$idapp" ;
      	$param['base'] = clListeRadios::getBaseFromIdApp ( $idapp ) ;
      	$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
      	$l = '' ;
      	if ( $res['INDIC_SVC'][2] ) {
      		switch ( $res['etat'][0] ) {
      			case 'a': $text = '<b>Statut : </b> Demande de radio effectuée.' ; $l = 'A' ; break ;
      			case 'b': $text = '<b>Statut : </b> Patient pris en charge à la radio.' ; $l = 'B' ; break ;
      			case 'c': $text = '<b>Statut : </b> Radio en cours...' ; $l = 'C' ; break ;
      			case 'd': $text = '<b>Statut : </b> Radio terminée...' ; $l = 'D' ; break ;
      		}
            if ( $res['commentaire_radio'][0] ) $comm = "<br/><b>Commentaire : </b>".addslashes(htmlentities(nl2br($res['commentaire_radio'][0]))) ;
            else $comm = '' ;
            $text = $text.$comm ;
      		return '<img src="images/radio'.$l.($res['commentaire_radio'][0]?'C':'').'.png" alt="radio" onmouseover="return overlib(\''.$text.'\');" onmouseout="return nd();" />' ;
      	} else return '' ;
	}

	// Génération de l'affichage.
	function genAffichage ( $bandeau='' ) {
		global $session ;
		// Chargement du template de modeliXe.
    	if ( $bandeau) $mod = new ModeliXe ( "RadioBandeau.html" ) ;
    	else $mod = new ModeliXe ( "Radio.html" ) ;
    	$mod -> SetModeliXe ( ) ;
    	$req = new clResultQuery ;
    	// NON VUS
    	$param['cw'] = "where etat='a'" ;
      	$res = $req -> Execute ( "Fichier", "getRadiosGlob", $param, "ResultQuery" ) ;
      	$nbNonVus = $res['INDIC_SVC'][2] ;
      	if ( $nbNonVus > 1 ) $nonVus = 's' ; else $nonVus = '' ;
      	$mod -> MxText ( 'nbNonVus', $nbNonVus ) ;
      	$mod -> MxText ( 'nonVus', $nonVus ) ;
      	
      	// PEC
      	$param['cw'] = "where etat='b'" ;
      	$res = $req -> Execute ( "Fichier", "getRadiosGlob", $param, "ResultQuery" ) ;
      	$nbPec = $res['INDIC_SVC'][2] ;
      	$mod -> MxText ( 'nbPec', $nbPec ) ;
      	
      	// En cours
      	$param['cw'] = "where etat='c'" ;
      	$res = $req -> Execute ( "Fichier", "getRadiosGlob", $param, "ResultQuery" ) ;
      	$nbEnCours = $res['INDIC_SVC'][2] ;
      	$mod -> MxText ( 'nbEnCours', $nbEnCours ) ;
      	
      	// Présents
      	$param['cw'] = "where (etat='a' or etat='b' or etat='c')" ;
      	$res = $req -> Execute ( "Fichier", "getRadiosGlob", $param, "ResultQuery" ) ;
      	$nbPresents = $res['INDIC_SVC'][2] ;
      	if ( $nbPresents > 1 ) $presents = 's' ; else $presents = '' ;
      	$mod -> MxText ( 'nbPresents', $nbPresents ) ;
      	$mod -> MxText ( 'presents', $presents ) ;
      	
      	// TODAY
      	$date = new clDate ( ) ;
      	$param['cw'] = "where dt_creation LIKE '".$date->getDate('Y-m-d')."%'" ;
      	$res = $req -> Execute ( "Fichier", "getRadiosGlob", $param, "ResultQuery" ) ;
      	$nbToday = $res['INDIC_SVC'][2] ;
      	if ( $nbToday > 1 ) $today = 's' ; else $today = '' ;
      	$mod -> MxText ( 'nbToday', $nbToday ) ;
      	$mod -> MxText ( 'today', $today ) ;
      	
      	$t['alm']  = 'Global - Toutes sauf terminées' ;
      	$t['npec']  = 'Global - Non prises en charge' ;
		$t['all']  = 'Global - Toutes' ;
      	$t['d']    = 'Global - Terminés (12 dernières heures)' ;
        $t['d024']    = 'Global - Terminés (J-0)' ;
        $t['d2448']    = 'Global - Terminés (J-1)' ;
        $t['d4872']    = 'Global - Terminés (J-2)' ;
        $t['d7296']    = 'Global - Terminés (J-3)' ;
        $t['d96120']    = 'Global - Terminés (J-4)' ;
        $t['d120144']    = 'Global - Terminés (J-5)' ;
        $t['d144168']    = 'Global - Terminés (J-6)' ;
        $t['d168192']    = 'Global - Terminés (J-7)' ;
      	$t['rall'] = 'Radio - Toutes' ;
      	$t['ralm'] = 'Radio - Toutes sauf terminées' ;
      	$t['rd']   = 'Radio - Terminées' ;
      	$t['eall'] = 'Echo - Toutes' ;
      	$t['ealm'] = 'Echo - Toutes sauf terminées' ;
      	$t['ed']   = 'Echo - Terminées' ;
      	$t['sall'] = 'Scanner - Toutes' ;
      	$t['salm'] = 'Scanner - Toutes sauf terminées' ;
      	$t['sd']   = 'Scanner - Terminées' ;
      	$t['ec']  = 'Enquêtes - En cours' ;
      	$t['et']  = 'Enquêtes - Terminées' ;
      	    	
      	
      	
      	$j  = XhamTools::genAjax ( 'onChange', 'getRadios', 'navi='.$session->genNavi ( 'Ajax', 'getRadios'), URL ) ;
      	$mod -> MxSelect ( 'typeListe', 'type', $_SESSION['typeListe'], $t, '', '', 'id="typeListe" '.$j ) ;
      	//eko ( $_SESSION ) ;
      	if ( ! $bandeau )
			$mod -> MxText ( 'listePatients', $this->genListe ( ) ) ;
		//$this->af .= $this->genListe ( ) ;
		$mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session -> getNavi ( 0 ) ) ) ;
    	$this->af .= $mod -> MxWrite ( "1" ) ;
	}

	// Fabrication de la requête.
	function genRequete ( ) {
		$date = new clDate ( ) ;
		//$date -> addDays ( -1 ) ;
		$date -> addHours ( -12 ) ;
		$reqDate = " AND (dt_fin>'".$date->getDatetime()."' OR dt_fin='0000-00-00 00:00:00')" ;
		$date -> addHours ( -12 ) ;
		$reqDate2 = " AND (dt_fin>'".$date->getDatetime()."' OR dt_fin='0000-00-00 00:00:00')" ;
		// Préparation de la requête.
    	$req = new clResultQuery ;
    	//$param['tp'] = PPRESENTS ;
    	if ( $_POST['typeListe'] ) $_SESSION['typeListe'] = $_POST['typeListe'] ;
		elseif ( ! isset ( $_SESSION['typeListe'] ) ) $_SESSION['typeListe'] = 'all' ;
		//print "Type Liste : ".$_SESSION['typeListe'].'<br/>' ;
		//print affTab ( $_SESSION ) ;
        $date = new clDate ( ) ;
    	switch ( $_SESSION['typeListe'] ) {
    		case 'all':
    			$and = $reqDate ;
    		break ;
    		case 'alm':
    			$and = "AND (etat='a' OR etat='b' OR etat='c')" ;
    		break ;
    		case 'd':
    			$and = "AND etat='d'".$reqDate2 ;
    		break ;
            case 'd024':
    			$and = "AND etat='d' AND dt_fin LIKE '".$date->getDate('Y-m-d')."%'" ;
    		break ;
    		case 'd2448':
                $date->addDays(-1);
    			$and = "AND etat='d' AND dt_fin LIKE '".$date->getDate('Y-m-d')."%'" ;
    		break ;
    		case 'd4872':
                $date->addDays(-2);
    			$and = "AND etat='d' AND dt_fin LIKE '".$date->getDate('Y-m-d')."%'" ;
    		break ;
    		case 'd7296':
                $date->addDays(-3);
    			$and = "AND etat='d' AND dt_fin LIKE '".$date->getDate('Y-m-d')."%'" ;
    		break ;
    		case 'd96120':
                $date->addDays(-4);
    			$and = "AND etat='d' AND dt_fin LIKE '".$date->getDate('Y-m-d')."%'" ;
    		break ;
    		case 'd120144':
                $date->addDays(-5);
    			$and = "AND etat='d' AND dt_fin LIKE '".$date->getDate('Y-m-d')."%'" ;
    		break ;
    		case 'd144168':
                $date->addDays(-6);
    			$and = "AND etat='d' AND dt_fin LIKE '".$date->getDate('Y-m-d')."%'" ;
    		break ;
    		case 'd168192':
                $date->addDays(-7);
    			$and = "AND etat='d' AND dt_fin LIKE '".$date->getDate('Y-m-d')."%'" ;
    		break ;
    		case 'rall':
    			$and = $reqDate ;
    		break ;
    		case 'npec':
    			$and = "AND etat='a'  " ;
    		break ;
    		case 'ralm':
    			$and = "AND (etat='a' OR etat='b' OR etat='c')" ;
    		break ;
    		case 'rd':
    			$and = "AND etat='d'".$reqDate2 ;
    		break ;
    		case 'eall':
    			$and = $reqDate ;
    		break ;
    		case 'ealm':
    			$and = "AND (etat='a' OR etat='b' OR etat='c')" ;
    		break ;
    		case 'ed':
    			$and = "AND etat='d'".$reqDate2 ;
    		break ;
    		case 'sall':
    			$and = $reqDate ;
    		break ;
    		case 'salm':
    			$and = "AND (etat='a' OR etat='b' OR etat='c')" ;
    		break ;
    		case 'sd':
    			$and = "AND etat='d'".$reqDate2 ;
    		break ;
    		case 'ec':
    			$param['cw'] = ', radios_enquetes e where 1=1 AND r.idradio=e.idradio AND e.date=\'0000-00-00 00:00:00\''.$and ;
    			//$param['cw2'] = 'where r.idpatient=p.idpatient' ;
				// Exécution de la requête.
      			$res = $req -> Execute ( "Fichier", "getRadiosGlob", $param, "ResultQuery" ) ;
      			eko( $res['INDIC_SVC'] ) ;
      			return $res ;
    		break;
    		case 'et':
    			$param['cw'] = ', radios_enquetes e where 1=1 AND r.idradio=e.idradio AND e.date!=\'0000-00-00 00:00:00\''.$and ;
    			//$param['cw2'] = 'where r.idpatient=p.idpatient' ;
				// Exécution de la requête.
      			$res = $req -> Execute ( "Fichier", "getRadiosGlob", $param, "ResultQuery" ) ;
      			return $res ;
    		break;
    		default:
    			$and = $and = "AND (etat='a' OR etat='b' OR etat='c')" ;
    		break;
    	}
    	$param['cw'] = 'where 1=1 '.$and.' ORDER BY dt_fin' ;
    	//$param['cw2'] = 'where r.idpatient=p.idpatient' ;
		// Exécution de la requête.
      	$res = $req -> Execute ( "Fichier", "getRadiosGlob", $param, "ResultQuery" ) ;
		eko ( $res['INDIC_SVC'] ) ;
		
		return $res ;
	}
	
	// Fabrication de la requête.
	function getInfosPatient ( $idpatient, $idapplication ) {
		// Préparation de la requête.
    	$req = new clResultQuery ;
    	$param['base'] = $this->getBaseFromIdApp ( $idapplication ) ;
    	$param['cw'] = 'where r.idpatient=p.idpatient AND p.idpatient='.$idpatient.' and idapplication='.$idapplication ;
		// Exécution de la requête.
      	$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
      	// eko ( $res['INDIC_SVC'] ) ;
		return $res ;
	}

  	// Fabrication de la liste des radios.
  	function genListe ( ) {
  		global $session ;
  		$res = $this->genRequete ( ) ;
  		//eko ( $res ) ;
  		//print affTab ( $res ) ;
  		$list = new ListMaker ( "template/RadioListe.html" ) ;
  		$navi = $session -> genNavi ( "Radio" ) ;
		$list -> addUserVar ( 'navi', $navi ) ;
		$list -> addUrlVar  ( 'navi', $navi ) ;
  		$list -> addUserVar ( 'typeListe', $_SESSION['typeListe'] ) ;
		$list -> addUrlVar  ( 'typeListe', $_SESSION['typeListe'] ) ;
		$list -> setSortColumn ( 'col0', 'UF', 		    'uf' ) ;
  		$list -> setSortColumn ( 'col1', 'Patient',     'nomD' ) ;
    	$list -> setSortColumn ( 'col2', 'Age',         'ageD' ) ;
    	$list -> setSortColumn ( 'col3', 'Arrivée',     'arriveeD' ) ;
    	$list -> setSortColumn ( 'col4', 'Soignants',   'soignants' ) ;
    	$list -> setSortColumn ( 'col5', 'Motif',       'motif' ) ;
    	$list -> setSortColumn ( 'col6', 'Date demande','demande' ) ;
    	$list -> setSortColumn ( 'col7', 'Description', 'description' ) ;
    	$list -> setSortColumn ( 'col8', 'Etat',        'etatD' ) ;
    	$list -> setSortColumn ( 'col9', 'Détail', 		'detail' ) ;
		$list -> setdefaultSort ( 'col8' ) ;
  		$item = array ( ) ;
	  	$dureead = new clDuree ( ) ;
	  	$date = new clDate ( ) ;
  		// Parcours de la liste des patients récupérés par la requête.
    	for ( $i = 0 ; isset ( $res['idradio'][$i] ) ; $i++ ) {
  			
  			$ras = $this->getInfosPatient ( $res['idpatient'][$i], $res['idapplication'][$i] ) ;
  			
  			if ( $session->getDroit ( "Liste_Radio", "m" ) ) {		
  				$j  = XhamTools::genAjax ( 'onClick', 'mod', 'navi='.$session->genNavi ( 'Ajax', 'modRadios', $res['idradio'][$i]) ) ;
  			} else $j = '' ;
  			$item['js'] = $j ;
  			$item['ItemColor'] = 'rad'.$res['etat'][$i] ;
			$item['etatD'] = $res['etat'][$i] ;
			switch ( $res['etat'][$i] ) {
				case 'a': 
					$date -> setDate ( $res['dt_creation'][$i] ) ;
	  				$duree = $dureead -> getAge ( $date -> getDatetime ( ) ) ;
					
					if ( ! $dureead -> invertNegatif ( ) )
            			$item['etat'] = 'Non pris en charge<br/>Depuis '.$duree ; 
          			else {
            			
            			$temp = explode (" ",$res['dt_creation'][$i]);
            			list($annee,$mois,$jour) = explode ("-",$temp[0]);
                  $item['etat'] = 'A prendre en charge<br/>Dans '.$dureead -> getAge ( )."<br>le ".$jour."/".$mois."/".$annee." à ".$temp[1] ;
            			$item['ItemColor'] = 'rad'.'elephant' ; 
            		}
				break ;
				case 'b': 
					$date -> setDate ( $res['dt_pec'][$i] ) ;
					$duree = $dureead -> getAge ( $date -> getDatetime ( ) ) ;
					$item['etat'] = 'Pris en charge<br/>Depuis '.$duree ; 
				break ;
				case 'c': 
					$date -> setDate ( $res['dt_encours'][$i] ) ;
					$duree = $dureead -> getAge ( $date -> getDatetime ( ) ) ;
					$item['etat'] = 'En cours<br/>Depuis '.$duree ; 
				break ;
				case 'd': 
					$date -> setDate ( $res['dt_fin'][$i] ) ;
					$duree = $dureead -> getAge ( $date -> getDatetime ( ) ) ;
					$item['etat'] = 'Terminé<br/>Depuis '.$duree ; 
				break ;
			}
  			
  			// Calcul du sexe de la personne... (?!).
		    switch ( $ras['sexe'][0] ) {
		    	case 'M': $img = URLIMG."homme.png" ; break ;
		    	case 'F': $img = URLIMG."femme.png" ; break ;
		    	default: $img = URLIMG."Indefini.png" ; break ;
		    }
		    $item['sexe'] = "<img src=\"$img\" alt=\"".$ras['sexe'][0]."\" />" ;
		    // Calcul de l'âge.
		    $date = new clDate ( $ras['dt_naissance'][0] ) ;
		    $age = new clDuree ( $date->getTimestamp ( ) ) ;
		    $str = $age -> getAgePrecis ( $date->getTimestamp ( ) ) ;
		    if ( $ras['dt_naissance'][0] != "0000-00-00 00:00:00" ) {
				$item['age'] = $str ;
				$item['ageD'] = $date->getTimestamp ( ) ;
		    } else {
				$item['age'] = VIDEDEFAUT ;
				$item['ageD'] = VIDEDEFAUT ;
		    }
  			
  			global $ufs ;
  			$item['uf'] = (isset($ufs[$ras['uf'][0]])?$ufs[$ras['uf'][0]]:$ras['uf'][0]) ;
  			
  			if ( $ras['dt_admission'][0] != "0000-00-00 00:00:00" ) {
	  			$datead = new clDate ( $ras['dt_admission'][0] ) ;
	  			$dateSimple = $datead -> getDate ( "d-m-Y" ) ;
	  			$dateHeure = $datead -> getDate ( "H\hi" ) ;
	  			$item['arrivee'] = $dateSimple."<br />".$dateHeure ;
	  			$item['arriveeD'] = $datead -> getTimestamp ( ) ;
			} else { $item['arrivee'] = VIDEDEFAUT ; $item['arriveeD'] = VIDEDEFAUT ; }
			
			// Médecin et IDE.
			switch ( $res['idapplication'][$i] ) {
				case '1':
					if ( $ras['medecin_urgences'][0] ) $med = "Dr ".$ras['medecin_urgences'][0] ; else $med = VIDEDEFAUT ;
					if ( $ras['ide'][0] ) $ide = "Ide ".$ras['ide'][0] ; else $ide = VIDEDEFAUT ;
					$item['soignants'] = $med."<br />".$ide ;
					if ( $ras['salle_examen'][0] ) $sal = $ras['salle_examen'][0] ; else $sal = VIDEDEFAUT ;
					$item['salle'] = $sal ;
				break ;
				case '24':
					if ( $ras['medecin'][0] ) $med = "Dr ".$ras['medecin'][0] ; else $med = VIDEDEFAUT ;
					if ( $ras['chirurgien'][0] ) $ide = "Chir ".$ras['chirurgie'][0] ; else $ide = VIDEDEFAUT ;
					$item['soignants'] = $med."<br />".$ide ;
					if ( $ras['chambre'][0] ) $sal = $ras['chambre'][0] ; else $sal = VIDEDEFAUT ;
					$item['salle'] = $sal ;
				break ;
				default:
					if ( $ras['medecin_urgences'][0] ) $med = "Dr ".$ras['medecin_urgences'][0] ; else $med = VIDEDEFAUT ;
					if ( $ras['ide'][0] ) $ide = "Ide ".$ras['ide'][0] ; else $ide = VIDEDEFAUT ;
					$item['soignants'] = $med."<br />".$ide ;
					if ( $ras['salle_examen'][0] ) $sal = $ras['salle_examen'][0] ; else $sal = VIDEDEFAUT ;
					$item['salle'] = $sal ;
				break ;
			}
			
			// Motif de recours.
			if ( $ras['motif_recours'][0] ) $item['motif'] = $ras['motif_recours'][0] ;
			else $item['motif'] = VIDEDEFAUT ;
			
			// Patient
			$item['urlpatient'] = URLNAVI.$session->genNavi ( $session->getNavi(0), "FichePatient", $res['idpatient'][$i] ) ;
			// 	Concaténation du nom et du prénom.
	  		$item['patient'] = "<span ".clPatient::genInfoBulle ($ras,0,$res['idapplication'][$i]).">".strtoupper ( $ras['nom'][0] )."</span><br />".ucfirst(strtolower($ras['prenom'][0])."<br/>".$ras['nsej'][0]) ;
			
			if ( $res['dt_creation'][$i] != "0000-00-00 00:00:00" ) {
	  			$datead = new clDate ( $res['dt_creation'][$i] ) ;
	  			$dateSimple = $datead -> getDate ( "d-m-Y" ) ;
	  			$dateHeure = $datead -> getDate ( "H\hi" ) ;
	  			$item['demande'] = $dateSimple."<br />".$dateHeure ;
	  			$item['demandeD'] = $datead -> getTimestamp ( ) ;
			} else { $item['arrivee'] = VIDEDEFAUT ; $item['arriveeD'] = VIDEDEFAUT ; }
			
			$parp = '&etape='.$res['etat'][$i].'&idradio='.$res['idradio'][$i] ;
			//$item['detail'] = "<a onmouseout=\"request('".URLNAVI."QWpheHxnZXRSYWRpb3M=',null,'getRadios') ;\" href=\"".URLNAVI.$session->genNavi("Radio","",$res['idpatient'][$i],$ras['idu'][0],$ras['nsej'][0])."$parp&Formulaire2print=radio&FormX_ext_goto_=".$res['id_instance'][$i]."&act_print=1\" target=\"_blank\">" ;
			$jo = 'onClick="setWait(\'navigation\');location.reload();"' ;
			//$jo = 'onClick=""' ;			
			$item['detail'] = "<a $jo href=\"".URLNAVI.$session->genNavi("Radio","",$res['idpatient'][$i],$ras['idu'][0],$ras['nsej'][0])."$parp&Formulaire2print=radio&FormX_ext_goto_=".$res['id_instance'][$i]."&act_print=1\" target=\"_blank\">" ;
			$item['detail'] .= "<img  src=\"images/pdf.png\" alt=\"Afficher le PDF\"/></a>" ;
			
			$rel = new clRequete ( BDD, '', '', MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
      		$rese = $rel -> exec_requete ( "select * from radios_enquetes where idradio=".$res['idradio'][$i], 'resultquery' ) ;
			
			global $pi ;
			if ( $rese['INDIC_SVC'][2] ) {
				if ( $rese['date'][0] == '0000-00-00 00:00:00' ) $img = 'formko.gif' ;
				else $img = 'formok.gif' ;
				if ( $session->getDroit ( "Liste_Radio", "m" ) ) {		
  					$j  = XhamTools::genAjax ( 'onClick', 'mod', 'navi='.$session->genNavi ( 'Ajax', 'modRadiosEnquetes', $res['idradio'][$i]) ) ;
  				} else $j = '' ;
				$item['detail'] .= ' <img '.$j.' src="images/'.$img.'" '.$pi->genInfoBulle ( "Ouvrir le formulaire d'enquête." ).' style="cursor: pointer; pointer: hand;" />' ;
			}
			
			if ( $res['retour'][$i] ) {
				$dateR = new clDate ( $res['dt_retour'][$i] ) ;
				$retour = '<b>Formulaire de retour saisi par les urgences :</b>' ;
				$retour .= '<br/><u>Auteur :</u> '.$res['retourid'][$i] ;
				$retour .= '<br/><u>Date :</u> '.$dateR->getDate ( 'd/m/Y H:i:s') ;
				switch ( $res['retour'][$i] ) {
					case 'Oui':
						$resultat = 'Anomalie détectée avec cette radio.' ;
					break;
					case 'Non':
						$resultat = 'Aucune anomalie détectée avec cette radio.' ;
					break;
					case 'NR':
						$resultat = "L'utilisateur signale que cette radio n'a pas été réalisée." ;
					break;
					case 'NI':
						$resultat = "L'utilisateur n'a pas interprété cette radio." ;
					break;
					default:	
						$resultat = "L'utilisateur n'a pas rempli le formulaire." ;
					break;
				}
				$retour .= '<br/><u>Résultat :</u> '.$resultat ;
				$retour .= '<br/><u>Commentaire :</u>'.($res['commentaire'][$i]?$res['commentaire'][$i]:'Aucun commentaire.') ;
				$item['detail'] .= " <img src=\"".URLIMGOBS."\" ".$pi->genInfoBulle ( $retour )." alt=\"Comm.\" />" ;
			}
			
			$html = $this->getDetailRadio ( $res, $i ) ;
			
			$item['description'] = $html ;
			
			$ok = 0 ;
			
			switch ( $_SESSION['typeListe'] ) {
				case 'all':
	    			$ok = 1 ;
	    		break ;
	    		case 'alm':
	    			$ok = 1 ;
	    		break ;
	    		case 'd':
	    			$ok = 1 ;
	    		break ;
	    		case 'rall':
	    			if ( eregi ( 'RADIO', $html ) ) $ok = 1 ;
	    		break ;
	    		case 'ralm':
	    			if ( eregi ( 'RADIO', $html ) ) $ok = 1 ;
	    		break ;
	    		case 'rd':
	    			if ( eregi ( 'RADIO', $html ) ) $ok = 1 ;
	    		break ;
	    		case 'eall':
	    			if ( eregi ( 'ECHO', $html ) ) $ok = 1 ;
	    		break ;
	    		case 'ealm':
	    			if ( eregi ( 'ECHO', $html ) ) $ok = 1 ;
	    		break ;
	    		case 'ed':
	    			if ( eregi ( 'ECHO', $html ) ) $ok = 1 ;
	    		break ;
	    		case 'sall':
	    			if ( eregi ( 'SCAN', $html ) ) $ok = 1 ;
	    		break ;
	    		case 'salm':
	    			if ( eregi ( 'SCAN', $html ) ) $ok = 1 ;
	    		break ;
	    		case 'sd':
	    			if ( eregi ( 'SCAN', $html ) ) $ok = 1 ;
    			break ;			
    			case 'ec':
    				$nb = 200 ;
    				$ok = 1 ;
    			break ;
    			case 'et':
    				$nb = 200 ;
    				$ok = 1 ;
    			break;
    			default:
    				$nb = 50 ;
    				$ok = 1 ;
    			break ;	
			}
			if ( $ok ) $list->addItem ( $item ) ;
    	}
    	// Récupération du code HTML généré.
    	return $list->getList ( $nb ) ;
  	}
  	
	static function getDetailRadio ( $res, $i ) {
		//eko ( $res['id_instance'][$i] ) ;
		$formx = new clFoRmX ( $res['idu'][$i], 'NO_POST_THREAT' ) ;
		$r = $formx -> getAllValuesFromFormx ( $res['id_instance'][$i], '', '', 'idinstance' ) ;
		//eko ( $r ) ;
		$html = '' ;
		if ( ( $r['Val_F_RADIO_CoteDroit'][0] == 'Aucune Radio à effectuer' AND $r['Val_F_RADIO_Centre'][0] == 'Aucune Radio à effectuer' AND $r['Val_F_RADIO_CoteGauche'][0] == 'Aucune Radio à effectuer' ) OR ( ! $r['Val_F_RADIO_CoteDroit'][0] AND ! $r['Val_F_RADIO_Centre'][0] AND ! $r['Val_F_RADIO_CoteGauche'][0] ) ) {
      	} else { 
      		$html = "<u>RADIOGRAPHIES</u><br/>" ;
      		if ( $r['Val_F_RADIO_CoteDroit'][0] != 'Aucune Radio à effectuer' ) {
      			$exp = explode ( '|', $r['Val_F_RADIO_CoteDroit'][0] ) ;
      			while ( list ( $key, $val ) = each ( $exp ) ) {
      				if ( $val )
      					$html .= $val." côté droit<br>" ;
      			}
      			$exp = explode ( '|', $r['Val_F_RADIO_Centre'][0] ) ;
      			while ( list ( $key, $val ) = each ( $exp ) ) {
      				if ( $val )
      					$html .= $val." centre<br>" ;
      			}
      			$exp = explode ( '|', $r['Val_F_RADIO_CoteGauche'][0] ) ;
      			while ( list ( $key, $val ) = each ( $exp ) ) {
      				if ( $val )
      					$html .= $val." côté gauche<br>" ;
      			}
      		}		
		}
		if ( $r['Val_F_RADIO_TDM'][0] == 'Aucun Scanner à effectuer' OR ! $r['Val_F_RADIO_TDM'][0] ) {
      	} else { 
      		$html .= "<u>SCANNER</u><br/>" ;
      		$exp = explode ( '|', $r['Val_F_RADIO_TDM'][0] ) ;
   			while ( list ( $key, $val ) = each ( $exp ) ) {
   				$html .= "$val<br/>" ;
  			}
		}
		if ( $r['Val_F_RADIO_Echo'][0] == 'Aucune Echographie à effectuer' OR ! $r['Val_F_RADIO_Echo'][0] ) {
      	} else { 
			$html .= "<u>ECHOGRAPHIE</u><br/>" ;
   			$exp = explode ( '|', $r['Val_F_RADIO_Echo'][0] ) ;
   			while ( list ( $key, $val ) = each ( $exp ) ) {
   				$html .= "$val<br/>" ;
  			}
		}
		
		if ( $r['Val_F_RADIO_Autres_E'][0] AND $r['Val_F_RADIO_Autres_E'][0] != 'Aucun.' ) {
			$html .= "<u>AUTRES</u><br/>" ;
   			$exp = explode ( '|', $r['Val_F_RADIO_Autres_E'][0] ) ;
   			while ( list ( $key, $val ) = each ( $exp ) ) {
   				$html .= "$val<br/>" ;
  			}
		}
			
		if ( $r['Val_F_RADIO_Comm'][0] AND $r['Val_F_RADIO_Comm'][0] != 'Aucun.' ) {
			$html .= "<u>COMMENTAIRES</u><br/>" ;
   			$exp = explode ( '|', $r['Val_F_RADIO_Comm'][0] ) ;
   			while ( list ( $key, $val ) = each ( $exp ) ) {
   				$html .= "$val<br/>" ;
  			}
		}
		return $html ;
	}
	
  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	if ( $this->ajax ) { 
    		global $stopAffichage ;
    		$stopAffichage = 1 ; 
    		print $this->af ;
    	} else return $this->af ;
  	}
}

?>
