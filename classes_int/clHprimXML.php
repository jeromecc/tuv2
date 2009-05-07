<?php

// Titre  : Classe HprimXML
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 14 mars 2008

// Description : 
// Gestion de la BAL HprimXML du terminal.

class clHprimXML {

  	// Constructeur.
  	function __construct ( ) {
		 $this->mbtv2ToHxml ( ) ;
  	}
  	
  	// On parcourt la table MBTV2 et on génère les fichiers Hprim XML.
  	function mbtv2ToHxml ( ) {
      
      global $options ;
  		$req = new clResultQuery ( ) ;
  		$param['cw'] = "where etat='P' group by discr" ;
     	$res = $req -> Execute ( "Fichier", "CCAM_getActesBAL", $param, "ResultQuery" ) ;
     //eko($res);
     	
      for ( $i = 0 ; isset ( $res['ID'][$i] ) ; $i++ ) {
      //for ( $i = 0 ; $i < 10 ; $i++ ) {
        // On récupère les actes NGAP et CCAM
        $param['cw'] = "where etat='P' AND ( type='CCAM' OR type='NGAP' ) AND discr=".$res['DISCR'][$i]." order by type, ID" ;
	     	$resccamngap = $req -> Execute ( "Fichier", "CCAM_getActesBAL", $param, "ResultQuery" ) ;
        //eko($resccamngap);
        // On récupère les diags
        $param['cw'] = "where etat='P' AND type='DIAG' AND discr=".$res['DISCR'][$i]." order by ID" ;
	     	$resdiag = $req -> Execute ( "Fichier", "CCAM_getActesBAL", $param, "ResultQuery" ) ;
	     	$this->af .= "<h4>Génération des fichiers HprimXML</h4>" ;
	     	
         for ( $j = 0 ; isset ( $resdiag['ID'][$j]) ; $j++ )	$this->createDiag ( $resdiag, $j ) ;
	     	 
          
          if ( $options -> getOption ( "HprimXML_EnvoiGroupe" ) ) {
           
				// debut specificité pour le ch-avignon par exemple on envoie les actes par intervenant
           		if ( $options -> getOption ( "HprimXML_EnvoiGroupeParIntervenant" ) ) {
                  // On envoie les actes par internant :  1 intervenant = 1 fichier
                  unset($paramRq);
                  $paramRq["cw"] = "(type='ACTE') and idEvent=".$res['DISCR'][$i]." and idDomaine=".CCAM_IDDOMAINE." group by envoi_matriculeIntervenant";
                  $req           = new clResultQuery;
                  $res2          = $req->Execute("Fichier","CCAM_getActesDiagsCotation2",$paramRq,"ResultQuery");
                  //eko($res2);
                  if ( isset ( $temp2 ) ) unset($temp2);
           		    for ( $elephant = 0 ; $elephant < $res2["INDIC_SVC"][2] ; $elephant++ ) {
                    
                    $temp = explode ("|",$res2["envoi_matriculeIntervenant"][$elephant]);
           		      if ($temp[1]) $temp2[$temp[1]] = $temp[1];
           		      else $temp2[$temp[0]] = $temp[0];
                    	
                    	
                  } 
                  //eko ($temp2);
                  if ( is_array($temp2) ) 
                  	while ( list ( $key, $val ) = each ( $temp2 ) ) {
                    $param['cw'] = "where (contenu LIKE '%||".$val."|%' AND etat='P' AND ( type='CCAM' OR type='NGAP' ) AND discr=".$res['DISCR'][$i].") order by type, ID" ;
	              	  $resparintervenant = $req -> Execute ( "Fichier", "CCAM_getActesBAL", $param, "ResultQuery" ) ;
	              	  
	              	  
	              	  $param['cw'] = "where (contenu LIKE '%||".$val."|%' AND (etat='P' or etat='W') AND ( type='CCAM' OR type='NGAP' ) AND discr=".$res['DISCR'][$i].") order by ID asc" ;
	              	  $resid = $req -> Execute ( "Fichier", "CCAM_getActesBAL", $param, "ResultQuery" ) ;
	              	  //eko($resid);
                    //eko($resid["ID"][0]);
                    
                    if ( $resparintervenant['INDIC_SVC'][2] )
                    	$this->createActes ( $resparintervenant, '', 1,$resid["ID"][0] ) ;
                    //eko($resparintervenant);
	              	  //eko($resparintervenant);
	              	}
              }
			// fin specificité pour le ch-avignon par exemple on envoie les actes par intervenant
              
              
              else
                // On envoie les actes groupés.
                $this->createActes ( $resccamngap, '', 1 ) ;
          } 
         else {
          // On envoie les actes dans un fichier séparés : 1 acte = 1 fichier
	     		for ( $j = 0 ; isset ( $resccamngap['ID'][$j]) ; $j++ ) $this->createActes ( $resccamngap, $j ) ;
	     	 }  	
     	}
     	if ( $options->getOption ( 'HprimXML_4dir' ) ) {
        $this->launchFTP ( "ngapxml/" ) ;
        $this->launchFTP ( "ccamxml/" ) ;
        $this->launchFTP ( "diagxml/" ) ;
        $this->launchFTP ( "atuxml/" ) ;
      } else $this->launchFTP ( ) ;
     	$this->af .= "<br/><br/>" ;
  }
	///////////////////////////////////////////////////////////////////////////////
  	
  	// Envoi des fichiers par FTP.
  	function launchFTP ( $rep='' ) {
  		global $options ;
  		global $errs ;
  		$this->af .= "<h4>Transfert des fichiers HprimXML</h4>" ;
 		if ( $options->getOption ( 'HprimXML_FTPHost' ) ) {
  		$r = opendir ( 'hprim/xml/'.$rep ) ;
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
  					$repc = ftp_chdir ( $con, ($rep?$rep:$options->getOption ( 'HprimXML_FTPRep' )) ) ;
  					$this->af .= "Changement de répertoire sur le serveur : '".$options->getOption('HprimXML_FTPRep')."' -> " ;
  					if ( ! $repc ) {
  						$this->af .= "<font color='red'>KO</font><br/>" ;
  						$errs->addErreur ( 'HprimXML : Impossible de changer de répertoire "'.$options->getOption ( 'HprimXML_FTPRep' ).'"' ) ;
  					} else {
  						$this->af .= "<font color='green'>OK</font><br/>" ;
  						closedir ( $r ) ;
  						$r = opendir ( 'hprim/xml/'.$rep ) ;
  						while ( $fic = readdir ( $r ) ) {
  							if ( $fic != "." AND $fic != ".." AND $fic != "ok" AND $fic != "diag" AND $fic != "atu" AND $fic != "ngap"  AND $fic != "ccam" ) {
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
	  								//eko ( 'hprim/xml/'.$key.'.xml' ) ;
	  								$put1 = ftp_put ( $con, $key.".xml", 'hprim/xml/'.$rep.$key.'.xml', FTP_ASCII ) ;
	  								$put2 = ftp_put ( $con, $key.".ok", 'hprim/xml/'.$rep.$key.'.ok', FTP_ASCII ) ;
	  								
	  								if ( ! $put1 OR ! $put2 ) {
	  									$this->af .= "<font color='red'>KO</font><br/>" ;
	  									$errs->addErreur ( 'HprimXML : Impossible d\'envoyer le fichier "'.$key.'.xml".' ) ;
	  								} else {
	  									$this->af .= "<font color='green'>OK</font><br/>" ;
	  									rename ( 'hprim/xml/'.$rep.$key.".ok", 'hprim/xml/ok/'.$key.".ok" ) ;
	  									rename ( 'hprim/xml/'.$rep.$key.".xml", 'hprim/xml/ok/'.$key.".xml" ) ;
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
  	}

	// Création des fichiers Hprim XML pour les actes.
	function createActes ( $res, $j='', $tous='',$id='' ) {
	//eko($res);
	//eko($id);
		global $options ;
		if ( ! $tous ) { $deb = $j ; $max = $j ; } else { $deb = 0 ; $max = $res['INDIC_SVC'][2] - 1 ;	}
		$mod = new ModeliXe ( "HprimXMLActes.html" ) ;
    	$mod -> SetModeliXe ( ) ;
		$tabActe = explode ( '|', XhamTools::sansAccent($res['CONTENU'][$deb]) ) ;
		$type = $res['TYPE'][$deb] ; $idpass = $tabActe[0] ; $idu = $tabActe[1] ; $nomu = $tabActe[2] ; $pren = $tabActe[3] ;
		$sexe = $tabActe[4] ; $dtnai = $tabActe[5] ; $dtdem = $tabActe[6] ;	$hhdem = $tabActe[7] ; $ufd = $tabActe[8] ; $action = $tabActe[9] ;
		$date = new clDate ( $dtdem.' '.$hhdem ) ;
        $date = new clDate (  ) ;
        if ( $options -> getOption ( "HprimXML_UF" ) ) {
            $ufr = $options -> getOption ( "HprimXML_UF" ) ;
            $ufd = $options -> getOption ( "HprimXML_UF" ) ;
        }
    if ( $options -> getOption ( 'HprimXML_AttributsESA' ) ) $mod -> MxText ( "attributsesa", ' version="1.03a" xsi:schemaLocation="http://www.hprim.org/hprimXML msgEvenementsServeurActes103a.xsd" xmlns="http://www.hprim.org/hprimXML" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' ) ;
    else $mod -> MxText ( "attributsesa", "" ) ;
    if ( $options -> getOption ( 'HprimXML_EnvoiGroupeParIntervenant' ) ) {
      if ( $options->getOption ( 'HprimXML_EmetInterv' ) == 'ID' )
      $emetti = $id+1;
    }
    else {
      if ( $options->getOption ( 'HprimXML_EmetInterv' ) == 'ID' )
        $emetti   = $res['ID'][$deb] ;
      elseif ( $options->getOption ( 'HprimXML_EmetInterv' ) )
        $emetti = $options->getOption ( 'HprimXML_EmetInterv' ) ;
		  else
        $emetti = $res['DISCR'][$deb] ;
    }

		
		
		if ( $options -> getOption ( 'HprimXML_EnvoiGroupeParIntervenant' ) ) {
      		if ( $options->getOption ( 'HprimXML_EmetInterv' ) == 'ID' ) {
        		$mod -> MxText ( 'identifiantMessage', $id ) ; 
        		//eko("avignon");
        	} else $mod -> MxText ( 'identifiantMessage', $res['ID'][$deb] ) ; 
    	} else {
    		$mod -> MxText ( 'identifiantMessage', $res['ID'][$deb] ) ; 
		    // eko("reste");
		}		  
		  
		  
		  
		if ( $options -> getOption ( 'HprimXML_DateT' ) ) $mod -> MxText ( 'dateHeureProduction', $date -> getDate ( "Y-m-d\TH:i:s" ) ) ;
		else $mod -> MxText ( 'dateHeureProduction', $date -> getDatetime ( ) ) ;
		$mod -> MxText ( 'codeEmetteur', ($options->getOption ( 'HprimXML_Emet' )?$options->getOption ( 'HprimXML_Emet' ):$res['DISCR'][$deb]) ) ;
		$mod -> MxText ( 'codeDestinataire', $options->getOption ( 'HprimXML_Dest' ) ) ;
		$mod -> MxText ( 'patientEmetteur', $idu ) ;
		$mod -> MxText ( 'patientRecepteur', $idu ) ;
		$mod -> MxText ( 'sexe', $sexe ) ;
		$mod -> MxText ( 'patientNom', $nomu ) ;
		$mod -> MxText ( 'patientPrenom', $pren ) ;
		
    if ( $dtnai != "00/00/0000" )
		  $mod -> MxText ( 'patientNaissance', $dtnai ) ;
		else
		  $mod -> MxText ( 'patientNaissance', "" ) ;
		$pati = new clPatient ( $res['DISCR'][$deb], ($action=='suppression'?'':'Sortis')  ) ;
        //$pati -> debugInfos ( ) ;
        //eko ( "UUUUUUUUUUUUUUUUUUUUUUUUFFFFFFFFFFFFFFFFFF : ".$pati->getInformation('uf').' pour '.$pati->getDateNaissance() ) ;
        // Correction date foireuse module ccam
        $datadmi = new clDate ( $pati->getDateAdmission ( ) ) ;
        $datexam = new clDate ( $pati->getDateExamen ( ) ) ;
        $datsort = new clDate ( $pati->getDateSortie ( ) ) ;
        if ( $options->getOption ( "ChoixHeureAffectationActes") == "Heure d'admission" ) $datdema = $dateadmi ;
        elseif ( $options->getOption ( "ChoixHeureAffectationActes") == "Heure d'examen" ) $datdema = $datexam ;
        elseif ( $options->getOption ( "ChoixHeureAffectationActes") == "Heure de sorti" ) $datdema = $datsort ;
        else {

            if (  $datadmi->getDatetime ( ) != '1999-12-31 00:00:00' AND $datsort->getDatetime ( ) != '1999-12-31 00:00:00' )
                $time = (int) ( $datadmi->getTimestamp ( ) / 2 ) + ( $datsort->getTimestamp ( ) / 2 ) ;
            elseif ( $datadmi->getDatetime ( ) != '1999-12-31 00:00:00' ) $time = $datadmi->getTimestamp ( ) ;
            else $time = $datsort->getTimestamp ( ) ;
            $datdema = new clDate ( $time ) ;
            eko ( $datadmi->getTimestamp ( ).' + '.$datsort->getTimestamp ( ).' = '.$time.' = '.$datdema -> getTimestamp ( ) ) ;
            eko ( $datadmi->getDatetime ( ).' + '.$datsort->getDatetime ( ).' = '.$time.' = '.$datdema -> getDatetime ( ) ) ;
        }
        $dtdem = $datdema -> getDate ( 'Y-m-d' ) ;
        $hhdem = $datdema -> getDate ( 'H:i' ) ;

    $mod -> MxText ( 'venueEmetteur', $idpass ) ;
		$mod -> MxText ( 'venueRecepteur', $idpass ) ;
		$mod -> MxText ( 'venueDate', $dtdem ) ;
    $mod -> MxText ( 'venueHeure', $hhdem ) ;
		$mod -> MxText ( 'interventionDate', $dtdem ) ;
		$mod -> MxText ( 'interventionHeure', $hhdem ) ;
		$mod -> MxText ( 'interventionEmetteur', $emetti ) ;
		$mod -> MxText ( 'interventionDemandeDate', $dtdem ) ;
		$mod -> MxText ( 'interventionDemandeHeure', $hhdem ) ;
		$mod -> MxText ( 'interventionUF',  $pati->getUF () ) ;
		$nbngap = 0 ;
		$nbccam = 0 ;

	    	$datenai = new clDate ( $pati->getDateNaissance() ) ;
	    	$duree = new clDuree ( $datenai->getTimestamp ( ) ) ;
            $duree -> getDuree ( $datenai->getTimestamp ( ) ) ;
	    	$ageannees = $duree -> getYears ( ) ;
		//eko ( $ageannees ) ;
		for ( $i = $deb ; $i <= $max ; $i++ ) {
			$tabActe = explode ( '|', XhamTools::sansAccent($res['CONTENU'][$i]) ) ;
			$type    = $res['TYPE'][$i] ; $idpass = $tabActe[0]  ; $idu    = $tabActe[1]  ; $nomu    = $tabActe[2]  ; $pren    = $tabActe[3] ;
			$sexe    = $tabActe[4]      ; $dtnai  = $tabActe[5]  ; $dtdem  = $tabActe[6]  ;	$hhdem   = $tabActe[7]  ; $ufd     = $tabActe[8] ;
			$action  = $tabActe[9]      ; $idact  = $tabActe[10] ; $cdccam = $tabActe[11] ; $cddiags = $tabActe[12] ; $cdacti  = $tabActe[13] ;
			$cdphase = $tabActe[14]     ; $dtr    = $tabActe[15] ; $hhr    = $tabActe[16] ; $nomumed = $tabActe[17] ; $prenmed = $tabActe[18] ;
			$adeli   = $tabActe[19]     ; $ufr    = $tabActe[20] ; $modif  = $tabActe[21] ; $ngapl   = $tabActe[22] ; $ngapc   = $tabActe[23] ;
			$factu   = $tabActe[24]     ; $cdasso = $tabActe[25] ; $nuitjf = $tabActe[26] ; $tabm    = explode ( '~', $modif ) ;
            if ( $options -> getOption ( "HprimXML_UF" ) ) {
                $ufr = $options -> getOption ( "HprimXML_UF" ) ;
                $ufd = $options -> getOption ( "HprimXML_UF" ) ;
            }
			if ( $options -> getOption ( "HprimXML_CodeMedecin" ) == 'ADELI' ) $codeade = $adeli; 
      elseif ( $options -> getOption ( "HprimXML_CodeMedecin" ) == 'NOMMED' ) $codeade = $nomumed;
      else $codeade = 'x' ;
			if ( $options -> getOption ( "HprimXML_ExecPrinc" ) ) $medExec = ' principal="oui"' ; else $medExec = '' ;
			if ( $options->getOption ( 'HprimXML_StatutFT' ) ) $modStatut = ' statut="ft"' ; else $modStatut = '' ;	
			if ( $action == 'creation' ) $action = utf8_encode ( 'création' ) ;
			if ( $nuitjf == 'F' ) $isFerie = "oui" ; else $isFerie = "non" ;
			if ( $nuitjf == 'N' ) $isNuit = '1t' ;
			elseif ( $nuitjf == 'NM' ) $isNuit = '2t' ;
			else $isNuit = 'non' ;
			$listeTraited[$res['ID'][$i]] = $res['ID'][$i] ;
			if ( $options->getOption('LettreCleCSPsy') AND $ngapl == 'CNPSY' ) $ngapl = $options->getOption('LettreCleCSPsy') ;

		  //$mod -> MxText ( 'identifiantMessage', $res['ID'][$deb] ) ;

        // Correction date foireuse module ccam
        $dtdem = $datdema -> getDate ( 'Y-m-d' ) ;
        $hhdem = $datdema -> getDate ( 'H:i' ) ;
        
        if ( $type == 'NGAP' ) {
				if ( $ngapl == 'MNO' AND $ageannees > 2 ) $erreurngap = 1 ;
				if ( ! ( $ngapl == 'AMI' AND $factu == 'non' ) AND ! $erreurngap ) {
  					if ( $ngapl == 'C' OR $ngapl == 'CS' ) $tri = '2.' ;
  					else $tri = '3.' ;
  					$mod -> MxText ( 'codeAdeliMedecin', $this->adeliMedecin ) ;
  					$mod -> MxText ( 'actesngap.ngap.action', $action ) ;
  					$mod -> MxText ( 'actesngap.ngap.facturable', $factu ) ;
  					$mod -> MxText ( 'actesngap.ngap.executionNuit', $isNuit ) ;
  					$mod -> MxText ( 'actesngap.ngap.executionDimancheJourFerie', $isFerie ) ;
  					$mod -> MxText ( 'actesngap.ngap.ngapEmetteur', $idact ) ;
  					$mod -> MxText ( 'actesngap.ngap.lettreCle', $ngapl ) ;
  					$mod -> MxText ( 'actesngap.ngap.coefficient', $ngapc ) ;
  					$mod -> MxText ( 'actesngap.ngap.ngapDate', $dtdem ) ;
  					$mod -> MxText ( 'actesngap.ngap.ngapHeure', $hhdem ) ;
  					$mod -> MxText ( 'actesngap.ngap.medecinADELI', $adeli ) ;
  					$mod -> MxText ( 'actesngap.ngap.medecinCode', $codeade ) ;
  					$mod -> MxText ( 'actesngap.ngap.medecinNom', $nomumed ) ;
                    if ( $ngapl == 'CS' ) $mod -> MxText ( 'actesngap.ngap.medecinUF', $ufr ) ;
  					else $mod -> MxText ( 'actesngap.ngap.medecinUF', $pati->getUF () ) ;
  					$mod -> MxBloc ( 'actesngap.ngap', 'loop' ) ;
  					$nbngap++;
  					if ( $ngapl == 'ATU' ) $repfic = "atuxml/" ;
  					else $repfic = "ngapxml/" ;
  				}
				$erreurngap = 0 ;
	   		} elseif ( $type == 'CCAM' ) {
	   			$tri = '1.' ;
	   			$mod -> MxText ( 'codeAdeliMedecin', $this->adeliMedecin ) ;
	   			$mod -> MxText ( 'actesccam.ccam.action', $action ) ;
	   			$mod -> MxText ( 'actesccam.ccam.ccamEmetteur', $idact ) ;
	   			if ( $options -> getOption ( 'HprimXML_Recepteur' ) )
	   				$mod -> MxText ( 'actesccam.ccam.ccamRecepteur', '<recepteur>'.$options -> getOption ( 'HprimXML_Recepteur' ).'</recepteur' ) ;
	   			else $mod -> MxText ( 'actesccam.ccam.ccamRecepteur', '' ) ;
	   			$mod -> MxText ( 'actesccam.ccam.codeActe', $cdccam ) ;
	   			$mod -> MxText ( 'actesccam.ccam.codeActivite', $cdacti ) ;
	   			$mod -> MxText ( 'actesccam.ccam.codePhase', $cdphase ) ;
	   			$mod -> MxText ( 'actesccam.ccam.ccamDate', $dtdem ) ;
	   			$mod -> MxText ( 'actesccam.ccam.ccamHeure', $hhdem ) ;
	   			$mod -> MxText ( 'actesccam.ccam.medExec', $medExec ) ;
	   			$mod -> MxText ( 'actesccam.ccam.medecinADELI', $adeli ) ;
				$mod -> MxText ( 'actesccam.ccam.medecinCode', $codeade ) ;
				$mod -> MxText ( 'actesccam.ccam.medecinNom', $nomumed ) ;
				//$mod -> MxText ( 'actesccam.ccam.medecinUF', $ufr ) ;
                $mod -> MxText ( 'actesccam.ccam.medecinUF', $pati->getUF () ) ;
				if ( $options -> getOption ( 'HprimXML_AssoNonVide' ) ) $asso = ($cdasso==''?1:$cdasso) ;
				else $asso = $cdasso ;
				$mod -> MxText ( 'actesccam.ccam.codeAssociationNonPrevue', $asso ) ;
              	$nbMod = 0 ;
              	for ( $k = 0 ; isset($tabm[$k]) ; $k++ ) {
                	if ( $tabm[$k] ) {
                		$mod -> MxText ( 'actesccam.ccam.modificateur.modificateur', $tabm[$k] ) ;
                		$mod -> MxText ( 'actesccam.ccam.modificateur.modStatut', $modStatut ) ;
                		$mod -> MxBloc ( 'actesccam.ccam.modificateur', 'loop' ) ;
                		$nbMod++ ;	
                	}
              	}
              	if ( $nbMod == 0 ) $mod -> MxBloc ( 'actesccam.ccam.modificateur', 'delete' ) ;
	   			$mod -> MxBloc ( 'actesccam.ccam', 'loop' ) ;	   			
	   			$nbccam++ ;
	   			$repfic = "ccamxml/" ;
	   		}
		}
		if ( $nbngap == 0 ) $mod -> MxBloc ( 'actesngap', 'delete' ) ;
		if ( $nbccam == 0 ) $mod -> MxBloc ( 'actesccam', 'delete' ) ;
		if ( $options->getOption ( "HprimXML_tri_CCAM_CCS_NGAP" ) ) {
			$numfic = $tri.$res['ID'][$deb] ;
		} else $numfic = $res['ID'][$deb] ;
		if ( $options->getOption('HprimXML_NomFic') ) $nomFic = $options->getOption('HprimXML_NomFic').'_'.$numfic.'' ;
		else $nomFic = 'fic'.$options->getOption('HprimXML_ChaineFic').'TV2_'.$numfic.'' ;
		
    	$num = $res['ID'][$deb] ;
		$this->genFile ( $mod -> MxWrite ( "1" ), $num, $nomFic, $listeTraited, $repfic ) ;
	}

	// Création d'un fichier Hprim XML pour le diagnostic
	function createDiag ( $res, $i ) {
		global $options ;
		global $stopAffichage ;
		
		$tabActe = array ( ) ;
 		$tabActe = explode ( '|', XhamTools::sansAccent($res['CONTENU'][$i]) ) ;
		$type  = $res['TYPE'][$i] ; $idpass = $tabActe[0]  ; $idu     = $tabActe[1]  ; $nomu  = $tabActe[2]  ; $pren   = $tabActe[3] ;
		$sexe  = $tabActe[4]      ; $dtnai  = $tabActe[5]  ; $dtdem   = $tabActe[6]  ; $hhdem = $tabActe[7]  ; $action = $tabActe[9] ;
		$idact = $tabActe[10]     ; $cdccam = $tabActe[11] ; $cddiags = $tabActe[12] ; $dtr   = $tabActe[15] ; $hhr    = $tabActe[16] ;
		$adeli = $tabActe[19]     ; $ufr    = $tabActe[20] ;
		$this->adeliMedecin = $adeli ;
    if ( $action == 'creation' ) $action = utf8_encode ( 'création' ) ;
		$date = new clDate ( ) ;
		if ( $options->getOption('HprimXML_NomFic') ) $nomFic = $options->getOption('HprimXML_NomFic').'_'.$res['ID'][$i].'' ;
		else $nomFic = 'fic'.$options->getOption('HprimXML_ChaineFic').'TV2_'.$res['ID'][$i].'' ;
		$num = $res['ID'][$i] ;
		$pati = new clPatient ( $res['DISCR'][$i], ($action=='suppression'?'':'Sortis')  ) ;
        
		$mod = new ModeliXe ( "HprimXMLDiag.html" ) ;
    	$mod -> SetModeliXe ( ) ;
    	$mod -> MxText ( 'identifiantMessage', $res['ID'][$i] ) ;
    	if ( $options -> getOption ( 'HprimXML_DateT' ) ) $mod -> MxText ( 'dateHeureProduction', $date -> getDate ( "Y-m-d\TH:i:s" ) ) ;
		else $mod -> MxText ( 'dateHeureProduction', $date -> getDatetime ( ) ) ;
    	$mod -> MxText ( 'codeEmetteur', ($options->getOption ( 'HprimXML_Emet' )?$options->getOption ( 'HprimXML_Emet' ):$res['DISCR'][$i]) ) ;
    	$mod -> MxText ( 'codeDestinataire', $options->getOption ( 'HprimXML_Dest' ) ) ;
    	$mod -> MxText ( 'patientEmetteur', $idu ) ;
    	$mod -> MxText ( 'patientRecepteur', $idu ) ;
    	$mod -> MxText ( 'sexe', $sexe ) ;
    	$mod -> MxText ( 'patientNom', $nomu ) ;
    	$mod -> MxText ( 'patientPrenom', $pren ) ;
    	
      if ( $dtnai != "00/00/0000" )
		    $mod -> MxText ( 'patientNaissance', $dtnai ) ;
		  else
		    $mod -> MxText ( 'patientNaissance', "" ) ;
		  
      $mod -> MxText ( 'venueRecepteur', $idpass ) ;
    	$mod -> MxText ( 'venueDate', $dtdem ) ;
    	$mod -> MxText ( 'venueHeure', $hhdem ) ;
    	$mod -> MxText ( 'action', $action ) ;
    	$mod -> MxText ( 'rumEmetteur', $idpass ) ;
		$mod -> MxText ( 'rumDate', $dtr ) ;
    	$mod -> MxText ( 'ADELI', $adeli ) ;
    	//$mod -> MxText ( 'rumUF', $ufr ) ;
        $mod -> MxText ( 'rumUF', $pati->getUF () ) ;
    	$mod -> MxText ( 'rumUFDate', $dtdem ) ;
    	$mod -> MxText ( 'rumUFHeure', $hhdem ) ;
    	
    	
    	switch( $options->getOption ( 'HprimXML_CIM10_encodage' )) {
    		case 'alphanum' :
    			$cdccam = ereg_replace( "[^A-Za-z0-9]" , "" , $cdccam );
    			break ;	
    		case 'alphanumcross' :
    			$cdccam = ereg_replace( "[^A-Za-z0-9+]" , "" , $cdccam );
    			break ;
		    default :
		    	$cdccam = $cdccam ;
		    	break ;	
    	}

    			
    	$mod -> MxText ( 'cim10princ', $cdccam ) ;
    	$tabd = explode ( '~', $cddiags ) ;
        eko ( count ( $tabd ).' : '.$cddiags );
        if ( count ( $tabd ) < 1 OR $cddiags == '~' OR $cddiags == '' ) {
            //$mod -> MxBloc ( 'diagsign', 'replace', '<diagnosticSignificatif><codeCim10/></diagnosticSignificatif>' ) ;
                    eko ( "ici" ) ;
            $mod -> MxBloc ( 'diagssign', 'replace', ' ' ) ;
        }
        else for ( $i = 0 ; isset($tabd[$i]) ; $i++ ) {
        	switch( $options->getOption ( 'HprimXML_CIM10_encodage' )) {
    		case 'alphanum' :
    			$codeCim10sign = ereg_replace( "[^A-Za-z0-9]" , "" , $tabd[$i] );
    			break ;	
    		case 'alphanumcross' :
    			$codeCim10sign = ereg_replace( "[^A-Za-z0-9+]" , "" , $tabd[$i] );
    			break ;
		    default :
		    	$codeCim10sign = $tabd[$i] ;
		    	break ;	
    		}
    		$mod -> MxText ( 'diagssign.diagsign.codeCim10sign',$codeCim10sign ) ;
    		$mod -> MxBloc ( 'diagssign.diagsign', 'loop' ) ;
        }
		$this->genFile ( $mod -> MxWrite ( "1" ), $num, $nomFic, array(), "diagxml/" ) ;
	}
  	
  	function genFile ( $file, $num, $nomFic, $listeTraited=array(), $rep='' ) {
  	//eko($file);
  	  global $options ;
  	  if ( ! $options->getOption ( 'HprimXML_4dir' ) ) $rep = '' ;
  		$this->af .= "- Génération du fichier '".'hprim/xml/'.$nomFic.'.xml'.' -> ' ;
	    $FIC = fopen ( 'hprim/xml/'.$rep.$nomFic.'.xml', "w" ) ;
      	$creation = fwrite ( $FIC, iconv ( "UTF-8", "ISO-8859-1", $file ) ) ;
      	//$creation = fwrite ( $FIC, $file ) ;
      	fclose ( $FIC ) ;
      	if ( $creation ) {
      		$FIC = fopen ( 'hprim/xml/'.$rep.$nomFic.'.ok', "w" ) ;
      		$creation2 = fwrite ( $FIC, '' ) ;
      		fclose ( $FIC ) ;
      	}
      	if ( $creation ) {
      		$this->mbtv2Traited ( $num ) ;
      		if ( count ( $listeTraited ) ) {
      			while ( list ( $key, $val ) = each ( $listeTraited ) )
      				$this->mbtv2Traited ( $key ) ;
      		}
      		$this->af .= '<font color="green">OK</font><br/>' ;
      	} else {
      		global $errs ;
      		$errs -> addErreur ( 'Problème création pour ID='.$num ) ;	
      		$this->af .= '<font color="red">KO</font><br/>' ;
      	}
  	}
  	
	// On marque un message de la table MBTV2 comme traité.
	function mbtv2Traited ( $id, $dateT='' ) {
		$date = new clDate ( $dateT ) ;
		$param['DTTRT'] = $date -> getDatetime ( ) ;
		$param['ETAT'] = "W" ;
		$majrq = new clRequete ( CCAM_BDD, "MBTV2", $param ) ;
		if ( $id ) $sql = $majrq->updRecord ( "ID=$id" ) ;
	}

  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>
