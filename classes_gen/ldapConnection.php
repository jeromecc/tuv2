<?php
/*ldapConnection.php
 * Created on 22 mai 2006
 * Author : Emmanuel Cervetti ecervetti@ch-hyeres.fr
 * Version 0.1A
 */
 
 class ldapConnection {
 	//variable de connection
 	private $conn; 	
 	
 	// Initialise la connexion à l'annuaire LDAP.
 	function ldapConnection ( $host='', $port='' ) {
 		global $xham;
 		$this->conn = ldap_connect ( ($host?$host:LDAP_HOST), ($port?$port:LDAP_PORT) ) ;
 		ldap_set_option ( $this->conn, LDAP_OPT_PROTOCOL_VERSION, 3 ) ;
 		if ( ! $this->conn )
 			$xham -> addErreur ( "ldapConnection : Connexion impossible à l'annuaire LDAP.", 1 ) ;
 	}
 	
 	// Initialise la connexion à l'annuaire LDAP.
 	function ldapBind ( $dn='', $pd='' ) {
 		global $xham;
 		$r = ldap_bind ( $this->conn, ($dn?$dn:LDAP_DN), ($pd?$pd:LDAP_PD) ) ;
 		if ( ! $r )
 			$xham -> addErreur ( "ldapConnection : Bind impossible à l'annuaire LDAP.", 1 ) ;
 	}
 	
 	// Recherche.
 	function search ( $filter, $base='' ) {
 	    //print $filter ;
 	    //print LDAP_BASE ;
 		$result = ldap_search ( $this->conn, ($base?$base:LDAP_BASE), utf8_encode($filter) ) ;
 		//print "Filtre : $filter & Base : ".LDAP_BASE." -> ".$result ;
    	return ldap_get_entries ( $this->conn, $result ) ;
 	}
 	
 	// Création dans l'annuaire.
 	function add ( $dn, $data ) {
 		$this->ldapBind ( ) ;
 		return ldap_add ( $this->conn, $dn, $data ) ;
 	}
 	
 	// Mise à jour dans l'annuaire...
 	function mod ( $dn, $data ) {
 		$this->ldapBind ( ) ;
 		return ldap_modify ( $this->conn, $dn, $data ) ;
 	}
 	
 	// Suppression d'une entrée dans l'annuaire.
 	function del ( $dn ) {
 		$this->ldapBind ( ) ;
 		return ldap_delete ( $this->conn, $dn ) ;
 	}
 	
 	 // Suppression d'un champs
 	function modDel ( $dn, $data ) {
 		$this->ldapBind ( ) ;
 		return ldap_mod_del ( $this->conn, $dn, $data ) ;
 	}
 	
 	// Destructeur : fermeture de la connexion à l'annaire.
 	function __destruct() {
 		ldap_close ( $this->conn ) ;
 	}
 }
 
?>