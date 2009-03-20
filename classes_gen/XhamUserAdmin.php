<?php
/*
 * Created on 9 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
class XhamUserAdmin extends XhamUserAbstract {
	
	// R�cup�ration de diff�rentes informations.
	

	// Connexion d'un admin.
	function connect ( ) {
		//if($this->xham->getr('password') == ADMINPASSWORD AND ADMINACTIF AND ADMINPASSWORD AND defined('ADMINNOLOGIN'))
		//	print "POUOUOUOUOUO";
		
		if ( ( $this->xham->getr('login') == ADMINLOGIN AND $this->xham->getr('password') == ADMINPASSWORD AND ADMINACTIF AND ADMINLOGIN AND ADMINPASSWORD ) 
				||
			( $this->xham->getr('password') == ADMINPASSWORD AND ADMINACTIF AND ADMINPASSWORD AND defined('ADMINNOLOGIN') AND ADMINNOLOGIN	)
		) {
			$this->informations['type']       = "Config" ;
	  		$this->informations['nom']        = ADMINLOGIN ;
	  		$this->informations['prenom']     = "" ;
	  		$this->informations['pseudo']     = ADMINLOGIN ;
	  		$this->informations['mail']       = "" ;
	  		$this->informations['iduser']     = ADMINLOGIN ;
	  		$this->informations['idgroupe']   = "999999" ;
	  		$this->informations['superadmin'] = "1" ;
	  		$this->informations['idapp']      = IDAPPLICATION ;
	  		$this->informations['fonctions']  = array ( ) ;
	  		$this->informations['service']    = array ( ) ;
	  		$this->informations['org']        = '' ;
	  		$this->informations['ip']         = $_SERVER['REMOTE_ADDR'] ;
    		$this->informations['navigateur'] = $_SERVER["HTTP_USER_AGENT"] ;
    		return true ;
		} else return false ;
	}
	
	// D�connexion
	function disconnect ( ) {
		return 1 ;
	}
	
	// Effectu� � chaque clic
	function reconnect ( ) {
		return 1 ;
	}
	
	function getLogin() {
		return $this->informations['iduser'] ;	
	}
	
	function getIdentite() {
		return $this->informations['iduser'] ;	
	}
	
	function getId () {
		return $this->informations['iduser'] ;	
	}
}

?>
