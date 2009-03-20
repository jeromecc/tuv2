<?php

// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date : 21 janvier 2008
//
// Le fichier doit etre renomme en Importation.php et il faut 
// seulement le faire dans le repertoire modules/ de Avignon Adultes
//
// UF2 : Il s'agit de l'UF des urgences enfants
//

define ( "BDD2", "terminale_tuv2" ) ;
define ( "UF2", "3211" ) ;

define ( "BDD3", "terminalg_tuv2" ) ;
define ( "UF3a", "3251" ) ;
define ( "UF3b", "9999" ) ;

define ( "DOUBLESERVICE", "1" ) ;

$hl7 = new clHL7 ( ) ;
$this->af .= $hl7 -> getAffichage ( ) ;

?>
