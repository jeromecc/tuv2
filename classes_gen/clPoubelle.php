<?php

// Titre  : Classe Poubelle
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 18 Mai 2005

// Description : 
// Cette classe gère le nettoyage, le vidage et
// l'archivage de répertoires.


class clPoubelle {

  private $rep ;

  function __construct ( $rep='' ) {
    $this->setRepertoire ( $rep ) ;
  }

  function setRepertoire ( $rep='' ) {
    $this->rep = $rep ;
  }

  function viderRepertoire ( ) {
    $this->purgerRepertoire ( 0 ) ;
  }

 //duree en heure
  function purgerRepertoire ( $duree ) {
    $rep = $this->rep ;
    $r = opendir ( $rep ) ;
    while ( $fic = readdir ( $r ) ) {
      if ( $fic != "." AND $fic != ".." AND $fic != "css" AND $fic != "images" ) {
	$date = filemtime ( "$rep/$fic" ) ;
	if ( $date < time ( ) - ( $duree * 3600 ) AND is_dir ( "$rep/$fic" ) ) {
	  $r_in = opendir ( "$rep/$fic" ) ;
	  while ( $fic_in = readdir ( $r_in ) ) {
	    if ( $fic_in != "." && $fic_in != ".." ) {
	      unlink ( "$rep/$fic/$fic_in" ) ;
	    }
	  }
	  closedir ( $r_in ) ;
	  rmdir ( "$rep/$fic" ) ;
	}
	elseif ( $date < time ( ) - ( $duree * 3600 ) && ! is_dir ( "$rep/$fic" ) && is_file ( "$rep/$fic" ) ) {
	  unlink ( "$rep/$fic" ) ;
	}
      }
    }
    closedir ( $r ) ;
  }
}

?>
