<?php

// Titre  : Classe MySQLInterne
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 07 Juin 2005

// Description : 
// Permet de s'authentifier sur la base MySQL interne du Terminal.

class clMySQLInterne {

  function __construct ( ) {

  }

  function Valide ( $valid='' ) {
    global $errs ;
    if ( $valid ) {
      $param['cw'] = "WHERE idutilisateur=".$_POST['iduser'] ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getUtilisateurs", $param, "ResultQuery" ) ;
    } else {
      $param['password'] = $_POST['password'] ;
      $param['uid'] = $_POST['login'] ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "authUtilisateur", $param, "ResultQuery" ) ;
    }
    if ( $res['uid'][0] == $_POST['login'] OR ( $res['idutilisateur'][0] == $_POST['iduser'] AND $_POST['iduser'] ) ) {
      $this->informations['password']   = XhamTools::chiffre($_POST['password']) ;
      $this->informations['type']     = "MySQLInt" ;
      $this->informations['nom']      = $res['nom'][0] ;
      $this->informations['prenom']   = $res['prenom'][0] ;
      $this->informations['pseudo']   = $res['uid'][0] ;
      $this->informations['mail']     = $res['mail'][0] ;
      $this->informations['iduser']   = $res['uid'][0] ;
      $param[cw] = "WHERE idutilisateur='".$res['idutilisateur'][0]."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getGroupesUtilisateur", $param, "ResultQuery" ) ;
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
