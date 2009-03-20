<?php

// Titre  : Classe ExportV2
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 07 Juin 2007

// Description : 
// Permet d'exporter les données du terminal des urgences 
// sous une n-ième forme... nice !


class clExportV2 {

  	// Attributs de la classe.
  	// Contient l'affichage généré par la classe.
  	private $af ;

  	// Constructeur.
  	function __construct ( $ajax='' ) {
    	global $session ;
		$this->ajax = $ajax ;
		if ( $session->getNavi ( 1 ) == 'getExportV2' ) {
			$this->getResultats ( ) ;
		} else {
			if ( isset ( $_REQUEST['setTraitement'] ) ) $this->setTraitement ( ) ;
			else $this->genAffichage ( ) ;
		}
  	}

	function setTraitement ( ) {
		global $session ;
		global $stopAffichage ;
		//$stopAffichage = 1 ;
		$n = $_REQUEST['type'] ;
		$req = new clRequete ( BDD, '', '' ) ;
		$sql = "select * from export where idpatient='".$_REQUEST['idpatient']."'" ;
  		$exp = $req -> exec_requete ( $sql, 'resultquery' ) ;
  		
  		if ( isset ( $exp['etat'.$n][0] ) and $exp['etat'.$n][0] )
			$data['etat'.$n] = 0 ;
		else $data['etat'.$n] = 1 ;
		$data['iduser'.$n] = $session->getUid();
		$data['date'.$n] = date('Y-m-d H:i:s') ;
		$data['idpatient'] = $_REQUEST['idpatient'] ;
		$requete = new clRequete ( BDD, "export", $data ) ;
		$res = $requete->uoiRecord ( 'idpatient='.$_REQUEST['idpatient'] ) ;
		print affTab ( $res ) ;
	}

  	// Génération de l'affichage de cette classe.
  	function genAffichage ( ) {
    	global $session ;
    	global $options ;
    	// Chargement du template ModeliXe.
		$mod = new ModeliXe ( "ExportV2.html" ) ;
		$mod -> SetModeliXe ( ) ;
		
		$j = XhamTools::genAjax ( 'onChange', 'getExportV2', 'navi='.$session->genNavi ( 'Ajax', 'getExportV2') ) ; ;
		$mod -> MxSelect ( 'listeDates', 'dt_sortie', $_REQUEST['dt_sortie'], $this->getListeDates(), '', '', 'id="listeDates" onChange="reload(this.form)"' ) ;

		$mod -> MxText ( 'listeExportV2', $this->getResultats ( "get" ) ) ;

		// Variable de navigation.
		$mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
		// On retourne le code HTML généré par le template.
		$this->af .= $mod -> MxWrite ( "1" ) ;
  	}

	// Affichage des résultats de l'export.
	function getResultats ( $get='' ) {
		if ( ! $_REQUEST['dt_sortie'] ) $_REQUEST['dt_sortie'] = date ( 'Y-m-d' ) ;
		$_SESSION['dt_sortie'] = $_REQUEST['dt_sortie'] ;
		$req = new clResultQuery ;
      	$param['table'] = PSORTIS ;
      	$param['cw'] = "WHERE dt_sortie LIKE '".$_REQUEST['dt_sortie']."%' ORDER BY nom" ;
      	$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      	if ( $res['INDIC_SVC'][2] > 1 ) $s = 's' ; else $s = '' ;
      	global $debTemps ;
      	$finTemps = temps ( ) ;
      	$temps = $finTemps - $debTemps ;
      	$af = "<br/><i>".$res['INDIC_SVC'][2]." résultat$s en ".sprintf('%.2f',$temps)."s</i>" ;
      	//eko ( $res ) ;
      	for ( $i = 0 ; isset ( $res['idpatient'][$i] ) ; $i++ ) {
      		$af .= $this->genFichePassage ( $res, $i ) ;
      	}
      	if ( $get ) return $af ;
      	else $this->af .= $af ;
	}

	// Affichage d'une fiche passage.
	function genFichePassage ( $res, $i ) {
		global $session ;
    	global $options ;
    	// Chargement du template ModeliXe.
		if ( $options -> getOption ( 'ModuleExport' ) == 'V2Allegee' ) $mod = new ModeliXe ( "ExportV2FicheAllegee.html" ) ;
		else $mod = new ModeliXe ( "ExportV2Fiche.html" ) ;
		$mod -> SetModeliXe ( ) ;
		
		$req = new clRequete ( BDD, '', '' ) ;
		$sql = "select * from export where idpatient='".$res['idpatient'][$i]."'" ;
  		$exp = $req -> exec_requete ( $sql, 'resultquery' ) ;
		
		if ( isset ( $exp['etat1'][0] ) and $exp['etat1'][0] ) {
			$mod -> MxText ( 'color1', 'green' ) ;
			$date = new clDate ( $exp['date1'][0] ) ;
			$aja = XhamTools::genAjax ( '', 'setTraitement', 'navi='.$session->genNaviFull ( ).'&idpatient='.$res['idpatient'][$i].'&setTraitement=invalide&type=1' ) ;
			$ajax = 'onclick="'.$aja.'inverserTraitement('.$res['idpatient'][$i].',1,\'CCAM\');"' ;
			$img = '<img src="images/gg.gif" id="img1'.$res['idpatient'][$i].'" alt="annuler" '.$ajax.' style="cursor: pointer; pointer: hand;" />' ;
			$mod -> MxText ( 'traitement1', '<div id="text1'.$res['idpatient'][$i].'" style="display:inline;">La partie CCAM de cet export a été traitée par '.$exp['iduser1'][0].' le '.$date->getDateTextFull('à').'</div> '.$img ) ;
		} else {
			$mod -> MxText ( 'color1', 'red' ) ;
			$aja = XhamTools::genAjax ( '', 'setTraitement', 'navi='.$session->genNaviFull ( ).'&idpatient='.$res['idpatient'][$i].'&setTraitement=valide&type=1' ) ;
			$ajax = 'onclick="'.$aja.'inverserTraitement('.$res['idpatient'][$i].',1,\'CCAM\');"' ;
			$img = '<img src="images/dd.gif" id="img1'.$res['idpatient'][$i].'" alt="valider" '.$ajax.' style="cursor: pointer; pointer: hand;" />' ;
			$mod -> MxText ( 'traitement1', '<div id="text1'.$res['idpatient'][$i].'" style="display:inline;">La partie CCAM de cet export n\'est pas traitée.</div> '.$img ) ;
		}
		if ( isset ( $exp['etat2'][0] ) and $exp['etat2'][0] ) {
			$mod -> MxText ( 'color2', 'green' ) ;
			$date = new clDate ( $exp['date2'][0] ) ;
			$aja = XhamTools::genAjax ( '', 'setTraitement', 'navi='.$session->genNaviFull ( ).'&idpatient='.$res['idpatient'][$i].'&setTraitement=invalide&type=2' ) ;
			$ajax = 'onclick="'.$aja.'inverserTraitement('.$res['idpatient'][$i].',2,\'NGAP\');"' ;
			$img = '<img src="images/gg.gif" id="img2'.$res['idpatient'][$i].'" alt="annuler" '.$ajax.' style="cursor: pointer; pointer: hand;" />' ;
			$mod -> MxText ( 'traitement2', '<div id="text2'.$res['idpatient'][$i].'" style="display:inline;">La partie NGAP de cet export a été traitée par '.$exp['iduser2'][0].' le '.$date->getDateTextFull('à').'</div> '.$img ) ;
		} else {
			$mod -> MxText ( 'color2', 'red' ) ;
			$aja = XhamTools::genAjax ( '', 'setTraitement', 'navi='.$session->genNaviFull ( ).'&idpatient='.$res['idpatient'][$i].'&setTraitement=valide&type=2' ) ;
			$ajax = 'onclick="'.$aja.'inverserTraitement('.$res['idpatient'][$i].',2,\'NGAP\');"' ;
			$img = '<img src="images/dd.gif" id="img2'.$res['idpatient'][$i].'" alt="valider" '.$ajax.' style="cursor: pointer; pointer: hand;" />' ;
			$mod -> MxText ( 'traitement2', '<div id="text2'.$res['idpatient'][$i].'" style="display:inline;">La partie NGAP de cet export n\'est pas traitée.</div> '.$img ) ;
		}
		if ( isset ( $exp['etat3'][0] ) and $exp['etat3'][0] ) {
			$mod -> MxText ( 'color3', 'green' ) ;
			$date = new clDate ( $exp['date3'][0] ) ;
			$aja = XhamTools::genAjax ( '', 'setTraitement', 'navi='.$session->genNaviFull ( ).'&idpatient='.$res['idpatient'][$i].'&setTraitement=invalide&type=3' ) ;
			$ajax = 'onclick="'.$aja.'inverserTraitement('.$res['idpatient'][$i].',3,\'diagnostics\');"' ;
			$img = '<img src="images/gg.gif" id="img3'.$res['idpatient'][$i].'" alt="annuler" '.$ajax.' style="cursor: pointer; pointer: hand;" />' ;
			$mod -> MxText ( 'traitement3', '<div id="text3'.$res['idpatient'][$i].'" style="display:inline;">La partie diagnostics de cet export a été traitée par '.$exp['iduser3'][0].' le '.$date->getDateTextFull('à').'</div> '.$img ) ;
		} else {
			$mod -> MxText ( 'color3', 'red' ) ;
			$aja = XhamTools::genAjax ( '', 'setTraitement', 'navi='.$session->genNaviFull ( ).'&idpatient='.$res['idpatient'][$i].'&setTraitement=valide&type=3' ) ;
			$ajax = 'onclick="'.$aja.'inverserTraitement('.$res['idpatient'][$i].',3,\'diagnostics\');"' ;
			$img = '<img src="images/dd.gif" id="img3'.$res['idpatient'][$i].'" alt="valider" '.$ajax.' style="cursor: pointer; pointer: hand;" />' ;
			$mod -> MxText ( 'traitement3', '<div id="text3'.$res['idpatient'][$i].'" style="display:inline;">La partie diagnostics de cet export n\'est pas traitée.</div> '.$img ) ;
		}
		
		// Etat Civil
		$mod -> MxText ( 'idpatient', XhamTools::getAV ( $res['idpatient'][$i] ) ) ;
		$mod -> MxText ( 'idu', XhamTools::getAV ( $res['idu'][$i]) ) ;
		$mod -> MxText ( 'ilp', XhamTools::getAV ( $res['ilp'][$i]) ) ;
		$mod -> MxText ( 'nsej', XhamTools::getAV ( $res['nsej'][$i]) ) ;
		$mod -> MxText ( 'uf', XhamTools::getAV ( $res['uf'][$i] )) ;
		$mod -> MxText ( 'manuel', ($res['manuel'][$i]?'Oui':'Non') ) ;
		$mod -> MxText ( 'sexe', XhamTools::getAV ( $res['sexe'][$i]) ) ;
		$mod -> MxText ( 'nom', XhamTools::getAV ( strtoupper($res['nom'][$i])) ) ;
		$mod -> MxText ( 'prenom', XhamTools::getAV ( ucfirst(strtolower($res['prenom'][$i]))) ) ;
		$mod -> MxText ( 'naissance', XhamTools::getAV ( $res['dt_naissance'][$i], 'd/m/Y' ) ) ;
		$mod -> MxText ( 'adresse', nl2br(XhamTools::getAV ( $res['adresse_libre'][$i] )) ) ;
		$mod -> MxText ( 'cp', XhamTools::getAV ( $res['adresse_cp'][$i] ) ) ;
		$mod -> MxText ( 'ville', XhamTools::getAV ( $res['adresse_ville'][$i] ) ) ;
		$mod -> MxText ( 'tel', XhamTools::getAV ( $res['telephone'][$i] ) ) ;
		$mod -> MxText ( 'medecintraitant', nl2br(XhamTools::getAV ( $res['medecin_traitant'][$i] )) ) ;
		$mod -> MxText ( 'aPrevenir', nl2br(XhamTools::getAV ( $res['prevenir'][$i] )) ) ;
		$mod -> MxText ( 'dt_admission', XhamTools::getAV ( $res['dt_admission'][$i], 'd/m/Y H:i:s' ) ) ;
		$mod -> MxText ( 'dt_examen', XhamTools::getAV ( $res['dt_examen'][$i], 'd/m/Y H:i:s' ) ) ;
		$mod -> MxText ( 'dt_sortie', XhamTools::getAV ( $res['dt_sortie'][$i], 'd/m/Y H:i:s' ) ) ;
		// Informations de passage
		$mod -> MxText ( 'medecin', XhamTools::getAV ( $res['medecin_urgences'][$i] ) ) ;
		$mod -> MxText ( 'ide', XhamTools::getAV ( $res['ide'][$i] ) ) ;
		$mod -> MxText ( 'mode_admission', XhamTools::getAV ( $res['mode_admission'][$i] ) ) ;
		$mod -> MxText ( 'adresseur', XhamTools::getAV ( $res['adresseur'][$i] ) ) ;
		$mod -> MxText ( 'provenance', XhamTools::getAV ( $res['provenance'][$i] ) ) ;
		$mod -> MxText ( 'recours_categorie', XhamTools::getAV ( $res['recours_categorie'][$i] ) ) ;
		$mod -> MxText ( 'recours', XhamTools::getAV ( $res['motif_recours'][$i] ) ) ;
		$mod -> MxText ( 'recours_code', XhamTools::getAV ( $res['recours_code'][$i] ) ) ;
		$mod -> MxText ( 'gravite', XhamTools::getAV ( $res['code_gravite'][$i] ) ) ;
		$mod -> MxText ( 'ccmu', XhamTools::getAV ( $res['ccmu'][$i] ) ) ;
		$mod -> MxText ( 'gemsa', XhamTools::getAV ( $res['gemsa'][$i] ) ) ;
		$mod -> MxText ( 'souhaitee', XhamTools::getAV ( $res['dest_souhaitee'][$i] ) ) ;
		$mod -> MxText ( 'confirmee', XhamTools::getAV ( $res['dest_attendue'][$i] ) ) ;
		$mod -> MxText ( 'salle', XhamTools::getAV ( $res['salle_examen'][$i] ) ) ;
		$mod -> MxText ( 'traumato', XhamTools::getAV ( $res['traumato'][$i] ) ) ;
		$mod -> MxText ( 'motifTransfert', XhamTools::getAV ( $res['motif_transfert'][$i] ) ) ;
		$mod -> MxText ( 'moyenTransport', XhamTools::getAV ( $res['motif_transport'][$i] ) ) ;
		$mod -> MxText ( 'destPMSI', XhamTools::getAV ( $res['dest_pmsi'][$i] ) ) ;
		$mod -> MxText ( 'orientation', XhamTools::getAV ( $res['orientation'][$i] ) ) ;
		$mod -> MxText ( '', XhamTools::getAV ( $res[''][$i] ) ) ;
		// CCAM
		$req = new clResultQuery ( ) ;
		$param['cw'] = "WHERE DISCR=".$res['idpatient'][$i].' order by ID' ;
     	$ras = $req -> Execute ( "Fichier", "CCAM_getActesBAL", $param, "ResultQuery" ) ;
		//$mod -> MxText ( 'ccam', affTab ( $res['INDIC_SVC'] ) ) ;
		//eko ( $ras['INDIC_SVC'] ) ;
		if ( $ras['INDIC_SVC'][2] ) {
		
			$toDelete = array ( ) ;
			// Epuration des actes et diagnostics supprimés
			for ( $k = 0 ; isset ( $ras['ID'][$k] ) ; $k++ ) {
				$tabActe = explode ( '|', $ras['CONTENU'][$k] ) ;
				if ( $tabActe[9] == "suppression" )
					$toDelete[$tabActe[10].$tabActe[22]] = $tabActe[10].$tabActe[22] ;
			}
			
			//eko ( $toDelete ) ;
			
			
			for ( $i = 0 ; isset ( $ras['ID'][$i] ) ; $i++ ) {
				
				$tabActe = explode ( '|', $ras['CONTENU'][$i] ) ;
				$type    = $ras['TYPE'][$i] ;
				$idpass  = $tabActe[0] ;
				$idu     = $tabActe[1] ;
				$nomu    = $tabActe[2] ;
				$pren    = $tabActe[3] ;
				$sexe    = $tabActe[4] ;
				$dtnai   = $tabActe[5] ;
				$dtdem   = $tabActe[6] ;
				$hhdem   = $tabActe[7] ;
				$ufd     = $tabActe[8] ;
				$action  = $tabActe[9] ;
				$idact   = $tabActe[10] ;
				$cdccam  = $tabActe[11] ;
				$cddiags = $tabActe[12] ;
				$cdacti  = $tabActe[13] ;
				$cdphase = $tabActe[14] ;
				$dtr     = $tabActe[15] ;
				$hhr     = $tabActe[16] ;
				$nomumed = $tabActe[17] ;
				$prenmed = $tabActe[18] ;
				$adeli   = $tabActe[19] ;
				$ufr     = $tabActe[20] ;
				$modif   = $tabActe[21] ;
				$ngapl   = $tabActe[22] ;
				$ngapc   = $tabActe[23] ;
				$factu   = $tabActe[24] ;
				$cdasso  = $tabActe[25] ;
				$nuitjf  = $tabActe[26] ;
				
				if ( ! in_array( $idact.$ngapl, $toDelete ) AND $action!='suppression' ) {
				//if ( ! in_array( $idact, $toDelete ) ) {
					$lib = $this->getActeLibelle ( $tabActe[11], $ras['TYPE'][$i] ) ;
					$libSansHTML = $this->getActeLibelle ( $tabActe[11], $ras['TYPE'][$i], "1" ) ;
					
					
					$mod -> MxText ( 'acte.ligne', $action ) ;
					//$mod -> MxText ( 'acte.action', $action ) ;
					$mod -> MxText ( 'acte.type', $type ) ;
					$mod -> MxText ( 'acte.date', $dtr ) ;
					$mod -> MxText ( 'acte.heure', $hhr ) ;
					$mod -> MxText ( 'acte.uf', $ufr ) ;
					$mod -> MxText ( 'acte.code', $cdccam ) ;
					$mod -> MxText ( 'acte.autreDiags', XhamTools::getAV ( $cddiags ) ) ;
					$mod -> MxText ( 'acte.asso', XhamTools::getAV ( $cdasso ) ) ;
					$mod -> MxText ( 'acte.facture', XhamTools::getAV ( $factu ) ) ;
					$mod -> MxText ( 'acte.nuitjf', XhamTools::getAV ( $nuitjf ) ) ;
					$mod -> MxText ( 'acte.modificateurs', XhamTools::getAV ( $modif ) ) ;
					$mod -> MxText ( 'acte.codeActivite', XhamTools::getAV ( $cdacti ) ) ;
					$mod -> MxText ( 'acte.codePhase', XhamTools::getAV ( $cdphase ) ) ;
					$mod -> MxText ( 'acte.lettreCle', XhamTools::getAV ( $ngapl.'-'.$ngapc ) ) ;
					$mod -> MxText ( 'acte.medecin', XhamTools::getAV ( $nomumed ) ) ;
					$mod -> MxText ( 'acte.adeli', XhamTools::getAV ( $adeli ) ) ;
					$mod -> MxText ( 'acte.description', XhamTools::getAV ( $libSansHTML ) ) ;
					$mod -> MxBloc ( 'acte', 'loop' ) ;
				} else unset ( $toDelete[$idact.$ngapl] ) ;
			}
		} else $mod -> MxBloc ( 'acte', 'replace', '<tr><td colspan=16>Aucun acte.</td></tr>' ) ;
		// On retourne le code HTML généré par le template.
		return $mod -> MxWrite ( "1" ) ;       
	}

	// Retourne la liste des dates.
	function getListeDates ( ) {
		$dateDeb = new clDate ( DATELANCEMENT ) ;
		$dateFin = new clDate ( ) ;
		$tDeb = $dateDeb -> getTimestamp ( ) ;
		$tab = array ( ) ;
		for ( ; $dateFin -> getTimestamp ( ) >= $tDeb ; $dateFin -> addDays ( -1 ) )
			$tab[$dateFin->getDate ( "Y-m-d")] = $dateFin -> getDate ( "d/m/Y" ) ;
		return $tab ;
	}
	
	// Récupération du libellé de l'acte.
	function getActeLibelle ( $code, $type, $sansHTML='' ) {
		switch ( $type ) {
			case 'CCAM':
				$req = new clResultQuery ( ) ;
				$param['table'] = "ccam_acte" ;
				$param['cw'] = "WHERE CODE='$code'" ;
     			//$res = $req -> Execute ( "Fichier", "CCAM_getActeLibelle", $param, "ResultQuery" ) ;
     			if ( $sansHTML ) return "($code) ".$res['LIBELLE_COURT'][0]." : ".$res['LIBELLE_LONG'][0] ;
     			return "<b><u>($code) ".$res['LIBELLE_COURT'][0]."</u></b> :<br />".$res['LIBELLE_LONG'][0] ;
     			break ;
     		case 'NGAP':
     			$req = new clResultQuery ( ) ;
     			$param['table'] = "ccam_actes_domaine" ;
				$param['cw'] = "WHERE idActe='$code'" ;
     			//$res = $req -> Execute ( "Fichier", "CCAM_getActeLibelle", $param, "ResultQuery" ) ;
     			if ( $sansHTML ) return "($code) ".$res['cotationNGAP'][0]." : ".$res['libelleActe'][0] ;
     			return "<b><u>($code) ".$res['cotationNGAP'][0]."</u></b> :<br />".$res['libelleActe'][0] ;
     			break ;
     		default:
     			break ;
		}
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