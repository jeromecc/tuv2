<?php

// Titre  : Classe Export
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 18 Mai 2005

// Description : 
// Permet d'exporter les données du terminal des urgences 
// dans un format "txt-tabulé".


class clExport {

  // Attributs de la classe.
  // Contient l'affichage généré par la classe.
  private $af ;

  // Constructeur.
  function __construct ( ) {
    global $session ;
    if ( $session -> getDroit ( "Administration_Exports", "r" ) ) {
      set_time_limit(0) ;
      $this->genAffichage ( ) ;
    }
  }

  // Génération de l'affichage de cette classe.
  function genAffichage ( ) {
    global $session ;
    global $options ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "Export.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Initialisation des dates en fonction de l'état actuel :
    // A la date du jour si aucune valeur n'a été passée.
    if ( $_POST['date2'] ) {
      $dt1 = new clDate ( $_POST['date1'] ) ;
      $dt2 = new clDate ( $_POST['date2'] ) ;
    } else {
      $dt2 = new clDate ( date ( "Y-m-d 00:00:00" ) ) ;
      $dt1 = new clDate ( date ( "Y-m-d 00:00:00" ) ) ;
      $dt1 -> addDays ( -1 ) ;
    }
    // Retrait d'un jour à la date1 si date1 == date2
    if ( $dt1 -> getTimestamp ( ) == $dt2 -> getTimestamp ( ) )
      $dt1 -> addDays ( -1 ) ;
    // Inversion des dates 1 et 2 si la date 1 est supérieur à la date 2.
    if ( $dt1 -> getTimestamp ( ) > $dt2 -> getTimestamp ( ) ) {
      $tmp_dt3 = $dt1 -> getTimestamp ( ) ;
      $tmp_dt4 = $dt2 -> getTimestamp ( ) ;
      $dt1 = new clDate ( $tmp_dt4 ) ;
      $dt2 = new clDate ( $tmp_dt3 ) ;
    }
    // Calcul de la liste des valeurs possibles pour la date1.
    $old = '' ;
    for ( $dt = new clDate ( DATELANCEMENT ) ; $dt->getTimestamp ( ) < $dt2 -> getTimestamp ( ) ; $dt -> addDays ( 1 ) ) {
      if ( $old == $dt->getDate ( "Y-m-d" ) ) {
      		$dt -> addHours ( 5 ) ;
      		$enleverHeure = 1 ;
      } else $enleverHeure = 0 ;
      $old = $dt->getDate ( "Y-m-d" ) ;	
      $dttmp = new clDate ( $dt->getDate ( "Y-m-d" ) ) ;
      $listeDates1[$dttmp->getTimestamp()] = $dt->getDate ( "d-m-Y" ) ;
      // if ( $enleverHeure ) $dt -> addHours ( -2 ) ;
      //eko ( $dttmp->getDatetime ( ).' -> '.$dt->getDatetime ( ) ) ;
    }
    if ( ! is_array ( $listeDates1 ) ) $listeDates1 = Array ( ) ;
    // Calcul de la liste des valeurs possibles pour la date2.
    $dtj = new clDate ( date ( "Y-m-d 00:00:00" ) ) ;
    $dtj -> addDays ( 1 ) ;
    $dt = new clDate ( DATELANCEMENT ) ;
    $old = '' ;
    for ( $dt -> addDays ( 1 )  ; $dt->getTimestamp ( ) <= $dtj -> getTimestamp ( ) ; $dt -> addDays ( 1 ) ) {
      if ( $old == $dt->getDate ( "Y-m-d" ) ) {
      	$dt -> addHours ( 5 ) ;
      	$enleverHeure = 1 ;
      } else $enleverHeure = 0 ;
      $old = $dt->getDate ( "Y-m-d" ) ;	
      $dttmp = new clDate ( $dt->getDate ( "Y-m-d" ) ) ;
      $listeDates2[$dttmp->getTimestamp()] = $dt->getDate ( "d-m-Y" ) ;
      //if ( $enleverHeure ) $dt -> addHours ( -2 ) ;
      //eko ( $dttmp->getDatetime ( ).' -> '.$dt->getDatetime ( ) ) ;
    }
    
    if ( ! is_array ( $listeDates2 ) ) $listeDates2 = Array ( ) ;
    // Fabrication des listes dans ModeliXe.
    $mod -> MxSelect( "date1", "date1", $dt1->getTimestamp( ), $listeDates1, '', '', "onChange=\"reload(this.form)\"") ; 
    $mod -> MxSelect( "date2", "date2", $dt2->getTimestamp( ), $listeDates2, '', '', "onChange=\"reload(this.form)\"") ; 
    // Gestion du filtre avec des bouttons radio.
    if ( ! isset ( $_POST['filtre'] ) ) $_POST['filtre'] = "tous" ;
    $mod -> MxCheckerField ( "filtre1", "radio", "filtre", "tous", (($_POST['filtre']=="tous")?true:false));
    $mod -> MxCheckerField ( "filtre2", "radio", "filtre", "norm", (($_POST['filtre']=="norm")?true:false));
    $mod -> MxCheckerField ( "filtre3", "radio", "filtre", "uhcd", (($_POST['filtre']=="uhcd")?true:false));
    // Si le bouton "Chercher" n'a pas été pressé, alors on n'affiche pas
    // le bloc contenant le lien vers l'export.
    if ( ! $_POST['Chercher'] AND ! $_POST['Chercher_x'] ) {
      $mod -> MxBloc ( "donnees", "modify", " " ) ;
    } else {
      // Affichage du lien vers le fichier contenant l'export.
      // Récupération de tous les patients entre les deux dates données.
      $req = new clResultQuery ;
      $param[table] = PSORTIS ;
      // En fonction du filtre sélectionné.
      switch ( $_POST['filtre'] ) {
      /*
      case 'norm': $filter = "AND salle_examen NOT LIKE 'UHCD%'" ;  break ;
      case 'uhcd': $filter = "AND salle_examen LIKE 'UHCD%'" ; break;
      */
      case 'norm': $filter = "AND uf!=".$options->getOption('numUFUHCD')."" ;  break ;
      case 'uhcd': $filter = "AND uf=".$options->getOption('numUFUHCD')."" ; break;
      default: $filter = "" ; break ;
      }
      $param[cw] = "WHERE dt_admission BETWEEN '".$dt1->getDatetime()."' AND '".$dt2->getDatetime()."' $filter" ;
      //$param[cw] = " p, logs_mails m WHERE p.idpatient=m.idpatient AND dt_naissance>'1932-01-01 00:00:00' AND `dt_mail` >= '2006-10-01 00:00:00' AND type_mail='Procédure dépistage maltraitance'" ;
      $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      // newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ;
      //eko ( $res['INDIC_SVC'] ) ;
      if ( $res[INDIC_SVC][2] > 1 ) $s = "s" ;
      // Affichage d'un résumé des résultats.
      $mod -> MxText ( "donnees.nombre", $res[INDIC_SVC][2] ) ;
      $mod -> MxText ( "donnees.resultat", "entrée$s exportée$s" ) ;

	if ( ! $options->getOption ( 'CCAMExterne' ) ) { 	
      $jj = new clCCAMExportActesDiags ( array ( 'dateDebut'=>$dt1->getDate ( 'Y-m-d' ), 'dateFin'=>$dt2->getDate ( 'Y-m-d' ) ) ) ;
      $jj -> initTableauActesDiag ( $res ) ;
	}
	
	if ( $options->getOption ( "GestionCCMU" ) ) {
		if ( $res[ccmu][$i] ) $ccmu = "\t".$res[ccmu][$i] ; else $ccmu = "\t" ;
		$titreCCMU = "\tCCMU" ;
	} else {
		$ccmu = '' ;
		$titreCCMU = '' ;	
	}
	if ( $options->getOption ( "GestionGEMSA" ) ) {
		if ( $res[gemsa][$i] ) $gemsa = "\t".$res[gemsa][$i] ; else $gemsa = "\t" ;
		$titreGEMSA = "\tGEMSA" ;
	} else {
		$gemsa = '' ;
		$titreGEMSA = '' ;	
	}
	if ( $options->getOption ( "GestionTraumato" ) ) {
		if ( $res[traumato][$i] ) $traumato = "\t".$res[traumato][$i] ; else $traumato = "\t" ;
		$titreTraumato = "\tTraumato" ;
	} else {
		$traumato = '' ;
		$titreTraumato = '' ;	
	}
	
	

      // Préparation du fichier contenant l'export.
      // Ecriture de la première ligne contenant le titre des colonnes.
      // $fic .= "ILP\tIDPASS\tNom\tPrénom\tNaissance\tSexe\tCP\tMed trait\tDate Adm\tHeure Adm\tAdresseur\tMode admission\tDate exam\tHeure exam\tMed urg\tSalle exam\tCatégorie recours\tMotif recours\tCode recours\tCode grav.\tDest souhaitée\tDest attendue\tDate sortie\tHeure sortie\tIDE\tMoyen transport\tMotif transfert\tType dest\tCat Diag\tDiagnostic\tCode Diag\n" ;
      if ( $options->getOption ( 'CCAMExterne' ) ) { 
      	if ( $session->getDroit ( 'Identite_Patient', 'r' ) )
			$fic .= "ILP\tIDPASS\tUF\tNom\tPrénom\tNaissance\tSexe\tCP\tMed trait\tDate Adm\tHeure Adm\tMode admission\tDate exam\tHeure exam\tMed urg\tSalle exam\tCatégorie recours\tMotif recours\tCode recours\tCode grav.\tDate UHCD".$titreGEMSA.$titreCCMU.$titreTraumato."\tDest souhaitée\tDest attendue\tDate sortie\tHeure sortie\tIDE\tMoyen transport\tMotif transfert\tType dest\tCat Diag\tDiagnostic\tCode Diag\n" ;
      	else $fic .= "ILP\tIDPASS\tUF\tNaissance\tSexe\tCP\tMed trait\tDate Adm\tHeure Adm\tMode admission\tDate exam\tHeure exam\tMed urg\tSalle exam\tCatégorie recours\tMotif recours\tCode recours\tCode grav.\tDate UHCD".$titreGEMSA.$titreCCMU.$titreTraumato."\tDest souhaitée\tDest attendue\tDate sortie\tHeure sortie\tIDE\tMoyen transport\tMotif transfert\tType dest\tCat Diag\tDiagnostic\tCode Diag\n" ;
      } else {
     	if ( $session->getDroit ( 'Identite_Patient', 'r' ) )
			$fic .= "ILP\tIDPASS\tUF\tNom\tPrénom\tNaissance\tSexe\tCP\tMed trait\tDate Adm\tHeure Adm\tMode admission\tDate exam\tHeure exam\tMed urg\tSalle exam\tCatégorie recours\tMotif recours\tCode recours\tCode grav.\tDate UHCD".$titreGEMSA.$titreCCMU.$titreTraumato."\tDest souhaitée\tDest attendue\tDate sortie\tHeure sortie\tIDE\tMoyen transport\tMotif transfert\tType dest\tCat Diag\tDiagnostic\tCode Diag\t".$jj -> getTitreColonnes ( ) ;
      	else $fic .= "ILP\tIDPASS\tUF\tNaissance\tSexe\tCP\tMed trait\tDate Adm\tHeure Adm\tMode admission\tDate exam\tHeure exam\tMed urg\tSalle exam\tCatégorie recours\tMotif recours\tCode recours\tCode grav.\tDate UHCD".$titreGEMSA.$titreCCMU.$titreTraumato."\tDest souhaitée\tDest attendue\tDate sortie\tHeure sortie\tIDE\tMoyen transport\tMotif transfert\tType dest\tCat Diag\tDiagnostic\tCode Diag\t".$jj -> getTitreColonnes ( ) ;
      }
      // Parcours des différents résultats.
      for ( $i = 0 ; isset ( $res[idpatient][$i] ) ; $i++ ) {
	// Préparation des différentes dates pour affichage.
	
	$naissance = new clDate ( $res[dt_naissance][$i] ) ;
	$dtn = $naissance -> getDate ( "d/m/Y" ) ;
	if ( $res[dt_admission][$i] != '0000-00-00 00:00:00' ) {
	  $admission = new clDate ( $res[dt_admission][$i] ) ;
	  $dta = $admission -> getDate ( "d/m/Y" ) ;
	  $hma = $admission -> getDate ( "H:i" ) ;
	} else {
	  $dta = '--' ;
	  $hma = '--' ;
	}
	if ( $res[dt_examen][$i] != '0000-00-00 00:00:00' ) {
	  $examen = new clDate ( $res[dt_examen][$i] ) ;
	  $dte = $examen -> getDate ( "d/m/Y" ) ;
	  $hme = $examen -> getDate ( "H:i" ) ;
	} else {
	  $dte = '--' ;
	  $hme = '--' ;
	}
	if ( $res[dt_sortie][$i] != '0000-00-00 00:00:00' ) {
	  $sortie = new clDate ( $res[dt_sortie][$i] ) ;
	  $dts = $sortie -> getDate ( "d/m/Y" ) ;
	  $hms = $sortie -> getDate ( "H:i" ) ;
	} else {
	  $dts = '--' ;
	  $hme = '--' ;
	}
	
	$uf     = $res[uf][$i] ;
    $ufUHCD = $options->getOption ( 'numUFUHCD' ) ;
    $ufUHCDrepere = $options->getOption ( 'numUFUHCDrepere' ) ;
    if ( ($ufUHCD && ereg ( $ufUHCD, $uf )) OR ( $ufUHCDrepere && ereg (  $ufUHCDrepere, $uf ) ) ) $isuhcd = 1 ;
    else $isuhcd = 0 ;
	//eko($jj -> tabExport);	
	// Préparation des différents champs de l'enregistrement parcouru.
	if ( $res[ilp][$i] ) $ilp = $res[ilp][$i] ; else $ilp = "-" ;
	if ( $res[uf][$i] ) $uf = $res[uf][$i] ; else $uf = "-" ;
	if ( $res[dt_UHCD][$i] AND $isuhcd ) $dtUHCD = $res[dt_UHCD][$i] ; else $dtUHCD = "0000-00-00 00:00:00" ;
	if ( $res[nsej][$i] ) $nsej = $res[nsej][$i] ; else $nsej = "-" ;
	if ( $res[nom][$i] ) $nom = $res[nom][$i] ; else $nom = "-" ;
	if ( $res[prenom][$i] ) $prenom = $res[prenom][$i] ; else $prenom = "-" ;
	if ( $res[sexe][$i] ) $sexe = $res[sexe][$i] ; else $sexe = "-" ;
	if ( $res[adresse_cp][$i] ) $adresse_cp = $res[adresse_cp][$i] ; else $adresse_cp = "-" ;
	if ( $res[medecin_traitant][$i] ) $medecin_traitant = preg_replace("/(\r\n|\n|\r)/", " ", $res[medecin_traitant][$i] ) ; else $medecin_traitant = "-" ;
	if ( $res[adresseur][$i] ) $adresseur = $res[adresseur][$i] ; else $adresseur = "-" ;
	if ( $res[mode_admission][$i] ) $mode_admission = $res[mode_admission][$i] ; else $mode_admission = "-" ;
	if ( $res[medecin_urgences][$i] ) $medecin_urgences = $res[medecin_urgences][$i] ; else $medecin_urgences = "-" ;
	if ( $res[salle_examen][$i] ) $salle_examen = $res[salle_examen][$i] ; else $salle_examen = "-" ;
	if ( $res[recours_categorie][$i] ) $recours_categorie = $res[recours_categorie][$i] ; else $recours_categorie = "-" ;
	if ( $res[motif_recours][$i] ) $motif_recours = $res[motif_recours][$i] ; else $motif_recours = "-" ;
	if ( ! $options->getOption ( 'getRecoursCIM10' ) ) {
		if ( $res[recours_code][$i] ) $recours_code = $res[recours_code][$i] ; else $recours_code = "-" ;
	} else {
		if ( $res[recours_code][$i] ) $recours_code = strtr ( $res[recours_code][$i], '.', '' ) ; else $recours_code = "-" ;
	}
	if ( $res[code_gravite][$i] ) $code_gravite = $res[code_gravite][$i] ; else $code_gravite = "-" ;
	if ( $res[dest_souhaitee][$i] ) $dest_souhaitee = $res[dest_souhaitee][$i] ; else $dest_souhaitee = "-" ;
	if ( $res[dest_attendue][$i] ) $dest_attendue = ' '.$res[dest_attendue][$i] ; else $dest_attendue = "-" ;
	if ( $res[ide][$i] ) $ide = $res[ide][$i] ; else $ide = "-" ;
	if ( $res[motif_transfert][$i] ) $motif_transfert = $res[motif_transfert][$i] ; else $motif_transfert = "-" ;
	if ( $res[moyen_transport][$i] ) $moyen_transport = $res[moyen_transport][$i] ; else $moyen_transport = "-" ;
	if ( $res[type_destination][$i] ) $type_destination = $res[type_destination][$i] ; else $type_destination = "-" ;
	if ( $res[diagnostic_categorie][$i] ) $diagnostic_categorie = $res[diagnostic_categorie][$i] ; else $diagnostic_categorie = "-" ;
	if ( $res[diagnostic_libelle][$i] ) $diagnostic_libelle = $res[diagnostic_libelle][$i] ; else $diagnostic_libelle = "-" ;
	if ( $res[diagnostic_code][$i] ) $diagnostic_code = $res[diagnostic_code][$i] ; else $diagnostic_code = "-" ;
	if ( $options->getOption ( "GestionCCMU" ) ) {
		if ( $res[ccmu][$i] ) $ccmu = "\t".$res[ccmu][$i] ; else $ccmu = "\t" ;
		$titreCCMU = "\tCCMU" ;
	} else {
		$ccmu = '' ;
		$titreCCMU = '' ;	
	}
	if ( $options->getOption ( "GestionGEMSA" ) ) {
		if ( $res[gemsa][$i] ) $gemsa = "\t".$res[gemsa][$i] ; else $gemsa = "\t" ;
		$titreGEMSA = "\tGEMSA" ;
	} else {
		$gemsa = '' ;
		$titreGEMSA = '' ;	
	}
	if ( $options->getOption ( "GestionTraumato" ) ) {
		if ( $res[traumato][$i] ) $traumato = "\t".$res[traumato][$i] ; else $traumato = "\t" ;
		$titreTraumato = "\tTraumato" ;
	} else {
		$traumato = '' ;
		$titreTraumato = '' ;	
	}
	
	// Génération de la ligne correspondant à l'enregistrement.
	//	$fic .= "$ilp\t$nsej\t$nom\t$prenom\t$dtn\t$sexe\t$adresse_cp\t$medecin_traitant\t$dta\t$hma\t$adresseur\t$mode_admission\t$dte\t$hme\t$medecin_urgences\t$salle_examen\t$recours_categorie\t$motif_recours\t$recours_code\t$code_gravite\t$dest_souhaitee\t$dest_attendue\t$dts\t$hms\t$ide\t$moyen_transport\t$motif_transfert\t$type_destination\t$diagnostic_categorie\t$diagnostic_libelle\t$diagnostic_code\n" ;
	if ( $options->getOption ( 'CCAMExterne' ) ) { 
		if ( $session->getDroit ( 'Identite_Patient', 'r' ) ) $fic .= "$ilp\t$nsej\t$uf\t$nom\t$prenom\t$dtn\t$sexe\t$adresse_cp\t$medecin_traitant\t$dta\t$hma\t$mode_admission\t$dte\t$hme\t$medecin_urgences\t$salle_examen\t$recours_categorie\t$motif_recours\t$recours_code\t$code_gravite\t$dtUHCD".$gemsa.$ccmu.$traumato."\t$dest_souhaitee\t$dest_attendue\t$dts\t$hms\t$ide\t$moyen_transport\t$motif_transfert\t$type_destination\t$diagnostic_categorie\t$diagnostic_libelle\t$diagnostic_code\n" ;
		else $fic .= "$ilp\t$nsej\t$uf\t$dtn\t$sexe\t$adresse_cp\t$medecin_traitant\t$dta\t$hma\t$mode_admission\t$dte\t$hme\t$medecin_urgences\t$salle_examen\t$recours_categorie\t$motif_recours\t$recours_code\t$code_gravite\t$dtUHCD".$gemsa.$ccmu.$traumato."\t$dest_souhaitee\t$dest_attendue\t$dts\t$hms\t$ide\t$moyen_transport\t$motif_transfert\t$type_destination\t$diagnostic_categorie\t$diagnostic_libelle\t$diagnostic_code\n" ;
	} else {
		if ( $session->getDroit ( 'Identite_Patient', 'r' ) ) $fic .= "$ilp\t$nsej\t$uf\t$nom\t$prenom\t$dtn\t$sexe\t$adresse_cp\t$medecin_traitant\t$dta\t$hma\t$mode_admission\t$dte\t$hme\t$medecin_urgences\t$salle_examen\t$recours_categorie\t$motif_recours\t$recours_code\t$code_gravite\t$dtUHCD".$gemsa.$ccmu.$traumato."\t$dest_souhaitee\t$dest_attendue\t$dts\t$hms\t$ide\t$moyen_transport\t$motif_transfert\t$type_destination\t$diagnostic_categorie\t$diagnostic_libelle\t$diagnostic_code\t".$jj -> getActesDiagsPatient ( $i ) ;
		else $fic .= "$ilp\t$nsej\t$uf\t$dtn\t$sexe\t$adresse_cp\t$medecin_traitant\t$dta\t$hma\t$mode_admission\t$dte\t$hme\t$medecin_urgences\t$salle_examen\t$recours_categorie\t$motif_recours\t$recours_code\t$code_gravite\t$dtUHCD".$gemsa.$ccmu.$traumato."\t$dest_souhaitee\t$dest_attendue\t$dts\t$hms\t$ide\t$moyen_transport\t$motif_transfert\t$type_destination\t$diagnostic_categorie\t$diagnostic_libelle\t$diagnostic_code\t".$jj -> getActesDiagsPatient ( $i ) ;
	}
      }
      // Calcul du nom du fichier temporaire.
      $nomfic = "export".date ( "YmdHis" ).".xls" ;
      // Création, écriture et fermeture du fichier.
      $FIC = fopen ( URLCACHE.$nomfic, "w" ) ;
      fwrite ( $FIC, $fic ) ;
      fclose ( $FIC ) ;
      // Calcul du lien vers ce fichier.
      $mod -> MxUrl  ( "donnees.lienExport", URLCACHEWEB.$nomfic ) ;    
      // On purge le répertoire temporaire de tous les fichiers qui ont plus de deux heures.
      $poub = new clPoubelle ( URLCACHE ) ;
      $poub -> purgerRepertoire ( "2" ) ;
    }
    // Variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
    // On retourne le code HTML généré par le template.
    $this->af .= $mod -> MxWrite ( "1" ) ;
  }
  

  


  // Renvoie l'affichage généré par la classe.
  function getAffichage ( ) {
    
    return $this->af ;
  }
}

?>