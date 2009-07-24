<?php

// Titre  : Classe RPU
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 12 Février 2007

// Description : 
// Extraction des RPU.

class clRPU {

	// Attributs de la classe.
	private $xmlRpu ;
	private $message ;
	private $xmlH1N1 = '' ;

  	// Constructeur.
  	function __construct ( $param='' ) {

		set_time_limit(0);
		ini_set('memory_limit', '512M');

 		if ( $param == 'showLogs' ) $this->showLogs ( ) ;
 		else $this->gestSend ( ) ;
  	}

	// Génération du fichier XML.
	function genXML ( $dat='' ) {
		global $options ;
    	global $session ;
    	// On prend la date passée en paramètre si elle existe.
    	if ( $dat )	$date = new clDate ( $dat ) ;
    	else {
    		// Sinon, on initialise avec la date de la veille.
    		$date = new clDate ( ) ;
    		$date -> addDays ( -1 ) ; 
    	}
    	//eko ( $_REQUEST ) ;
   		$_REQUEST['dateRPU'] = $date->getDate ( 'Y-m-d' ) ;
    	// Calcul de la date minimum (J-7).
    	$dateMin = new clDate ( $date -> getDatetime ( ) ) ;
    	$nbJours = $options->getOption ( 'RPU_NombreJours' ) ;
    	$dateMin -> addDays ( -$nbJours ) ;
    	
    	// Création des instances de dates.
    	$dateN = new clDate ( ) ;
    	$dateS = new clDate ( ) ;
    	$dateE = new clDate ( ) ;
    	
    	// Fabrication de la requête en fonction du nombre de jours.
    	if ( $nbJours == 1 )
    		$param['cw'] = "WHERE dt_admission LIKE '".$date->getDate ( 'Y-m-d' )."%' AND valide>=1 AND type_destination!='X'" ;
    	else
    		$param['cw'] = "WHERE dt_admission BETWEEN '".$dateMin->getDate ( 'Y-m-d 00:00:00' )."' AND '".$date->getDate ( 'Y-m-d 23:59:59')."' AND valide>=1 AND type_destination!='X'" ;
   		$req = new clResultQuery ;
   		// Exécution de la requête.
    	$res = $req -> Execute ( "Fichier", "getPatientsRPU", $param, "ResultQuery" ) ;
    	// eko ( $res['INDIC_SVC'] ) ;
    	// Chargement du template ModeliXe.
    	$mod = new ModeliXe ( "rpu.html" ) ;
    	$mod -> SetModeliXe ( ) ;

		// On remplit les champs "fixes" fu fichier RPU.
    	$mod -> MxText ( 'idActeur', $options->getOption ( 'RPU_IdActeur' ) ) ;
    	$mod -> MxText ( 'cleActeur', $options->getOption ( 'RPU_CleActeur' ) ) ;
    	$mod -> MxText ( 'AR', $options->getOption ( 'RPU_AR_Actif' ) ) ;
    	$mod -> MxText ( 'mailAR', $options->getOption ( 'RPU_AR_Mail' ) ) ;
		
		// On parcourt les différents passages trouvés par la requête.
		for ( $i = 0 ; isset ( $res['idpatient'][$i] ) ; $i++ ) {
			// Récupération des actes du passage dans la base "xham_ccam".
			if ( $options -> getOption ( "ActiverModuleActes" ) ) {
				$param['idEvent'] = $res['idpatient'][$i] ;
				$param['idDomaine'] = 1 ;
    			$diags = $req -> Execute ( "Fichier", "CCAM_getDiagCotes", $param, "ResultQuery" ) ; 
    			$ccam = $req -> Execute ( "Fichier", "CCAM_getCcamCotes", $param, "ResultQuery" ) ; 
    			//eko ( $ccam['INDIC_SVC'] ) ;
			} else {
				// TODO : Récupération des informations nécessaires dans CORA.
				$param['idEvent'] = $res['idpatient'][$i] ;
				$param['idDomaine'] = 1 ;
				$diags = $req -> Execute ( "Fichier", "CCAM_getDiagCotes", $param, "ResultQuery" ) ;
				unset ( $param ) ;
				$param['nsej'] = $res['nsej'][$i] ;
				$ccam = $req -> Execute ( "Fichier", "CCAM_CoraCCAM", $param, "ResultQuery" ) ;
			}
    		
			// Calcul de la "date_event".
			$dateE -> setDate ( $res['dt_admission'][$i] ) ;
			if ( $res['dt_admission'][$i] != '0000-00-00 00:00:00' )
				$mod -> MxText ( 'rpu.date_event', $dateE->getDate ( 'd/m/Y H:i' ) ) ;
			else $mod -> MxText ( 'rpu.date_event', '' ) ;
			
			// Calcul de la date d'examen
			$dateE -> setDate ( $res['dt_examen'][$i] ) ;
			if ( $res['dt_examen'][$i] != '0000-00-00 00:00:00' )
				$mod -> MxText ( 'rpu.medic', $dateE->getDate ( 'd/m/Y  H:i' ) ) ;
			else $mod -> MxText ( 'rpu.medic', '' ) ;
			
			// CP & Ville
			$mod -> MxText ( 'rpu.cp', $res['adresse_cp'][$i] ) ;
			$mod -> MxText ( 'rpu.commune', $res['adresse_ville'][$i] ) ;
			// Calcul de la date de naissance.
			$dateN -> setDate ( $res['dt_naissance'][$i] ) ;
			if ( $res['dt_naissance'][$i] != '0000-00-00 00:00:00' )
				$mod -> MxText ( 'rpu.naissance', $dateN->getDate ( 'd/m/Y' ) ) ;
			else $mod -> MxText ( 'rpu.naissance', '' ) ;
			// Sexe
			$mod -> MxText ( 'rpu.sexe', $res['sexe'][$i] ) ;
			// Calcul de la date d'admission.
			$dateE -> setDate ( $res['dt_admission'][$i] ) ;
			if ( $res['dt_admission'][$i] != '0000-00-00 00:00:00' )
				$mod -> MxText ( 'rpu.entree', $dateE->getDate ( 'd/m/Y H:i' ) ) ;
			else $mod -> MxText ( 'rpu.entree', '' ) ;
			
			// Calcul de la provenance, du mode d'entrée et du transport.
			$prov = $res['provenance'][$i] ;
			$trans = explode ( ' ', $res['mode_admission'][$i] ) ;
			$mod -> MxText ( 'rpu.mode_entree', $prov[0] ) ;
			$mod -> MxText ( 'rpu.provenance', $prov[1] ) ;
			$mod -> MxText ( 'rpu.transport', $trans[0] ) ;
			$mod -> MxText ( 'rpu.transport_pec', $trans[1] ) ;
			
			// Recours & gravité
			$mod -> MxText ( 'rpu.motif', $res['recours_code'][$i] ) ;
			$mod -> MxText ( 'rpu.gravite', $res['code_gravite'][$i] ) ;	
			
			// Diagnostic principal
			$mod -> MxText ( 'rpu.dp', $diags['identifiant'][0] ) ;

            // Hash de l'IDU
            $mod -> MxText ( 'rpu.idpatientetab', sha1( $res['idu'][$i]) ) ;

			// Affichage des diagnostics secondaires.
			if ( $diags['INDIC_SVC'][2] <= 1 ) $mod -> MxBloc ( 'rpu.da', 'delete' ) ;
			else {
				for ( $j = 1 ; isset ( $diags['identifiant'][$j]) ; $j++ ) {
					$mod -> MxText ( 'rpu.da.da', $diags['identifiant'][$j] ) ;
					$mod -> MxBloc ( 'rpu.da', 'loop' ) ;
				}
			}

			// Affichage des actes.
			if ( $ccam['INDIC_SVC'][2] == 0 ) $mod -> MxBloc ( 'rpu.acte', 'delete' ) ;
			else {
				for ( $j = 0 ; isset ( $ccam['identifiant'][$j]) ; $j++ ) {
					$mod -> MxText ( 'rpu.acte.acte', $ccam['identifiant'][$j] ) ;
					$mod -> MxBloc ( 'rpu.acte', 'loop' ) ;
				}
			}
			
			// Calcul de la date de sortie.
			$dateS -> setDate ( $res['dt_sortie'][$i] ) ;
			if ( $res['dt_sortie'][$i] != '0000-00-00 00:00:00' )
				$mod -> MxText ( 'rpu.sortie', $dateS->getDate ( 'd/m/Y h:i' ) ) ;
			else $mod -> MxText ( 'rpu.sortie', '00/00/0000 00:00' ) ;
			
			// Calcul du mode de sortie.
			$dest = $res['type_destination'][$i] ;
			switch ( $res['type_destination'][$i] ) {
				case 'T': $modeS = '7' ; break;
				case 'H': $modeS = '6' ; break;
				case 'D': $modeS = '9' ; break;
				default:  $modeS = '8' ; break;
			}			
			$mod -> MxText ( 'rpu.mode_sortie', $modeS ) ;
			
			// Calcul de la destination et de l'orientation.
			if ( $dest == '6' OR $dest == '7' ) { 
					$destP = $dest ; 
					$ori = '' ; 
			} elseif ( $dest == 'F' ) {
					$ori = 'FUGUE' ;
					$destP = '' ;
			// { OR $dest == 'S' OR $dest == 'P' OR $dest == 'R' ) {
			} elseif ( $dest == 'S' ) {
					$ori = 'SCAM' ;
					$destP = '' ;	
			} elseif ( $dest == 'P' ) {
					$ori = 'PSA' ;
					$destP = '' ;
			} elseif ( $dest == 'R' ) {
					$ori = 'REO' ;
					$destP = '' ;
			} elseif ( $dest ) { 
					$destP = $res['dest_pmsi'][$i] ;
					$ori = $res['orientation'][$i] ;
			}			
			$mod -> MxText ( 'rpu.destination', $destP ) ;
			$mod -> MxText ( 'rpu.orient', $ori ) ;
			
			$mod -> MxBloc ( 'rpu', 'loop' ) ;
		}
		$mod -> MxText ( 'data_supplementaire',$this->xmlH1N1);
		
		// Récupération du code HTML généré.  
    	$this->xmlRpu = $mod -> MxWrite ( "1" ) ;
    	//$this->af .= nl2br(htmlentities($this->xmlRpu)) ;
	}








	// Génération du fichier XML.
	function genXMLH1N1 ( $dat='' ) {
		global $options ;
    	global $session ;


		if( ! $options->getOption('RPU_Envoi_Pandemie') )
		{
			return ;
		}
		
    	// On prend la date passée en paramètre si elle existe.
    	if ( $dat )	$date = new clDate ( $dat ) ;
    	else {
    		// Sinon, on initialise avec la date de la veille.
    		$date = new clDate ( ) ;
    		$date -> addDays ( -1 ) ;
    	}
    	//eko ( $_REQUEST ) ;
   		$_REQUEST['dateRPU'] = $date->getDate ( 'Y-m-d' ) ;
    	// Calcul de la date minimum (J-7).
    	$dateMin = new clDate ( $date -> getDatetime ( ) ) ;
    	$nbJours = $options->getOption ( 'RPU_NombreJours' ) ;
    	$dateMin -> addDays ( -$nbJours ) ;

		//passages en rapport avec le H1N1

		$requete = " SELECT COUNT(*) as nb FROM patients_sortis WHERE dt_admission BETWEEN '".$dateMin->getDate ( 'Y-m-d 00:00:00' )."' AND '".$date->getDate ( 'Y-m-d 23:59:59')."' AND valide>=1 AND type_destination!='X'" ;

		$requete.= " AND ilp IN ( SELECT ipp FROM ".CCAM_BDD.".ccam_cotation_actes WHERE `codeActe` = 'J09' )" ;

		$obRequete = new clRequete( BDD, 'patients_sortis', array()) ;

		$res = $obRequete ->exec_requete($requete, 'tab');

		$nbPassageH1N1 = $res[0]['nb'] ;

		//hospis en rapport avec le H1N1

		$requete = " SELECT COUNT(*) as nb FROM patients_sortis WHERE dt_admission BETWEEN '".$dateMin->getDate ( 'Y-m-d 00:00:00' )."' AND '".$date->getDate ( 'Y-m-d 23:59:59')."' AND valide>=1 AND type_destination = 'H' " ;

		$requete.= " AND ilp IN ( SELECT ipp FROM ".CCAM_BDD.".ccam_cotation_actes WHERE `codeActe` = 'J09' )" ;

		$res = $obRequete ->exec_requete($requete, 'tab');

		$nbHospisH1N1 = $res[0]['nb'] ;

		//Deces en rapport avec le H1N1

		$requete = " SELECT COUNT(*) as nb FROM patients_sortis WHERE dt_admission BETWEEN '".$dateMin->getDate ( 'Y-m-d 00:00:00' )."' AND '".$date->getDate ( 'Y-m-d 23:59:59')."' AND valide>=1 AND type_destination = 'D' " ;

		$requete.= " AND ilp IN ( SELECT ipp FROM ".CCAM_BDD.".ccam_cotation_actes WHERE `codeActe` = 'J09' )" ;

		$res = $obRequete ->exec_requete($requete, 'tab');

		$nbDecesH1N1 = $res[0]['nb'] ;

    	//$mod -> MxText ( 'idActeur', $options->getOption ( 'RPU_IdActeur' ) ) ;
    	//$mod -> MxText ( 'cleActeur', $options->getOption ( 'RPU_CleActeur' ) ) ;
    	//$mod -> MxText ( 'AR', $options->getOption ( 'RPU_AR_Actif' ) ) ;
    	//$mod -> MxText ( 'mailAR', $options->getOption ( 'RPU_AR_Mail' ) ) ;

		// Récupération du code HTML généré.
    	/* $this->xmlH1N1 = '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>' ; */
		//$this->xmlH1N1.= '<result>' ;
		//$this->xmlH1N1.= '<entete>' ;
		//$this->xmlH1N1.= '<idActeur>'.	$options->getOption ( 'RPU_IdActeur' ).'</idActeur>' ;
		//$this->xmlH1N1.= '<cleActeur>'. $options->getOption ( 'RPU_CleActeur' ).'</cleActeur>' ;
		//$this->xmlH1N1.= '<arRequis>'.$options->getOption ( 'RPU_AR_Actif' ).'</arRequis>';
		//$this->xmlH1N1.= '<mail>'.$options->getOption ( 'RPU_AR_Mail' ).'</mail>' ;
		//$this->xmlH1N1.= '</entete>' ;
		$this->xmlH1N1.= "\n".'<element>' ;
		$this->xmlH1N1.= "\n".'<nomForm>pandemie</nomForm>' ;
		$this->xmlH1N1.= "\n".'<date_event>'.$date->getDate ( 'd/m/Y H:i' ).'</date_event>' ;
		$this->xmlH1N1.= "\n".'<H1N1consult>'.$nbPassageH1N1.'</H1N1consult>';
		$this->xmlH1N1.= "\n".'<H1N1hospis>'.$nbHospisH1N1.'</H1N1hospis>';
		$this->xmlH1N1.= "\n".'<H1N1deces>'.$nbDecesH1N1.'</H1N1deces>';
		$this->xmlH1N1.= "\n".'</element>' ;
		//$this->xmlH1N1.= '</result>' ;

	}











	// Envoi des fichiers XML en attente.
	function sendXML ( $send='' ) {
		if ( $send ) { 
			global $options ;
			global $errs ;
			$date = new clDate ( ) ;
			
			// Création du fichier XML.
			$nomFic = $options->getOption ( 'RPU_IdActeur' )."_".$date->getDate('YmdHis').'.xml' ;
			$nomFicC = URLRPU.$nomFic ;
			$FIC = fopen ( $nomFicC, "w" ) ;
			if ( fwrite ( $FIC, $this->xmlRpu ) ) $this->message .= "<font color=\"green\">La création du fichier ($nomFicC) s'est bien déroulée.<br/></font>" ;
			else $this->message .= "<font color=\"red\">La création du fichier ($nomFicC) a échoué.<br/></font>" ;
			fclose ( $FIC ) ;
			
			if ( ! $options->getOption ( 'RPU_SansCryptEnvoi' ) ) {
				// Cryptage du fichier RPU.
				$mailE = $options->getOption('RPU_Envoi_Mail') ;
				$gpg = new gnuPG(false,GNUPG);
				$gpg -> EncryptFile ( $mailE, $nomFicC ) ;
				if ( ! $gpg -> error ) 
					$this->message .= "<font color=\"green\">Le cryptage du fichier ($nomFicC.gpg) s'est bien déroulé.<br/></font>" ;
				else
					$this->message .= "<font color=\"red\">Le cryptage du fichier ($nomFicC.gpg) a échoué :".$gpg -> error."<br/></font>" ;
				
				
				
				if ( $options -> getOption ( 'RPU_TypeEnvoi' ) == 'mail' ) {
					// Envoi du fichier RPU.
					$contenuFic = fread ( fopen ( $nomFicC.'.gpg', "r" ), filesize ( $nomFicC.'.gpg' ) ) ;
					// eko ( $contenuFic ) ;
					$mail = new mime_mail ( ) ;
					$mail->to = $mailE ;
					$mail->subject = "Envois RPU (".$_REQUEST['dateRPU'].")" ; 
					$mail->body = "Envois RPU (".$_REQUEST['dateRPU'].")" ;    
					$mail->from = Erreurs_MailApp ;    
					$mail->attach ( $contenuFic, $nomFic.'.gpg' ) ;
					if ( $options->getOption ( 'SMTP_BCC' ) )
						$mail->headers = "CC: ".$options->getOption ( 'SMTP_BCC' )."\r" ;  
					if ( $mail->sendXham ( ) ) $this->message .= "<font color=\"green\">L'envoi du fichier ($nomFicC.gpg) s'est bien déroulé.<br/></font>" ;
					else $this->message .= "<font color=\"red\">L'envoi du fichier ($nomFicC.gpg) a échoué.<br/></font>" ;
					rename ( URLRPU.$nomFic, URLRPU.'ok/'.$nomFic ) ;
					rename ( URLRPU.$nomFic.'.gpg', URLRPU.'ok/'.$nomFic.'.gpg' ) ;
				} else {
					rename ( URLRPU.$nomFic, URLRPU.'ok/'.$nomFic ) ;
					$this->message .= "Connexion au serveur FTP '".$options->getOption ( 'RPU_FTP_Host' ).':'.$options->getOption ( 'RPU_FTP_Port' )."' -> " ;
  					$con = ftp_connect ( $options->getOption ( 'RPU_FTP_Host' ) ) ;
  					if ( ! $con ) {
  						 $this->message .= "<font color='red'>KO</font><br/>" ;
  						$errs->addErreur ( 'RPU : Impossible de se connecter au serveur "'.$options->getOption ( 'RPU_FTP_Host' ).':'.$options->getOption ( 'RPU_FTP_Port' ).'"' ) ;
  					} else {
  						$this->message .= "<font color='green'>OK</font><br/>" ;
  						$this->message .= "Authentification au serveur FTP avec l'utilisateur '".$options->getOption('RPU_FTP_User')."' -> " ;
  						$log = ftp_login ( $con, $options->getOption ( 'RPU_FTP_User' ), $options->getOption ( 'RPU_FTP_Pass' ) ) ;
  						if ( ! $log ) {
  							$this->message .= "<font color='red'>KO</font><br/>" ;
  							$errs->addErreur ( 'RPU : Impossible de se connecter au serveur avec l\'utilisateur "'.$options->getOption ( 'RPU_FTP_User' ).'"' ) ;
  						} else {
  							$this->message .= "<font color='green'>OK</font><br/>" ;
  							
	  						$r = opendir ( 'rpu/' ) ;
  							while ( $fic = readdir ( $r ) ) {
  								if ( $fic != "." AND $fic != ".." AND $fic != "ok" AND $fic != "logs" AND $fic != 'arh' ) {		
			  						$this->message .= "Envoi du fichier '$fic' -> " ;
			  						$put = ftp_put ( $con, $fic, URLRPU.$fic, FTP_BINARY ) ;
			  						if ( ! $put ) {
			  							$this->message .= "<font color='red'>KO</font><br/>" ;
			  							$errs->addErreur ( 'RPU : Impossible d\'envoyer le fichier "'.$fic.'".' ) ;
			  						} else {
			  							$this->message .= "<font color='green'>OK</font><br/>" ;
			  							rename ( URLRPU.$fic, URLRPU.'ok/'.$fic ) ;
			  						}
  								}
  							}
  						}
					}
				}
				
				// Création du fichier Logs.
				$nomFicL = $options->getOption ( 'RPU_IdActeur' )."_".$date->getDate('YmdHis').'.html' ;
				$nomFicLC = URLRPULOGS.$nomFicL ;
				$FICL = fopen ( $nomFicLC, "w" ) ;
				fwrite ( $FICL, '<h3>RPU du '.$_REQUEST['dateRPU'].'</h3><h4>Messages :</h4>'.$this->message.'<h4>Contenu du fichier XML envoyé :</h4>'
				.nl2br(htmlentities($this->xmlRpu)) ) ;
				fclose ( $FICL ) ;
				

			}
			
			return '<br/><br/>'.$this->message ;
		} else return '<br/><br/>Affichage des RPU.' ;
	}

	function gestSend ( )
	{
		global $session ;
		// Chargement du template ModeliXe.
    	$mod = new ModeliXe ( "rpuGestSend.html" ) ;
    	$mod -> SetModeliXe ( ) ;

		$dateL = new clDate ( DATELANCEMENT ) ;
		$dateA = new clDate ( ) ;
		$dateA -> addDays ( -1 ) ;
		$tabDate[$dateA->getDate('Y-m-d')] = 'Hier' ;
		$dateA -> addDays ( -1 ) ;
		for ( ; $dateL -> getTimestamp ( ) < $dateA -> getTimestamp ( ) ; $dateA -> addDays ( -1 ) )
			$tabDate[$dateA->getDate ( 'Y-m-d')] = $dateA -> getDateText ( ) ;

		$mod -> MxSelect ( 'listeDates', 'dateRPU', $_POST['dateRPU'], $tabDate, '', '', 'onChange="reload(this.form)"' ) ;
		$mod -> MxHidden ( 'hidden', 'navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1)) ) ;

		$this->genXMLH1N1($_POST['dateRPU']) ;

		$this->genXML ( $_POST['dateRPU'] ) ;
		
		
		$mod -> MxText ( 'xmlRpu', '<p>'.nl2br(htmlentities($this->xmlRpu)).'</p>' ) ;
		
		$mod -> MxText ( 'message', $this->sendXML ( $_REQUEST['EnvoyerRPU'] ) ) ;
		
		$this->af .= $mod -> MxWrite ( "1" ) ;
	}

	// Affichage des logs.
	function showLogs ( ) {
		global $session ;
		// Chargement du template ModeliXe.
    	$mod = new ModeliXe ( "rpuShowLogs.html" ) ;
    	$mod -> SetModeliXe ( ) ;
		
		$r = opendir ( URLRPULOGS ) ;
    	while ( $fic = readdir ( $r ) ) {
      		if ( $fic != "." AND $fic != ".." ) {
				$tabLogs[$fic] = $fic ;
	  		}
		}
		
	    closedir ( $r ) ;
		rsort($tabLogs) ;
		if ( ! is_array ( $tabLogs ) ) $tabLogs = array ( ) ;
		$mod -> MxSelect ( 'listeLogs', 'nomLog', $_POST['nomLog'], $tabLogs, '', '', 'onChange="reload(this.form)"' ) ;
		$mod -> MxHidden ( 'hidden', 'navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1)) ) ;
		
		if ( ! $_POST['nomLog'] ) $_POST['nomLog'] = 0 ;//current($tabLogs) ;
		
		eko ( URLRPULOGS.$_POST['nomLog'] ) ;
		eko ( $tabLogs[$_POST['nomLog']] ) ;
		$mod -> MxText ( 'logsRpu', fread ( fopen ( URLRPULOGS.$tabLogs[$_POST['nomLog']], "r" ), filesize ( URLRPULOGS.$tabLogs[$_POST['nomLog']] ) ) ) ;
		
		$this->af .= $mod -> MxWrite ( "1" ) ;
	}

  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>