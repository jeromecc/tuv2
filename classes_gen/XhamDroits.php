<?php
/*
 * Created on 7 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 *
 */
 
 class XhamDroits {
 
	// Attribut contenant les informations de l'utilisateur.
	private $idgroupe ;
  	// Attribut contenant les droits de l'utilisateur.
	private $droits ;

	function __construct ( $xham, $idgroupe='' ) {
    	$this->xham = $xham ;
    	// Si un utilisateur est transmis, alors on est en mode récupération et accès aux droits.
    	if ( $idgroupe == "CHECK" ) {
    		  
    	} elseif ( $idgroupe ) {
      		$this->idgroupe = $idgroupe ;
      		$this->setDroits ( ) ;
    	}
  	}

  	// Vérifie si un droit passé en argument existe. Il est créé s'il n'existe pas.
  	function checkDroit ( $droit, $description ) {
    	// Récupération de tous les droits existants.
    	$param['cw'] = "WHERE libelle='$droit' AND idapplication='".IDAPPLICATION."'" ;
    	$restous = $this->xham -> Execute ( "Fichier", "getDroitsTous", $param, "ResultQuery" ) ;
    	if ( ! $restous['INDIC_SVC'][2] ) {
      		$param2['idgroupe'] = 0 ;
      		$param2['idapplication'] = IDAPPLICATION ;
      		$param2['libelle'] = $droit ;
      		$param2['descriptiondroit'] = addslashes ( $description ) ;
      		$requete = new XhamRequete ( BASEXHAM, TABLEDROITS, $param2 ) ;
      		$sql = $requete->addRecord ( ) ;
    	}
  	}

  	// Récupère et calcule les droits de l'utilisateur.
  	function setDroits ( ) {
  		
    	// On récupère la liste de tous les droits en rapport avec l'utilisateur.
    	$param['idgroupe'] = $this->idgroupe ;
    	$param['idapplication'] = IDAPPLICATION ;
    	$res = $this->xham -> Execute ( "Fichier", "getDroitsGroupe", $param, "ResultQuery" ) ;
		//print affTab($res);
    	// Pour chaque droit trouvé, on le décompose et on calcule ses valeurs.
    	for ( $i = 0 ; isset ( $res['iddroit'][$i] ) ; $i++ ) {
    		//print("trouve undroit");
    		// Décomposition en binaire.
      		$bin = sprintf ( "%05b", $res['valeur'][$i] ) ;
      		$lib = $res['libelle'][$i] ;
      		if ( ! isset ( $this->droits[$lib]['r'] ) ) $this->droits[$lib]['r'] = 0 ;
      		if ( ! isset ( $this->droits[$lib]['w'] ) ) $this->droits[$lib]['w'] = 0 ;
      		if ( ! isset ( $this->droits[$lib]['m'] ) ) $this->droits[$lib]['m'] = 0 ;
      		if ( ! isset ( $this->droits[$lib]['d'] ) ) $this->droits[$lib]['d'] = 0 ;
      		if ( ! isset ( $this->droits[$lib]['a'] ) ) $this->droits[$lib]['a'] = 0 ;
      		// Application du XOR.
      		if ( ! $this->droits[$lib]['r'] ) $this->droits[$lib]['r'] = $bin[4] ;
      		if ( ! $this->droits[$lib]['w'] ) $this->droits[$lib]['w'] = $bin[3] ;
      		if ( ! $this->droits[$lib]['m'] ) $this->droits[$lib]['m'] = $bin[2] ;
      		if ( ! $this->droits[$lib]['d'] ) $this->droits[$lib]['d'] = $bin[1] ;
      		if ( ! $this->droits[$lib]['a'] ) $this->droits[$lib]['a'] = $bin[0] ;
    	}
    	if ( $this->xham -> getOption ( "Indisponible" ) ) {
      		if ( ! $this->droits['Configuration_Options']['a'] ) {
				$this->xham -> pi -> addPostIt ( "Attention", "Une opération de maintenance est actuellement en cours. L'application sera disponible aux alentours de ".$this->xham->getOption ( "HeureDisponibilite" ).".", "alerte", "1" ) ;
				$this->droits = '' ;
				$this->droits['Accueil']['r'] = 1 ;
      		}
    	}
  	}

  	// Renvoie le tableau contenant tous les droits relatifs à un utilisateur.
  	function getDroits ( ) {
    	return $this->droits ;
  	}
 } 
 
?>
