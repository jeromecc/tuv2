<?php

// Titre  : Classe ObjetBasique
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 28 Décembre 2006

// Description : 
// ObjetBasique

abstract class XhamObjetBasique {

	public $reqand ;

	public function getBDD() {
		if(isset($this->bdd)) return $this->bdd;
		else return BDD;
	}
	public function getHost() {
		if(isset($this->host)) return $this->host;
		else return MYSQL_HOST;
	}
	
	public function getUser() {
		if(isset($this->user)) return $this->user;
		else return MYSQL_USER;
	}
	public function getPass() {
		if(isset($this->pass)) return $this->pass;
		else return MYSQL_PASS;
	}
	

  // Constructeur de la classe.
  public function initialisation ( $id='', $and='' ) {
    global $xham ;
    // Si un identifiant de l'objet est passé en paramètre,
    // on récupère les informations.
    if ( $id ) {
      // Préparation de la requête.
      if ( $and ) $param['cw'] = "AND ".$this->nomId."= $id ".$this->reqand ;
      else $param['cw'] = "WHERE ".$this->nomId."= $id  ".$this->reqand ;
      $param['table'] = $this->nomTable ;
      // Récupération des informations de l'objet.
      $res = $xham -> Execute ( "Fichier", $this->nomRequete, $param, "ResultQuery" ) ;
      //eko ( affTab ( $res['INDIC_SVC'] ) ) ;
      if ( $res['INDIC_SVC'][2] ) {
		$this->info = $res ;
		$this->id = $id ;
		$this->i = 0 ;
		// On lance une procédure d'erreur si la news n'a pas pu être trouvée.
      } else {
		// Pas utile
		//global $errs ;
		//$xham -> addErreur ( "Impossible de trouver l'objet (".$this->nomId."=$id)" ) ;
      }
    }
  }

  // Retourne vrai si l'objet existe.
  public function exist ( ) {
    if ( isset ( $this->info ) ) return 1 ;
  }

  // Retourne soit le tableau entier, soit une case du tableau.
  public function getInfo ( $ind='', $defaut='', $date='' ) {
    if ( $ind ) {
      if ( isset ( $this->info ) AND isset ( $this->info[$ind] ) ) {
        if ( $this->info[$ind][$this->i] ) {
          if ( $date ) {
            $date = new clDate ( $this->info[$ind][$this->i] ) ;
            if ( $this->info[$ind][$this->i] != '0000-00-00 00:00:00' ) return $date->getDatetime () ;
            else return $defaut ;
          } else return $this->info[$ind][$this->i] ;
        } else return $defaut ;
      }
    } else {
      if ( isset ( $this->info ) ) return $this->info ;
    }
  }

  // Change un attribut de l'objet.
  public function setInfo ( $ind, $val, $request='' ) {
    if ( $this->id ) {
      if ( $request ) $param[$ind] = (isset($_REQUEST[$val])?$_REQUEST[$val]:$request) ;
      else $param[$ind] = $val ;
      $requete = new XhamRequete ( $this->getBDD(), $this->nomTable, $param,$this->getHost(), $this->getUser(),$this->getPass()) ;
      $sql = $requete->updRecord ( $this->nomId."= ".$this->id." ".$this->reqand ) ;
    } else {
    	//print "padid";
    	//die ;	
    }
  }

  // Création d'un nouvel objet.
  //abstract protected function add ( ) ;
  
  //creation d'un nouvel objet generique
  public function addGen($param) {
  		$requete = new XhamRequete ( $this->getBDD(), $this->nomTable, $param,$this->getHost(), $this->getUser(),$this->getPass()) ;
  		$sql = $requete->addRecord();
  		$this->id = $sql['cur_id'];
  }
  
  //renvoie le tableau de valeur pour un id precis
  public function getTabFromId($id) {
  	$requete = new XhamRequete ( $this->getBDD(), $this->nomTable, '',$this->getHost(), $this->getUser(),$this->getPass()) ;
  	$tab = $requete -> getGen($this->nomId.'='.$id.' '.$this->reqand,'tab');
  	$this->dataTab = $tab ;
  	return $tab[0];
  }
  

  // Mise à jour d'un objet.
  //abstract protected function maj ( ) ;

  public function majGen($valIndex='',$param) {
  	if($valIndex=='') $valIndex = $this->id;
  	$requete = new XhamRequete ( $this->getBDD(), $this->nomTable, $param,$this->getHost(), $this->getUser(),$this->getPass()) ;
  	return $requete->updRecord ( $this->nomId.'='.$valIndex.' '.$this->reqand ) ;
  }

  // Création si mise à jour en erreur.
  public function uoiGen($valIndex='',$param) {
  	if($valIndex=='') $valIndex = $this->id;
  	$requete = new XhamRequete ( $this->getBDD(), $this->nomTable, $param,$this->getHost(), $this->getUser(),$this->getPass()) ;
  	return $requete->uoiRecord ( $this->nomId.'='.$valIndex.' '.$this->reqand ) ;
  }

  // Suppression d'un objet.
  public function del ($id='') {
  	if($id=='') $id = $this->id ;
  	$requete = new XhamRequete ( $this->getBDD(), $this->nomTable, '',$this->getHost(), $this->getUser(),$this->getPass()) ;
    $sql = $requete->delRecord ( $this->nomId.'='.$id.' '.$this->reqand ) ;
  }

  // Retourne la liste des objets.
  public function getListe ( $filtre ) {
    global $xham ;
    // Préparation de la requête.
    $param['cw'] = "$filtre" ;
    // Récupération des informations de la news.
    $res = $xham -> Execute ( "Fichier", $this->nomRequete, $param, "ResultQuery" ) ;
    return $res ;
  }
 
  //retourne la liste des objets au format resultquery 
  public function getListeGen($filtre=' 1 = 1 ',$format='resultquery') {
  	$requete = new XhamRequete ( $this->getBDD(), $this->nomTable, '',$this->getHost(), $this->getUser(),$this->getPass()) ;
  	return $requete -> getGen($filtre,$format);
  }
  
  //vide la table
  public function truncate() {
  	$requete = new XhamRequete ( $this->getBDD(), $this->nomTable, '',$this->getHost(), $this->getUser(),$this->getPass()) ;
  	return $requete -> exec_requete('TRUNCATE TABLE `'.$this->nomTable.'`');
  }
  
}

?>
