<?php
/*
 * Created on 9 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
class XhamPreferences {

  // Attributs de la classe.
  // Contient l'affichage généré par la classe.
  private $af ;
  // Contient les messages d'informations
  private $infos ;
  // Contient les messages d'erreurs.
  private $erreurs ;
  // Préférences
  private $preferences ;

  // Constructeur.
  function __construct ( $xham ) {
	$this->xham = $xham ;
    if ( ! isset ( $this->preferences ) ) {
      $param['cw'] = "WHERE idUser='".$this->xham->user->getLogin ( )."'" ;
      $res = $this -> xham -> Execute ( "Fichier", "getPreferences", $param, "ResultQuery" ) ;
      for ( $i = 0 ; isset ( $res['idPreference'][$i] ) ; $i++ ) {
	$this->preferences[$res['libellePreference'][$i]] = $res['valeurPreference'][$i] ;
      }
    }
  }

  function genAffichage ( ) {
       
  }

  // Change la valeur d'une préférence utilisateur.
  function setPreference ( $libelle, $valeur, $idUser='' ) {
    if ( $idUser ) $user = $idUser ;
    else $user = $this->xham->user->getlogin ( ) ;

    $data['idApplication'] = IDAPPLICATION ;
    $data['libellePreference'] = $libelle ;
    $data['valeurPreference'] = $valeur ;
    $data['idUser'] = $user ;
    $req = $this->xham->newRequete ( BASEXHAM, (defined('TABLEPREF')?TABLEPREF:'preferences'), $data ) ;
    $res = $this->xham->uoiRecord ( "libellePreference='$libelle' AND idUser='$user'" ) ;
  }

  // Retourne la valeur de la préférence de l'utilisateur.
  function getPreference ( $libelle, $idUser='' ) {
    if ( $idUser ) {
      $param['cw'] = "WHERE idUser='".$this->xham->user->getLogin ( )."' AND libellePreference='$libelle'" ;
      $res = $this -> xham -> Execute ( "Fichier", "getPreferences", $param, "ResultQuery" ) ;
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
?>
