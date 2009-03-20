<?php

// Titre  : Classe HprimXML
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 20 Novembre 2006

// Description : 
// Gestion de la BAL HprimXML du terminal.

class clHprimXML {

  	// Constructeur.
  	function __construct ( ) {
		 $this->mbtv2ToHxml ( ) ;
  	}
  	
  	// On parcourt la table MBTV2 et on génère les fichiers Hprim XML.
  	function mbtv2ToHxml ( ) {
  		$req = new clResultQuery ( ) ;
		$param['cw'] = "where etat='P' order by ID" ; // $this->filtre ;
     	$res = $req -> Execute ( "Fichier", "CCAM_getActesBAL", $param, "ResultQuery" ) ;
     	$this->af .= "<h4>Génération des fichiers HprimXML</h4>" ;
     	if ( $res['INDIC_SVC'][2] == 0 ) $this->af .= "Aucun acte dans la BAL MySQL." ;
     	for ( $i = 0 ; isset ( $res['ID'][$i]) ; $i++ )
     		$this->hxmlCreate ( $res, $i ) ;
     	//eko ( $res['INDIC_SVC'] ) ;
     	$this->launchFTP ( ) ;
     	$this->af .= "<br/><br/>" ;
  	}
  	
  	// Envoi des fichiers par FTP.
  	function launchFTP ( ) {
  		global $options ;
  		global $errs ;
  		$this->af .= "<h4>Transfert des fichiers HprimXML</h4>" ;

  		$r = opendir ( 'hprim/xml/' ) ;
  		for ( $i = 0 ; ( ( $fic = readdir ( $r ) ) AND ( $i < 4 ) ) ; $i++ ) ;
    	if ( $i == 4 ) {		
	  		$this->af .= "Connexion au serveur FTP '".$options->getOption ( 'HprimXML_FTPHost' ).':'.$options->getOption ( 'HprimXML_FTPPort' )."' -> " ;
  			$con = ftp_connect ( $options->getOption ( 'HprimXML_FTPHost' ) ) ;
  			if ( ! $con ) {
  				$this->af .= "<font color='red'>KO</font><br/>" ;
  				$errs->addErreur ( 'HprimXML : Impossible de se connecter au serveur "'.$options->getOption ( 'HprimXML_FTPHost' ).':'.$options->getOption ( 'HprimXML_FTPPort' ).'"' ) ;
  			} else {
  				$this->af .= "<font color='green'>OK</font><br/>" ;
  				$this->af .= "Authentification au serveur FTP avec l'utilisateur '".$options->getOption('HprimXML_FTPUser')."' -> " ;
  				$log = ftp_login ( $con, $options->getOption ( 'HprimXML_FTPUser' ), $options->getOption ( 'HprimXML_FTPPass' ) ) ;
  				if ( ! $log ) {
  					$this->af .= "<font color='red'>KO</font><br/>" ;
  					$errs->addErreur ( 'HprimXML : Impossible de se connecter au serveur avec l\'utilisateur "'.$options->getOption ( 'HprimXML_FTPUser' ).'"' ) ;
  				} else {
  					$this->af .= "<font color='green'>OK</font><br/>" ;
  					$repc = ftp_chdir ( $con, $options->getOption ( 'HprimXML_FTPRep' ) ) ;
  					$this->af .= "Changement de répertoire sur le serveur : '".$options->getOption('HprimXML_FTPRep')."' -> " ;
  					if ( ! $repc ) {
  						$this->af .= "<font color='red'>KO</font><br/>" ;
  						$errs->addErreur ( 'HprimXML : Impossible de changer de répertoire "'.$options->getOption ( 'HprimXML_FTPRep' ).'"' ) ;
  					} else {
  						$this->af .= "<font color='green'>OK</font><br/>" ;
  						closedir ( $r ) ;
  						$r = opendir ( 'hprim/xml/' ) ;
  						while ( $fic = readdir ( $r ) ) {
  							if ( $fic != "." AND $fic != ".." AND $fic != "ok" ) {
  								// Gestion du fichier .ok
  								$name = explode ( '.', $fic ) ;
  								// eko ( $name ) ;
  								if ( $name[1] == 'ok' )
  									$tab[$name[0]] = 1 ;
  							}
  						}
  						if ( is_array ( $tab ) ) {
	  						while ( list ( $key, $val ) = each ( $tab ) ) {
	  								$this->af .= " - Envoi du fichier '$key' -> " ;
	  								eko ( 'hprim/xml/'.$key.'.xml' ) ;
	  								$put1 = ftp_put ( $con, $key.".xml", 'hprim/xml/'.$key.'.xml', FTP_ASCII ) ;
	  								$put2 = ftp_put ( $con, $key.".ok", 'hprim/xml/'.$key.'.ok', FTP_ASCII ) ;
	  								
	  								if ( ! $put1 OR ! $put2 ) {
	  									$this->af .= "<font color='red'>KO</font><br/>" ;
	  									$errs->addErreur ( 'HprimXML : Impossible d\'envoyer le fichier "'.$fic.'".' ) ;
	  								} else {
	  									$this->af .= "<font color='green'>OK</font><br/>" ;
	  									rename ( 'hprim/xml/'.$key.".ok", 'hprim/xml/ok/'.$key.".ok" ) ;
	  									rename ( 'hprim/xml/'.$key.".xml", 'hprim/xml/ok/'.$key.".xml" ) ;
	  								}
	  						}
  						}
  					}
  				}
  			}
    	} else {
    		$this->af .= "Aucun fichier à traiter dans le répertoire 'hprim/xml/'.<br/>" ;
    	} 
  	}
  	
  	// On crée un fichier HprimXML.
  	function hxmlCreate ( $res, $i ) {
		global $options ;
		global $stopAffichage ;
		//$stopAffichage = 1 ;
		
		$tabActe = array ( ) ;
 		$tabActe = explode ( '|', $res['CONTENU'][$i] ) ;
					
		$type    = $res['TYPE'][$i] ;
		$idpass  = $tabActe[0] ;
		$idu     = $tabActe[1] ;
		$nomu    = XhamTools::sansAccent($tabActe[2]) ;
		$pren    = XhamTools::sansAccent($tabActe[3]) ;
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
		$nomumed = XhamTools::sansAccent($tabActe[17]) ;
		$prenmed = XhamTools::sansAccent($tabActe[18]) ;
		$adeli   = $tabActe[19] ;
		$ufr     = $tabActe[20] ;
		$modif   = $tabActe[21] ;
		$ngapl   = $tabActe[22] ;
		$ngapc   = $tabActe[23] ;
		$factu   = $tabActe[24] ;
		$cdasso  = $tabActe[25] ;
		$nuitjf  = $tabActe[26] ;
		
		// eko ( $tabActe ) ;
		if ( $action == 'creation' ) $action = utf8_encode ( 'création' ) ;
		if ( $nuitjf == 'F' ) $isFerie = "oui" ; else $isFerie = "non" ;
		if ( $nuitjf == 'N' ) $isNuit = '1t' ;
		elseif ( $nuitjf == 'NM' ) $isNuit = '2t' ;
		else $isNuit = 'non' ;
		
		//eko ( $tabActe ) ;			
		$date = new clDate ( ) ;
		$nomFic = 'ficTV2_'.$res['ID'][$i].'' ;
		$num = $res['ID'][$i] ;
		
		if ( $type != 'DIAG' ) {
		
		$dom      = new DOMDocument ( '1.0', 'ISO-8859-1' ) ;
		$evtsSA   = $dom->createElement ( 'evenementsServeurActes', '' ) ;
		  $enteteM  = $dom->createElement ( 'enteteMessage', '' ) ;
		    $idMe     = $dom->createElement ( 'identifiantMessage', $res['ID'][$i] ) ;
		    if ( $options -> getOption ( 'HprimXML_DateT' ) )
		    	$dHProd   = $dom->createElement ( 'dateHeureProduction', $date -> getDate ( "Y-m-d\TH:i:s" ) ) ;
		    else $dHProd   = $dom->createElement ( 'dateHeureProduction', $date -> getDatetime ( ) ) ;
		    $emett    = $dom->createElement ( 'emetteur', '' ) ;
		      $agentse  = $dom->createElement ( 'agents', '' ) ;
		        $agente   = $dom->createElement ( 'agent', '' ) ;
		        $agente  -> setAttribute ( 'categorie', 'application' ) ;
		          $codee    = $dom->createElement ( 'code', ($options->getOption ( 'HprimXML_Emet' )?$options->getOption ( 'HprimXML_Emet' ):$res['DISCR'][$i]) ) ;
		    $desti    = $dom->createElement ( 'destinataire', '' ) ;
		      $agentsd  = $dom->createElement ( 'agents', '' ) ;
		        $agentd   = $dom->createElement ( 'agent', '' ) ;
		        $agentd  -> setAttribute ( 'categorie', 'application' ) ;
		          $coded    = $dom->createElement ( 'code', $options->getOption ( 'HprimXML_Dest' ) ) ;
		  $evtSA    = $dom->createElement ( 'evenementServeurActe', '' ) ;
			$patient  = $dom->createElement ( 'patient', '' ) ;
			  $identp   = $dom->createElement ( 'identifiant', '' ) ;
				$emettp   = $dom->createElement ( 'emetteur', '' ) ;
				  $valpe    = $dom->createElement ( 'valeur', $idu ) ;
				$recepp   = $dom->createElement ( 'recepteur', '' ) ;
				  $valpp    = $dom->createElement ( 'valeur', $idu ) ;
			  $persP    = $dom->createElement ( 'personnePhysique', '' ) ;  
			  $persP   -> setAttribute ( 'sexe', $sexe ) ;
			    $nomUs    = $dom->createElement ( 'nomUsuel', $nomu ) ;
			    $prenoms  = $dom->createElement ( 'prenoms', '' ) ;
			      $prenom   = $dom->createElement ( 'prenom', $pren ) ;
			    $dateNa   = $dom->createElement ( 'dateNaissance', '' ) ;
			      $dateN    = $dom->createElement ( 'date', $dtnai ) ;
			$venue    = $dom->createElement ( 'venue', '' ) ;
			  $identv   = $dom->createElement ( 'identifiant', '' ) ;
				$emettv   = $dom->createElement ( 'emetteur', '' ) ;
				  $valve    = $dom->createElement ( 'valeur', $idpass ) ;
				$recepv   = $dom->createElement ( 'recepteur', '' ) ;
				  $valvp    = $dom->createElement ( 'valeur', $idpass ) ;
			$interv   = $dom->createElement ( 'intervention', '' ) ;
			  $identi   = $dom->createElement ( 'identifiant', '' ) ;
			    if ( $options->getOption ( 'HprimXML_EmetInterv' ) == 'ID' ) 
			    	$emetti   = $dom->createElement ( 'emetteur', $res['ID'][$i] ) ;
			    elseif ( $options->getOption ( 'HprimXML_EmetInterv' ) )
			    	$emetti   = $dom->createElement ( 'emetteur', $options->getOption ( 'HprimXML_EmetInterv' ) ) ;
			    else $emetti   = $dom->createElement ( 'emetteur', $res['DISCR'][$i] ) ;
			    //$emetti   = $dom->createElement ( 'emetteur',  ) ;
			  $demande  = $dom->createElement ( 'demande', '' ) ;
			    $datePr   = $dom->createElement ( 'datePrescription', '' ) ;
			      $dateP    = $dom->createElement ( 'date', $dtdem ) ;
			      $heureP   = $dom->createElement ( 'heure', $hhdem ) ;
			    $uniteF   = $dom->createElement ( 'uniteFonctionnelle', '' ) ;
			      $code     = $dom->createElement ( 'code', $ufd ) ;
			  $debut    = $dom->createElement ( 'debut', '' ) ;
			    $dateID    = $dom->createElement ( 'date', $dtdem ) ;
			    $heureID   = $dom->createElement ( 'heure', $hhdem ) ;
			  
	   switch ( $type ) {
	   	case 'CCAM':
	   	  $actesCCAM = $dom->createElement ( 'actesCCAM', '' ) ;
	   	    $acteCCAM  = $dom->createElement ( 'acteCCAM', '' ) ;
	   	    $acteCCAM  -> setAttribute ( 'action', $action ) ;
	   	      $identa    = $dom->createElement ( 'identifiant', '' ) ;
	   	  		$emettia   = $dom->createElement ( 'emetteur', $idact ) ;
	   	  		$recepia   = $dom->createElement ( 'recepteur', '' ) ;
	   	  	  $codeActe  = $dom->createElement ( 'codeActe', $cdccam ) ;
	   	  	  $codeActi  = $dom->createElement ( 'codeActivite', $cdacti ) ;
	   	  	  $codePhas  = $dom->createElement ( 'codePhase', $cdphase ) ;
	   	  	  $execute   = $dom->createElement ( 'execute', '' ) ;
	   	  	    $datee     = $dom->createElement ( 'date', $dtdem ) ;
	   	  	    $heuree    = $dom->createElement ( 'heure', $hhdem ) ;
	   	  	  $executant = $dom->createElement ( 'executant', '' ) ;
	   	  	    $medecins  = $dom->createElement ( 'medecins', '' ) ;
	   	  	      $medExec   = $dom->createElement ( 'medecinExecutant', '' ) ;
	   	  	      if ( $options -> getOption ( "HprimXML_ExecPrinc" ) )	$medExec -> setAttribute ( 'principal', "oui" ) ;	
	   	  	        $medE      = $dom->createElement ( 'medecin', '' ) ;
	   	  	          $numADELI  = $dom->createElement ( 'numeroAdeli', $adeli ) ;
	   	  	          $identm    = $dom->createElement ( 'identification', '' ) ;
	   	  	            if ( $options -> getOption ( "HprimXML_CodeMedecin" ) == 'ADELI' ) $codeade = $adeli; else $codeade = 'x' ;
	   	  	            $codeia    = $dom->createElement ( 'code', $codeade ) ;
	   	  	          $persom    = $dom->createElement ( 'personne', '' ) ;
	   	  	            $nomUm     = $dom->createElement ( 'nomUsuel', $nomumed ) ;
	   	  	            $prenomsm  = $dom->createElement ( 'prenoms', '' ) ;
	   	  	              $prenomm   = $dom->createElement ( 'prenom', $prenmed ) ;
	   	  	    $uniteFo   = $dom->createElement ( 'uniteFonctionnelle', '' ) ;
	   	  	      $codeuf    = $dom->createElement ( 'code', $ufr ) ;
	   	  	    $modifs    = $dom->createElement ( 'modificateurs', '' ) ;
	   	  	    $tabd = explode ( '~', $modif ) ;
              	for ( $i = 0 ; isset($tabd[$i]) ; $i++ ) {
                	if ( $tabd[$i] ) {
                		eval ( '$modifc'.$i.'  = $dom->createElement ( "modificateur", "'.$tabd[$i].'" ) ;' ) ;
                		if ( $options->getOption ( 'HprimXML_StatutFT' ) )
                		eval ( '$modifc'.$i.'  -> setAttribute ( "statut", "ft" ) ;' ) ;
                	}
              	}
	   	  	  //$modifs    = $dom->createElement ( 'modificateurs', '' ) ;
	   	  	  //  $modifc    = $dom->createElement ( 'modificateur', $modif ) ;
	   	  	  $codeANP   = $dom->createElement ( 'codeAssociationNonPrevue', $cdasso ) ;
	   	  	  
	   	  	    $identa   -> appendChild ( $emettia ) ;
	   	  	    $identa   -> appendChild ( $recepia ) ;
	   	  	  $acteCCAM  -> appendChild ( $identa ) ;
	   	  	  $acteCCAM  -> appendChild ( $codeActe ) ;
	   	  	  $acteCCAM  -> appendChild ( $codeActi ) ;
	   	  	  $acteCCAM  -> appendChild ( $codePhas ) ;
	   	  	  	$execute  -> appendChild ( $datee ) ;
	   	  	  	$execute  -> appendChild ( $heuree ) ;
	   	  	  $acteCCAM  -> appendChild ( $execute ) ;
	   	  	          $medE      -> appendChild ( $numADELI ) ;
	   	  	            $identm    -> appendChild ( $codeia ) ;
	   	  	          $medE      -> appendChild ( $identm ) ;
	   	  	            $persom    -> appendChild ( $nomUm ) ;
	   	  	              $prenomsm  -> appendChild ( $prenomm ) ;
	   	  	            $persom    -> appendChild ( $prenomsm ) ;
	   	  	          $medE      -> appendChild ( $persom ) ;
	   	  	        $medExec   -> appendChild ( $medE ) ;
	   	  	      $medecins  -> appendChild ( $medExec ) ;
	   	  	    $executant -> appendChild ( $medecins ) ;
	   	  	      $uniteFo   -> appendChild ( $codeuf ) ;
	   	  	    $executant -> appendChild ( $uniteFo ) ;
	   	  	  $acteCCAM  -> appendChild ( $executant ) ;
	   	  	  for ( $i = 0 ; isset($tabd[$i]) ; $i++ ) {
		      	if ( $tabd[$i] )
		      		eval ( '$modifs  -> appendChild ( $modifc'.$i.' ) ;' ) ;
		      }
		      $acteCCAM  -> appendChild ( $modifs ) ;
	   	  	  $acteCCAM  -> appendChild ( $codeANP ) ;
	   	  	$actesCCAM -> appendChild ( $acteCCAM ) ;    
	   	  $raes = $actesCCAM ;
	   	break;
	   	case 'NGAP':
		  $actesNGAP  = $dom->createElement ( 'actesNGAP', '' ) ;
		  $acteNGAP  = $dom->createElement ( 'acteNGAP', '' ) ;
		  $acteNGAP -> setAttribute ( 'action', $action ) ;
		  $acteNGAP -> setAttribute ( 'facturable', $factu ) ;
		  $acteNGAP -> setAttribute ( 'executionNuit', $isNuit ) ;
		  $acteNGAP -> setAttribute ( 'executionDimancheJourFerie', $isFerie ) ;
		    $identn   = $dom->createElement ( 'identifiant', '' ) ;
		      $emettn   = $dom->createElement ( 'emetteur', $idact ) ;
		    $lettreC  = $dom->createElement ( 'lettreCle', $ngapl ) ;
		    $coeff    = $dom->createElement ( 'coefficient', $ngapc ) ;
		    $exec     = $dom->createElement ( 'execute', '' ) ;
		      $dateen   = $dom->createElement ( 'date', $dtdem ) ;
		      $heureen   = $dom->createElement ( 'heure', $hhdem ) ;
		    $presta   = $dom->createElement ( 'prestataire', '' ) ;
		      $medecins  = $dom->createElement ( 'medecins', '' ) ;
    	  	    $medE      = $dom->createElement ( 'medecin', '' ) ;
	   	  	      $numADELI  = $dom->createElement ( 'numeroAdeli', $adeli ) ;
	   	  	      $identm    = $dom->createElement ( 'identification', '' ) ;
	   	  	      if ( $options -> getOption ( "HprimXML_CodeMedecin" ) == 'ADELI' ) $codeade = $adeli; else $codeade = 'x' ;
	   	  	        $codeia    = $dom->createElement ( 'code', $codeade ) ;
	   	  	      $persom    = $dom->createElement ( 'personne', '' ) ;
	   	  	        $nomUm     = $dom->createElement ( 'nomUsuel', $nomumed ) ;
	   	  	        $prenomsm  = $dom->createElement ( 'prenoms', '' ) ;
	   	  	          $prenomm   = $dom->createElement ( 'prenom', $prenmed ) ;
	   	  	  $uniteFo   = $dom->createElement ( 'uniteFonctionnelle', '' ) ;
	   	  	    $codeuf    = $dom->createElement ( 'code', $ufr ) ;
	   	    $identn -> appendChild ( $emettn ) ;	    
	   	  $acteNGAP -> appendChild ( $identn ) ;
	   	  $acteNGAP -> appendChild ( $lettreC ) ;
	   	  $acteNGAP -> appendChild ( $coeff ) ;
	   	    $exec      -> appendChild ( $dateen ) ;
	   	    $exec      -> appendChild ( $heureen ) ;
	   	  $acteNGAP -> appendChild ( $exec ) ;
	   	            $medE      -> appendChild ( $numADELI ) ;
	   	  	        $identm    -> appendChild ( $codeia ) ;
	   	  	      $medE      -> appendChild ( $identm ) ;
	   	  	        $persom    -> appendChild ( $nomUm ) ;
	   	  	        $prenomsm  -> appendChild ( $prenomm ) ;
	   	  	      $persom    -> appendChild ( $prenomsm ) ;
	   	  	    $medE      -> appendChild ( $persom ) ;
	   	  	  $medecins -> appendChild ( $medE ) ;
    	    $presta   -> appendChild ( $medecins ) ;
    	      $uniteFo  -> appendChild ( $codeuf ) ;
    	    $presta   -> appendChild ( $uniteFo ) ;
	   	  $acteNGAP -> appendChild ( $presta ) ;
	   	  $actesNGAP -> appendChild (  $acteNGAP ) ;
		  $raes = $actesNGAP ;  	  
	   	break;
	   }
			      
		  $enteteM -> appendChild ( $idMe ) ; 
		  $enteteM -> appendChild ( $dHProd ) ;
		        $agente  -> appendChild ( $codee ) ; 
		      $agentse -> appendChild ( $agente ) ;
		    $emett   -> appendChild ( $agentse ) ;
		  $enteteM -> appendChild ( $emett ) ;
		        $agentd  -> appendChild ( $coded ) ; 
		      $agentsd -> appendChild ( $agentd ) ;
		    $desti   -> appendChild ( $agentsd ) ;
		  $enteteM -> appendChild ( $desti ) ;
		$evtsSA  -> appendChild ( $enteteM ) ;
				$emettp  -> appendChild ( $valpe ) ; 
			  $identp  -> appendChild ( $emettp ) ;	  
				$recepp  -> appendChild ( $valpp ) ; 
			  $identp  -> appendChild ( $recepp ) ;	  
			$patient -> appendChild ( $identp ) ;
		      $persP   -> appendChild ( $nomUs ) ;
			    $prenoms -> appendChild ( $prenom ) ;
			  $persP   -> appendChild ( $prenoms ) ;
			    $dateNa  -> appendChild ( $dateN ) ;	
			  $persP   -> appendChild ( $dateNa ) ;    
			$patient -> appendChild ( $persP ) ;
		  $evtSA   -> appendChild ( $patient ) ;
		      	$emettv  -> appendChild ( $valve ) ; 
			  $identv  -> appendChild ( $emettv ) ;	  
				$recepv  -> appendChild ( $valvp ) ; 
			  $identv  -> appendChild ( $recepv ) ;	
		    $venue   -> appendChild ( $identv ) ;
		  $evtSA   -> appendChild ( $venue ) ;
		  	  $debut -> appendChild ( $dateID ) ;
		  	  $debut -> appendChild ( $heureID ) ;
		  	$interv  -> appendChild ( $debut ) ;
		  	  $identi -> appendChild ( $emetti ) ;
		    $interv  -> appendChild ( $identi ) ;
		        $datePr  -> appendChild ( $dateP ) ;
		        $datePr  -> appendChild ( $heureP ) ;
		      $demande -> appendChild ( $datePr ) ;
		        $uniteF  -> appendChild ( $code ) ;
		      $demande -> appendChild ( $uniteF ) ;
		    $interv  -> appendChild ( $demande ) ;
		  $evtSA   -> appendChild ( $interv ) ;
		  $evtSA   -> appendChild ( $raes ) ;
		$evtsSA  -> appendChild ( $evtSA ) ;
		$dom     -> appendChild ( $evtsSA ) ;
		
		} else {
		  $dom      = new DOMDocument ( '1.0', 'ISO-8859-1' ) ;
		  $evtsPMSI = $dom->createElement ( 'evenementsPMSI', '' ) ;
		    $enteteM  = $dom->createElement ( 'enteteMessage', '' ) ;
		      $idMe     = $dom->createElement ( 'identifiantMessage', $res['ID'][$i] ) ;
		      $dHProd   = $dom->createElement ( 'dateHeureProduction', $date -> getDatetime ( ) ) ;
		      $emett    = $dom->createElement ( 'emetteur', '' ) ;
		        $agentse  = $dom->createElement ( 'agents', '' ) ;
		          $agente   = $dom->createElement ( 'agent', '' ) ;
		          $agente  -> setAttribute ( 'categorie', 'application' ) ;
		            $codee    = $dom->createElement ( 'code', $options->getOption ( 'HprimXML_Emet' ) ) ;
		      $desti    = $dom->createElement ( 'destinataire', '' ) ;
		        $agentsd  = $dom->createElement ( 'agents', '' ) ;
		          $agentd   = $dom->createElement ( 'agent', '' ) ;
		          $agentd  -> setAttribute ( 'categorie', 'application' ) ;
		            $coded    = $dom->createElement ( 'code', $options->getOption ( 'HprimXML_Dest' ) ) ;
			$evtPMSI   = $dom->createElement ( 'evenementPMSI', '' ) ; 
			  $patient   = $dom->createElement ( 'patient', '' ) ;
			    $identp  = $dom->createElement ( 'identifiant', '' ) ;
				  $emettp  = $dom->createElement ( 'emetteur', '' ) ;
				    $valpe   = $dom->createElement ( 'valeur', $idu ) ;
				  $recepp  = $dom->createElement ( 'recepteur', '' ) ;
				    $valpp   = $dom->createElement ( 'valeur', $idu ) ;
			    $persP   = $dom->createElement ( 'personnePhysique', '' ) ;  
			    $persP  -> setAttribute ( 'sexe', $sexe ) ;
			      $nomUs   = $dom->createElement ( 'nomUsuel', $nomu ) ;
			      $prenoms = $dom->createElement ( 'prenoms', '' ) ;
			        $prenom  = $dom->createElement ( 'prenom', $pren ) ;
			      $dateNa  = $dom->createElement ( 'dateNaissance', '' ) ;
			        $dateN   = $dom->createElement ( 'date', $dtnai ) ;
			  $venue     = $dom->createElement ( 'venue', '' ) ;
			    $identv    = $dom->createElement ( 'identifiant', '' ) ;
			      $recepv    = $dom->createElement ( 'recepteur', '' ) ;
			        $valeur    = $dom->createElement ( 'valeur', $idpass ) ;
			    $entreev   = $dom->createElement ( 'entree', '' ) ;
			    $entreev  -> setAttribute ( 'typeEntree', '' ) ;  
			      $dateHO    = $dom->createElement ( 'dateHeureOptionnelle', '' ) ;
			        $dateho    = $dom->createElement ( 'date', $dtdem ) ;
			        $heureho   = $dom->createElement ( 'heure', $hhdem ) ;
			  $rss       = $dom->createElement ( 'rss', '' ) ;
			    $rum       = $dom->createElement ( 'rum', '' ) ;
			    $rum      -> setAttribute ( 'action', $action ) ;
			      $identr    = $dom->createElement ( 'identifiant', '' ) ;
			        $emettr    = $dom->createElement ( 'emetteur', $idpass ) ;
              	  $dateAc    = $dom->createElement ( 'dateAction', $dtr ) ;
              	  $actAc     = $dom->createElement ( 'acteurAction', '' ) ;
              	    $numAA	   = $dom->createElement ( 'numeroAdeli', $adeli ) ;
              	  $uniteMed  = $dom->createElement ( 'uniteMedicale', '' ) ;
              	    $codeU     = $dom->createElement ( 'code', $ufr ) ;
              	    $entreeU   = $dom->createElement ( 'entree', '' ) ;
              	    $entreeU  -> setAttribute ( 'mode', '' ) ;
              	    $entreeU  -> setAttribute ( 'complementMode', '' ) ;
              	      $dateHOe  = $dom->createElement ( 'dateHeureOptionnelle', '' ) ;
              	        $datHoe   = $dom->createElement ( 'date', $dtr ) ;
              	        $heurHoe  = $dom->createElement ( 'heure', $hhr ) ;
              	    $diags     = $dom->createElement ( 'diagnostics', '' ) ;
              	      $diagPri   = $dom->createElement ( 'diagnosticPrincipal', '' ) ;
              	        $codeCim   = $dom->createElement ( 'codeCim10', $cdccam ) ;
              	      $diagsSi   = $dom->createElement ( 'diagnosticsSignificatifs', '' ) ;
              	        $tabd = explode ( '~', $cddiags ) ;
              	        for ( $i = 0 ; isset($tabd[$i]) ; $i++ ) {
              	        	eval ( '$diagSi'.$i.'    = $dom->createElement ( "diagnosticSignificatif", "" ) ;' ) ;
              	          	eval ( '$codeCimS'.$i.'  = $dom->createElement ( "codeCim10", "'.$tabd[$i].'" ) ;' ) ;
              	        }
              
		  	  $enteteM  -> appendChild ( $idMe ) ; 
		      $enteteM  -> appendChild ( $dHProd ) ;
		            $agente   -> appendChild ( $codee ) ; 
		          $agentse  -> appendChild ( $agente ) ;
		        $emett    -> appendChild ( $agentse ) ;
		      $enteteM  -> appendChild ( $emett ) ;
		            $agentd   -> appendChild ( $coded ) ; 
		          $agentsd  -> appendChild ( $agentd ) ;
		        $desti    -> appendChild ( $agentsd ) ;
		      $enteteM  -> appendChild ( $desti ) ;
		    $evtsPMSI -> appendChild ( $enteteM ) ;
		            $emettp   -> appendChild ( $valpe ) ;
		          $identp   -> appendChild ( $emettp ) ;
		            //$valpp   = $dom->createElement ( 'valeur', $idu ) ;
		            $recepp   -> appendChild ( $valpp ) ;
		          $identp   -> appendChild ( $recepp ) ;
		        $patient  -> appendChild ( $identp ) ;
		          $persP    -> appendChild ( $nomUs ) ;
		            $prenoms  -> appendChild ( $prenom ) ;
		          $persP    -> appendChild ( $prenoms ) ;
		            $dateNa   -> appendChild ( $dateN ) ;
		          $persP    -> appendChild ( $dateNa ) ;
		        $patient  -> appendChild ( $persP ) ;
		      $evtPMSI  -> appendChild ( $patient ) ;
		            $recepv   -> appendChild ( $valeur ) ;
		      	  $identv   -> appendChild ( $recepv ) ;
		        $venue    -> appendChild ( $identv ) ;
		            $dateHO -> appendChild ( $dateho ) ;
		            $dateHO -> appendChild ( $heureho ) ;
		      	  $entreev  -> appendChild ( $dateHO ) ;
		        $venue    -> appendChild ( $entreev ) ;
		      $evtPMSI  -> appendChild ( $venue ) ;
		            $identr   -> appendChild ( $emettr ) ;
		          $rum      -> appendChild ( $identr ) ;
		          $rum      -> appendChild ( $dateAc ) ;
		            $actAc    -> appendChild ( $numAA ) ;
		          $rum      -> appendChild ( $actAc ) ;
		            $uniteMed -> appendChild ( $codeU ) ;
					    $dateHOe  -> appendChild ( $datHoe ) ;
					    $dateHOe  -> appendChild ( $heurHoe ) ;
					  $entreeU  -> appendChild ( $dateHOe ) ;
		            $uniteMed -> appendChild ( $entreeU ) ;
		          $rum      -> appendChild ( $uniteMed ) ;
		                $diagPri  -> appendChild ( $codeCim ) ;
		              $diags    -> appendChild ( $diagPri ) ;
		              	for ( $i = 0 ; isset($tabd[$i]) ; $i++ ) {
		                  	eval ( '$diagSi'.$i.'   -> appendChild ( $codeCimS'.$i.' ) ;' ) ;
		                	eval ( '$diagsSi  -> appendChild ( $diagSi'.$i.' ) ;' ) ;
		              	}
		              $diags    -> appendChild ( $diagsSi ) ;
		            //$uniteMed -> appendChild ( $diags ) ;
		          $rum      -> appendChild ( $diags ) ;
		        $rss      -> appendChild ( $rum ) ;
		      $evtPMSI  -> appendChild ( $rss ) ;
		    $evtsPMSI -> appendChild ( $evtPMSI ) ;
		  $dom      -> appendChild ( $evtsPMSI ) ;
		}
		
		if ( $type == 'DIAG' AND $options->getOption("HprimXML_noDIAG") ) {
			// Création, écriture et fermeture du fichier.
			$this->af .= "- Génération du fichier '".'hprim/xml/'.$nomFic.'.xml'.' -> ' ;
			$this->mbtv2Traited ( $num ) ;
	      	$this->af .= '<font color="orange">Pas d\'envoi des DIAG</font><br/>' ;
		} else {
			// Création, écriture et fermeture du fichier.
			$this->af .= "- Génération du fichier '".'hprim/xml/'.$nomFic.'.xml'.' -> ' ;
	     	$FIC = fopen ( 'hprim/xml/'.$nomFic.'.xml', "w" ) ;
	      	$creation = fwrite ( $FIC, $dom->saveXML ( ) ) ;
	      	fclose ( $FIC ) ;
	      	if ( $creation ) {
	      		$FIC = fopen ( 'hprim/xml/'.$nomFic.'.ok', "w" ) ;
	      		$creation2 = fwrite ( $FIC, '' ) ;
	      		fclose ( $FIC ) ;
	      	}
	      	//eko ( $nomFic." -> ".$res['ID'][$i]." ($num)" ) ;
	      	if ( $creation ) {
	      		$this->mbtv2Traited ( $num ) ;
	      		$this->af .= '<font color="green">OK</font><br/>' ;
	      	} else {
	      		global $errs ;
	      		$errs -> addErreur ( 'Problème création pour ID='.$num ) ;	
	      		$this->af .= '<font color="red">KO</font><br/>' ;
	      	}
		}
      	
  	}

	// On marque un message de la table MBTV2 comme traité.
	function mbtv2Traited ( $id, $dateT='' ) {
		$date = new clDate ( $dateT ) ;
		$param['DTTRT'] = $date -> getDatetime ( ) ;
		$param['ETAT'] = "W" ;
		$majrq = new clRequete ( CCAM_BDD, "MBTV2", $param ) ;
		$sql = $majrq->updRecord ( "ID=$id" ) ;
	}

  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>