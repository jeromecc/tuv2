<?php

// Titre  : Classe Purge
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 20 Novembre 2006

// Description : 
// Purge du terminal des urgences.

class clPurge {

  	// Constructeur.
  	function __construct ( $id='' ) {
 		$this->launchPurge ( ) ;
  	}

	// Purge
	function launchPurge ( ) {
		$this->af .= '<h4>Les fichiers suivants de plus de 30 jours sont supprimés</h4>' ;
		$this->af .= 'Purge du répertoire "hprim/ok/"<br>' ;
		$poub = new clPoubelle ( "hprim/ok/" ) ;
		$poub -> purgerRepertoire ( '720' ) ;
		$this->af .= 'Purge du répertoire "hprim/xml/ok/"<br>' ;
		$poub = new clPoubelle ( "hprim/xml/ok/" ) ;
		$poub -> purgerRepertoire ( '720' ) ;
		$this->af .= '<h4>Les fichiers suivants de plus de 30 jours sont supprimés</h4>' ;
		$this->af .= 'Purge du répertoire "rpu/ok/"<br>' ;
		$poub = new clPoubelle ( "rpu/ok/" ) ;
		$poub -> purgerRepertoire ( '720' ) ;
		$this->af .= 'Purge du répertoire "rpu/logs/"<br><br>' ;
		$poub = new clPoubelle ( "rpu/logs/" ) ;
		$poub -> purgerRepertoire ( '720' ) ;
		$this->af .= 'Purge du répertoire "rpu/arh/ok/"<br><br>' ;
		$poub = new clPoubelle ( "rpu/arh/ok/" ) ;
		$poub -> purgerRepertoire ( '720' ) ;
		
	}


  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>