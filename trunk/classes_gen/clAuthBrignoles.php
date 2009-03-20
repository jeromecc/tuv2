<?php

// Titre  : Classe AuthBrignoles
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 23 Novembre 2007

// Description : 
// Permet de récupérer l'identité depuis le SSO de Brignoles.

class clAuthBrignoles {

  function __construct ( ) {

  }

  function Valide ( $valid='' ) {
    global $errs ;

	// Alain : Tu dois remplacer "fonctionSSO" par le nom de ta fonction.
    $param['cw'] = "WHERE uid='".fonctionSSO ()."'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getUtilisateurs", $param, "ResultQuery" ) ;
    
    // On récupère les informations de l'utilisateur.
    if ( $res['INDIC_SVC'][2] ) {
      $this->informations['type']     = "SSOBrignoles" ;
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
