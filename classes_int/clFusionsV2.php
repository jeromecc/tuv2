<?php

// Titre  : Classe Fusions
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 13 mars 2008

// Description : 
// Gestion de la fusion des patients entrés manuellement et les patients
// importés automatiquement (en cas de panne généralement).

class clFusionsV2 {

	  // Attributs de la classe.
	  // Contient l'affichage généré par la classe.
	  private $af ;
	  // Contient les messages d'informations
	  private $infos ;
	  // Contient les messages d'erreurs.
	  private $erreurs ;
	  // Sommes-nous en mode AJAX ou non ?

	// Constructeur.
	function __construct ( ) {
		global $session ;
		
		$this->ajax = $_GET['ajax'] ;
		switch ( $session->getNavi ( 2 ) ) {
			case 'Liste':
				$this->af = $this->genListe ( ) ;
			break ;
			case 'Fusions' :
				$this->genListeFusions ( ) ;
			break ;
			case 'setFusions':
				$this->af = $this->genFusion ( ) ;
				$this->af = $this->genListe ( ) ;
			break ;
			case 'supManuel':
				$this->af = $this->setAuto ( $session->getNavi(3), $session->getNavi(4) ) ;
				$this->af = $this->genListe ( ) ;
			break;
			case 'showFusions':
				$this->showFusions ( ) ;
			break;
			default :
				$this->genFusions ( ) ;
			break ;
		}
	}


	// Affichage des fusions proposées pour ce patient.
	function showFusions ( ) {
		global $session ;
		$res = $this->getFusionsProposees ( $session->getNavi(3) ) ;
		//print affTab ( $res ) ;
		$mod = new ModeliXe ( "FusionShowPatients.html" ) ;
	    $mod -> SetModeliXe ( ) ;
	    $i = 0 ;
	    $inf = clPatient::genInfoBulle ($this->p,0) ;
	    
	    $mod -> MxText ( 'survol', $inf ) ;
		$mod -> MxText ( 'sexe', $this->p['sexe'][0] ) ;
		$mod -> MxText ( 'nom', $this->p['nom'][0] ) ;
		$mod -> MxText ( 'prenom', $this->p['prenom'][0] ) ;
		$date = new clDate ( $this->p['dt_admission'][0] ) ;
		$mod -> MxText ( 'dt_admission', $date->getDate ( 'd-m-Y H:i' ) ) ;
		$date -> setDate ( $this->p['dt_naissance'][0] ) ;
		$mod -> MxText ( 'dt_naissance', $date->getDate ( 'd-m-Y' ) ) ;
		$mod -> MxText ( 'nsej', $this->p['nsej'][0] ) ;
	    if ( $this->p['dt_sortie'][0] == '0000-00-00 00:00:00' ) $mantable = PPRESENTS ;
		else $mantable = PSORTIS ;
		$nomMan = $this->p['nom'][0]." ".$this->p['prenom'][0] ;
	    $sup = "onclick=\"if (window.confirm('ATTENTION : Etes-vous certain de vouloir supprimer de cette liste le patient manuel $nomMan ?')){request('index.php?ajax=1&navi=".$session->genNavi ( 'Administration', 'FusionsV2', 'supManuel', $this->p['idpatient'][0], $mantable)."',null,'fusion');return true;} else {return false;}\"" ;
	    $mod -> MxText ( 'supprimer', $sup ) ;
	    
	    
	    
	    while ( list ( $key, $val ) = each ( $res ) ) {
			$mod -> MxText ( 'patient.survol', $val['survol'] ) ;
			$mod -> MxText ( 'patient.js', $val['js'] ) ;
			$mod -> MxText ( 'patient.sexe', $val['sexe'] ) ;
			$mod -> MxText ( 'patient.nom', $val['nom'] ) ;
			$mod -> MxText ( 'patient.prenom', $val['prenom'] ) ;
			$mod -> MxText ( 'patient.dt_admission', $val['dt_admission'] ) ;
			$mod -> MxText ( 'patient.dt_naissance', $val['dt_naissance'] ) ;
			$mod -> MxText ( 'patient.nsej', $val['nsej'] ) ;
			$mod -> MxText ( 'patient.prc', $val['prc'] ) ;
			$mod -> MxBloc ( "patient", "loop" ) ;
			$i++ ;
	    }
	    if ( ! $i ) $mod -> MxBloc ( "patient", "replace", "<td colspan=\"7\">Aucune correspondance trouvée dans la base...</td>" ) ;
		$this -> af .= $mod -> MxWrite ( "1" ) ;
	}

	function genFusions ( ) {
		$mod = new ModeliXe ( "FusionPatients.html" ) ;
    	$mod -> SetModeliXe ( ) ;
		$mod -> MxText ( 'liste', $this->genListe ( ) ) ;
		$this -> af .= $mod -> MxWrite ( "1" ) ;
	}

	// Génère l'affichage de la liste des patients à fusionner
	function genListe ( ) {
		global $session ;
		global $options ;
		$res = $this->getPatientsManuels ( ) ;
		$list = new ListMaker ( "template/ListePatientsManuels.html" ) ;
		$navi = $session -> genNavi ( $session -> getNavi ( 0 ), $session -> getNavi ( 1 ), $session -> getNavi ( 2 ) ) ;
		$list -> addUserVar ( 'navi', $navi ) ;
		$list -> addUrlVar  ( 'navi', $navi ) ;
		$list -> setAlternateColor ( "manuelspaire", "manuelsimpaire" ) ;
		// Parcours de la liste des patients récupérés par la requête.
    	for ( $i = 0 ; isset ( $res['idpatient'][$i] ) ; $i++ ) {
      		$item['fusion']  = XhamTools::genAjax ( 'onClick', 'mod', 'ajax=1&amp;navi='.$session->genNavi ( 'Administration', 'FusionsV2', 'showFusions', $res['idpatient'][$i])."" ) ;
      		// Calcul du sexe de la personne... (?!).
		    switch ( $res['sexe'][$i] ) {
		    case 'M': $img = URLIMG."homme.png" ; break ;
		    case 'F': $img = URLIMG."femme.png" ; break ;
		    default: $img = URLIMG."Indefini.png" ; break ;
		    }
			  $item['nsej'] = $res['nsej'][$i] ;
		      $item['sexe'] = "<img src=\"$img\" alt=\"".$res['sexe'][$i]."\" />" ;
		      if ( $res['dt_naissance'][$i] != "0000-00-00 00:00:00" ) $item['naissance'] = substr ( $res['dt_naissance'][$i], 0, 10 ) ;
		      else $item['naissance'] = VIDEDEFAUT ;
			// Concaténation du nom et du prénom.
			$item['details'] = clPatient::genInfoBulle ($res,$i) ;
			$item['patient'] = strtoupper ( $res['nom'][$i] )." ".ucfirst(strtolower($res['prenom'][$i])) ;
			// Heure d'arrivée.
			if ( $res['dt_admission'][$i] != "0000-00-00 00:00:00" ) {
			  $dateTemp = new clDate ( $res['dt_admission'][$i] ) ;
			  $item['admission'] = $dateTemp -> getDate ( "d-m-Y H:i" ) ;
			} else { $item['arrivee'] = VIDEDEFAUT ; $item['arriveeD'] = VIDEDEFAUT ; }
			// Heure de sortie.
			if ( $res['dt_sortie'][$i] != "0000-00-00 00:00:00" ) {
			  $dateTemp = new clDate ( $res['dt_sortie'][$i] ) ;
			  $item['sortie'] = $dateTemp -> getDate ( "d-m-Y H:i" ) ;
			} else { $item['sortie'] = VIDEDEFAUT ; $item['sortieD'] = VIDEDEFAUT ; }
			$list->addItem ( $item ) ;
		    }
    	// Récupération du code HTML généré.
    	return $list->getList ( ) ;
	}

	// Cette fonction lance le processus de fusion des patients.
	function genFusion ( ) {
		global $errs ;
	   	global $options ;
		global $session ;    
		    
		global $fusion;
	    global $table_patient_manuel;
	    global $table_patient_automatique;
		    
		$auto = $session->getNavi(5) ;
		$tabauto = $session->getNavi(6) ;
		$manu = $session->getNavi(3) ;
		$tabmanu = $session->getNavi(4) ;
		
	    if ( $auto AND $tabauto AND $manu AND $tabmanu ) {
	        // En fonction de son état (Presents ou Sortis), on en déduit sa table.
	        //$param['table'] = $tabauto ;
	        $param['table'] = PSORTIS ;
	        $param['cw'] = "WHERE idpatient='".$auto."'" ;
	        // Lancement de la requête pour récupérer toutes ses informations.
	        $req = new clResultQuery ;
	        $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
	        //print affTab ( $res['INDIC_SVC'] ) ;
	        $table_patient_automatique = $param['table']; 
	        // On vérifie que le patient automatique existe.
		    if ( $res['INDIC_SVC'][2] < 1 ) {
		    	$param['table'] = PPRESENTS ;
	        	$param['cw'] = "WHERE idpatient='".$auto."'" ;
	        	// Lancement de la requête pour récupérer toutes ses informations.
	        	$req = new clResultQuery ;
	        	$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
	        	//print affTab ( $res['INDIC_SVC'] ) ;
	        	$table_patient_automatique = $param['table']; 
		    }
	      
	        // On récupère l'idpatient et la table actuelle du patient manuel sélectionné.
	        $param2['table'] = PSORTIS ;
	        $param2['cw'] = "WHERE idpatient='".$manu."'" ;
	        // Lancement de la requête pour récupérer toutes ses informations.
	        $req2 = new clResultQuery ;
	        $ras = $req2 -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
	        //print affTab ( $ras['INDIC_SVC'] ) ;
	        $table_patient_manuel = $param2['table'];
			if ( $ras['INDIC_SVC'][2] < 1 ) {
				// On récupère l'idpatient et la table actuelle du patient manuel sélectionné.
	        	$param2['table'] = PPRESENTS ;
	        	$param2['cw'] = "WHERE idpatient='".$manu."'" ;
	        	// Lancement de la requête pour récupérer toutes ses informations.
	        	$req2 = new clResultQuery ;
	        	$ras = $req2 -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
	        	//print affTab ( $ras['INDIC_SVC'] ) ;
	        	$table_patient_manuel = $param2['table'];
			}      
			      
		    // On vérifie que le patient automatique existe.
		    if ( $res['INDIC_SVC'][2] < 1 ) {
				$this->erreurs .= "Le patient automatique (idpatient=\"$auto\") est introuvable dans la table des patients $tabauto. Problème signalé." ;
				$errs -> addErreur ( "clFusion : Le patient automatique (idpatient=\"$auto\") est introuvable dans la table des patients $tabauto." ) ;
		    // On vérifie que le patient manuel existe.
		    } elseif ( $ras['INDIC_SVC'][2] < 1 ) {
				$this->erreurs .= "Le patient manuel (idpatient=\"$manu\") est introuvable dans la table des patients $tabmanu. Problème signalé." ;
				$errs -> addErreur ( "clFusion : Le patient manuel (idpatient=\$manu\") est introuvable dans la table des patients $tabmanu." ) ;
			} else {
				$data['idu']                  = $res['idu'][0] ;	
				$data['ilp']                  = $res['ilp'][0] ;	
				$data['nsej']                 = $res['nsej'][0] ;	
				$data['uf']                   = $res['uf'][0] ;	
				$data['nom']                  = $res['nom'][0] ;	
				$data['prenom']               = $res['prenom'][0] ;	
				$data['sexe']                 = $res['sexe'][0] ;	
				$data['dt_naissance']         = $res['dt_naissance'][0] ;	
				$data['adresse_libre']        = $res['adresse_libre'][0] ;	
				$data['adresse_cp']           = $res['adresse_cp'][0] ;	
				$data['adresse_ville']        = $res['adresse_ville'][0] ;	
				$data['telephone']            = $res['telephone'][0] ;	
				$data['prevenir']             = $res['prevenir'][0] ;	
				$data['medecin_traitant']     = $res['medecin_nom'][0] ;	
				$data['dt_admission']         = $res['dt_admission'][0] ;
				if ( $res['mode_admission'][0] ) $data['mode_admission'] = $res['mode_admission'][0] ;	
				$data['iduser']               = "FUSION" ;
				$data['manuel']               = 0 ;
				// Appel de la classe Requete.
				$requete = new clRequete ( BDD, $param2['table'], $data ) ;
				// Exécution de la requete.
				$requete->updRecord ( "idpatient='".$manu."'" ) ;
				
				// Appel de la classe Requete.
				$requete = new clRequete ( BDD, $param['table'] ) ;
				// Exécution de la requete.
				$requete->delRecord ( "idpatient='".$auto."'" ) ;
				$this->infos .= "Fusion du patient (".$res['sexe'][0].") ".ucfirst(strtolower($res['prenom'][0]))." ".strtoupper($res['nom'][0])." effectuée.<br />" ;

                // Mise à jour de la table formx : FX_BDD
                $dataf['ids'] = $res['idu'][0] ;
                $requete = new clRequete ( FX_BDD, 'formx', $dataf ) ;
				// Exécution de la requete.
				$requete->updRecord ( "ids='".$ras['idu'][0]."'" ) ;
                $requete = new clRequete ( FX_BDD, 'formx_globvars', $dataf ) ;
				// Exécution de la requete.
				$requete->updRecord ( "ids='".$ras['idu'][0]."'" ) ;

				if ( $options -> getOption ( "Module_CCAM" ) ) {
			    	$fusion = 1;
			    	$ccam = new clCCAMCotationActesDiags ( Array ( ) ) ;
					$ccam -> writeBALall ( Array ( $auto , $manu ) ) ;
				}
			}
		}
	}

	// Retourne la liste des fusions proposées.
	function getFusionsProposees ( $idpatient ) {
		
		$tab = array ( ) ;
		$req = new clResultQuery ;
		$param['table'] = PPRESENTS ;
		// Récupération des informations sur le patient à fusionner.
		$param['cw'] = "WHERE idpatient=$idpatient UNION SELECT * FROM patients_sortis WHERE idpatient=$idpatient ORDER BY dt_sortie, nom ASC" ;
		$p = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
		$this->p = $p ;
		$pre = addslashes($p['prenom'][0]) ;
		$nom = addslashes($p['nom'][0]) ;
		$sex = $p['sexe'][0] ;
		$date = new clDate ( $p['dt_naissance'][0] ) ;
		$dtn = $date -> getDate ( 'Y-m-d' ) ;
		$ann = $date -> getDate ( 'Y' ) ;
		$date -> setDate ( $p['dt_admission'][0] ) ;
		$dta = $date -> getDate ( 'Y-m-d' ) ;
		$date -> addDays ( 1 ) ;
		$dtap = $date -> getDate ( 'Y-m-d' ) ;
		$date -> addDays ( -2 ) ;
		$dtam = $date -> getDate ( 'Y-m-d' ) ;
		
		$this->nbRes = 0 ;
		
		set_time_limit(0);
		
		// Saisie parfaite
		$tab = $this->addToTab ( $tab, 100, "prenom='$pre' AND nom='$nom' AND sexe='$sex' AND dt_naissance LIKE '$dtn%' AND dt_admission LIKE '$dta%'" ) ;

		// Variation de la date d'admission à + ou - 1 jour et erreur dans la date de naissance. 
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  96, "prenom LIKE '%$pre%' AND nom LIKE '%$nom%' AND sexe='$sex' AND dt_naissance LIKE '$dtn%' AND dt_admission LIKE '$dta%'" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  84, "prenom LIKE '%$pre%' AND nom LIKE '%$nom%' AND sexe='$sex' AND dt_naissance LIKE '$ann%' AND dt_admission LIKE '$dta%'" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  76, "prenom LIKE '%$pre%' AND nom LIKE '%$nom%' AND sexe='$sex' AND dt_admission LIKE '$dta%'" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  72, "prenom LIKE '%$pre%' AND nom LIKE '%$nom%' AND sexe='$sex' AND dt_naissance LIKE '$dtn%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%')" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  68, "prenom LIKE '%$pre%' AND nom LIKE '%$nom%' AND sexe='$sex' AND dt_naissance LIKE '$ann%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%')" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  63, "prenom LIKE '%$pre%' AND nom LIKE '%$nom%' AND sexe='$sex' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%')" ) ;
		
		// Erreur de sexe
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  56, "prenom LIKE '%$pre%' AND nom LIKE '%$nom%' AND dt_naissance LIKE '$ann%' AND dt_admission LIKE '$dta%'" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  51, "prenom LIKE '%$pre%' AND nom LIKE '%$nom%' AND dt_admission LIKE '$dta%'" ) ;

		// Cas de l'inversion nom / prenom.		
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  49, "prenom LIKE '%$nom%' AND nom LIKE '%$pre%' AND sexe='$sex' AND dt_naissance LIKE '$dtn%' AND dt_admission LIKE '$dta%'" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  46, "prenom LIKE '%$nom%' AND nom LIKE '%$pre%' AND sexe='$sex' AND dt_naissance LIKE '$ann%' AND dt_admission LIKE '$dta%'" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  43, "prenom LIKE '%$nom%' AND nom LIKE '%$pre%' AND sexe='$sex' AND dt_admission LIKE '$dta%'" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  44, "prenom LIKE '%$nom%' AND nom LIKE '%$pre%' AND sexe='$sex' AND dt_naissance LIKE '$dtn%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%')" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  41, "prenom LIKE '%$nom%' AND nom LIKE '%$pre%' AND sexe='$sex' AND dt_naissance LIKE '$ann%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%')" ) ;
		if ( ! $this->nbRes ) $tab = $this->addToTab ( $tab,  35, "prenom LIKE '%$nom%' AND nom LIKE '%$pre%' AND sexe='$sex' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%')" ) ;

		
		if ( ! $this->nbRes ) {				
			// On cherche seulement sur les 4 premiers caractères du nom et du prénom		
			$tab = $this->addToTab ( $tab,  40, "nom LIKE '%".substr($nom,0,4)."%' AND prenom LIKE '%".substr($pre,0,4)."%' AND sexe='$sex' AND dt_naissance LIKE '$dtn%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			$tab = $this->addToTab ( $tab,  35, "nom LIKE '%".substr($nom,0,4)."%' AND prenom LIKE '%".substr($pre,0,4)."%' AND sexe='$sex' AND dt_naissance LIKE '$ann%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			$tab = $this->addToTab ( $tab,  30, "nom LIKE '%".substr($nom,0,4)."%' AND prenom LIKE '%".substr($pre,0,4)."%' AND sexe='$sex' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			// On cherche seulement sur le nom ou le prénom
			$tab = $this->addToTab ( $tab,  25, "nom LIKE '%$nom%' AND sexe='$sex' AND dt_naissance LIKE '$dtn%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			$tab = $this->addToTab ( $tab,  20, "nom LIKE '%$nom%' AND sexe='$sex' AND dt_naissance LIKE '$ann%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			$tab = $this->addToTab ( $tab,  15, "nom LIKE '%$nom%' AND sexe='$sex' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			$tab = $this->addToTab ( $tab,  20, "prenom LIKE '%$pre%' AND sexe='$sex' AND dt_naissance LIKE '$dtn%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			$tab = $this->addToTab ( $tab,  15, "prenom LIKE '%$pre%' AND sexe='$sex' AND dt_naissance LIKE '$ann%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			$tab = $this->addToTab ( $tab,  10, "prenom LIKE '%$pre%' AND sexe='$sex' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			// On cherche seulement sur les 2 premiers caractères
			$tab = $this->addToTab ( $tab,  15, "nom LIKE '%".substr($nom,0,2)."%' AND prenom LIKE '%".substr($pre,0,2)."%' AND sexe='$sex' AND dt_naissance LIKE '$dtn%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			$tab = $this->addToTab ( $tab,  10, "nom LIKE '%".substr($nom,0,2)."%' AND prenom LIKE '%".substr($pre,0,2)."%' AND sexe='$sex' AND dt_naissance LIKE '$ann%' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
			$tab = $this->addToTab ( $tab,   5, "nom LIKE '%".substr($nom,0,2)."%' AND prenom LIKE '%".substr($pre,0,2)."%' AND sexe='$sex' AND (dt_admission LIKE '$dtap%' OR dt_admission LIKE '$dtam%' OR dt_admission LIKE '$dta%')" ) ;
		}
		
		if ( ! $this->nbRes ) {
			$date -> addHours ( 16 ) ;
			$datmin = $date->getDate ( 'Y-m-d H:i:s' ) ;
			$date -> addHours ( 16 ) ;
			$datmax = $date->getDate ( 'Y-m-d H:i:s' ) ;
			$tab = $this->addToTab ( $tab,   0, "(dt_admission BETWEEN '$datmin' AND '$datmax')" ) ;
		}
		
		return $tab ;
	}

	// On enlève le status manuel d'un patient.
	function setAuto ( $idpatient, $table ) {
		global $session ;
		$data['manuel'] = 0 ;
		$data['valide'] = 0 ;
		$data['iduser'] = 'SUPPRIME' ;
		// Appel de la classe Requete.
		$requete = new clRequete ( BDD, $table, $data ) ;
		// Exécution de la requete.
		$requete->updRecord ( "idpatient=$idpatient" ) ;
		$session->setLogSup ( "Suppression du patient manuel (idpatient=$idpatient)" ) ;
	}


	// Retourne la liste des patients manuels.
	function getPatientsManuels ( ) {
		$param['table'] = PPRESENTS ;
		$param['cw'] = "WHERE manuel=1 UNION SELECT * FROM patients_sortis WHERE manuel=1 ORDER BY dt_sortie, nom ASC" ;
		$req = new clResultQuery ;
		$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
		return $res ;
 	}
	
	function addToTab ( $tab, $prc, $filtre ) {
		global $session ;
		$req = new clResultQuery ;
		$param['table'] = PPRESENTS ;
		//$param['cw'] = "WHERE $filtre UNION SELECT * FROM patients_sortis WHERE $filtre" ;
		$param['cw'] = "WHERE $filtre AND manuel=0 AND iduser!='SUPPRIME' UNION SELECT * FROM patients_sortis WHERE $filtre AND manuel=0 AND iduser!='SUPPRIME' ORDER BY dt_admission, nom" ;
		$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
		$date = new clDate ( ) ;
		for ( $i = 0 ; isset ( $res['idpatient'][$i] ) ; $i++ ) {
			if ( ! isset ( $tab[$res['idpatient'][$i]] ) ) {
				$tab[$res['idpatient'][$i]]['survol'] = clPatient::genInfoBulle ( $res, $i ) ;
				if ( $res['dt_sortie'][$i] == '0000-00-00 00:00:00' ) $auttable = PPRESENTS ;
				else $auttable = PSORTIS ;
				if ( $this->p['dt_sortie'][0] == '0000-00-00 00:00:00' ) $mantable = PPRESENTS ;
				else $mantable = PSORTIS ;
				//$js = XhamTools::genAjax ( 'onclick', 'fusion', 'ajax=1&amp;navi='.$session->genNavi ( 'Administration', 'FusionsV2', 'setFusions', $this->p['idpatient'][0], $mantable, $res['idpatient'][$i], $auttable) ) ;
				$nomAut = $res['nom'][$i]." ".$res['prenom'][$i] ;
				$nomMan = $this->p['nom'][0]." ".$this->p['prenom'][0] ;
				$js = "onclick=\"if (window.confirm('Etes-vous certain de vouloir fusionner le patient manuel ".addslashes($nomMan)." et le patient automatique ".addslashes($nomAut)." ?')){request('index.php?ajax=1&navi=".$session->genNavi ( 'Administration', 'FusionsV2', 'setFusions', $this->p['idpatient'][0], $mantable, $res['idpatient'][$i], $auttable)."',null,'fusion');return true;} else {return false;}\"" ;
				
				$tab[$res['idpatient'][$i]]['js'] = $js ;
				$tab[$res['idpatient'][$i]]['suppr'] = '' ;
				$tab[$res['idpatient'][$i]]['nom'] = $res['nom'][$i] ;
				$tab[$res['idpatient'][$i]]['nsej'] = $res['nsej'][$i] ;
				$tab[$res['idpatient'][$i]]['nom'] = $res['nom'][$i] ;
				$tab[$res['idpatient'][$i]]['prenom'] = $res['prenom'][$i] ;
				$tab[$res['idpatient'][$i]]['sexe'] = $res['sexe'][$i] ;
				$tab[$res['idpatient'][$i]]['sexe'] = $res['sexe'][$i] ;
				$tab[$res['idpatient'][$i]]['sexe'] = $res['sexe'][$i] ;
				$date -> setDate ( $res['dt_naissance'][$i] ) ;
				$tab[$res['idpatient'][$i]]['dt_naissance'] = $date -> getDate ( 'd-m-Y' ) ;
				$date -> setDate ( $res['dt_admission'][$i] ) ;
				$tab[$res['idpatient'][$i]]['dt_admission'] = $date -> getDate ( 'd-m-Y H:i' ) ;
				$tab[$res['idpatient'][$i]]['prc'] = $prc ;
				$this->nbRes++ ;
			} 
			//elseif ( $tab[$res['idpatient'][$i]]['prc'] < 99 ) $tab[$res['idpatient'][$i]]['prc']++ ;
		}
		return $tab ;
		//print affTab ( $tab ) ;
	}
	
	// Retourne la liste des patients automatiques.
	function getPatientsAutomatiques ( ) {
		// On cherche dans la table des présents tous les patients entrés automatiquement.
		$param['table'] = PPRESENTS ;
		$param['cw'] = "WHERE manuel='0' ".$this->filtre." ORDER BY nom" ;
		$req = new clResultQuery ;
		$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
		return $res ;
	}
	
	
	// Renvoie l'affichage généré par la classe.
	function getAffichage ( ) {
	  	if ( $this->ajax ) {
	  		print $this->af ;
	  		global $stopAffichage ;
	  		$stopAffichage = 1 ;
	  	} else return $this->af ;
	}
}

?>
