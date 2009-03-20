<?php

// Titre  : Classe Hyeres
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 07 Décembre 2006

// Description : 
// Affichage d'un lien vers le dossier médical & cyberlab.

class clHyeres {

  	// Constructeur.
  	function __construct ( ) {
  		$this->genAffichage ( ) ;
  	}

	// Génération de l'affichage.
	function genAffichage ( ) {
		global $stopAffichage ;
		global $session ;
		$stopAffichage = 1 ;
		
		// GENERATION DU LIEN VERS LE LABO.
	
		$url = "http://esculape/lab/axigate_session.php?login=termurg&pwd=termurg&numperm=".$_REQUEST['idu'] ;
		$af .= '<a target="_blank" href="'.$url.'" ><img src="images/cyberlab.gif" style="border: 0px;" alt="labo" /></a>' ;
		
		print $af ;				
	}

  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>
