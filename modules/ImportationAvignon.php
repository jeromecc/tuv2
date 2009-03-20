<?php

// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date : 21 janvier 2008
//
// Le fichier doit etre renomme en Importation.php et il faut 
// seulement le faire dans le repertoire modules/ de Avignon Adultes
//

// Base du terminal adulte
// Les param�tres de la base se trouvent dans le fichier define.xml.php
// BDD : terminala_tuv2
// Les param�tres d'UF se trouvent dans les options du terminal adulte.
// Attention � bien mettre toutes les UF des autres terminaux dans
// l'option "FiltreHprimUF" de la cat�gorie Importation (liste d'UF s�par�es par des |)

// Base du terminal enfant
define ( "BDD2", "terminale_tuv2" ) ;
// UF d'entree du terminal enfant
define ( "UF2", "3211" ) ;

// Base du terminal gyneco
define ( "BDD3", "terminalg_tuv2" ) ;
// UF d'entree de gyneco
define ( "UF3", "3128" ) ;

// Base du terminal d'obstetrique
define ( "BDD4", "terminalg_tuv2" ) ;
// UF d'entree d'obstetrique
define ( "UF4", "3108" ) ;

// Base inutilis�e
define ( "BDD5", "terminalx_tuv2" ) ;
// UF d'entree d'obstetrique
define ( "UF5", "9999" ) ;

// S'il y a plusieurs services, ce define doit �tre � 1
define ( "DOUBLESERVICE", "1" ) ;

$hl7 = new clHL7 ( ) ;
$this->af .= $hl7 -> getAffichage ( ) ;

?>
