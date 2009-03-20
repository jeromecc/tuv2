<?php

// Titre  : Classe AuthLdap
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 18 Octobre 2005

// Description : 
// Authentification d'un utilisateur en mode code d'accès unique.

class clAuthVeille {

  function __construct ( ) {

  }

   function Valide ( $valid='' ) {
    global $errs ;
    if ( $valid ) {
      $param['cw'] = "WHERE idActeur='".$_POST['idActeur']."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "authCodeAcces", $param, "ResultQuery" ) ;
    } else {
      $param['cw'] = "WHERE password='".$_POST['codeacces']."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "authCodeAcces", $param, "ResultQuery" ) ;
    }
    //print affTab ( $res['INDIC_SVC'] ) ;
    if ( $res['password'][0] == stripslashes($_POST['codeacces']) OR ( $res['idActeur'][0] == $_POST['idActeur'] AND $res['idActeur'][0] ) ) {
      global $pi ;
      $pi -> addPostIt ( 'Information', '<br>Pensez à déverrouiller vos formulaires (avec la petite flèche de retour tout en bas de chaque formulaire) afin de pouvoir les modifier. Merci.' ) ;
      $this->informations['type']     = "CodeAcces" ;
      $this->informations['nom']      = "" ;
      $this->informations['prenom']   = "" ;
      $this->informations['pseudo']   = $res['nomActeur'][0] ;
      $this->informations['mail']     = "" ;
      $this->informations['iduser']   = $res['idActeur'][0] ;
      $param['cw'] = "WHERE idacteur='".$res['idActeur'][0]."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getGroupesUtilisateur", $param, "ResultQuery" ) ;
      //print affTab ( $res['INDIC_SVC'] ) ;
      $groupes = $res['idgroupe'][0] ;
      for ( $i = 1 ; isset ( $res['idutilisateur'][$i] ) ; $i++ ) {
	$groupes .= ",".$res['idgroupe'][$i] ;
      }
      $this->informations['idgroupe'] = $groupes ;
      return 1 ;
    }
  }

  function getInformations ( ) {
    return $this->informations ;
  }

}

?>
