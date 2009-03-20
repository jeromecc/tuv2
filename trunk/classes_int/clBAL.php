<?php

// Titre  : Classe Importation
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 10 Juillet 2006

// Description : 
// Gestion de la BAL du terminal des urgences.

class clBAL extends clObjetBasique {

  // Attributs de la classe.


  // Constructeur.
  function __construct ( $id='' ) {
 	$this->nomId      = 'id' ;
    $this->nomTable   = 'bal' ;
    $this->nomRequete = 'getBAL' ;
    $this->initialisation ( $id ) ;
  }

  // Renvoie l'affichage généré par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}

?>