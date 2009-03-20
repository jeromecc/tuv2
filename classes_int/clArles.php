<?php

// Titre  : Classe Hyeres
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 07 Décembre 2006

// Description : 
// Affichage d'un lien vers le dossier médical & cyberlab.

class clArles {

  	// Constructeur.
  	function __construct ( ) {
  		$this->genAffichage ( ) ;
  	}

	// Génération de l'affichage.
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

  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>
