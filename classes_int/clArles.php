<?php

// Titre  : Classe Hyeres
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 07 D�cembre 2006

// Description : 
// Affichage d'un lien vers le dossier m�dical & cyberlab.

class clArles {

  	// Constructeur.
  	function __construct ( ) {
  		$this->genAffichage ( ) ;
  	}

	// G�n�ration de l'affichage.
	function genAffichage ( ) {
		global $stopAffichage ;
		global $session ;
		$stopAffichage = 1 ;
		
    	// Url de cyberlab
    	$urlcyberlab="http://sri/sriaccess/login.pl?logincode=terminurg&password=terminurg&ipp=".sprintf ( '%09d', $_REQUEST['ilp'] )."&hauteur=screenheight&largeur=screenwidth";
		//$urlcyberlab="http://bionet.ch-hyeres.fr/cyberlab/servlet/be.mips.cyberlab.web.APIEntry?Class=Login&loginName=$uid&password=$password&OnClose=$url_retour&screenResolution=$resolution".$browser;
		$af .= '<a target="_blank" href="'.$urlcyberlab.'" ><img src="images/cyberlab.gif" style="border: 0px;" alt="CBL" /></a>' ;
		
		
		print $af ;				
	}

  	// Renvoie l'affichage g�n�r� par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>
