<?php

// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date : 21 janvier 2008
//

// Les param�tres de la base du terminal 1 se trouvent dans le fichier define.xml.php
// BDD : terminal1_tuv2
// Les param�tres d'UF se trouvent dans les options du terminal 1
// Attention � bien mettre toutes les UF des autres terminaux dans
// l'option "FiltreHprimUF" de la cat�gorie Importation (liste d'UF s�par�es par des |)
// Exemple : FiltreHprimUF �gale � 1111|2222|3333|4444|5555
// Tous les patients seront import�s depuis le terminal 1 ; ils seront ensuite
// r�partis entre les bases des 5 terminaux en fonction des informations saisies plus bas.

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

// S'il y a plusieurs services, ce define doit �tre � 1
define ( "DOUBLESERVICE", "1" ) ;

$hl7 = new clHL7 ( ) ;
$this->af .= $hl7 -> getAffichage ( ) ;

?>
