<?php

// Titre  : Classe Hyeres
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 07 D�cembre 2006

// Description : 
// Affichage d'un lien vers le dossier m�dical & cyberlab.

class clHyeres {

  	// Constructeur.
  	function __construct ( ) {
  		$this->genAffichage ( ) ;
  	}

	// G�n�ration de l'affichage.
	function genAffichage ( ) {
		global $stopAffichage ;
		global $session ;
		$stopAffichage = 1 ;
		
		// GENERATION DU LIEN VERS LE LABO.
	
		$url = "http://esculape/lab/axigate_session.php?login=termurg&pwd=termurg&numperm=".$_REQUEST['idu'] ;
		$af .= '<a target="_blank" href="'.$url.'" ><img src="images/cyberlab.gif" style="border: 0px;" alt="labo" /></a>' ;
		
		print $af ;				
	}

  	// Renvoie l'affichage g�n�r� par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>
