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
		$this->af .= '<h4>Les fichiers suivants de plus de 30 jours sont supprim�s</h4>' ;
		$this->af .= 'Purge du r�pertoire "hprim/ok/"<br>' ;
		$poub = new clPoubelle ( "hprim/ok/" ) ;
		$poub -> purgerRepertoire ( '720' ) ;
		$this->af .= 'Purge du r�pertoire "hprim/xml/ok/"<br>' ;
		$poub = new clPoubelle ( "hprim/xml/ok/" ) ;
		$poub -> purgerRepertoire ( '720' ) ;
		$this->af .= '<h4>Les fichiers suivants de plus de 30 jours sont supprim�s</h4>' ;
		$this->af .= 'Purge du r�pertoire "rpu/ok/"<br>' ;
		$poub = new clPoubelle ( "rpu/ok/" ) ;
		$poub -> purgerRepertoire ( '720' ) ;
		$this->af .= 'Purge du r�pertoire "rpu/logs/"<br><br>' ;
		$poub = new clPoubelle ( "rpu/logs/" ) ;
		$poub -> purgerRepertoire ( '720' ) ;
		$this->af .= 'Purge du r�pertoire "rpu/arh/ok/"<br><br>' ;
		$poub = new clPoubelle ( "rpu/arh/ok/" ) ;
		$poub -> purgerRepertoire ( '720' ) ;
		
	}


  	// Renvoie l'affichage g�n�r� par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>