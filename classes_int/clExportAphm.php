<?php

// Titre  : Classe Export
// Auteur : Emmanuel Cervetti
// Date   : 18 Octobre 2008

// Description : 
// Permet d'exporter les données du terminal des urgences 
// dans un format "txt-tabulé".


class clExportAphm {

  // Attributs de la classe.
  // Contient l'affichage généré par la classe.
  private $af ;

  // Constructeur.
  function __construct ( ) {
    global $session ;

      set_time_limit(0) ;
      $this->genAffichage ( ) ;
  }

  // Génération de l'affichage de cette classe.
  function genAffichage ( ) {
    global $session ;
    global $options ;
    // Chargement du template ModeliXe.
    $outputLignes = array();
    $mod = new ModeliXe ( "ExportAPHM.mxt" ) ;
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
    $mod -> MxSelect( "date1", "date1", $dt1->getTimestamp( ), $listeDates1) ; 
    $mod -> MxSelect( "date2", "date2", $dt2->getTimestamp( ), $listeDates2) ; 


   
   
    // Si le bouton "Chercher" n'a pas été pressé, alors on n'affiche pas
    // le bloc contenant le lien vers l'export.
    if ( ! $_POST['Chercher'] AND ! $_POST['Chercher_x'] ) {
      $mod -> MxBloc ( "donnees", "modify", " " ) ;
    } else {
      // Affichage du lien vers le fichier contenant l'export.
      // Récupération de tous les patients entre les deux dates données.
      //Le calcul commence ici
      $req = new clResultQuery ;
      $param[table] = PSORTIS ;
      /*
      // En fonction du filtre sélectionné.
      switch ( $_POST['filtre'] ) {
      
      //case 'norm': $filter = "AND salle_examen NOT LIKE 'UHCD%'" ;  break ;
      ca//se 'uhcd': $filter = "AND salle_examen LIKE 'UHCD%'" ; break;
      
      case 'norm': $filter = "AND uf!=".$options->getOption('numUFUHCD')."" ;  break ;
      case 'uhcd': $filter = "AND uf=".$options->getOption('numUFUHCD')."" ; break;
      default: $filter = "" ; break ;
      }
      */
      
      if($_POST['idpassage']) {
      	$param['cw'] = " WHERE ilp = '".$_POST['idpassage']."' ";
      } else {
      	$param['cw'] = " WHERE  dt_admission BETWEEN '".$dt1->getDatetime()."' AND '".$dt2->getDatetime()."' ";
      }
      
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

      // Parcours des différents résultats.
      $tabResFinal = array();
      $nbMaxActesNGAP = 0 ;
      $indiceMaxNbActesNgap = 0 ;
      //PARCOURS
      for ( $i = 0 ; isset ( $res[idpatient][$i] ) ; $i++ ) {
	// Préparation des différentes dates pour affichage.
	$tabLigne = array(); 
	
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
    if (( $ufUHCD &&  ereg ( $ufUHCD, $uf ) ) OR ( $ufUHCDrepere && ereg ( $ufUHCDrepere, $uf ) )) $isuhcd = 1 ;
    else $isuhcd = 0 ;
    
    //le patient est uhcd mais pas dans une salle uhcd ?
    $isVirtualUhcd = false ;
    if( $ufUHCD && ! ereg($options->getOption ( 'FiltreSalleUHCD'),$res['salle_examen'][$i])) {
    	$isVirtualUhcd = true ;
    	$codeUm = $options->getOption ( 'UMUHCDFictif') ;
    }
	
	// Préparation des différents champs de l'enregistrement parcouru.
	if ( $res[ilp][$i] ) $ilp = $res[ilp][$i] ; else $ilp = "-" ;
	if ( $res[uf][$i] ) $uf = $res[uf][$i] ; else $uf = "-" ;
	if ( $res[dt_UHCD][$i] AND $isuhcd ) $dtUHCD = $res[dt_UHCD][$i] ; else $dtUHCD = "0000-00-00 00:00:00" ;

	$nbSecPassage = $sortie->getDifference($admission); 
	$nbHeurepassage = floor($nbSecPassage/3600);
	$nbMinpassage = floor(($nbSecPassage-$nbHeurepassage*3600)/60) ;
	$strDureePassage = ($nbHeurepassage>9?'':'0').$nbHeurepassage.':'.($nbMinpassage>9?'':'0').$nbMinpassage;

	$tabActesNGAP  = clExportAphm::getTabListeCodesNGAP($i,$jj -> tabExport);
	//eko($tabActesNGAP);
	

	$tabLigneCCAM['CODE_HOMON'] = '1' ;
	$tabLigneCCAM['NUMSEJ'] = $res['nsej'][$i].'B' ;
	$tabLigneCCAM['UM'] =  ($isVirtualUhcd ? $codeUm : '' );
	$tabLigneCCAM['DATENT'] = $dta.' '.$hma ;
	$tabLigneCCAM['DIAG_PPAL'] = $res['diagnostic_code'][$i] ;
	$tabLigneCCAM['DIAG_RELI'] ='';
	//eko(clExportAphm::getTabListeDiagsCCAM ($i,$jj -> tabExport));
	$tabLigneCCAM['DIAG_ASSO']  = implode(' ',clExportAphm::getTabListeDiagsCCAM ($i,$jj -> tabExport));
	$tabLigneCCAM['NPI'] = $res['ilp'][$i].'B' ;
	$tabLigneCCAM['DAT_EXAM'] = $dte.' '.$hme ;
	$tabLigneCCAM['NOM_PATRO'] = $res['nom'][$i] ;
	$tabLigneCCAM['PRENOM'] = $res['prenom'][$i];
	$tabLigneCCAM['MARITAL'] = '';
	$tabLigneCCAM['SEXE'] =  $res['sexe'][$i]=='M'?'H':'F';
	$tabLigneCCAM['DDN'] =  $dtn;
	$tabLigneCCAM['UF_EXEC'] =  $res['uf'][$i] ;
	$tabLigneCCAM['ACTES'] = implode(' ',clExportAphm::getTabListeCodesCCAM ($i,$jj -> tabExport));
	$tabLigneCCAM['DEST_SOUHA'] =  $res['dest_souhaitee'][$i];
	$tabLigneCCAM['DEST_ATTEN'] = $res['dest_attendue'][$i] ;
	$tabLigneCCAM['DUR_PASSAGE'] = $strDureePassage;
	$tabLigneCCAM['CODE_GRAV'] = $res['code_gravite'][$i];
	
	
	
	$tabLigneNGAP['NUMSEJ'] = $res['nsej'][$i].'B' ;
	$tabLigneNGAP['NOM_PATRO'] = $res['nom'][$i] ;
	$tabLigneNGAP['PRENOM'] = $res['prenom'][$i];
	$tabLigneNGAP['DAT_EXAM'] = $dte.' '.$hme ;
	$tabLigneNGAP['NOM_MARITAL'] = '';
	$tabLigneNGAP['DATE_PASSAGE'] = $dte.' '.$hme;
	$tabLigneNGAP['MAJORATIONS_EVENTUELLES'] = clExportAphm::getMajoration($examen,$naissance) ;
	for($cmptActe = 1; isset($tabActesNGAP[$cmptActe-1]); $cmptActe++ ) {
		$nbMaxActesNGAP = max($nbMaxActesNGAP,$cmptActe);
		$tabLigneNGAP['ACTE_'.$cmptActe]=$tabActesNGAP[$cmptActe-1];
	}
	
	$cmptActe--;

 	if($nbMaxActesNGAP == $cmptActe)
		$tabTypeNGAP = $tabLigneNGAP ;
	
	$outputLignesCCAM[]=implode("\t",$tabLigneCCAM);
	$outputLignesNGAP[]=implode("\t",$tabLigneNGAP);
   }
   
   $headerCCAM = array();
   if(isset($tabLigneCCAM)) 
   {
   	foreach($tabLigneCCAM as $k=>$v) {
   		$headerCCAM[] = $k ;
   	}
   }
   $headerNGAP = array();
   	foreach($tabTypeNGAP as $k=>$v)
   		$headerNGAP[] = $k ;

   
   
   
   $ficCCAM= implode("\t",$headerCCAM)."\n".implode("\n",$outputLignesCCAM);
   $ficNGAP= implode("\t",$headerNGAP)."\n".implode("\n",$outputLignesNGAP);

	// Calcul du nom du fichier temporaire.
	$nomfic = "exportCCAM".date ( "YmdHis" ).".xls" ;
	$nomfic2 = "exportNGAP".date ( "YmdHis" ).".xls" ;
    // Création, écriture et fermeture du fichier.
      $FIC = fopen ( URLCACHE.$nomfic, "w" ) ;
      $FIC2 = fopen ( URLCACHE.$nomfic2, "w" ) ;
      fwrite ( $FIC, $ficCCAM ) ;
      fwrite ( $FIC2, $ficNGAP ) ;
      fclose ( $FIC ) ;
       fclose ( $FIC2 ) ;
      // Calcul du lien vers ce fichier.
      $mod -> MxUrl  ( "donnees.lienExport", URLCACHEWEB.$nomfic ) ;    
       $mod -> MxUrl  ( "donnees.lienExport2", URLCACHEWEB.$nomfic2 ) ;
      // On purge le répertoire temporaire de tous les fichiers qui ont plus de deux heures.
      $poub = new clPoubelle ( URLCACHE ) ;
      $poub -> purgerRepertoire ( "2" ) ;
      $isExport = true ;
    }
    // Variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
    // On retourne le code HTML généré par le template.
     $this->af .= $mod -> MxWrite ( "1" ) ;
     
  }



  static function getTabListeCodesCCAM($i,$tabExportCcam) {
  	if( ! is_array($tabExportCcam))
  		return array();
	$tabExport=$tabExportCcam;
	$ret = array();
	reset($tabExport);
	while (list($key,$val)=each($tabExport)){
		if($val[$i] && ereg('CCAM',$key))
  		$ret[]=substr($val[$i],0,7);
	}
	return $ret;
  }
  
  static function getTabListeDiagsCCAM($i,$tabExportCcam) {
  	if( ! is_array($tabExportCcam))
  		return array();
	$tabExport=$tabExportCcam;
	$ret = array();
	reset($tabExport);
	while (list($key,$val)=each($tabExport)){
		if($val[$i] && ereg('DIAG',$key)) 
		{
  			$diag=substr($diag,0,7);
			$reg = array();
			ereg('^([^-]+)-.*$',$diag,$reg);
			$ret[] = $reg[1];
		}
	}
	//eko($ret);
	return $ret;
  }
  
  static function getTabListeCodesNGAP($i,$tabExportCcam) {
  	if( ! is_array($tabExportCcam))
  		return array();
	$tabExport=$tabExportCcam;
	$ret = array();
	reset($tabExport);
	while (list($key,$val)=each($tabExport)){
		if($val[$i] && ereg('NGAP',$key)) {
			$reg = array();
			if(ereg('^.*-([A-Z]+) ([0-9.]+)$',$val[$i],$reg)) {
				// if($reg[2]=='1') //on récupere que les facturables
					$ret[] = $reg[2].' '.$reg[1]; 
			} else {
				// eko("probleme: contenu non intégré: ".$val[$i]);	
			}	
		}
	}
	return $ret;
  }


static function getMajoration(clDate $datePassage , clDate $dateNaissance) {
	$tabMajorations = array();
	$today = new clDate();
	if($today->getDifference($dateNaissance) < 3600*24*365.25*2 )
		$tabMajorations[]='MNO';
	if($datePassage->getHours() < 6 && $datePassage->getHours() >= 0)
		$tabMajorations[] = 'MDN';
	if( ( $datePassage->getHours() >= 20)
		OR
		( $datePassage->getHours() < 8 && $datePassage->getHours() >= 6) )
		{
		$tabMajorations[] = 'MDI';
		}
	if($dateNaissance)
	return 	implode(',',$tabMajorations) ;
}


  // Renvoie l'affichage généré par la classe.
  function getAffichage ( ) {
    
    return $this->af ;
  }
}

?>