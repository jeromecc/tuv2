<?php
/*XhamUser.php
 * Created on 7 juin 2006
 * Author : Emmanuel Cervetti ecervetti@ch-hyeres.fr
 * Author : Damien Borel <dborel@ch-hyeres.fr>
 * Version 0.1A
 */
 
class XhamUserLdap extends XhamUserAbstract  {
 	// Connexion ldap
	protected $conne;

	//---------------------------------------------
	//    METHODE D'ACCES AUX ATTRIBUTS PRIVES
	//---------------------------------------------
 
 	// Diverses fonctions de recupération d'informations
 	
	// ajout par François Derock
	function getEmail ( ) {
		return $this->informations['mail'];
	}
	//////////////////////////////////////////
	
	function getName ( ) {
		return $this->informations['nom'] ;
	}
	function getPrenom ( ) {
		return $this->informations['prenom'] ;
	}
	
	function getPseudo ( ) {
		return $this->informations['pseudo'] ;
	}
	
 	
	function getLdapGroupes ( ) {
		return $this->informations['groupes'] ;
	}
 
 	function getTel ( ) {
 		return $this->informations['tel'] ;
	}
  
  	//retourne une liste paillepée 
  	function getOrgs ( ) {
  	  	return $this->informations['org'] ; 
  	}
  	//idem mais sous forme de tableau
  	function getOrgsA ( ) {
  	  	return explode('|',$this->informations['org'] ); 
  	}

 	function getOrg ( $i=0 ) {
 		$t = explode ( '|', $this->informations['org'] ) ;
 		return $t[$i] ;
 	}

  	// Renvoie un tableau
  	function getUFs ( ) {
  		if( ! is_array($this->informations['uf']))
  			return array();
  		return $this->informations['uf'] ;
  	}
  

 	function getFonction ( $i=0 ) {
 		return $this->informations['fonctions'][0] ;
 	}
 	
 	// Tableau
 	function getFonctions ( ) {
 		return $this->informations['fonctions'] ;
 	}
 
 	function getEquipes ( ) {
 		return $this->informations['equipes'] ;
 	}
 	
  	// Renvoie les un tableau constitué des noms des ufs de l'utilisateur en cours
  	function getNomsUfs ( ) {
  		$r = array ( ) ;
  		if(! $this->nomufs ) $this->genNomsUfs ( ) ;
  		foreach ( $this->getUFs() as $key=>$value ) {
  			if ( isset ( $this->nomufs[$value] ) )
  				$r[] = $this->nomufs[$value] ;
  			else
  				$r[] = $value ;
  		}
  		return $r;
  	}


	//---------------------------------------------
	//              RECUPERATIONS LDAP
	//---------------------------------------------
 	// Connecte un user avec les données de connexion définies dans l'argument
	function connect ( $noBind='',$manualUser='' ) {
		$xm=$this->xham;
		if($manualUser)
			$login = $manualUser ;
		else
			$login =  $xm->getr('login') ;
    	$this->conne = ldap_connect ( LDAP_HOST, LDAP_PORT ) ;
    	ldap_set_option ( $this->conne, LDAP_OPT_PROTOCOL_VERSION, 3 ) ;
    	if ( ! $this->conne )
    		$xm -> addErreur ( "clXhamUserLdap : Connexion impossible à l'annuaire LDAP.", 1 ) ;
    	$result = ldap_search ( $this->conne, LDAP_BASE, "(uid=".$xm->getr('login').")" ) ;
    	$info 	= ldap_get_entries ( $this->conne, $result ) ;
		if( sizeof($info ) <= 1 )
			return false;
    	if ( $noBind )
    		$this->bindLDAP = 1 ;
    	elseif ($xm->getMode() == 'manuel' || $manualUser )	{
    		//whereAmI();
    		$this->bindLDAP = @ldap_bind ( $this->conne, LDAP_DN, LDAP_PD) ;
    	} else {
    		$this->xham->errs->startCatch();
    		$this->bindLDAP = @ldap_bind ( $this->conne, $info[0]['dn'], $xm->getr('password')) ;
    		$this->xham->errs->stopCatch();
    	}
    	if ( $this->bindLDAP ) {	
      		$result = ldap_search ( $this->conne, LDAP_BASE, "(uid=$login)" ) ;
      		$info = ldap_get_entries ( $this->conne, $result ) ;
      		$this->informations['password']   = XhamTools::chiffre($_POST['password']) ;
      		$this->informations['type']	      = "LDAP" ;
      		$this->informations['nom']        = utf8_decode($info[0]["sn"][0]) ;
      		$this->informations['prenom']     = utf8_decode($info[0]["givenname"][0]) ;
      		$this->informations['iduser']     = $info[0]["uid"][0] ;
      		$this->informations['pseudo']     = utf8_decode($info[0]["cn"][0]) ;
      		$this->informations['mail']       = $info[0]["mail"][0] ;
      		if(isset( $info[0]["telephonenumber"]))
      			$this->informations['tel']     = $info[0]["telephonenumber"] ;
      		else
      			$this->informations['tel'] 		= "" ;
      		$this->informations['mob']        = (isset($info[0]["mobile"])?$info[0]["mobile"]:'') ;
      		$this->informations['org']        = XhamUserLdap::calcOrganisations ( $info[0]["chhatorganisation"] ) ;
	  		$this->informations['equipes']    = XhamUserLdap::calcEquipes($info[0]["chhatequipe"]);
	  		$this->informations['ip']         = $_SERVER['REMOTE_ADDR'] ;
    		$this->informations['navigateur'] = substr($_SERVER["HTTP_USER_AGENT"],0,255) ;
      		$results = ldap_search ( $this->conne, LDAP_BASE, "(&(memberUid=$login)(objectclass=mailgroup))" ) ;
      		$infoGroupe = ldap_get_entries ( $this->conne, $results ) ;
      		//tableau des groupes (definis par ldap) pour l'user
      		$this->informations['groupes'] = XhamUserLdap::calcLdapGroupList($infoGroupe);
      
      		$this->informations['idgroupe'] = $this->getGroupes ( $info[0]["uid"][0], $info[0]["chhatequipe"], $info[0]["chhatfonction"], $info[0]["chhatorganisation"], $infoGroupe ) ;
      		for ( $i = 0 ; isset ( $info[0]["chhatfonction"][$i] ) ; $i++ ) {
				$fonctions[$i] = XhamUserLdap::calcCN ( $info[0]["chhatfonction"][$i] ) ;
      		}
      		$this->informations['fonctions'] = $fonctions ;
      		for ( $i = 0 ; isset ( $info[0]["chhatequipe"][$i] ) ; $i++ ) {
				$services[$i] = XhamUserLdap::calcCN ( $info[0]["chhatequipe"][$i] ) ;
      		}
      		$this->informations['service']  = $services ;
      		ldap_close ( $this->conne ) ;
      		return true ;
    	} else {
      		return false ;
    	}
 	}

	// Calcul des groupes communs à XHAM (utilisé par connect)
	function getGroupes ( $uid, $equipes, $fonctions, $orgs, $groupes ) {
		// Recuperation des groupes forcés par xham (methode definie dans abstractuser)
    	$this->getGroupe ( $uid ) ;
    	// Recuperation des données diverses ldap
    	for ( $i = 0 ; isset ( $equipes[$i] ) ; $i++ ) { 
    		$tabE[XhamUserLdap::calcCN ($equipes[$i])] = $equipes[$i] ;
    		$result = ldap_search ( $this->conne, LDAP_BASE, "(cn=".XhamUserLdap::calcCN ($equipes[$i]).")" ) ;
    		$info = ldap_get_entries ( $this->conne, $result ) ;
    		for ( $j = 0 ; isset ( $info[$j] ) ; $j++ ) { 
    			for ( $k = 0 ; isset ( $info[$j]["chhatcodeuf"][$k] ) ; $k++ )
      			$this->informations['uf'][] = $info[$j]["chhatcodeuf"][$k] ; 
      		}
    	}
    	for ( $i = 0 ; isset ( $groupes[$i] ) ; $i++ ) { $tabG[XhamUserLdap::calcCN ($groupes[$i]['dn'])] = $groupes[$i]['dn'] ;}
    	for ( $i = 0 ; isset ( $orgs[$i] ) ; $i++ ) { $tabO[XhamUserLdap::calcCN ($orgs[$i])] = $orgs[$i] ; }
    	for ( $i = 0 ; isset ( $fonctions[$i] ) ; $i++ ) { $tabF[XhamUserLdap::calcCN ($fonctions[$i])] = $fonctions[$i] ; }
    	// Recuperation de l'ensemble des groupes appartenant à xham pour cette appli
    	$res = XhamTools::getListeGroupes();
    	for ( $i = 0 ; isset ( $res['nomgroupe'][$i] ) ; $i++ ) {
      		// print $res['nomgroupe'][$i]."<br>" ;
      		$tab = explode ( '&', $res['nomgroupe'][$i] ) ;
      		$bool = 1 ;
      		while ( list ( $key, $val ) = each ( $tab ) ) {
				if ( ! isset ( $tabO[$val] ) AND ! isset ( $tabF[$val] ) AND ! isset ( $tabE[$val] ) AND $val != $uid AND ! isset ( $tabG[$val] ) )
	  			$bool = 0 ;
      		}
      		if ( $bool ) {
				if ( $this->lg ) $this->lg .= ",".$res['idgroupe'][$i] ;
				else $this->lg = $res['idgroupe'][$i] ;
      		}
    	}
    	// eko ( $this->lg ) ;
    	// print "<br><br>".$this->lg ;
    	//GROUPEEVERYBODY
    	
    	$gd = getDefine('GROUPEEVERYBODY','everybody') ;
    	//eko($gd);
    	for ( $i = 0 ; isset ( $res['nomgroupe'][$i] ) ; $i++ ) {
    		if($res['nomgroupe'][$i] == $gd ) {
    			if($this->lg)
    				$this->lg .= ",".$res['idgroupe'][$i];
    			else
    				$this->lg = $res['idgroupe'][$i];
    		}
    	}
    	
    	return $this->lg ;
  	}
  	
  	
  	
  	
  	

 
	//----------------------------------------------------- 
	//-----------METHODES STATIQUES-----------------------
	//-----------------------------------------------------

  
  	// SIMPLE liste des groupes LDAP auquel appartient l'utilisateur
  	static function calcLdapGroupList($tabldap) {
  		$res = array();
  		for($i=0;$i<$tabldap['count'];$i++) {
  			$res[]=XhamUserLdap::calcUidFromLdapString($tabldap[$i]['dn'],"cn");
  		}
 		return $res;  	
  	}
  
  	static function calcEquipes($orgs) {
  		$res = array();
  		for ( $i = 0 ; isset ( $orgs[$i] ) ; $i++ ) {
  		$res[]=XhamUserLdap::calcCN ( $orgs[$i] ) ;
  		}
  		return $res;
  	}

  	static function calcOrganisations ( $orgs ) {
    	for ( $i = 0 ; isset ( $orgs[$i] ) ; $i++ ) {
      		if ( isset ( $cn ) ) $cn .= "|".XhamUserLdap::calcCN ( $orgs[$i] ) ;
      		else $cn = XhamUserLdap::calcCN ( $orgs[$i] ) ;
    	}
    	return $cn ;
  	}

	// renvoie le cn dans une chaine d'attribut LDAP
	static  function calcCN ( $search ) {
    	$tab1 = explode ( "=", $search ) ;
    	$tab2 = explode ( ",", $tab1[1] ) ;
    	return $tab2[0] ;
  	}
  
  	// renvoie l'uid  ou autre donnée précidée de dans une la chaine d'attribut LDAP
  	// plus legere en ressources que la précédente, mais plus moche visuellement
  	// (C'est toi qui est moche.)	
	static function calcUidFromLdapString($srt,$mark='uid') {
		$matches = array();
		if( preg_match('/^'.$mark.'=([^,]+),/', $srt, $matches  ))
			return $matches[1];
		return false;
	}
  
	//--------------------------------------------------  
	//-----------pour compatibilité avec classe virtuelle
	//--------------------------------------------------
 
 	// Operations effectuées sur un user lors de chaque clic dans une même session
 	function reconnect ( ) { return true ; }
 
 	// Déconnecte l'user en cours
 	// rien à faire
 	function disconnect ( ) { return true ; }
 
  
}
 
 
?>
