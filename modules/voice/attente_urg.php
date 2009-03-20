<?php
	$relocate="/home/www/terminal_urgences/";
	include ($relocate."config.php") ;

    $req = new clResultQuery ;
    $param[table] = PPRESENTS ;
	// Calcul des patients vus.
    $param[cw] = "WHERE dt_examen!='0000-00-00 00:00:00'" ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    $nbVus = $res[INDIC_SVC][2] ;

    // Calcul des patients non-vus.
    $param[cw] = "WHERE dt_examen='0000-00-00 00:00:00'" ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    $nbNonVus = $res[INDIC_SVC][2] ;
    echo $nbVus."\n".$nbNonVus;
?>
