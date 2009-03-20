<?php

// Titre  : Classe Radio
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 29 Mai 2007

// Description : 
// 

class clRadio {

  // Attributs de la classe.


  	// Constructeur.
  	function __construct ( $id='' ) {
 		$this->nomId      = 'id' ;
    	$this->nomTable   = 'radios' ;
    	$this->nomRequete = 'getRadios' ;
    	$this->initialisation ( $id ) ;
  	}

  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>