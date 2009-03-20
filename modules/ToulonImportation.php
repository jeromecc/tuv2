<?php

//
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date : 2007-06-20
// Description : Module d'importation spécial Toulon / La Seyne
// 

$mod = new ModeliXe ( "ImportationToulon.html" ) ;
$mod -> SetModeliXe ( ) ;
global $news1 ; global $mods1 ; global $errs1 ; global $news2 ; global $mods2 ; global $errs2 ; 
$news1 = 0 ; $mods1 = 0 ; $errs1 = 0 ; $news2 = 0 ; $mods2 = 0 ; $errs2 = 0 ;

// Récupération des entrées à importer.
$param['cw'] = "WHERE dt_traitement='0000-00-00 00:00:00'" ;
$req = new clResultQuery ;
$res = $req -> Execute ( "Fichier", "getImports", $param, "ResultQuery" ) ; 



// Parcours des entrées trouvées.
for ( $i = 0 ; isset ( $res['idimport'][$i] ) ; $i++ ) {
	if ( $res['uf'][$i] == '6004' ) $base = 'terminal2_tuv2' ; else $base = BDD ;
	
	// On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
	$param2['table'] = $base.".".PPRESENTS ;
	$param2['cw'] = "WHERE nsej='".$res['idpass'][$i]."'" ;
	$ras = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
	// On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
	$param3['table'] = $base.".".PSORTIS ;
	$param3['cw'] = "WHERE nsej='".$res['idpass'][$i]."'" ;
	$rus = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery" ) ;
	if ( $ras['INDIC_SVC'][2] ) {
		majPatientSQL ( $res['idimport'][$i], $ras['idpatient'][0], PPRESENTS ) ;
	} elseif ( $rus['INDIC_SVC'][2] ) {
		majPatientSQL ( $res['idimport'][$i], $rus['idpatient'][0], PSORTIS ) ;
	} else {
		addPatientSQL ( $res['idimport'][$i], PPRESENTS ) ;
	}
}






// Nombre d'entrées de la BAL MySQL
$mod -> MxText ( "titre", "Table imports dans la base MySQL." ) ;
$mod -> MxText ( "total1", $news1 + $mods1 + $errs1 ) ;
$mod -> MxText ( "news1", $news1 ) ;
$mod -> MxText ( "modif1", $mods1 ) ;
$mod -> MxText ( "errs1", $errs1 ) ;
$mod -> MxText ( "total2", $news2 + $mods2 + $errs2 ) ;
$mod -> MxText ( "news2", $news2 ) ;
$mod -> MxText ( "modif2", $mods2 ) ;
$mod -> MxText ( "errs2", $errs2 ) ;
// Récupération du code HTML généré.
$this->af .= $mod -> MxWrite ( "1" ) ;

// Ajout d'un patient dans une des tables du terminal (présents ou sortis).
function addPatientSQL ( $idimport, $table ) {
	global $errs ; global $news1 ; global $mods1 ; global $errs1 ; global $news2 ; global $mods2 ; global $errs2 ;
    // Récupération des informations sur le patient à créer.
	$param['cw'] = "WHERE idimport='$idimport'" ;
	$req = new clResultQuery ;
	$res = $req -> Execute ( "Fichier", "getImports", $param, "ResultQuery" ) ; 
	$i = 0 ;
	if ( $res['uf'][$i] == '6004' ) { $base = 'terminal2_tuv2' ; $news2++ ; } else { $base = BDD ; $news1++ ; }
	
	if ( $res['INDIC_SVC'][2] ) {
		  
		  $ras = $req -> Execute ( "Fichier", "getMaxIdToulon", $param, "ResultQuery" ) ; 
		  $max = 1 ;
		  for ( $j = 0 ; isset ( $ras['idpatient'][$j] ) ; $j++ )
		  	if ( $ras['idpatient'][$j] > $max ) $max = $ras['idpatient'][$j] ;
		  $max++ ;
		  $data['idpatient']            = $max ;
		  $data['idu']                  = $res['idu'][$i] ;	
		  $data['ilp']                  = $res['ilp'][$i] ;	
		  $data['nsej']                 = $res['idpass'][$i] ;	
		  $data['uf']                   = $res['uf'][$i] ;	
		  $data['nom']                  = $res['nom'][$i] ;	
		  $data['prenom']               = $res['prenom'][$i] ;	
		  $data['sexe']                 = $res['sexe'][$i] ;	
		  $data['dt_naissance']         = $res['dt_naissance'][$i] ;	
		  $data['adresse_libre']        = $res['adresse_libre'][$i] ;	
		  $data['adresse_cp']           = $res['adresse_cp'][$i] ;	
		  $data['adresse_ville']        = $res['adresse_ville'][$i] ;	
		  $data['telephone']            = $res['telephone'][$i] ;	
		  $data['prevenir']             = str_replace( '^', '<br/>', $res['prevenir'][$i] ) ;	
		  $data['medecin_traitant']     = $res['medecin_traitant'][$i] ;	
		  $data['dt_admission']         = $res['dt_admission'][$i] ;
		  $data['mode_admission']       = $res['mode_admission'][$i] ;	
		  $data['iduser']               = "IMPORT" ;	
		  $data['manuel']               = 0 ;
		  //newfct ( gen_affiche_tableau, $data ) ;
		 
		  // Calcul de la durée depuis lequel le patient est admis.
     	$d1 = new clDate ( ) ;
     	$d2 = new clDate ( $data['dt_admission'] ) ;
     	$duree = new clDuree ( $d1 -> getDifference ( $d2 ) ) ;
     	$duree -> invertNegatif ( ) ;
		 
		  // Appel de la classe Requete.
		  $requete = new clRequete ( $base, $table, $data ) ;
		  // Exécution de la requete.
		  $res = $requete->addRecord ( ) ;
		
		  // Si le patient est admis depuis plus de 30 minutes, alors il est placé dans la table des sortis
		  if ( $duree -> getMinutes ( ) > 30 ) {
			  $pat = new clPatient ( $res['cur_id'], '', $base ) ;
			  $pat -> sortirPatient ( 'simple' ) ;
		  }
		
		  // Mise à jour de la date de traitement de l'import.
		  $date = new clDate ( ) ;
		  $data2['dt_traitement'] = $date -> getDatetime ( ) ;
		  // Appel de la classe Requete.
		  $requete = new clRequete ( BDD, IMPORTS, $data2 ) ;
		  // Exécution de la requete.
		  $requete->updRecord ( "idimport='$idimport'" ) ;
		
		  
	} else {
		  // En cas d'erreur, on la signale...
		  $errs -> addErreur ( "clImportation : Impossible d'importer ce nouveau patient, l'identifiant de l'import est introuvable (idimport=\"$idimport\")." ) ;
		  $errs1++ ; $errs2++ ;
	}
}

// Mise à jour des informations d'un patient dans une des tables du terminal (présents ou sortis).
function majPatientSQL ( $idimport, $idpatient, $table ) {
	global $errs ; global $news1 ; global $mods1 ; global $errs1 ; global $news2 ; global $mods2 ; global $errs2 ;
	// Récupération des informations sur le patient à mettre à jour.
	$param['cw'] = "WHERE idimport='$idimport'" ;
	$req = new clResultQuery ;
	$res = $req -> Execute ( "Fichier", "getImports", $param, "ResultQuery" ) ; 
	if ( $res['INDIC_SVC'][2] ) {
		  $i = 0 ;
		  $data['idu']                  = $res['idu'][$i] ;	
		  $data['ilp']                  = $res['ilp'][$i] ;	
		  $data['nsej']                 = $res['idpass'][$i] ;	
		  $data['uf']                   = $res['uf'][$i] ;	
		  $data['nom']                  = $res['nom'][$i] ;	
		  $data['prenom']               = $res['prenom'][$i] ;	
		  $data['sexe']                 = $res['sexe'][$i] ;	
		  $data['dt_naissance']         = $res['dt_naissance'][$i] ;	
		  $data['adresse_libre']        = $res['adresse_libre'][$i] ;	
		  $data['adresse_cp']           = $res['adresse_cp'][$i] ;	
		  $data['adresse_ville']        = $res['adresse_ville'][$i] ;	
		  $data['telephone']            = $res['telephone'][$i] ;	
		  $data['prevenir']             = str_replace( '^', '<br/>', $res['prevenir'][$i] ) ;	
		  $data['medecin_traitant']     = $res['medecin_nom'][$i] ;	
		  $data['dt_admission']         = $res['dt_admission'][$i] ;
		  $data['mode_admission']       = $res['mode_admission'][$i] ;	
		  $data['iduser']               = "IMPORT" ;	
		  $data['manuel']               = 0 ;
		  //newfct ( gen_affiche_tableau, $data ) ;
		  if ( $res['uf'][$i] == '6004' ) { $base = 'terminal2_tuv2' ; $mods2++ ; } else { $base = BDD ; $mods1++ ; }
		  // Appel de la classe Requete.
		  $requete = new clRequete ( $base, $table, $data ) ;
		  // Exécution de la requete.
		  $requete->updRecord ( "idpatient='$idpatient'" ) ;
		
		  // Mise à jour de la date de traitement de l'import.
		  $date = new clDate ( ) ;
		  $data2['dt_traitement'] = $date -> getDatetime ( ) ;
		  // Appel de la classe Requete.
		  $requete = new clRequete ( BDD, IMPORTS, $data2 ) ;
		  // Exécution de la requete.
		  $requete->updRecord ( "idimport='$idimport'" ) ;
		
	} else {
		  // En cas d'erreur, on la signale...
		  $errs -> addErreur ( "clImportation : Impossible d'importer ce nouveau patient, l'identifiant de l'import est introuvable (idimport=\"$idimport\")." ) ;
		  $errs1++ ; $errs2++ ;
	}
}

?>
