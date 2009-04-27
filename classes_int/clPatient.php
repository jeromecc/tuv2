<?php

// Titre  : Classe Patient
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 01 Mars 2005

// Description : 
// Cette classe gère les attributs d'un patient.
// Elle permet d'accéder à ces attributs facilement.

class clPatient {

  private $idpatient ;
  private $patient ;
  private $table ;
  private $etat ;
  private $af ;
  public $paramCCAM ;

  function __construct ( $idpatient, $etat='', $base=BDD ) {
    if ( $etat == "Sortis" ) $this->table = PSORTIS ;
    else $this->table = PPRESENTS ;
    $this->base = $base ;
    $this->idpatient = $idpatient ;
    $this->getPatient ( ) ;
  }

  function retourPatient ( ) {
    global $session ;
    global $errs ;
    $date = new clDate ( ) ;
    // Appel de la classe Requete.
    $data[idu]              = $this->patient[idu] ;
    $data[ilp]              = $this->patient[ilp] ;
    $data[nsej]             = $this->patient[nsej] ;
    $data[uf]               = $this->patient[uf] ;
    $data[nom]              = $this->patient[nom] ;
    $data[prenom]           = $this->patient[prenom] ;
    $data[sexe]             = $this->patient[sexe] ;
    $data[dt_naissance]     = $this->patient[dt_naissance] ;
    $data[adresse_libre]    = $this->patient[adresse_libre] ;
    $data[adresse_cp]       = $this->patient[adresse_cp] ;
    $data[adresse_ville]    = $this->patient[adresse_ville] ;
    $data[telephone]        = $this->patient[telephone] ;
    $data[prevenir]         = $this->patient[prevenir] ;
    $data[medecin_traitant] = $this->patient[medecin_traitant] ;
    $data[dt_admission]     = $date -> getDatetime ( ) ;
    $requete = new clRequete ( $this->base, PPRESENTS, $data ) ;
    // Exécution de la requete.
    $res = $requete->addRecord ( ) ;
    if ( ! $res[error] ) return 1 ;
    else $errs -> addErreur ( "clPatient : ".$res[error] ) ;
  }

  
  function sortirPatient ( $simple='' ) {
  	
  	//regarde si des modules de sortie sont activés (scripts dans modules/mouvements/enabled/sorties_*.php , et passe le patient en argument )
  	if ( file_exists(URLLOCAL.'modules/mouvements')) {
		$dir = new DirectoryIterator(URLLOCAL.'modules/mouvements/enabled/');
		foreach($dir as $file ) {
			$matchTab = array();
			if($file->isFile() and preg_match('/^sortie_(.*).php$/',$file->getFilename(),$matchTab) ) {
				require_once URLLOCAL.'modules/mouvements/enabled/'.$file->getFilename();
				$functionName = 'sortie_'.$matchTab[1] ;
				eval($functionName.'($this);');
			}
		}
  	}
  	
    if ( $simple ) {
    	$res = $this->insertPatient ( PSORTIS ) ;
    	if ( $res ) { $this->delPatient ( ) ; }
    } else { 
    	$res = $this->insertPatient ( PSORTIS ) ;
    	$this->logSortie ( ) ;
    	if ( $res ) { $this->delPatient ( ) ; }
    	clFoRmX_manip::rangerDossMedChrono($this);
    	clFoRmX_manip::rangerDossMedAEV($this);
    }
  }

  // Ajout d'une ligne dans la BAL du terminal.
  function logSortie ( ) {
  	global $session ;
  	$date = new clDate ( ) ;
  	$data['idpatient'] = $this->getID ( ) ;
	$data['idu'] = $this->getIDU ( ) ;
	$data['ilp'] = $this->getILP ( ) ;
	$data['nsej'] = $this->getNSej ( ) ;
	$data['uf'] = $this->getUF ( ) ;
	$data['nom'] = $this->getNom ( ) ;
	$data['prenom'] = $this->getPrenom ( ) ;
	$data['dest_attendue'] = $this->getDestinationAttendue ( ) ;
	$data['type'] = 'Sortie' ;
	// Récupération de la date de sortie.
	$data['date'] = $this->getDateSortie ( ) ;
	$data['action'] = 'Sortie du patient' ;
	$data['iduser'] = $session->getUid ( ) ;
	$requete = new clRequete ( $this->base, 'bal', $data ) ;
    $requete->addRecord ( ) ;
  }

	// Suppression du Log de sortie.
	function deLogSortie ( ) {
		$requete = new clRequete ( $this->base, 'bal' ) ;
    	$requete->delRecord ( 'idpatient='.$this->getID ( )." AND type='Sortie'" ) ;
	}

  function entrerPatient ( ) {
    $res = $this->insertPatient ( PPRESENTS ) ;
    $this->deLogSortie ( ) ;
    if ( $res ) { $this->delPatient ( ) ; }
  }

  function insertPatient ( $table ) {
    global $session ;
    global $errs ;
    // Appel de la classe Requete.
    $this->patient[idpatient] = $this->idpatient ;
    //$this->patient[iduser] = $session->getUser ;
    $requete = new clRequete ( $this->base, $table, $this->patient ) ;
    // Exécution de la requete.
    $res = $requete->addRecord ( ) ;
    //$errs -> addErreur ( "Débugage (c'est normal) : Insertion d'un patient dans la table $table<br>Requête : ".$res[requete]."<br>error : ".$res[error]."<br>errno : ".$res[errno]."<br>current_id : ".$res[cur_id] ) ;
    if ( ! $res[error] ) return 1 ;
    else $errs -> addErreur ( "clPatient : ".$res[error] ) ;
  }

  function delPatient ( ) {
    // Appel de la classe Requete.
    $requete = new clRequete ( $this->base, $this->table ) ;
    // Exécution de la requete.
    $requete->delRecord ( "idpatient='".$this->idpatient."'" ) ;
  }

  function getPatient ( ) {
    global $errs ;
    $param[cw] = "WHERE idpatient='".$this->idpatient."'" ;
    $param[table] = $this->base.'.'.$this->table ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    //print affTab ( $res[INDIC_SVC] ) ; 
    //newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ;
    if ( $res[INDIC_SVC][2] ) {
      while ( list ( $key, $val ) = each ( $res ) ) { 		
	if ( $key != "INDIC_SVC" AND $key != "idpatient" ) {
	  $this->patient[$key] = $res[$key][0] ;
	}
      }
    } else {
      //$errs->addErreur ( "clPatient : Le patient spécifié (idpatient=".$this->idpatient.") est introuvable dans la table \"".$this->table."\"." ) ;
      $this->idpatient = 0 ;
    }
    if ( DEBUGPATIENT ) print affTab ( $this->patient ) ;
  }

  function setPatient ( $data ) {
    $requete = new clRequete ( $this->base, $this->table, $data ) ;
    $requete->updRecord ( "idpatient='".$this->idpatient."'" ) ;
  }
  
  // fonction pour les ATU des patients sortis sans avis médical
  function setPatient2 ( $data ) {
    $requete = new clRequete ( $this->base, "patients_sortis", $data ) ;
    $requete->updRecord ( "idpatient='".$this->idpatient."'" ) ;
  }
  
  function getTablePatient ( ) {
    return $this->table;
  }

  function resetAffichage ( ) {
    $this->af = '' ;
  }

  function getAffichage ( ) {
    return $this->af ;
  }

  function getInformation ( $lib ) {
    return $this->patient[$lib] ;
  }

  function debugInfos ( ) {
      eko ( $this->patient ) ;
  }

/**
 * export minipal à fournir lors de l'export des formulaires
 * @return array
 */
  function getMiniExport()
  {
      $ret = array();
      $ret['sexe'] = $this->getGenre();
      $ret['date_naissance'] = $this->getDateNaissance();
      $ret['provenance'] = $this->getProvenance();
      $ret['adresseur'] = $this->getAdresseur();
      $ret['mode_arrivee'] = $this->getModeAdmission();
      $ret['date_admission'] = $this->getDateAdmission();
      $ret['DateExamen'] = $this->getDateExamen();
      $ret['CodeDiagnostic'] = $this->getCodeDiagnostic();
      $ret['TypeDestAttendue'] = $this->getTypeDestination();
      $ret['mode_admission'] = $this->getTypeAdmission();
      $ret['DateSortie'] = $this->getDateSortie();
      return $ret ;
  }

  function setAttribut ( $nom, $valeur ) {
    switch ( $nom ) {
    case 'ModeAdmission':        $data[mode_admission] = $valeur ;       break ;
    case 'Adresseur':            $data[adresseur] = $valeur ;            break ;
    case 'Prevenir':             $data[prevenir] = $valeur ;             break ;
    case 'MedecinTraitant':      $data[medecin_traitant]     = $valeur ; break ;
    case 'DateSortie':           $data[dt_sortie]            = $valeur ; break ;
    case 'DateExamen':           $data[dt_examen]            = $valeur ; break ;
    case 'CategorieDiagnostic':  $data[diagnostic_categorie] = $valeur ; break ;
    case 'Diagnostic':           $data[diagnostic_libelle]   = $valeur ; break ;
    case 'CodeDiagnostic':       $data[diagnostic_code] = $valeur ;      break ;
    case 'Medecin':              $data[medecin_urgences] = $valeur ;     break ;
    case 'Ide':                  $data[ide] = $valeur ;                  break ;
    case 'CategorieRecours':     $data[recours_categorie] = $valeur ;    break ;
    case 'Recours':              $data[motif_recours] = $valeur ;        break ;
    case 'CodeRecours':          $data[recours_code] = $valeur ;         break ;
    case 'SalleExamen':          $data[salle_examen] = $valeur ;         break ;
    case 'CodeGravite':          $data[code_gravite] = $valeur ;         break ;
    case 'DestinationSouhaitee': $data[dest_souhaitee] = $valeur ;       break ;
    case 'DestinationAttendue':  $data[dest_attendue] = $valeur ;        break ;
    case 'MoyenTransport':       $data[moyen_transport] = $valeur ;      break ;
    case 'MotifTransfert':       $data[motif_transfert] = $valeur ;      break ;
    case 'TypeDestAttendue':     $data[type_destination] = $valeur ;     break ;
    case 'GEMSA':                $data[gemsa] = $valeur ;                break ;
    case 'CCMU':                 $data[ccmu] = $valeur ;                 break ;
    case 'Traumato':			 $data[traumato] = $valeur ;			 break ;
    case 'DateUHCD':			 $data[dt_UHCD] = $valeur ;				 break ;
    case 'EtatUHCD':			 $data[etatUHCD] = $valeur ;			 break ;
    case 'UF':					 $data[uf] = $valeur ;					 break ;
    case 'Provenance':			 $data[provenance] = $valeur ;			 break ;
    case 'DestPMSI':			 $data[dest_pmsi] = $valeur ;			 break ;
    case 'Orientation':			 $data[orientation] = $valeur ;			 break ;
    case 'IPP':					 $data[ilp] = $valeur ;					 break ;
    case 'NSej':				 $data[nsej] = $valeur ;				 break ;
    case 'Sexe':				 $data[sexe] = $valeur ;				 break ;
    case 'Nom':				 	 $data[nom] = $valeur ;					 break ;
    case 'Prenom':				 $data[prenom] = $valeur ;				 break ;
    case 'Naissance':			 $data[dt_naissance] = $valeur ;		 break ;
    case 'Adresse':				 $data[adresse_libre] = $valeur ;		 break ;
    case 'CP':				 	 $data[adresse_cp] = $valeur ;			 break ;
    case 'Ville':				 $data[adresse_ville] = $valeur ;		 break ;
    case 'Tél':				 	 $data[telephone] = $valeur ;			 break ;
    case 'TISS':				 $data[tiss] = $valeur ;				 break ;
    case 'Valide':               $data[valide] = $valeur ;				 break ;
    }
    $this->setPatient ( $data ) ;
  }
  
  function isUHCD ( ) {
    $salle = $this->getSalle ( ) ;
    if ( ereg ( "UHCD", $salle ) ) return 1 ;
  }
  
  function ifInUHCD ( ) {
    
    global $options;
    
    $uf     = $this->getUF ( ) ;
    $ufUHCD = $options->getOption ( 'numUFUHCD' ) ;
    $ufUHCDrepere = $options->getOption ( 'numUFUHCDrepere' ) ;
    if ( ereg ( $ufUHCD, $uf ) OR ($ufUHCDrepere AND ereg ( $ufUHCDrepere, $uf )) ) 
      return 1 ;
    else
      return 0;
  }

  function getID                   ( ) { return $this->idpatient                                 ; }
  function getIDU                  ( ) { return $this->getInformation ( "idu"                  ) ; }
  function getILP                  ( ) { return $this->getInformation ( "ilp"                  ) ; }
  function getNSej                 ( ) { return $this->getInformation ( "nsej"                 ) ; }
  function getGEMSA                ( ) { return $this->getInformation ( "gemsa"                ) ; }
  function getCCMU                 ( ) { return $this->getInformation ( "ccmu"                 ) ; }
  function getTraumato			   ( ) { return $this->getInformation ( "traumato"             ) ; }
  function getUF                   ( ) { return $this->getInformation ( "uf"                   ) ; }
  function getNom                  ( ) { return $this->getInformation ( "nom"                  ) ; }
  function getPrenom               ( ) { return $this->getInformation ( "prenom"               ) ; }
  function getGenre                ( ) { return $this->getInformation ( "sexe"                 ) ; }
  function getSexe                 ( ) { return $this->getInformation ( "sexe"                 ) ; }
  function getDateNaissance        ( ) { return $this->getInformation ( "dt_naissance"         ) ; } //datetime
  function getAdresse              ( ) { return $this->getInformation ( "adresse_libre"        ) ; }
  function getCodePostal           ( ) { return $this->getInformation ( "adresse_cp"           ) ; }
  function getVille                ( ) { return $this->getInformation ( "adresse_ville"        ) ; }
  function getTel                  ( ) { return $this->getInformation ( "telephone"            ) ; }
  function getPrevenir             ( ) { return $this->getInformation ( "prevenir"             ) ; }
  function getMedecinTraitant      ( ) { return $this->getInformation ( "medecin_traitant"     ) ; }
  function getDateAdmission        ( ) { return $this->getInformation ( "dt_admission"         ) ; }
  function getAdresseur            ( ) { return $this->getInformation ( "adresseur"            ) ; }
  function getModeAdmission        ( ) { return $this->getInformation ( "mode_admission"       ) ; }
  function getDateExamen           ( ) { return $this->getInformation ( "dt_examen"            ) ; }
  function getMedecin              ( ) { return $this->getInformation ( "medecin_urgences"     ) ; }
  function getIDE                  ( ) { return $this->getInformation ( "ide"                  ) ; }
  function getSalle                ( ) { return $this->getInformation ( "salle_examen"         ) ; }
  function getSalleExamen          ( ) { return $this->getInformation ( "salle_examen"         ) ; }
  function getMotifRecours         ( ) { return $this->getInformation ( "motif_recours"        ) ; }
  function getRecours              ( ) { return $this->getInformation ( "motif_recours"        ) ; }
  function getRecoursUTF8          ( ) { return utf8_encode($this->getInformation ( "motif_recours" )) ; }
  function getCodeGravite          ( ) { return $this->getInformation ( "code_gravite"         ) ; }
  function getCodeFacturation      ( ) { return $this->getInformation ( "code_facturation"     ) ; }
  function getDestinationSouhaitee ( ) { return $this->getInformation ( "dest_souhaitee"       ) ; }
  function getDestinationAttendue  ( ) { return $this->getInformation ( "dest_attendue"        ) ; }
  function getMoyenTransport       ( ) { return $this->getInformation ( "moyen_transport"      ) ; }
  function getMotifTransfert       ( ) { return $this->getInformation ( "motif_transfert"      ) ; }
  function getDateSortie           ( ) { return $this->getInformation ( "dt_sortie"            ) ; }
  function getCodeRecours          ( ) { return $this->getInformation ( "recours_code"         ) ; }
  function getCategorieRecours     ( ) { return $this->getInformation ( "recours_categorie"    ) ; }
  function getTypeDestination      ( ) { return $this->getInformation ( "type_destination"     ) ; }
  function getCategorieDiagnostic  ( ) { return $this->getInformation ( "diagnostic_categorie" ) ; }
  function getLibelleDiagnostic    ( ) { return $this->getInformation ( "diagnostic_libelle"   ) ; }
  function getCodeDiagnostic       ( ) { return $this->getInformation ( "diagnostic_code"      ) ; }
  function getIdUser               ( ) { return $this->getInformation ( "iduser"               ) ; }
  function getManuel               ( ) { return $this->getInformation ( "manuel"               ) ; }
  function getDateUHCD			   ( ) { return $this->getInformation ( "dt_UHCD"              ) ; }
  function getEtatUHCD             ( ) { return $this->getInformation ( "etatUHCD"             ) ; }
  function getProvenance		   ( ) { return $this->getInformation ( "provenance"           ) ; }
  function getDestPMSI			   ( ) { return $this->getInformation ( "dest_pmsi"            ) ; }
  function getOrientation		   ( ) { return $this->getInformation ( "orientation"          ) ; }
  function getTISS				   ( ) { return $this->getInformation ( "tiss"                 ) ; }
  function getValide 			   ( ) { return $this->getInformation ( "valide"               ) ; }
  
  function isSoinsContinus ( ) {
  	global $options;
    $uf     = $this->getUF ( ) ;
    $ufUHCD = $options->getOption ( 'numUFSC' ) ;
    if ( $ufUHCD && ereg ( $ufUHCD, $uf ) ) return 1 ;
    else return 0 ;
  }

    function getHashIDU( )
    {
        return $this->getHashIDU($this->getIDU()) ;
    }

  //Regarde si le patient a un formulaire pour le passage
  function hasFormxPassage($idFormx,$options='')
  {
	  if( ! $options ) $options = array() ;
      $tab = formxTools::exportsGetTabIdsIdformFilterValue($this->getIDU(), $idFormx, 'id_passage', $this->getNSej());
	  return (count($tab)?true:false) ;
  }


//Regarde si le patient a un formulaire pour tous passage
  function hasFormx($idFormx,$options='')
  {
	  if( ! $options ) $options = array() ;
      $tab = formxTools::exportsGetTabIdsIdform($this->getIDU(), $idFormx);
	  return (count($tab)?true:false) ;
  }

  //renvoie un tableau avec les codes des actes (les NGAP commencent par NGAP )
  function getTabActes()
  {
	$idPassage = $this->getNSej();
	$requete = " SELECT `codeActe`  FROM `ccam_cotation_actes`  WHERE `numSejour` = 	'$idPassage'  " ;
	$obRequete = new clRequete(CCAM_BDD, 'ccam_cotation_actes') ;
	$res = $obRequete->exec_requete($requete, 'tab');
	$ret = array() ;
	foreach( $res as $ligne )
	{
		$ret[] = $ligne['codeActe'] ;
	}
	 return $ret;
  }
  
  function getTypeAdmission ( ) { 
    $param[nomitem] = addslashes(stripslashes($this->getDestinationAttendue ( ))) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getTypeDestinationAttendue", $param, "ResultQuery" ) ; 
    return $res[localisation][0] ;
  }
  function getMatriculeMedecin ( ) { 
    
    global $options;
    
    $param[nomitem] = addslashes(stripslashes($this->getMedecin ( ))) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getMatriculeMedecin", $param, "ResultQuery" ) ;
    
    return $res[matricule][0];
    
    /*if ( $res[matricule][0] == "" )
      return $options -> getOption ('codeAdeliChefService');
    else
      return $res[matricule][0];*/
  }


  
  function getTypeMedecin ( ) { 
    $param[nomitem] = addslashes(stripslashes($this->getMedecin ( ))) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getTypeMedecin", $param, "ResultQuery" ) ;
    //eko ( $res ) ;
    switch ( $res[localisation][0] ) {
    	case 'U': return 'URG'; break ;
    	case 'P': return 'PED'; break ;
    	case 'F': return 'SAF'; break ;
    	case 'G': return 'OBS'; break ;
    	default : return 'URG'; break ;
    } 
  }
  
  function getNewDestPMSI ( ) {
  	$param['nomliste'] = 'Destinations attendues' ;
  	$param['nomitem'] = addslashes(stripslashes($this->getDestinationAttendue())) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getChampLibre", $param, "ResultQuery" ) ;
    return $res[libre][0][0] ; 
  }
  function getNewOrientation ( ) {
  	$param['nomliste'] = 'Destinations attendues' ;
  	$param['nomitem'] = addslashes(stripslashes($this->getDestinationAttendue())) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getChampLibre", $param, "ResultQuery" ) ;
    return substr ( $res['libre'][0], 1, strlen ( $res['libre'][0] ) - 1 ) ;
  }
  function getMatriculeIDE     ( ) { 
    $param[nomitem] = addslashes(stripslashes($this->getIDE ( ))) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getMatriculeIDE", $param, "ResultQuery" ) ;
    return $res[matricule][0] ; 
  }
  function getCodeRecoursFirst ( ) {
    $param[nomliste] = addslashes(stripslashes($this->getCategorieRecours ( ))) ;
    $param[nomitem] = addslashes(stripslashes($this->getRecours ( ))) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getCodeRecours", $param, "ResultQuery" ) ;
    //newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ;
    return $res[code][0] ; 
  }
  
  
  // Cette fonction génère une info-bulle sur un patient.
  static function genInfoBulle ( $res, $i ) {
  	global $options ;
  	$e = '' ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "InfoBulle.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Génération de l'id pour rattacher l'info-bulle à une entrée de la liste.
    $mod -> MxText ( "iddiv", "id".$res['idpatient'][$i] ) ;
    $libelle = '' ;
  	$libelle .= "<b>idpatient : </b>".$res['idpatient'][$i]."<br/>" ;
  	if ( $res['idu'][$i] != $res['ilp'][$i] ) $libelle .= "<b>IDU : </b>".$res['idu'][$i]."<br/>" ;
  	$libelle .= "<b>IPP : </b>".$res['ilp'][$i]."<br/>" ;
  	$libelle .= "<b>NSej : </b>".$res['nsej'][$i]."<br/>" ;
	
  	// Affichage des informations sur l'UF
  	$ufExec = $options->getOption ( 'numUFexec' ) ;
  	$ufUHCD = $options->getOption ( 'numUFUHCD' ) ;
  	$ufUHCDrepere = $options->getOption ( 'numUFUHCDrepere' ) ;
  	$ufSC   = $options->getOption ( 'numUFSC' ) ;
  	$dtUHCD = new clDate ( $res['dt_UHCD'][$i] ) ;
  	if ( $res['uf'][$i] == $ufUHCD ) $sup = '(UHCD)' ;
  	elseif ( $res['uf'][$i]  == $ufSC ) $sup = '(Soins Continus)' ;
  	elseif ( $res['uf'][$i] == $ufUHCDrepere AND $res['uf'][$i] ) $sup = '(UHCD repéré)' ;
  	elseif ( !$res['uf'][$i] ) $sup = '(???)' ;
  	else $sup = '(Urgences)' ;
  		
  	$libelle .= "<b>UF : </b>".$res['uf'][$i]." $sup<br/>" ;
    // Etat civil
    $libelle .= "<b>(".$res['sexe'][$i].") ".ucfirst(strtolower($res['prenom'][$i]))." ".strtoupper($res['nom'][$i])."</b><br />" ;
    $age = new clDate ( $res['dt_naissance'][$i] ) ;
    $dta = new clDate ( $res['dt_admission'][$i] ) ;
    $dte = new clDate ( $res['dt_examen'][$i] ) ;
    $dts = new clDate ( $res['dt_sortie'][$i] ) ;
    $dateSimple = $age -> getDate ( "d-m-Y" ) ;
    $dateComple = $age -> getDateText ( ) ;
    $duree = new clDuree ( ) ;
    if ( $res['dt_naissance'][$i] != "0000-00-00 00:00:00" ) $libelle .= "Né$e le $dateComple (".str_replace("<br>"," et ",$duree->getAgePrecis ( $age -> getTimestamp ( ) ) ).")<br />" ;
    else $libelle .= "Date de naissance inconnue<br />" ;
    // Date d'admission.
    if ( $res['dt_admission'][$i] != "0000-00-00 00:00:00" ) $libelle .= "<b>Date d'admission :</b> ".$dta -> getDate ( "d-m-Y H:i" )."<br />" ;
    else $libelle .= "<b>Date d'admission :</b> ".VIDEDEFAUT."<br />" ;
    // Date d'examen.
    if ( $res['dt_examen'][$i] != "0000-00-00 00:00:00" ) $libelle .= "<b>Date d'examen :</b> ".$dte -> getDate ( "d-m-Y H:i" )."<br />" ;
    else $libelle .= "<b>Date d'examen :</b> ".VIDEDEFAUT."<br />" ;
    // Date de sortie.
    if ( $res['dt_sortie'][$i] != "0000-00-00 00:00:00" ) $libelle .= "<b>Date de sortie :</b> ".$dts -> getDate ( "d-m-Y H:i" )."<br />" ;
    else $libelle .= "<b>Date de sortie :</b> ".VIDEDEFAUT."<br />" ;
    // Adresseur.
    if ( $res['adresseur'][$i] ) $libelle .= "<b>Adresseur :</b> ".$res['adresseur'][$i]."<br />" ;
    else $libelle .= "<b>Adresseur :</b> ".VIDEDEFAUT."<br />" ;
    // Mode d'admission.
    if ( $res['mode_admission'][$i] ) $libelle .= "<b>Mode d'admission :</b> ".$res['mode_admission'][$i]."<br />" ;
    else $libelle .= "<b>Mode d'admission :</b> ".VIDEDEFAUT."<br />" ;
    // Médecin.
    if ( $res['medecin_urgences'][$i] ) $libelle .= "<b>Médecin :</b> ".$res['medecin_urgences'][$i]."<br />" ;
    else $libelle .= "<b>Médecin :</b> ".VIDEDEFAUT."<br />" ;
    // IDE.
    if ( $res['ide'][$i] ) $libelle .= "<b>IDE :</b> ".$res['ide'][$i]."<br />" ;
    else $libelle .= "<b>IDE :</b> ".VIDEDEFAUT."<br />" ;
    // Salle d'examen.
    if ( $res['salle_examen'][$i] ) $libelle .= "<b>Salle d'examen :</b> ".$res['salle_examen'][$i]."<br />" ;
    else $libelle .= "<b>Salle d'examen :</b> ".VIDEDEFAUT."<br />" ;
    // Motif de recours.
    if ( $res['motif_recours'][$i] ) $libelle .= "<b>Motif de recours :</b> ".$res['motif_recours'][$i]."<br />" ;
    else $libelle .= "<b>Motif de recours :</b> ".VIDEDEFAUT."<br />" ;
    // Destination souhaitée.
    if ( $res['dest_souhaitee'][$i] ) $libelle .= "<b>Dest. souhaitée :</b> ".$res['dest_souhaitee'][$i]."<br />" ;
    else $libelle .= "<b>Dest. souhaitée :</b> ".VIDEDEFAUT."<br />" ;
    // Destination attendue.
    if ( $res['dest_attendue'][$i] ) $libelle .= "<b>Dest. attendue :</b> ".$res['dest_attendue'][$i]."<br />" ;
    else $libelle .= "<b>Dest. attendue :</b> ".VIDEDEFAUT."<br />" ;
    // Catégorie de diagnostic.
    if ( $res['diagnostic_categorie'][$i] ) $libelle .= "<b>Cat. de diag. :</b> ".$res['diagnostic_categorie'][$i]."<br />" ;
    else $libelle .= "<b>Cat. de diag. :</b> ".VIDEDEFAUT."<br />" ;
    // Diagnostic.
    if ( $res['diagnostic_libelle'][$i] ) $libelle .= "<b>Diagnostic :</b> ".$res['diagnostic_libelle'][$i]."<br />" ;
    else $libelle .= "<b>Diagnostic :</b> ".VIDEDEFAUT."<br />" ;
   // CCMU.
    if ( $res['ccmu'][$i] ) $libelle .= "<b>CCMU :</b> ".$res['ccmu'][$i]."<br />" ;
   // GEMSA.
    if ( $res['gemsa'][$i] ) $libelle .= "<b>GEMSA :</b> ".$res['gemsa'][$i]."<br />" ;
    // Ajout des informations dans l'info-bulle.
    $text = preg_replace("/(\r\n|\n|\r)/", " ", nl2br($libelle));
    $mod -> MxText ( "libelle", str_replace("'","\'",$text) ) ;
    // Récupération du code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
  }
/**
 *
 * @param <int> $idu
 * @return clPatient
 */
  static function getObjPatientFromIdPassage($idpassage)
  {
      $req =" SELECT idpatient from patients_presents WHERE nsej = '$idpassage' " ;
      $obReq = new clRequete( BDD, 'patients_presents');
      $resTab = $obReq->exec_requete($req,'tab');
	  $idPatient = 1 ;
	  $etat = 'Presents' ;
      if(count($resTab)>0)
	  {
		$etat = 'Presents' ;
        $idPatient = $resTab[0]['idpatient'] ;
	  }
      else
      {
		  $etat = 'Sortis' ;
          $req =" SELECT idpatient from patients_sortis WHERE nsej = '$idpassage' " ;
          $resTab = $obReq->exec_requete($req,'tab');
          if(count($resTab) == 0)
            return null ;
          $idPatient = $resTab[0]['idpatient'] ;
      }
      return new clPatient($idPatient,$etat);
  }
  
}

?>
