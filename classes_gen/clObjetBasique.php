<?php

// Titre  : Classe ObjetBasique
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 07 Avril 2006

// Description : 
// ObjetBasique

abstract class clObjetBasique {

  // Constructeur de la classe.
  public function initialisation ( $id='' ) {
    // Si un identifiant de l'objet est passé en paramètre,
    // on récupère les informations.
    if ( $id ) {
      // Préparation de la requête.
      $param['cw'] = "WHERE ".$this->nomId."=$id" ;
      $req = new clResultQuery ;
      // Récupération des informations de l'objet.
      $res = $req -> Execute ( "Fichier", $this->nomRequete, $param, "ResultQuery" ) ;
      if ( $res['INDIC_SVC'][2] ) {
	$this->info = $res ;
	$this->id = $id ;
	$this->i = 0 ;
	// On lance une procédure d'erreur si la news n'a pas pu être trouvée.
      } else {
	global $errs ;
	$errs -> addErreur ( "Impossible de trouver l'objet (".$this->nomId."=$id)" ) ;
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
      $requete = new clRequete ( BDD, $this->nomTable, $param ) ;
      $sql = $requete->updRecord ( $this->nomId.'='.$this->id ) ;
    }
  }

  // Création d'un nouvel objet.
  abstract protected function add ( ) ;

  // Mise à jour d'un objet.
  abstract protected function maj ( ) ;

  // Suppression d'un objet.
  public function del ( ) {
    $requete = new clRequete ( BDD, $this->nomTable ) ;
    $sql = $requete->delRecord ( $this->nomId.'='.$this->id ) ;
  }

  // Retourne la liste des objets.
  public function getListe ( $filtre ) {
    // Préparation de la requête.
    $param['cw'] = "$filtre" ;
    $req = new clResultQuery ;
    // Récupération des informations de la news.
    $res = $req -> Execute ( "Fichier", $this->nomRequete, $param, "ResultQuery" ) ;
    return $res ;
  }
}

?>
