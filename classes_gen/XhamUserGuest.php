<?php
/*
 * Created on 9 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
class XhamUserGuest extends XhamUserAbstract {
	
	// R�cup�ration de diff�rentes informations.
	
	
	// Connexion d'un admin.
	function connect ( ) {
      	$this->informations['type']       = "Echec" ;
      	$this->informations['nom']        = "Invit�" ;
      	$this->informations['prenom']     = "" ;
      	$this->informations['pseudo']     = "Invit�" ;
      	$this->informations['mail']       = "no-mail@ch-hyeres.fr" ;
      	$this->informations['iduser']     = "Invit�" ;
      	$this->informations['idgroupe']   = "1" ;
      	$this->informations['fonctions']  = array ( ) ;
      	$this->informations['service']    = array ( ) ;
      	$this->informations['org']        = '' ;
  		$this->informations['ip']         = $_SERVER['REMOTE_ADDR'] ;
   		$this->informations['navigateur'] = substr($_SERVER["HTTP_USER_AGENT"],0,255) ;
   		return true ;
	}
	
	// D�connexion
	function disconnect ( ) {
		return 1 ;
	}
	
	function getLogin() {
		return "Invit�";	
	}
	
	// Effectu� � chaque clic.
	function reconnect ( ) {
		return 1 ;
	}
	
	function getIdentite()  {
		return "Invit�";	
	}
	
		
}

?>
