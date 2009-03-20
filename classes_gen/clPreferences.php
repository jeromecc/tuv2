<?php

// Titre  : Classe Preference
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 06 Février 2006

class clPreferences {

  // Attributs de la classe.
  // Contient l'affichage généré par la classe.
  private $af ;
  // Contient les messages d'informations
  private $infos ;
  // Contient les messages d'erreurs.
  private $erreurs ;

  // Constructeur.
  function __construct ( ) {
    global $session ;
    if ( ! isset ( $this->preferences ) ) {
      $param['cw'] = "WHERE idUser='".$session->getUid ( )."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getPreferences", $param, "ResultQuery" ) ;
      for ( $i = 0 ; isset ( $res['idPreference'][$i] ) ; $i++ ) {
	$this->preferences[$res['libellePreference'][$i]] = $res['valeurPreference'][$i] ;
      }
    }
  }

  function genAffichage ( ) {
    global $patient ;
    global $session ;
    
        
  }

  // Change la valeur d'une préférence utilisateur.
  function setPreference ( $libelle, $valeur, $idUser='' ) {
    global $session ;
    if ( $idUser ) $user = $idUser ;
    else $user = $session->getUid ( ) ;

    $data['idApplication'] = IDAPPLICATION ;
    $data['libellePreference'] = $libelle ;
    $data['valeurPreference'] = $valeur ;
    $data['idUser'] = $user ;
    $req = new clRequete ( BASEXHAM, (defined('TABLEPREF')?TABLEPREF:'preferences'), $data ) ;
    $res = $req -> uoiRecord ( "libellePreference='$libelle' AND idUser='$user'" ) ;
  }

  // Retourne la valeur de la préférence de l'utilisateur.
  function getPreference ( $libelle, $idUser='' ) {
    global $session ;
    if ( $idUser ) {
      $param['cw'] = "WHERE idUser='".$session->getUid ( )."' AND libellePreference='$libelle'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getPreferences", $param, "ResultQuery" ) ;
      if ( $res['INDIC_SVC'][2] ) return $res['valeurPreference'][0] ;
    } else {
      if ( isset ( $this->preferences[$libelle] ) ) return $this->preferences[$libelle] ;
    }    
  }

  // Renvoie l'affichage généré par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}
