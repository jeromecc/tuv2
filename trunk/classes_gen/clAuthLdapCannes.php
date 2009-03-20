<?php

// Titre  : Classe AuthLdap
// Auteur : Damien Borel <dborel@orupaca.fr>
// Date   : 28 novembre 2008

// Description : 
// Authentification et récupération des informations de l'utilisateur dans un annuaire LDAP.

class clAuthLdapCannes {

  function __construct ( ) {

  }

  function Valide ( $noBind='' ) {
    global $errs ;
    $this->conn = ldap_connect ( LDAP_HOST, LDAP_PORT ) ;
    ldap_set_option ( $this->conn, LDAP_OPT_PROTOCOL_VERSION, 3 ) ;
    if ( ! $this->conn ) $errs -> addErreur ( "clAuthLdap : Connexion impossible à l'annuaire LDAP.", 1 ) ;
    $result = ldap_search ( $this->conn, LDAP_BASE, "(uid=".$_POST['login'].")" ) ;
    $info = ldap_get_entries ( $this->conn, $result ) ;
    if ( $noBind ) $this->bindLDAP = 1 ;
    else $this->bindLDAP = @ldap_bind ( $this->conn, $info[0]['dn'], $_POST['password'] ) ;
    if ( $this->bindLDAP ) {	
      $result = ldap_search ( $this->conn, LDAP_BASE, "(uid=".$_POST['login'].")" ) ;
      $info = ldap_get_entries ( $this->conn, $result ) ;
      //$this->informations['ldapdata'] = $info ;
      $this->informations['password']   = XhamTools::chiffre($_POST['password']) ;
      $this->informations['type']	  ="LDAPCannes";
      $this->informations['nom']      = $info[0]["sn"][0] ;
      $this->informations['prenom']   = $info[0]["givenname"][0] ;
      $this->informations['iduser']   = $info[0]["uid"][0] ;
      $this->informations['pseudo']   = $info[0]["cn"][0] ;
      $this->informations['mail']     = $info[0]["mail"][0] ;
      $this->informations['tel']      = $info[0]["telephonenumber"] ;
      $this->informations['mob']      = $info[0]["mobile"] ;
      $this->informations['org']      = $this->getOrganisations ( $info[0]["chhatorganisation"] ) ;
	  $this->informations['equipes']  =	$this->getEquipes($info[0]["chhatequipe"]);
      $results = ldap_search ( $this->conn, LDAP_BASE, "(&(memberUid=".$_POST['login'].")(objectclass=mailgroup))" ) ;
      $infoGroupe = ldap_get_entries ( $this->conn, $results ) ;
      //tableau des groupes (definis par ldap) pour l'user
      $this->informations['groupes'] = $this->getLdapGroupList($infoGroupe);
      
      $this->informations['idgroupe'] = $this->getGroupes ( $info[0]["uid"][0], $info[0]["chhatequipe"], $info[0]["chhatfonction"], $info[0]["chhatorganisation"], $infoGroupe ) ;
      for ( $i = 0 ; isset ( $info[0]["chhatfonction"][$i] ) ; $i++ ) {
	$fonctions[$i] = $this->getCN ( $info[0]["chhatfonction"][$i] ) ;
      }
      $this->informations['fonctions'] = $fonctions ;
      for ( $i = 0 ; isset ( $info[0]["chhatequipe"][$i] ) ; $i++ ) {
	$services[$i] = $this->getCN ( $info[0]["chhatequipe"][$i] ) ;
      }
      $this->informations['service']  = $services ;
      // print affTab ( $info[0] ) ;
      // print affTab ( $info[0]["uid"] ) ;
      ldap_close ( $this->conn ) ;
      return 1 ;
    } else {
    
    }
  }
  
  //SIMPLE liste des groupes LDAP auquel appartient l'utilisateur
  function getLdapGroupList($tabldap) {
  	$res = array();
  	for($i=0;$i<$tabldap['count'];$i++) {
  		$res[]=$this->getUidFromLdapString($tabldap[$i]['dn'],"cn");
  	}
 	return $res;  	
  }
  
  function getEquipes($orgs) {
  $res = array();
  for ( $i = 0 ; isset ( $orgs[$i] ) ; $i++ ) {
  	$res[]=$this->getCN ( $orgs[$i] ) ;
  	}
  return $res;
  }

  function getOrganisations ( $orgs ) {
    for ( $i = 0 ; isset ( $orgs[$i] ) ; $i++ ) {
      if ( isset ( $cn ) ) $cn .= "|".$this->getCN ( $orgs[$i] ) ;
      else $cn = $this->getCN ( $orgs[$i] ) ;
    }
    return $cn ;
  }

  function getInformations ( ) {
    return $this->informations ;
  }

  function getCN ( $search ) {
    $tab1 = explode ( "=", $search ) ;
    $tab2 = explode ( ",", $tab1[1] ) ;
    return $tab2[0] ;
  }
  
  //renvoie l'uid  ou autre donnée précidée de dans une la chaine d'attribut LDAP	
  static function getUidFromLdapString($srt,$mark='uid') {
	$matches = array();
	if( preg_match('/^'.$mark.'=([^,]+),/', $srt, $matches  ))
		return $matches[1];
	return false;
}

  /*
  function getGroupes ( $uid, $equipes, $fonctions ) {
    $this->getGroupe ( $uid ) ;
    for ( $i = 0 ; isset ( $equipes[$i] ) ; $i++ ) {
      $this->getGroupe ( $this->getCN ( $equipes[$i] ) ) ;
      $result = ldap_search ( $this->conn, LDAP_BASE, "(cn=".$this->getCN ($equipes[$i]).")" ) ;
      $info = ldap_get_entries ( $this->conn, $result ) ;
      for ( $j = 0 ; isset ( $info[$j] ) ; $j++ ) {
	for ( $k = 0 ; isset ( $info[$j]["chhatcodeuf"][$k] ) ; $k++ )
	  $this->informations['uf'][] = $info[$j]["chhatcodeuf"][$k] ;
      }
    }
    for ( $i = 0 ; isset ( $fonctions[$i] ) ; $i++ ) {
      $this->getGroupe ( $this->getCN ( $fonctions[$i] ) ) ;
      //      print "<br>fonction-$i : ".$fonctions[$i] ;
    }
    return $this->lg ;
  }
  */
  //Calcul des groupes
  function getGroupes ( $uid, $equipes, $fonctions, $orgs, $groupes ) {
    $this->getGroupe ( $uid ) ;
    for ( $i = 0 ; isset ( $equipes[$i] ) ; $i++ ) { 
    	$tabE[$this->getCN ($equipes[$i])] = $equipes[$i] ;
    	$result = ldap_search ( $this->conn, LDAP_BASE, "(cn=".$this->getCN ($equipes[$i]).")" ) ;
    	$info = ldap_get_entries ( $this->conn, $result ) ;
    	for ( $j = 0 ; isset ( $info[$j] ) ; $j++ ) { 
    		for ( $k = 0 ; isset ( $info[$j]["chhatcodeuf"][$k] ) ; $k++ )
      			$this->informations['uf'][] = $info[$j]["chhatcodeuf"][$k] ; 
    	}
    }
    for ( $i = 0 ; isset ( $groupes[$i] ) ; $i++ ) { $tabG[$this->getCN ($groupes[$i]['dn'])] = $groupes[$i]['dn'] ;}
    for ( $i = 0 ; isset ( $orgs[$i] ) ; $i++ ) { $tabO[$this->getCN ($orgs[$i])] = $orgs[$i] ; }
    for ( $i = 0 ; isset ( $fonctions[$i] ) ; $i++ ) { $tabF[$this->getCN ($fonctions[$i])] = $fonctions[$i] ; }
    $res = $this->getListeGroupes ( ) ;
    
    for ( $i = 0 ; isset ( $res['nomgroupe'][$i] ) ; $i++ ) {
      // print $res['nomgroupe'][$i]."<br>" ;
      $tab = explode ( '&', $res['nomgroupe'][$i] ) ;
      $bool = 1 ;
      while ( list ( $key, $val ) = each ( $tab ) ) {
		if ( ! isset ( $tabO[$val] ) AND ! isset ( $tabF[$val] ) 
		AND ! isset ( $tabE[$val] ) AND $val != $uid AND ! isset ( $tabG[$val] ) )
	  		$bool = 0 ;
      }
      if ( $bool ) {
	if ( $this->lg ) $this->lg .= ",".$res['idgroupe'][$i] ;
	else $this->lg = $res['idgroupe'][$i] ;
      }
    }
    // eko ( $this->lg ) ;
    //print "<br><br>".$this->lg ;
    return $this->lg ;
  }
  
  function getListeGroupes ( ) {
    //    $param['cw'] = "" ;
    //    $req = new clResultQuery ;
    //    $res = $req -> Execute ( "Fichier", "getGroupes", $param, "ResultQuery" ) ;
    // Récupération de la liste des groupes.
    $param['aw'] = " AND idapplication=".IDAPPLICATION." ORDER BY nomgroupe" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getGroupesApplication", $param, "ResultQuery" ) ;
    return $res ; 
  }
  
  function getGroupe ( $nom ) {
    $param['cw'] = "where nomgroupe='$nom'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getGroupes", $param, "ResultQuery" ) ;
    if ( $res['idgroupe'] ) {
      if ( $this->lg ) $this->lg .= ",".$res['idgroupe'][0] ;
      else $this->lg = $res['idgroupe'][0] ;
    }
  }


/*
 * ############################  UTIL #####################################
 */







}

?>
