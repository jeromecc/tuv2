<?php

// Titre  : Classe Patient
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 15 Juillet 2005

// Description : 
// Cette classe gère les attributs d'un patient.
// Elle permet d'accéder à ces attributs facilement.

class clPatient {

  private $idu ;
  private $patient ;
  private $af ;
  private $ilps ;
  private $secu ;
  private $mutuelle;

  // Constructeur de la classe patient.
  function __construct ( $IDU_Patient='' ) {
    global $session ;
    global $errs;
    global $xham;
    if(defined("VERSIONXHAM") && VERSIONXHAM == "2" ) {
    	 $session =  $xham  ; 
    	 $errs = $xham  ; 
    }
    //$errs->whereAmI();
    // Si on est en mode sélection de patient, on initialise la variable de session IDUPatient.
    if ( $session->getNavi ( 2 ) == "setPatient" ) {
      $_SESSION['IDUPatient'] = $session -> getNavi ( 3 ) ;
      // Sinon, on vérifie si un IDU n'a pas été transmis au constructeur et on initialise 
      // la variable de session avec.
    } else if ( $IDU_Patient ) {
      $_SESSION['IDUPatient'] = $IDU_Patient ;
    }

    // Si un IDU est sélectionné, on récupère les informations du patient.
    if ( isset ( $_SESSION['IDUPatient'] ) AND $_SESSION['IDUPatient'] ) {
      $param['cw'] = "WHERE p.IDU=r.IDU AND r.IDU='".$_SESSION['IDUPatient']."'"  ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getPatient", $param, "ResultQuery" ) ;  
      //      eko ( $res['INDIC_SVC'] ) ;
      if ( $res['INDIC_SVC'][2] ) {
	$this->patient = $res ;
	$this->setILPTable ( ) ;
	/*19 janv. 2006, modified by Emmanuel Cervetti ecervetti@ch-hyeres.fr */
	$this->getCouverture();
	$this->getMutuelle();
	/*endmodif*/
      } else unset ( $_SESSION['IDUPatient'] ) ;
    }
  }

  // Renvoie vrai si un patient est sélectionné.
  function isPatient ( ) {
    return $this->patient['INDIC_SVC'][2] ;
  }

  // Changement de patient.
  function setPatient ( $idu ) {
    $this->idu = $idu ;
    //rechargement données secu
    $this->getCouverture();
    $this->getMutuelle();
  }

  // Récupération d'une information basique d'un patient.
  function getInformation ( $lib ) {
    return $this->patient[$lib][0] ;
  }


  // Cette fonction retourne les informations concernant la
  // dernière hospitalisation du patient.
  function getLastHospi ( ) {
    $param['idu'] = $this->getIDU ( ) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "Hospi_getLast", $param, "ResultQuery" ) ;
    //    eko ( $res['INDIC_SVC'] ) ;
    return $res ;
  }

  // Récupération des données "en accès direct" du patient.
  function getIDU      ( ) { return $this->getInformation ( "IDU"    ) ; }
  function getNom      ( ) { return $this->getInformation ( "NOMU"   ) ; }
  function getNomFille ( ) { return $this->getInformation ( "NOMPAT" ) ; }
  function getPrenom   ( ) { return $this->getInformation ( "PREN"   ) ; }
  function getSexe     ( ) { return $this->getInformation ( "SEXE"   ) ; }
  function getDate     ( ) { return $this->getInformation ( "DTNAI"  ) ; }
  function getSitFam   ( ) { return $this->getInformation ( "SIFAM"  ) ; }
  function getAdresse  ( ) { return $this->getInformation ( "ADR1"   ) ; }
  function getAdresse2 ( ) { return $this->getInformation ( "ADR2"   ) ; }
  function getCP       ( ) { return $this->getInformation ( "CDPOST" ) ; }
  function getVille    ( ) { return $this->getInformation ( "LIEU"   ) ; }
  function getTelFixe  ( ) { return $this->getInformation ( "TEL"    ) ; }
  function getTelPort  ( ) { return $this->getInformation ( "TELP"   ) ; }
  function getNumSecu  ( ) { return $this->getInformation ( "NOSS"   ) ; }
  function getTel() {
  	return "Fixe:".$this->getTelFixe().".Portable:".$this->getTelPort();
  } 
  
  // Récupération  de la situation familiale en clair.
  function getSitFamT  ( ) { 
    $param['sitfa'] = $this->getSitFam() ; 
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getSituationFamiliale", $param, "ResultQuery" ) ;
    if ( $res['INDIC_SVC'][2] )
      return $res['LIBELLE'][0] ;
    else return '' ;
  }

  // Récupération des différents ILP attachés au patient dans un tableau.
  function setILPTable ( ) {
    $param['cw'] = "WHERE IDU='".$this->getIDU ( )."'"  ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getILP", $param, "ResultQuery" ) ;
    //   eko ( $res['INDIC_SVC'] ) ;
    for ( $i = 0 ; isset ( $res['ILP'][$i] ) ; $i++ ) {
	    //eko ( $res['ILP'] ) ; eko ( $res['IDC'] 
      $this->ilpALL[$i] = $res['ILP'][$i] ; 
      $this->ilps[$res['IDC'][$i]] = $res['ILP'][$i] ;
    }
    // eko ( $this->ilps ) ;
  }

  function getILPAll ( ) {
    return $this->ilpALL ;
  }

  // Récupération de l'ILP du patient.
  function getILP ( $idc='automatique' ) {
    return $this->getILPTable ( $idc ) ;
  }
  
/*19 janv. 2006, modified by Emmanuel Cervetti ecervetti@ch-hyeres.fr */
//Recuperation de l'ILP du patient par default pour le contexte 1 (hopital)
//sinon pour le contexte i
function getHopiILP($i='1'){return $this->ilps[$i];}

  // Récupération de tous les ILP du patient.
  function getILPTable ( $indice='' ) {
    if ( $indice ) {
      if ( $indice == "automatique" ) {
	//reset ( $this->ilps ) ;
	//list ( $key, $val ) = each ( $this->ilps ) ;
	//return $val ;
	return $this->ilps[1];
      }
      // return $this->ilps[$indice] ;
    }
    else return $this->ilps ;
  }



  // Récupération des couvertures du patient.
  //TODO FIXME (manu) : pas de distingo entre secu et mutuelles ? le premier resultat
  //peut être une mutuelle non ?
  function getCouverture ( ) {
    $param['ilp'] = $this->getILP ( )  ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getCouverture", $param, "ResultQuery" ) ;
    
    /*19 janv. 2006, modified by Emmanuel Cervetti ecervetti@ch-hyeres.fr */
    //stockage pour acces futur sans requete
    $this->secu =$res;
       	
    return $res ;
  }
  
  /*19 janv. 2006, modified by Emmanuel Cervetti ecervetti@ch-hyeres.fr */
  //recuperation sous forme resulquery des mutuelles , des plus recentes
  //aux moins récentes (seule la premiere a un interet)
  function getMutuelle() {
  	$param['ilp'] = $this->getILP ( )  ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getMutuelle", $param, "ResultQuery" ) ;
    //stockage pour acces futur sans requete
    $this->mutuelle =$res;   	
    return $res ;
  }
  
  // Récupération de l'autorisation d'anesthésier.
  function getAutoAn ( ) {
    if ( ! isset ( $this->autoAn ) ) {
      $param['ilp'] = $this->getILP ( )  ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getAutorisations", $param, "ResultQuery" ) ;
      $this->autoAn = (isset($res['AUTO_ANESTH'][0])?$res['AUTO_ANESTH'][0]:'Non renseigné') ; 
      $this->autoOp = (isset($res['AUTO_OPER'][0])?$res['AUTO_OPER'][0]:'Non renseigné') ;
      $this->medT   = $res['MED'][0] ;
    }
      return $this->autoAn ;
  }

  // Récupération de l'autorisation d'opérer.
  function getAutoOp ( ) {
    if ( ! isset ( $this->autoOp ) ) {
      $param['ilp'] = $this->getILP ( )  ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getAutorisations", $param, "ResultQuery" ) ;
      $this->autoAn = $res['AUTO_ANESTH'][0] ;
      $this->autoOp = $res['AUTO_OPER'][0] ;
      $this->medT   = $res['MED'][0] ;
    }
    return $this->autoOp ;
  }
  
  // Récupération du médecin traitant.
  function getMedT ( ) {
    if ( ! isset ( $this->medT ) ) {
      $param['ilp'] = $this->getILP ( )  ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getAutorisations", $param, "ResultQuery" ) ;
      $this->autoAn = $res['AUTO_ANESTH'][0] ;
      $this->autoOp = $res['AUTO_OPER'][0] ;
      $this->medT   = $res['MED'][0] ;
    }
    return $this->autoOp ;
  }

  // Récupération des personnes à prévenir.
  function getAPrev ( ) {
    if ( ! isset ( $this->APrev ) ) {
      $param['ilp'] = $this->getILP ( )  ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getPersonnesAPrevenir", $param, "ResultQuery" ) ;
      // eko ( $res['INDIC_SVC'] ) ;
      for ( $i = 0 ; isset ( $res['PP'][$i] ) ; $i++ ) {
	if ( isset ( $this->APrev ) ) $this->APrev .= "<br>".$res['PP'][$i] ;
	$this->APrev = $res['PP'][$i] ;
      }
    }
    return (isset($this->APrev)?$this->APrev:'Non renseigné') ;
  }

  /*renvoie un tableau resultquery avec les tous les docs saisis
  URLDOC
  DTDOC (jj/mm/aaaa)
  CREATEUR DOC
  TITRE DOC
  NUMDOC
  SEJDOC  MEDDOC*/
  function getDocsHopi($passage='') {
  	if ($passage)
  		$param['cw'] = "AND SEJDOC = '$passage' " ;
  	else
  		$param['cw'] = "" ;
  	$ilps = $this->getILPAll();
  	$param['IPP'] = implode ("','",$ilps);
  	$req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getDocsHopi", $param, "ResultQuery" ) ;
    return $res ;
    }
  
  /*renvoie un tableau resultquery avec les hospitalisations
   	NUHOHO	NOM	PRENOM	NUUFSE	PERIODE	TRI	MENTSE	MSORSE	NUHOSE	NSSESE	LBRMUF1	
	LBRMUF2	DTENT	DTSORT	NVL(UFHEB,NUUFSE)	NUPISE	NULISE	 */
  function getHospis() {
  	$param['IPP'] = $this->getILP() ;
  	$req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getHospis", $param, "ResultQuery" ) ;
    //eko ($res["INDIC_SVC"]);
    return $res ;
    }
    
  function getHospis_avant2002() {
  	$param['IPP'] = $this->getILP() ;
  	$req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getHospis_avant2002", $param, "ResultQuery" ) ;
    //eko ($res["INDIC_SVC"]);
    return $res ;
    }
  /*renvoie un tableau avec les consulatations externes
   PERIODE	TRI	NUHOHO LBRMUF DTENT	MOENHO	NOUF*/
  function getConsults() {
  	$param['IPP'] = $this->getILP() ;
  	$req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getConsults", $param, "ResultQuery" ) ;
    return $res ;
  }
  

  function genHidden ( ) {
    $diag = new clDialogueJS ( ) ;
    $tab['idu']    = $this->getInformation ( "IDU" ) ;
    $tab['ilp']    = $this->getInformation ( "ILP" ) ;
    $tab['nomu']   = $this->getInformation ( "NOMU" ) ;
    $tab['nompat'] = $this->getInformation ( "NOMPAT" ) ;
    $tab['pren']   = $this->getInformation ( "PREN" ) ;
    $tab['sexe']   = $this->getInformation ( "SEXE" ) ;
    $tab['dtnai']  = $this->getInformation ( "DTNAI" ) ;
    $diag -> addVars ( $tab ) ;
    return $diag -> getAffichage ( ) ;
  }
  

  
  
  
  
  
  
  /*19 janv. 2006, modified by Emmanuel Cervetti ecervetti@ch-hyeres.fr */
  //Recuperation d'une donnée patient avec alias de libellé eventuel
  //va chercher également données secu, données mutelles
  /*
   * --ITEMS GENERAUX---
   *nom,prenom,nomjf, IDU,SEXE DTNAI SIFAM ADR1 ADR2 CDPOST LIEU TEL ilp ipp IPP
   * TELP NOSS situation_famille
   * --renseignements supplementaires
   * medecinT (medecin traitant)
   * derhopi (date de derniere hospitalisation)
   * prevenir (personne à prévenir)
   * ---ITEMS SECU---
   * !!! doivent être prefixés par secu_ ou mutuelle_
   * ASS_NOM,ASS_NOMJF,ASS_PRN,ASS_ADR,ASS_CDPOS,ASS_VILLE,ASS_TEL,TYPASS,TYPDEB,
   * DEBNOM,DEBADR,DEBCDPOST,DEBVILLE,RANGDEB,PARTIC,DTDDROIT,DTFDROIT,DTMAJ,
   * TAUX,CDRISQ ,adresse_totale,nom_assure_complet
   * 
   * */
  function get($lib) {
  	$reg = array();
  	//renommage éventuel libelles
  	$renommage=array(	'nom'=>'NOMU'
  						,'prenom'=>'PREN'
  						,'nomjf'=>'NOMPAT'
  						,'idu'=>'IDU'
  						);
  	if (array_key_exists($lib,$renommage))
  			$lib = $renommage[$lib];
  	//liste d'items à distinguer : mutuelle
  	//memes items que secu mais precedes par "mutuelle_"
  	if(ereg("mutuelle_(.*)",$lib,$reg)) {
  		$tabcouv=& $this->mutuelle;
  		$lib=$reg[1];
  		}
  	if(ereg("secu_(.*)",$lib,$reg)) {
  		$tabcouv=& $this->secu;
  		$lib=$reg[1];
  		}
  	if(isset($tabcouv)) {
  		switch($lib) {
  		case 'adresse_totale':
  			return $tabcouv['DEBNOM'][0].' '.$tabcouv['DEBADR'][0].' '.$tabcouv['DEBCDPOST'][0].' '.$tabcouv['ASS_VILLE'][0];
  		case 'nom_assure_complet':
  			return $tabcouv['ASS_NOM'][0]." ".$tabcouv['ASS_PRN'][0];
  		default:
  			if(! array_key_exists($lib,$tabcouv))
  				return "-donnée secu inconnue-";
  			return $tabcouv[$lib][0];	
  		}		
  	}	

  	//donnee basique
  		switch ($lib) {
			case 'nom_complet':
				$a = $this->patient['NOMU'][0]." ".$this->patient['PREN'][0];
				if($this->patient['NOMPAT'][0]) $a.=" né(e) ".$this->patient['NOMU'][0];
				return $a;
			case 'medecinT':
				return $this->getMedT();
			case 'derhopi':
				$a =$this->getHospis();
				if(!isset($a['DTENT'][0])) return '';
				$a =  $a['DTENT'][0];
				$a = new clDate($a);
				return $a->getSimpleDate('-');
			case 'prevenir':
				return $this->getAPrev();
			case 'ilp':
			case 'ipp':
			case 'IPP':
				return $this->getHopiILP();
			case 'situation_famille':
				return $this->getSitFamT();
			case 'datenaiss':
				$a = new clDate($this->patient['DTNAI'][0]);
				return $a->getSimpleDate();
			default:
				if(! array_key_exists($lib,$this->patient))
  					return '-donnée inconnue-';
				return $this->patient[$lib][0];
		}	
  }
  
  
  
  
  
  
  
  /*----------------------------------------
   * methodes statiques
   ---------------------------------------*/
  
    //fonction statique retournant le nom et le prenom à partir de l'idu
  static function getNameFromIdu($idu){
  	global $errs;
  	global $xham;
  	if(defined("VERSIONXHAM") && VERSIONXHAM == "2" ) { 
    	 $errs = $xham  ; 
    }
  	$param['cw'] = "WHERE p.IDU=r.IDU AND r.IDU='$idu'"  ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getPatient", $param, "ResultQuery" ) ;
	return $res['NOMU'][0]." ".$res['PREN'][0];
  }
  
  
  //renvoie l'idu à partir de l'ilp
  static function getIDUfromILP($ilp) {
	global $errs;
	global $xham;
  	if(defined("VERSIONXHAM") && VERSIONXHAM == "2" ) { 
    	 $errs = $xham  ; 
    }
  	$param = array();
  	$param['ilp']=$ilp;
  	$req = new clResultQuery ;
  	$res = $req -> Execute ( "Fichier", "getIDUfromILP", $param, "ResultQuery" );
  	if($res['INDIC_SVC'][2]>0){
  		return $res['IDU'][0];	
  } else {
  		$errs->addErreur("aucun idu trouvé pour l'ilp $ilp");
  		return false;
  }	
}
  
  
}

?>
