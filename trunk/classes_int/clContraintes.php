<?php

// Titre  : Classe Contrainte
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 22 Avril 2005

// Description : 
// Gestion des contraintes pour autoriser la sortie
// d'un patient.

class clContraintes {

  // Attributs de la classe.
  // Contient l'affichage généré par la classe.
  private $af ;
  // Identifiant du patient.
  private $idpatient ;
  // Contient les données actuelles du patient.
  private $patient ;
  // Booléen
  private $bool ;

  // Constructeur.
  function __construct ( $idpatient, $paramCCAM='' ) {
    $this->idpatient = $idpatient ;
    $this->bool = 1 ;
    $this->paramCCAM = $paramCCAM ;
    $this->patient = new clPatient ( $this->idpatient, PPRESENTS ) ;
	
  }

  // Vérification des contraintes.
  function runCheck ( ) {
    global $options ;
    
    $nom1 = $options->getOption ( "sansContrainte1"    ) ;
    $val1 = $options->getOption ( "sansContrainteVal1" ) ;
    $nom2 = $options->getOption ( "sansContrainte2"    ) ;
    $val2 = $options->getOption ( "sansContrainteVal2" ) ;
    $nom3 = $options->getOption ( "sansContrainte3"    ) ;
    $val3 = $options->getOption ( "sansContrainteVal3" ) ;
    if ( ! $this->checkSansContrainte($nom1,$val1) AND ! $this->checkSansContrainte($nom2,$val2) AND ! $this->checkSansContrainte($nom3,$val3) ) {
      $this->check ( "recours_categorie", "CategorieRecours", "Catégorie de recours",  ERR_RECOURS_CATEGORIE ) ;
      $this->check ( "code_gravite",      "CodeGravite",      "Code Gravité",          ERR_CODE_GRAVITE      ) ;
      $this->check ( "dt_examen",         "DateExamen",       "Date d'examen",         ERR_DATE_EXAMEN       ) ;
      $this->check ( "dest_attendue",     "DestAttendue",     "Destination attendue",  ERR_DEST_ATTENDUE     ) ;
      $this->check ( "dest_souhaitee",    "DestSouhaitee",    "Destination souhaitée", ERR_DEST_SOUHAITEE    ) ;
      $this->check ( "ide",               "IDE",              "IDE",                   ERR_IDE               ) ;
      $this->check ( "medecin_urgences",  "Medecin",          "Médecin",               ERR_MEDECIN           ) ;
      $this->check ( "motif_transfert",   "MotifTransfert",   "Motif de transfert",    ERR_MOTIF_TRANSFERT, "type_destination", "T" ) ;
      $this->check ( "moyen_transport",   "MoyenTransport",   "Moyen de transport",    ERR_MOYEN_TRANSPORT, "type_destination", "T" ) ;
      $this->check ( "dest_pmsi",         "DestPMSI",         "Destination PMSI",      ERR_DEST_PMSI,       "type_destination" ) ;
      $this->check ( "orientation",       "Orientation",      "Orientation",           ERR_ORIENTATION,     "type_destination" ) ;
      $this->check ( "motif_recours",     "Recours",          "Recours",               ERR_RECOURS           ) ;
      $this->check ( "salle_examen",      "Salle",            "Salle d'examen",        ERR_SALLE_EXAMEN      ) ;
      $this->check ( "provenance",        "Provenance",       "Provenance",            ERR_PROVENANCE        ) ;
      $this->check ( "tiss",              "TISS",             "TISS",                  ERR_TISS              ) ;
      if ( $options -> getOption ( "GestionAdresseur" ) )
    	  $this->check ( "adresseur", "Adresseur", "Adresseur", ERR_ADRESSEUR ) ;
      if ( $options -> getOption ( "GestionModeAdmission" ) )
    	$this->check ( "mode_admission", "ModeAdmission", "Mode d'admission", ERR_MODE_ADMISSION ) ;
      if ( $options -> getOption ( "GestionCCMU" ) )
    	$this->check ( "ccmu", "CCMU", "Code CCMU", ERR_CCMU ) ;
      if ( $options -> getOption ( "GestionGEMSA" ) )
    	$this->check ( "gemsa", "GEMSA", "GEMSA", ERR_GEMSA ) ;
	  if ( $options -> getOption ( "GestionTraumato" ) )
    	$this->check ( "traumato", "Traumato", "Traumato", ERR_TRAUMATO ) ;
	  if ( $options -> getOption ( "CCAMExterne" ) ) {
	  	
	  }
	
      // Récupération des contraintes liées aux actes et aux diagnostics
      $contrainteActesDiag = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;
      $tabContraintesActesDiag = $contrainteActesDiag -> getContraintes ( ) ;
      if ( $this->patient->getTypeDestination() != 'H' AND $this->patient->getUF() != $options->getOption('numUFUHCD') AND 
        $this->patient->getUF() != $options->getOption('numUFSC' ) AND $this->patient->getUF() != $options->getOption('numUFUHCDrepere' ) ) {
	      if ( is_array ( $tabContraintesActesDiag[acte] ) ) {
	        $this -> bool = 0 ;
	        $this -> messages[$tabContraintesActesDiag[acte][nom]] = $tabContraintesActesDiag[acte][description] ;
	      }
      }
      if ( is_array ( $tabContraintesActesDiag[diag] ) ) {
        $this -> bool = 0 ;
        $this->messages[$tabContraintesActesDiag[diag][nom]]=$tabContraintesActesDiag[diag][description] ;
      }
      if ( is_array ( $tabContraintesActesDiag[consultation] ) ) {
        $this -> bool = 0 ;
        $this->messages[$tabContraintesActesDiag[consultation][nom]]=$tabContraintesActesDiag[consultation][description] ;
      }
        
    
      if ( $this->patient->getTypeDestination() != 'H' AND $this->patient->getUF() != $options->getOption('numUFUHCD') AND $this->patient->getUF() != $options->getOption('numUFSC' ) AND $this->patient->getUF() != $options->getOption('numUFUHCDrepere' ) ) {
    	if ( ! $this->checkCora ( ) ) {
		  $this->bool = 0 ;
		  $this->messages['CORA'] = 'Les actes doivent etre saisis dans CORA.' ;
    	}
      }
    
    
      //contraintes "plugin"
      $this->checkCustom();
    }
    return $this->bool ;
  }

  
  //verification des contraintes "cutom" activées dans modules/mouvements/avalaible
  function checkCustom() {
   	//regarde si des modules de sortie sont activés (scripts dans modules/mouvements/enabled/sorties_*.php , et passe le patient en argument )
  	if ( file_exists(URLLOCAL.'modules/mouvements')) {
		$dir = new DirectoryIterator(URLLOCAL.'modules/mouvements/enabled/');
		foreach($dir as $file ) {
			$matchTab = array();
			if($file->isFile() and preg_match('/^contraintes_(.*).php$/',$file->getFilename(),$matchTab) ) {
				require_once URLLOCAL.'modules/mouvements/enabled/'.$file->getFilename();
				$functionName = 'contraintes_'.$matchTab[1] ;
				eval('$tabRetour = '.$functionName.'($this->patient);');
				if($tabRetour['isContrainte']) {
					$this->bool = 0 ;
					$this->messages[$tabRetour['titreContrainte']] = $tabRetour['messageContrainte'] ;
				}
			}
		}
  	}
  }
  
  
  
  function checkCora ( ) {
	global $options ;  
    if ( $options -> getOption ( "SaisieActeObligatoire" ) AND $options -> getOption ( 'ActiverCORAModuleActes' ) ) {
	  $param['nsej'] = $this->patient->getNSej ( ) ;
	  $req = new clResultQuery ;
	  $res = $req -> Execute ( "Fichier", "CCAM_Cora", $param, "ResultQuery" ) ;
	  eko ( $res ) ;
	  if ( $res['INDIC_SVC'][2] ) return 1 ;
	  else return 0 ;
	} else return 1 ;
  }

  // Affichage des contraintes non respectées.
  function getContraintes ( ) {
    global $session ;
    global $options ;
    // Chargement du template.
    $mod = new ModeliXe ( "ErreursSortie.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Génération du lien pour fermer.
    $mod -> MxImage ( "imgCloseErreurs", URLIMGFER, "Fermer" ) ;
    $mod -> MxUrl  ( "lienCloseErreurs", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ; 
    // Variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
    $tab = $this->messages ;
    // Parcours et affichage des messages d'erreur.
    while ( list ( $key, $val ) = each ( $tab ) ) { 
      $mod -> MxText ( "erreur.nom", $key ) ;
      $mod -> MxText ( "erreur.description", $val ) ;
      $mod -> MxBloc ( "erreur", "loop" ) ;
    }
    if ( $session->getDroit ( "Presents_EtatCivil", "a" ) ) {
      $mod -> MxHidden ( "forcer.hiddenForcer", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "modDateSortie" ) ) ;
      // Pour générer des formulaires plus loin.
      $form = new clForm ( ) ;
      // Date d'exécution.
      $now = new clDate ( ) ;
      // Récupération des options.
      $retourmax = $options -> getOption ( "Dates Patient" ) ;
      $tranches  = $options -> getOption ( "DatesDécoup Patient" ) ;
      // Préparation de la première date de la liste.
      $now -> addHours ( -$retourmax ) ;
      $min = $now -> getTimestamp ( ) ;
      $now -> addHours ( $retourmax ) ;
      if ( $tranches >= 5 ) {
	$minutes = $now -> getMinutes ( ) ;
	$minutesless = ( $minutes % 5 ) ;
	$now -> addMinutes ( -$minutesless ) ;
      }
      $data[now] = 'Maintenant' ;
      $t = $now -> getTimestamp ( ) ;
      $data[$t] = $now -> getDate ( "d-m-Y H:i" ) ; 
      // On parcourt les dates en fonctions des options.
      for ( $i = 0 ; $now -> getTimestamp ( ) >= $min ; $i += $tranches ) {
	$t = $now -> getTimestamp ( ) ;
	$data[$t] = $now -> getDate ( "d-m-Y H:i" ) ; 
	$now -> addMinutes ( -$tranches ) ;
      }
      // On génère le select contenant la liste des dates possibles.
      $mod -> MxText ( "forcer.date", $form -> genSelect ( "modDateSortie", 1, $data ) ) ;
    } else {
      $mod -> MxBloc ( "forcer", "modify", " " ) ;
    }
    // Récupération du code HTML généré.
    $af .= $mod -> MxWrite ( "1" ) ;
    return $af ;
  }

  function checkSansContrainte ( $nom, $val ) {
  	if ( $nom AND $val ) {
  		if ( $this->patient->getInformation ( $nom ) == $val ) return 1 ;
  	}
  }

  // Vérification qu'un champs est bien renseigné.
  function check ( $nom, $option, $libelle, $message, $verif='', $lettre='' ) {
    global $options ;
    // eko ( $nom." ".$lettre." ".$this->patient->getInformation ( $verif ) ) ;
    // On vérifie que la contrainte est active sur ce champs.
    if ( $options -> getOption ( "Contrainte".$option ) ) {
      // Cas d'un champs normal.
      if ( ! $verif ) {
	// S'il n'est pas renseigné, le booléen devient faux et un message d'erreur est ajouté.
	if ( ! $this->patient->getInformation ( $nom ) ) {
	  $this->bool = 0 ;
	  $this->messages[$libelle] = $message ;
	}
      // Cas d'un champs dépendant du type de destination.
      } else {
		if ( $lettre ) {
			// S'il n'est pas renseigné, le booléen devient faux et un message d'erreur est ajouté.
			if ( ! $this->patient->getInformation ( $nom ) AND ( $this->patient->getInformation ( $verif ) == $lettre ) ) {
	  			$this->bool = 0 ;
	  			$this->messages[$libelle] = $message ;
			}
		} else {
			// S'il n'est pas renseigné, le booléen devient faux et un message d'erreur est ajouté.
			if ( ! $this->patient->getInformation ( $nom ) AND ( ( $this->patient->getInformation ( $verif ) == "T" ) OR ( $this->patient->getInformation ( $verif ) == "H" ) ) ) {
	  			$this->bool = 0 ;
	  			$this->messages[$libelle] = $message ;
			}
		}
      }
    }
  }

  // Renvoie l'affichage généré par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}

?>
