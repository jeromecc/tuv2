<?php

// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date : 21 janvier 2008
//

// Les paramètres de la base du terminal 1 se trouvent dans le fichier define.xml.php
// BDD : terminal1_tuv2
// Les paramètres d'UF se trouvent dans les options du terminal 1
// Attention à bien mettre toutes les UF des autres terminaux dans
// l'option "FiltreHprimUF" de la catégorie Importation (liste d'UF séparées par des |)
// Exemple : FiltreHprimUF égale à 1111|2222|3333|4444|5555
// Tous les patients seront importés depuis le terminal 1 ; ils seront ensuite
// répartis entre les bases des 5 terminaux en fonction des informations saisies plus bas.

// Base du terminal 2
define ( "BDD2", "terminal2_tuv2" ) ;
// UF d'entree du terminal 2
define ( "UF2", "2222" ) ;

// Base du terminal 3
define ( "BDD3", "terminal3_tuv2" ) ;
// UF d'entree du terminal 3
define ( "UF3", "3333" ) ;

// Base du terminal 4
define ( "BDD4", "terminal4_tuv2" ) ;
// UF d'entree du terminal 4
define ( "UF4", "4444" ) ;

// Base du terminal 5
define ( "BDD5", "terminal5_tuv2" ) ;
// UF d'entree du terminal 5
define ( "UF5", "5555" ) ;

// S'il y a plusieurs services, ce define doit être à 1
define ( "DOUBLESERVICE", "1" ) ;

$hl7 = new clHL7 ( ) ;
$this->af .= $hl7 -> getAffichage ( ) ;

?>
