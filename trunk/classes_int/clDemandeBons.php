<?php

// Titre  : Classe Importation
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 19 Avril 2007

// Description : 
// Gestion de l'impression des demandes de bons.

class clDemandeBons {

	function __construct ( $idu, $nsej, $type='radio' ) {
    	global $tab ;
    	
    	// Préparation du document PDF.
    	$liste = new clListes ( "Documents", "1" ) ;
    	$tub = $liste -> getListes ( ) ;
    	//newfct ( gen_affiche_tableau, $tab ) ;
    	while ( list ( $key, $val ) = each ( $tub ) ) { 
      		$tab[$val] = $liste -> getListes ( $val ) ;
      		//newfct ( gen_affiche_tableau, $tab[$val] ) ;
    	}
    	
		//if ( headers_sent() ) print "<br/><b>Avant Formx : Headers déjà envoyés</b><br/>" ;
		//else print "<br/><b>Avant Formx : Headers non envoyés</b><br/>" ;
		
		$formx = new clFoRmX ( $idu, 'NO_POST_THREAT' ) ;
		//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		// setcookie("pouet","pouet");
		//if ( headers_sent() ) print "<br/><b>Après Formx : Headers envoyés</b><br/><br/>" ;
		//else print "<br/><b>Après Formx : Headers non envoyés</b><br/><br/>" ;
		
		
		if ( $_REQUEST['Formulaire2print'] == 'radio' ) {
		
			//if ( headers_sent() ) print "<br/><b>Headers déjà envoyés</b><br/>" ;
	
			$r = $formx -> getAllValuesFromFormx ( $_REQUEST['FormX_ext_goto_'], '', '', 'idinstance' ) ;
			//$r = $formx -> getAllItems ( ) ;
		
			//print affTab ( $r ) ;
    		
    		$pdf = new clFPDF ( ) ;
    		$pdf -> footerOn = 1 ;
    		$pdf -> AliasNbPages ( ) ;
    		$pdf -> SetFont ( 'Times', '', 12 ) ;
    		$pdf -> AddFont ( 'code39h48', '', 'IDAutomation_Code_39.php' ) ;
    		
      		for ( $i = 0 ; isset ( $r['Val_IDENT_NomPatient'][$i] ) ; $i++ ) {
				if ( $r['Val_IDENT_NsejPatient'][$i] == $nsej ) {
                    
					if ( $r['Val_IDENT_SexePat'][0] == "M"  or $r['Val_IDENT_SexePat'][0] == "Masculin" ) { $e = '' ; $titre = 'Mr ' ; }
                    if ( $r['Val_IDENT_SexePat'][0] == "F"  or $r['Val_IDENT_SexePat'][0] == "Feminin"  or $r['Val_IDENT_SexePat'][0] == "Féminin" or $r['Val_IDENT_SexePat'][0] == utf8_encode("Féminin")  ) { $e = 'e' ; $titre = 'Mme ' ; }

					$html = "<b>Nom : </b>".$titre.$r['Val_IDENT_NomPatient'][$i].'<br>' ;
					$html .= "<b>Prénom : </b>".$r['Val_IDENT_PrenomPatient'][$i].'<br>' ;
					$html .= "<b>Né$e le : </b>".$r['Val_IDENT_DateNPat2'][$i].'<br><br>' ;
					$html .= "<b>Motif : </b>".$r['Val_F_RADIO_Motif'][$i].'<br><br>' ;
      				// Saut de page.
      				$pdf -> AddPage ( ) ;
      				// On remplace les balises par leurs valeurs réelles.
      				$pdf -> SetFont ( 'times', '', 10 ) ;
      				// Génération du contenu.
      				$pdf -> writehtml ( $html ) ;
      				if ( ( $r['Val_F_RADIO_CoteDroit'][$i] == 'Aucune Radio à effectuer' AND $r['Val_F_RADIO_Centre'][$i] == 'Aucune Radio à effectuer' AND $r['Val_F_RADIO_CoteGauche'][$i] == 'Aucune Radio à effectuer' ) OR ( ! $r['Val_F_RADIO_CoteDroit'][$i] AND ! $r['Val_F_RADIO_Centre'][$i] AND ! $r['Val_F_RADIO_CoteGauche'][$i] ) ) {
      				} else { 
      					$pdf -> SetFont ( 'times', 'BU', 12 ) ;
      					$pdf -> setx ( 65 ) ;
      					$pdf -> Cell ( 80, 10, "RADIOGRAPHIES", 0, 1, L ) ;
      					$pdf -> SetFont ( 'times', '', 12 ) ;
      					if ( $r['Val_F_RADIO_CoteDroit'][$i] != 'Aucune Radio à effectuer' ) {
      						$exp = explode ( '|', $r['Val_F_RADIO_CoteDroit'][$i] ) ;
      						while ( list ( $key, $val ) = each ( $exp ) ) {
      							if ( $val )
      								$pdf -> Cell ( 80, 3, "1x ".$val." côté droit", 0, 1, L ) ;
      						}
      						$exp = explode ( '|', $r['Val_F_RADIO_Centre'][$i] ) ;
      						while ( list ( $key, $val ) = each ( $exp ) ) {
      							if ( $val )
      								$pdf -> Cell ( 80, 3, "1x ".$val." centre", 0, 1, L ) ;
      						}
      						if ( $r['Val_F_RADIO_Indication'][0] OR $r['Val_F_RADIO_Recherche'][0] ) { 
      							$pdf -> Cell ( 80, 3, '', 0, 1, L ) ;
      							$pdf -> Cell ( 80, 3, "Indication : ".$r['Val_F_RADIO_Indication'][0], 0, 1, L ) ;
      							$pdf -> Cell ( 80, 3, '', 0, 1, L ) ;
      							$pdf -> Cell ( 80, 3, "Je recherche : ".$r['Val_F_RADIO_Recherche'][0], 0, 1, L ) ;
      							$req = new clResultQuery ;
								$param['base'] = BDD ;
								$param['cw'] = ", radios_enquetes e where e.idradio=r.idradio and r.idpatient=p.idpatient and p.idu='$idu'" ;
				   				$res = $req -> Execute ( "Fichier", "getRadios", $param, "ResultQuery" ) ;
				   				$pdf -> Cell ( 80, 3, '', 0, 1, L ) ;
      							$pdf -> Cell ( 80, 3, "Destination confirmée : ".$res['dest_attendue'][0], 0, 1, L ) ;
      							$pdf -> Cell ( 80, 3, '', 0, 1, L ) ;
      						}
      						$exp = explode ( '|', $r['Val_F_RADIO_CoteGauche'][$i] ) ;
      						while ( list ( $key, $val ) = each ( $exp ) ) {
      							if ( $val )
      								$pdf -> Cell ( 80, 3, "1x ".$val." côté gauche", 0, 1, L ) ;
      						}
      					}		
					}
					
					

					
					if ( $r['Val_F_RADIO_TDM'][$i] == 'Aucun Scanner à effectuer' OR ! $r['Val_F_RADIO_TDM'][$i] ) {
      				} else { 
      					$pdf -> SetFont ( 'times', 'BU', 12 ) ;
      					$pdf -> setx ( 65 ) ;
      					$pdf -> Cell ( 80, 10, "SCANNER", 0, 1, L ) ;
      					$pdf -> SetFont ( 'times', '', 12 ) ;
   						$exp = explode ( '|', $r['Val_F_RADIO_TDM'][$i] ) ;
   						while ( list ( $key, $val ) = each ( $exp ) ) {
   							$pdf -> Cell ( 80, 3, "1x ".$val, 0, 1, L ) ;
  						}
					}
					if ( $r['Val_F_RADIO_Echo'][$i] == 'Aucune Echographie à effectuer' OR ! $r['Val_F_RADIO_Echo'][$i] ) {
      				} else { 
      					$pdf -> SetFont ( 'times', 'BU', 12 ) ;
      					$pdf -> setx ( 65 ) ;
      					$pdf -> Cell ( 80, 10, "ECHOGRAPHIES", 0, 1, L ) ;
      					$pdf -> SetFont ( 'times', '', 12 ) ;
   						$exp = explode ( '|', $r['Val_F_RADIO_Echo'][$i] ) ;
   						while ( list ( $key, $val ) = each ( $exp ) ) {
   							$pdf -> Cell ( 80, 3, "1x ".$val, 0, 1, L ) ;
  						}
					}
				
					if ( $r['Val_F_RADIO_Autres_E'][$i] AND $r['Val_F_RADIO_Autres_E'][$i] != 'Aucun.' ) {
						$pdf -> SetFont ( 'times', 'BU', 12 ) ;
      					$pdf -> setx ( 65 ) ;
      					$pdf -> Cell ( 80, 10, "AUTRES", 0, 1, L ) ;
      					$pdf -> SetFont ( 'times', '', 12 ) ;
   						$exp = explode ( '|', $r['Val_F_RADIO_Autres_E'][$i] ) ;
   						while ( list ( $key, $val ) = each ( $exp ) ) {
   							$pdf -> Cell ( 80, 3, "1x ".$val, 0, 1, L ) ;
  						}
					}
				
					if ( $r['Val_F_RADIO_Comm'][$i] AND $r['Val_F_RADIO_Comm'][$i] != 'Aucun.' ) {
						$pdf -> SetFont ( 'times', 'BU', 12 ) ;
      					$pdf -> setx ( 65 ) ;
      					$pdf -> Cell ( 80, 10, "COMMENTAIRES", 0, 1, L ) ;
      					$pdf -> SetFont ( 'times', '', 12 ) ;
   						$exp = explode ( '|', $r['Val_F_RADIO_Comm'][$i] ) ;
   						while ( list ( $key, $val ) = each ( $exp ) ) {
   							$pdf -> Cell ( 80, 3, "1x ".$val, 0, 1, L ) ;
  						}
					}
				
					$pdf -> Rect ( 15, 235, 115, 20 ) ;
					$pdf -> SetY ( 232 );       	
					$pdf -> SetFont ( 'times', '', 9 ) ;	
					$pdf -> Cell ( 0, 0, "Cotation", 0, 1, L ) ;
					
     				// IPP
					$pdf -> SetFont ( 'code39h48', '', 16 ) ;
      				$pdf -> sety ( 82 ) ;
      				$pdf -> Cell ( 0, 0, "*".$r['Val_IDENT_ILPPatient'][$i]."*", 0, 1, R ) ;
					$pdf -> SetY ( 76 );       	
					$pdf -> SetFont ( 'times', '', 11 ) ;		
     				$pdf -> Cell ( 80, 0, "N° IPP : ", 0, 1, R ) ;
     				// Nsej
					$pdf -> SetFont ( 'code39h48', '', 16 ) ;
      				$pdf -> sety ( 100 ) ;
      				$pdf -> Cell ( 0, 0, "*".$r['Val_IDENT_NsejPatient'][$i]."*", 0, 1, R ) ;
					$pdf -> SetY ( 96 );       	
					$pdf -> SetFont ( 'times', '', 11 ) ;		
     				$pdf -> Cell ( 80, 0, "N° Séjour : ", 0, 1, R ) ;					
				
      				// Signature du médecin.
					$pdf -> SetY ( 225 );       	
					$pdf -> SetFont ( 'times', '', 11 ) ;		
     				$pdf -> Cell ( 120, 25, "Docteur : ".$r['Val_F_RADIO_Nom_P'][$i], 0, 1, R ) ;		
      				//$pdf -> Footer ( ) ;
      			
				}
      		}
		} elseif ( $_REQUEST['Formulaire2print'] == 'labo' ) {
			$r = $formx -> getAllValuesFromFormx ( $_REQUEST['FormX_ext_goto_'], '', '', 'idinstance' ) ;
		
		 	//print affTab ( $r ) ;
    		$pdf = new clFPDF ( ) ;
    		$pdf -> footerOn = 1 ;
    		$pdf -> AliasNbPages ( ) ;
    		$pdf -> SetFont ( 'Times', '', 12 ) ;
    		$pdf -> AddFont ( 'code39h48', '', 'IDAutomation_Code_39.php' ) ;
      		for ( $i = 0 ; isset ( $r['Val_IDENT_NomPatient'][$i] ) ; $i++ ) {
				if ( $r['Val_IDENT_NsejPatient'][$i] == $nsej ) {
					if ( $r['Val_IDENT_SexePat'][0] == "M" ) { $e = '' ; $titre = 'Mr ' ; } else { $e = 'e' ; $titre = 'Mme ' ; }
					$html = "<b>Nom : </b>".$titre.$r['Val_IDENT_NomPatient'][$i].'<br>' ;
					$html .= "<b>Prénom : </b>".$r['Val_IDENT_PrenomPatient'][$i].'<br>' ;
					$html .= "<b>Né$e le : </b>".$r['Val_IDENT_DateNPat2'][$i].'<br><br>' ;
					$html .= "<b>Motif : </b>".$r['Val_F_BIO_Motif'][$i].'<br><br>' ;
      				// Saut de page.
      				$pdf -> AddPage ( ) ;
      				// On remplace les balises par leurs valeurs réelles.
      				$pdf -> SetFont ( 'times', '', 10 ) ;
      				// Génération du contenu.
      				$pdf -> writehtml ( $html ) ;
      				
      				$pdf -> SetFont ( 'times', 'BU', 11 ) ;
      				$pdf -> setx ( 65 ) ;
      				$pdf -> Cell ( 80, 10, "EXAMENS", 0, 1, L ) ;
      				
      				if ( $r['Val_F_BIO_F1'][$i] AND $r['Val_F_BIO_F1'][$i] != 'Aucun.' ) {
						//$pdf -> SetFont ( 'times', 'BU', 12 ) ;
      					//$pdf -> setx ( 65 ) ;
      					//$pdf -> Cell ( 80, 10, "", 0, 1, L ) ;
      					$pdf -> SetFont ( 'times', '', 11 ) ;
   						$exp = explode ( '|', $r['Val_F_BIO_F1'][$i] ) ;
   						while ( list ( $key, $val ) = each ( $exp ) ) {
   							$pdf -> Cell ( 80, 3, "1x ".$val, 0, 1, L ) ;
  						}
					}
      				
      				if ( $r['Val_F_BIO_F2'][$i] AND $r['Val_F_BIO_F2'][$i] != 'Aucun.' ) {
						//$pdf -> SetFont ( 'times', 'BU', 12 ) ;
      					//$pdf -> setx ( 65 ) ;
      					//$pdf -> Cell ( 80, 10, "", 0, 1, L ) ;
      					$pdf -> SetFont ( 'times', '', 11 ) ;
   						$exp = explode ( '|', $r['Val_F_BIO_F2'][$i] ) ;
   						while ( list ( $key, $val ) = each ( $exp ) ) {
   							$pdf -> Cell ( 80, 3, "1x ".$val, 0, 1, L ) ;
  						}
					}
					
					if ( $r['Val_F_BIO_Autres_E'][$i] AND $r['Val_F_BIO_Autres_E'][$i] != 'Aucun.') {
						$pdf -> SetFont ( 'times', 'BU', 11 ) ;
      					$pdf -> setx ( 65 ) ;
      					$pdf -> Cell ( 80, 10, "AUTRES", 0, 1, L ) ;
						$pdf -> SetFont ( 'times', '', 11 ) ;
						$pdf -> Cell ( 80, 3, $r['Val_F_BIO_Autres_E'][$i], 0, 1, L ) ;
					}
					
					if ( $r['Val_F_BIO_Comm'][$i] AND $r['Val_F_BIO_Comm'][$i] != 'Aucun.') {
						$pdf -> SetFont ( 'times', 'BU', 11 ) ;
      					$pdf -> setx ( 65 ) ;
      					$pdf -> Cell ( 80, 10, "COMMENTAIRES", 0, 1, L ) ;
						$pdf -> SetFont ( 'times', '', 11 ) ;
						$pdf -> Cell ( 80, 3, $r['Val_F_BIO_Comm'][$i], 0, 1, L ) ;
					}
					
					// Préleveur.
					$pdf -> SetY ( 225 );       	
					$pdf -> SetFont ( 'times', '', 11 ) ;		
     				$pdf -> Cell ( 0, 25, "Préleveur : ".$r['Val_F_BIO_Nom_Prel'][$i], 0, 1, L ) ;
     				$pdf -> SetY ( 229 );       	
					$pdf -> SetFont ( 'times', '', 11 ) ;		
     				$pdf -> Cell ( 0, 25, "Heure prélevement : ".$r['Val_F_BIO_Heure_Prel'][$i], 0, 1, L ) ;
     						
     				// IPP
					$pdf -> SetFont ( 'code39h48', '', 16 ) ;
      				$pdf -> sety ( 82 ) ;
      				$pdf -> Cell ( 0, 0, "*".$r['Val_IDENT_ILPPatient'][$i]."*", 0, 1, R ) ;
					$pdf -> SetY ( 76 );       	
					$pdf -> SetFont ( 'times', '', 11 ) ;		
     				$pdf -> Cell ( 80, 0, "N° IPP : ", 0, 1, R ) ;
     				// Nsej
					$pdf -> SetFont ( 'code39h48', '', 16 ) ;
      				$pdf -> sety ( 100 ) ;
      				$pdf -> Cell ( 0, 0, "*".$r['Val_IDENT_NsejPatient'][$i]."*", 0, 1, R ) ;
					$pdf -> SetY ( 96 );       	
					$pdf -> SetFont ( 'times', '', 11 ) ;		
     				$pdf -> Cell ( 80, 0, "N° Séjour : ", 0, 1, R ) ;
					
      				// Signature du médecin.
					$pdf -> SetY ( 225 );       	
					$pdf -> SetFont ( 'times', '', 11 ) ;		
     				$pdf -> Cell ( 120, 25, "Docteur : ".$r['Val_F_BIO_Nom_P'][$i], 0, 1, R ) ;		
      				//$pdf -> Footer ( ) ;
      			
				}
			}
		} else {
			$r = $formx -> getAllValuesFromFormx ( $_REQUEST['FormX_ext_goto_'], '', '', 'idinstance' ) ;
		
			//print affTab ( $r ) ;
    		$pdf = new clFPDF ( ) ;
    		$pdf -> footerOn = 1 ;
    		$pdf -> AliasNbPages ( ) ;
    		$pdf -> SetFont ( 'Times', '', 12 ) ;
    		$pdf -> AddFont ( 'code39h48', '', 'IDAutomation_Code_39.php' ) ;
      		for ( $i = 0 ; isset ( $r['Val_IDENT_NomPatient'][$i] ) ; $i++ ) {
				if ( $r['Val_IDENT_NsejPatient'][$i] == $nsej ) {
					if ( $r['Val_IDENT_SexePat'][0] == "M" ) { $e = '' ; $titre = 'Mr ' ; } else { $e = 'e' ; $titre = 'Mme ' ; }
					$html  = "<b>Nom : </b>".$titre.$r['Val_IDENT_NomPatient'][$i].'<br>' ;
					$html .= "<b>Prénom : </b>".$r['Val_IDENT_PrenomPatient'][$i].'<br>' ;
					$html .= "<b>Né$e le : </b>".$r['Val_IDENT_DateNPat2'][$i].'<br>' ;
					$html .= "<b>IPP : </b>".$r['Val_IDENT_ILPPatient'][$i].'<br><br>' ;
					$html .= "<b>Motif : </b>".$r['Val_F_CS_Motif'][$i].'<br>' ;
					$html .= "<b>Prescripteur : </b>".$r['Val_F_CS_Nom_P'][$i].'<br>' ;
					$html .= "<b>Consultation : </b>".$r['Val_F_CS_Con'][$i].'<br>' ;
					
      				// Saut de page.
      				$pdf -> AddPage ( ) ;
      				// On remplace les balises par leurs valeurs réelles.
      				$pdf -> SetFont ( 'times', '', 10 ) ;
      				// Génération du contenu.
      				$pdf -> writehtml ( $html ) ;
      				
      				$pdf -> SetFont ( 'times', 'BU', 11 ) ;
      				
      				// Compte rendu spécialiste
      				$pdf -> SetY ( 129 );       	
					$pdf -> SetFont ( 'times', 'b', 13 ) ;		
     				$pdf -> Cell ( 0, 10, "COMPTE RENDU SPECIALISTE", 0, 1, C ) ;
     				
     				// IPP
					$pdf -> SetFont ( 'code39h48', '', 16 ) ;
      				$pdf -> sety ( 85 ) ;
      				$pdf -> Cell ( 0, 0, "*".$r['Val_IDENT_ILPPatient'][$i]."*", 0, 1, R ) ;
					$pdf -> SetY ( 81 );       	
					$pdf -> SetFont ( 'times', '', 11 ) ;		
     				$pdf -> Cell ( 88, 0, "N° IPP : ", 0, 1, R ) ;
     				// Nsej
					$pdf -> SetFont ( 'code39h48', '', 16 ) ;
      				$pdf -> sety ( 100 ) ;
      				$pdf -> Cell ( 0, 0, "*".$r['Val_IDENT_NsejPatient'][$i]."*", 0, 1, R ) ;
					$pdf -> SetY ( 96 );       	
					$pdf -> SetFont ( 'times', '', 11 ) ;		
     				$pdf -> Cell ( 88, 0, "N° Séjour : ", 0, 1, R ) ;
     				      				
					// Signature
					$pdf -> SetY ( 216 );       	
					$pdf -> SetFont ( 'times', 'b', 11 ) ;		
     				$pdf -> Cell ( 0, 10, "Signature : ", 0, 1, L ) ;	
					
					// Spécialiste
					$pdf -> setx ( 65 ) ;
					$pdf -> SetY ( 255 );       	
					$pdf -> SetFont ( 'times', 'b', 11 ) ;		
     				$pdf -> Cell ( 0, 0, "Spécialiste : ", 0, 1, L ) ;
     				$pdf -> SetX ( 86 ) ;
     				$pdf -> SetFont ( 'times', '', 11 ) ;
     				$pdf -> Cell ( 0, 0, $r['Val_F_CS_Spe'][$i], 0, 1, L ) ;
					
      				// Cotation CCAM
					$pdf -> SetY ( 255 );       	
					$pdf -> SetFont ( 'times', 'b', 11 ) ;		
     				$pdf -> Cell ( 65, 10, "Cotation CCAM : ", 0, 1, L ) ;		

					$pdf -> Rect ( 97, 257, 101, 6 ) ;
      			
      				$pdf -> Rect ( 65, 130, 133, 7 ) ;
      				$pdf -> Rect ( 65, 138, 133, 80 ) ;
      				$pdf -> Rect ( 86, 219, 112, 7 ) ;
				}
			}
		}
      	
      	
    	$pdf -> Output ( ) ;
	}

	/*
	function Header ( ) {
    	global $options ;
    	global $tab ;
   		$this -> Image ( URLIMGLOGO, 15, 10, 45, 45 ) ;
   		reset ( $tab ) ;
    	list ( $key, $service ) = each ( $tab ) ;
		// Génération de la partie Service.
    	$this -> SetLeftMargin ( $options->getOption ( "Documents MG" ) ) ;
    	$this -> SetTopMargin ( $options->getOption ( "Documents MH" ) ) ;
        $this -> setxy ( 65, 10 ) ;
    	$this -> SetFont ( 'times', 'B', 12 ) ;
    	list ( $key, $val ) = each ( $service ) ;
    	$this -> Cell ( 0, 5, $val, 1, 2, C ) ;
    	$this -> SetFont('times','',6);
    	while ( list ( $key, $val ) = each ( $service ) ) { 		
     		$this -> setx ( 65 ) ;
      		$this -> Cell ( 0, 3, $val, 0, 1, C ) ;
    	}
    	reset ( $service ) ;
    	// Décalage entête.
    	$this -> Cell ( 0, 35, "", 0, 1, 0 ) ;
    	// Colonne de gauche.
    	while ( list ( $key_sous_bloc, $val_sous_bloc ) = each ( $tab ) ) { 		
      		$this -> SetFont ( 'times', 'B', 10 ) ;
      		$this -> Cell ( 45, 5, $key_sous_bloc, R, 1, L ) ;
      		$this -> SetFont ( 'times', '', 8 ) ;
      		while ( list ( $key, $val ) = each ( $val_sous_bloc ) ) {
      				//eko ( $val ) ;
      		 		if ( eregi ( '<i>' , $val ) ) $this -> SetStyle ( "I", true ) ;
					if ( eregi ( '<b>' , $val ) ) $this -> SetStyle ( "B", true ) ;
					if ( eregi ( '<u>' , $val ) ) $this -> SetStyle ( "U", true ) ;
					if ( eregi ( '<br>', $val ) ) $val = '' ;
					$this -> Cell ( 45, 3, ereg_replace ( "<[uibUIB]>", "", $val ), R, 1, L ) ;
					if ( eregi ( '<i>' , $val ) ) $this -> SetStyle ( "I", false ) ;
					if ( eregi ( '<b>' , $val ) ) $this -> SetStyle ( "B", false ) ;
					if ( eregi ( '<u>' , $val ) ) $this -> SetStyle ( "U", false ) ;
      			}
      		reset ( $val_sous_bloc ) ;
      		$this -> Cell ( 45, 5, " ", R, 1, L ) ;
    		}
    	reset ( $tab ) ;
		$this -> SetLeftMargin ( 65 ) ;
		if(IDAPPLICATION != "2") {
    		$this -> setxy ( 150 , 50 ) ;
    		$now   = date ( $options->getOption ( "Documents Date" ) ) ;
    		$ville = $options->getOption ( "Location Documents" ) ;
    		$this -> Cell ( 0, 5, "$ville, le $now", 0, 1, L ) ;
    		$this -> setxy ( 65, 75 ) ;
		} else {
			$this -> setxy ( 65, 40 ) ;
		}
  	}

   	//generation de l'entete pour tegeria, qui a des documents de plus d'une page
   	function gen_entete ( ) {
   		global $tab ;
		global $options ;
       	reset ( $tab ) ;
    	$this -> SetLeftMargin ( 65 ) ;
		$this -> SetTopMargin ( 0 ) ;
	
    	$this -> setxy ( 150 , 30 ) ;
    	$now   = date ( $options->getOption ( "Documents Date" ) ) ;
    	$ville = $options->getOption ( "Location Documents" ) ;
    	$this -> Cell ( 0, 5, "$ville, le $now", 0, 1, L ) ;
    	$this -> setxy ( 65, 40 ) ;
   	}
	
  	
	
  	// Génération du code pour le fichier PDF.
  	function WriteHTML ( $html ) {
     	global $tab ;
     	global $options ;
    	if(IDAPPLICATION == "2") $this->gen_entete();
    	// Parseur HTML
    	$html = str_replace ( "\n", ' ', $html ) ;
    	$a = preg_split ( '/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE ) ;
    	foreach ( $a as $i => $e ) {
      		if ( $i % 2 == 0 ) {
				// Texte.
				if ( $this->HREF )
	  				$this -> PutLink ( $this->HREF, $e ) ;
				else
	  				$this -> Write ( 5, $e ) ;
      		} else {
				// Balise.
				if ( $e{0} == '/' )
	  				$this -> CloseTag ( strtoupper ( substr ( $e, 1 ) ) ) ;
				else {
	  				// Extraction des attributs.
	  				$a2 = explode ( ' ', $e ) ;
	  				$tag = strtoupper ( array_shift ( $a2 ) ) ;
	  				$attr = array ( ) ;
	  				foreach ( $a2 as $v )
	    				if ( ereg ( '^([^=]*)=["\']?([^"\']*)["\']?$', $v, $a3 ) )
	    	  				$attr[strtoupper ( $a3[1] )] = $a3[2] ;
	  				$this -> OpenTag ( $tag, $attr ) ;
				}
      		}
    	}
  	}

 	function OpenTag ( $tag, $attr ) {
    	global $actions;
 
    	// Balise ouvrante
    	if ( $tag == 'B' or $tag == 'I' or $tag == 'U' )
      		$this -> SetStyle ( $tag, true ) ;
    	if( $tag == 'A' )
      		$this->HREF = $attr['HREF'] ;
    	if( $tag == 'BR' )
      		$this -> Ln ( 5 ) ;
      
    	//ajout 16/08/2005 Emmanuel Cervetti
    	//va chercher une variable globale dans la classe FoRmX
    
    	if(($tag == 'FORMX') && isset($actions) )  {
    		$this->Write(5,$actions->getVar($attr['VAR']) );
		}
     }
	
  	function CloseTag ( $tag ) {
    	// Balise fermante 
    	if ( $tag == 'B' or $tag == 'I' or $tag == 'U' )
      		$this -> SetStyle ( $tag, false ) ;
    	if ( $tag == 'A' )
      		$this->HREF='' ;
  	}
	
  	function SetStyle ( $tag, $enable ) {
    	// Modifie le style et sélectionne la police correspondante
    	$this->$tag += ( $enable ? 1 : -1 ) ;
    	$style = '' ;
    	foreach ( array ( 'B', 'I', 'U' ) as $s )
      		if ( $this->$s > 0 )
		$style .= $s ;
    	$this -> SetFont ( '', $style ) ;
  	}
	
  	function PutLink ( $URL, $txt ) {
    	// Place un hyperlien
    	$this -> SetTextColor ( 0, 0, 255 ) ;
    	$this -> SetStyle ( 'U', true ) ;
    	$this -> Write ( 5, $txt, $URL ) ;
    	$this -> SetStyle ( 'U', false ) ;
    	$this -> SetTextColor ( 0 ) ;
  	}
  
  	function Footer ( ) {
    	if(IDAPPLICATION == "2") {
    		//Positionnement à 1,5 cm du bas
    		$this->SetY(-15);
    		//Police Arial italique 8
    		$this->SetFont('Arial','I',8);
    		//Numéro de page
    		$this->Cell(0,10,'test',0,0,'C');
    	}
	}
	
	*/
}

?>
