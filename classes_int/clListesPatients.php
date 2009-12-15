<?php

// Titre  : Classe ListesPatients
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 03 Mars 2005

// Description : 
// Gestion de l'affichage des différentes listes de patients :
// Patients présents, patients sortis...

class clListesPatients {

  // Attributs de la classe ListesPatients
  // Contient l'affichage généré par la classe.
  private $af ;
  private $afbulles ;
  // Type de liste (Presents, UHCD, Attendus, Sortis).
  private $type ;
  // Table sur laquelle on doit requêter.
  private $table ;
  // Filtre de recherche dans la partie patients Sortis.
  private $filtre ;

  // Constructeur de la classe.
  function __construct ( $type, $table='', $filtre='' ) {
    // Récupération dy type de liste (Presents, UHCD, Attendus ou Sortis).
    $this->type = $type ;
    // Nom de la table à requêter.
    if ( $table ) $this->table = $table ;
    else {
      switch ( $type ) {
      case 'Presents':
		$this->table = PPRESENTS ;
	  break ;
      case 'UHCD':
		$this->table = PPRESENTS ;
	  break ;
      case 'Sortis':
		$this->table = PSORTIS ;
	  break ;
      case 'Attendus':
		$this->table = PATTENDUS ;
	  break ;
	  case 'Pédiatrie':
		$this->table = PPRESENTS ;
	  break ;
	  case 'Equipe':
	  	$this->table = PPRESENTS ;
	  	$this->equipe = $filtre ;
	  	$this->type= "Presents" ;
	  break ;
      }
    }
    // Génération de la liste.
    $this->genListe ( ) ;
  }

  // Génération des listes de patients.
  function genListe ( ) {
    global $session ;
    global $options ;
    // On regarde si on veut basculer un patient attendus en patient présent.
    if ( ( $session->getNavi ( 1 ) == "basculerPatient" ) AND $session->getDroit ( "Liste_Attendus", "m" ) ) {
      $this -> attenduToPresent ( ) ;
    }
    // On regarde si on veut supprimer un patient attendu de la liste.
    if ( ( $session->getNavi ( 1 ) == "delPatient" ) AND  $session->getDroit ( "Liste_Attendus", "d" ) ) {
      $this -> delPatient ( ) ;
    }
    if ( $_POST['Annuler'] OR $_POST['Annuler_x'] ) {
        header ( 'Location:'.URLNAVI.$session->genNavi($session->getNavi(0))) ;
    }
	if ( ( $_POST['Ajouter'] OR $_POST['Ajouter_x'] ) AND $this -> type == "Attendus" ) {
        header ( 'Location:'.URLNAVI.$session->genNavi($session->getNavi(0),'addPatientAttendu')) ;
    }
	if ( ( $_POST['Ajouter'] OR $_POST['Ajouter_x'] ) ) {
        header ( 'Location:'.URLNAVI.$session->genNavi($session->getNavi(0),'addPatientPresent')) ;
    }

    // On vérifie si l'ajout manuel d'un patient attendu est demandé.
    if ( ( $_POST['Ajouter'] OR $_POST['Ajouter_x'] OR ( $session -> getNavi ( 1 ) == "addPatientAttendu" AND ! $_POST['Annuler'] AND ! $_POST['Annuler_x'] ) ) AND ( ( $session->getDroit ( "Liste_".$this->type, "w" ) AND $options -> getOption ( "AjoutManuel" ) ) OR ( $session->getDroit ( "Liste_".$this->type, "w" ) AND $this -> type == "Attendus" ) ) ) {
      if ( $this -> type == "Attendus" )
	$this->addPatientAttendu ( ) ;
    }
   // On vérifie si l'ajout manuel d'un patient présent est demandé.
    if ( ( $_POST['Ajouter'] OR $_POST['Ajouter_x'] OR ( $session -> getNavi ( 1 ) == "addPatientPresent" AND ! $_POST['Annuler'] AND ! $_POST['Annuler_x'] ) ) AND ( ( $session->getDroit ( "Liste_".$this->type, "w" ) AND $options -> getOption ( "AjoutManuel" ) ) OR ( $session->getDroit ( "Liste_".$this->type, "w" ) AND $this -> type == "Presents" ) ) ) {
      if ( $this -> type == "Presents" OR $this -> type == "UHCD" )
	$this->addPatientPresent ( ) ;
    }
    // On vérifie si une modification de patient est demandée.
    if ( $session -> getNavi ( 1 ) == "modPatientAttendu" AND $session->getDroit ( "Liste_".$this->type, "m" ) AND ! $_POST['Annuler'] AND ! $_POST['Annuler_x'] ) {
      $this->modPatientAttendu ( ) ;
    }
    // On vérifie si le retour d'un patient est en cours.
    if ( $_POST['RetourPatient'] AND $session->getDroit ( "Liste_".$this->type, "m" ) ) {
      $this->retourPatient ( ) ;
    }

    $tmp = "RIEN" ;
    // On regarde si on veut voir la fiche d'un patient.
    if ( $session -> getNavi ( 1 ) == "FichePatient" ) {
      $fichePatient = new clFichePatient ( $this->type, $this->table, $session -> getNavi ( 2 ) ) ;
      $tmp = $fichePatient -> getAffichage ( ) ;
      if ( $tmp != "RIEN" ) $this->af .= $tmp ;
      // Sinon, on génère la liste de tous les patients.
    } 
    if ( $tmp == "SORTIE" ) header ( 'Location:'.URLNAVI.$session->genNavi($session->getNavi(0) ) ) ;
    if ( $tmp == "RIEN" ) {
      // Vérification du droit de lecture sur la liste de patients.
      if ( $session->getDroit ( "Liste_".$this->type, "r" ) ) {
	if ( $this->type == "Presents" OR $this->type == "UHCD" OR $this->type == "Attendus" OR $this->type == "Pédiatrie" ) {
	  $this->genInformationsPassages ( ) ;
	} else {
	  $this->filtreRecherche ( ) ;
	}
	$this->genListePatient ( 1 ) ;
	if ( $this->type == "Presents" ) $this->genListePatient ( 2 ) ;
      }
    }
  }

  // On crée une nouvelle fiche patient lors de son retour le jour même.
  function retourPatient ( ) {
    global $options ;
    global $session ;
    //$this->af .= "Ce patient id=".$session->getNavi(1)." est de retour, c'est pas beau ça ?" ;
    $this->patient = new clPatient ( $session -> getNavi ( 1 ), "Sortis" ) ;
    $this->patient -> retourPatient ( ) ;
  }

  // Cette fonction gère et génère le filtre de recherche des patients sortis.
  function filtreRecherche ( ) {
    global $session ;
    // Chargement du template du formulaire du filtre.
    $mod = new ModeliXe ( "FiltreRecherche.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Initialisation des variables.
    if ( isset ( $_POST['valeurILP'] ) )        $_SESSION['valeurILP']        = $_POST['valeurILP'] ;
    if ( isset ( $_POST['valeurSej'] ) )        $_SESSION['valeurSej']        = $_POST['valeurSej'] ;
    if ( isset ( $_POST['valeurIDP'] ) )        $_SESSION['valeurIDP']        = $_POST['valeurIDP'] ;
    if ( isset ( $_POST['valeurNom'] ) )        $_SESSION['valeurNom']        = $_POST['valeurNom'] ;
    if ( isset ( $_POST['valeurDate'] ) )       $_SESSION['valeurDate']       = $_POST['valeurDate'] ;
    if ( isset ( $_POST['valeurPrenom'] ) )     $_SESSION['valeurPrenom']     = $_POST['valeurPrenom'] ;
    if ( isset ( $_POST['valeurMedecin'] ) )    $_SESSION['valeurMedecin']    = $_POST['valeurMedecin'] ;
    if ( isset ( $_POST['valeurIDE'] ) )        $_SESSION['valeurIDE']        = $_POST['valeurIDE'] ;
    if ( isset ( $_POST['valeurDiag'] ) )       $_SESSION['valeurDiag']       = $_POST['valeurDiag'] ;
    if ( isset ( $_POST['valeurDateAdm'] ) )    $_SESSION['valeurDateAdm']    = $_POST['valeurDateAdm'] ;
    if ( isset ( $_POST['valeurDestConf'] ) )    $_SESSION['valeurDestConf']    = $_POST['valeurDestConf'] ;
    if ( isset ( $_POST['valeurFormulaire'] ) ) $_SESSION['valeurFormulaire'] = $_POST['valeurFormulaire'] ;
    //else if ( ! isset ( $_SESSION['valeurDate'] ) ) $_SESSION['valeurDate'] = date ( "d-m-Y" ) ;
    // Génération des champs du formulaire.
    $mod -> MxFormField ( "valeurILP", "text", "valeurILP", stripslashes($_SESSION['valeurILP']) ) ;
    $mod -> MxFormField ( "valeurSej", "text", "valeurSej", stripslashes($_SESSION['valeurSej']) ) ;
    $mod -> MxFormField ( "valeurIDP", "text", "valeurIDP", stripslashes($_SESSION['valeurIDP']) ) ;
    $mod -> MxFormField ( "valeurNom", "text", "valeurNom", stripslashes($_SESSION['valeurNom']) ) ;
    $mod -> MxFormField ( "valeurDate", "text", "valeurDate", stripslashes($_SESSION['valeurDate']) ) ;
    $mod -> MxFormField ( "valeurPrenom", "text", "valeurPrenom", stripslashes($_SESSION['valeurPrenom']) ) ;
    $listeGen = new clListesGenerales ( "recup" ) ;
    $lMedecins = $listeGen -> getListeItems ( "Medecins", "1", '', '', "1" ) ;
    $mod -> MxSelect ( 'valeurMedecin', 'valeurMedecin', stripslashes($_SESSION['valeurMedecin']), $lMedecins ) ;
    $lIDE = $listeGen -> getListeItems ( "I.D.E.", "1", '', '', "1" ) ;
    $mod -> MxSelect ( 'valeurIDE', 'valeurIDE', stripslashes($_SESSION['valeurIDE']), $lIDE ) ;  
    $lDestConf = $listeGen -> getListeItems ( "Destinations attendues", "1", '', '', "1" ) ;  
    $mod -> MxSelect ( 'valeurDestConf', 'valeurDestConf', stripslashes($_SESSION['valeurDestConf']), $lDestConf ) ;  
    //$mod -> MxFormField ( "valeurDiag", "text", "valeurDiag", stripslashes($_SESSION['valeurDiag']) ) ;
    $mod -> MxFormField ( "valeurDateAdm", "text", "valeurDateAdm", stripslashes($_SESSION['valeurDateAdm']) ) ;
    // Variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session -> getNavi ( 0 ), "filtre" ) ) ;
    // Fabrication du filtre pour la requête.
    $dateVal = new clDate ( $_SESSION['valeurDate'] ) ;
    $dateAdmVal = new clDate ( $_SESSION['valeurDateAdm'] ) ;
    
    $lFormulaires = $listeGen -> getListeItemsV2 ( "Formulaires", "1", '', 'libre', "1" ) ;
    $mod -> MxSelect ( 'valeurFormulaire', 'valeurFormulaire', stripslashes($_SESSION['valeurFormulaire']), $lFormulaires ) ;
    
    /*
    if ( $_SESSION['valeurNom'] AND $_SESSION['valeurDate'] ) {
      $this->filtre = "WHERE nom LIKE '".$_SESSION['valeurNom']."%' AND dt_sortie LIKE '".$dateVal->getDate("Y-m-d")."%'" ;
    } elseif ( $_SESSION['valeurNom'] ) {
      $this->filtre = "WHERE nom LIKE '".$_SESSION['valeurNom']."%'" ;
    } elseif ( $_SESSION['valeurDate'] ) {
      $this->filtre = "WHERE dt_sortie LIKE '".$dateVal->getDate("Y-m-d")."%'" ;
    } else {
      $this->message = "<br /><br /><font color=\"red\">Aucun filtre n'a été saisi. Aucun résultat ne sera renvoyé.</font>" ;
      $this->filtre = "WHERE idpatient<0" ;
    } 
    */
    $this->filtre  = "WHERE " ;
    if ( $_SESSION['valeurILP'] ) $this->filtre .= " (ilp='".$_SESSION['valeurILP']."' OR idu='".$_SESSION['valeurILP']."') " ;
	if ( $_SESSION['valeurSej'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " nsej='".$_SESSION['valeurSej']."' " ;
		else $this->filtre .= " AND nsej='".$_SESSION['valeurSej']."' " ;
	}
	if ( $_SESSION['valeurIDP'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " idpatient='".$_SESSION['valeurIDP']."' " ;
		else $this->filtre .= " AND idpatient='".$_SESSION['valeurIDP']."' " ;
	}
	if ( $_SESSION['valeurNom'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " (nom LIKE '".$_SESSION['valeurNom']."%' OR prenom LIKE '".$_SESSION['valeurNom']."%') " ;
		else $this->filtre .= " AND (nom LIKE '".$_SESSION['valeurNom']."%' OR prenom LIKE '".$_SESSION['valeurNom']."%') " ;
	}
	if ( $_SESSION['valeurDate'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " dt_sortie LIKE '".$dateVal->getDate("Y-m-d")."%' " ;
		else $this->filtre .= " AND dt_sortie LIKE '".$dateVal->getDate("Y-m-d")."%' " ;
	}
	if ( $_SESSION['valeurPrenom'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " prenom LIKE '".$_SESSION['valeurPrenom']."%' " ;
		else $this->filtre .= " AND prenom LIKE '".$_SESSION['valeurPrenom']."%' " ;
	}
	if ( $_SESSION['valeurMedecin'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " medecin_urgences LIKE '".$_SESSION['valeurMedecin']."%' " ;
		else $this->filtre .= " AND medecin_urgences LIKE '".$_SESSION['valeurMedecin']."%' " ;
	}
	if ( $_SESSION['valeurIDE'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " ide LIKE '".$_SESSION['valeurIDE']."%' " ;
		else $this->filtre .= " AND ide LIKE '".$_SESSION['valeurIDE']."%' " ;
	}
	if ( $_SESSION['valeurDiag'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " diagnostic_libelle LIKE '".$_SESSION['valeurDiag']."%' " ;
		else $this->filtre .= " AND diagnostic_libelle LIKE '".$_SESSION['valeurDiag']."%' " ;
	}
	if ( $_SESSION['valeurDateAdm'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " dt_admission LIKE '".$dateAdmVal->getDate("Y-m-d")."%' " ;
		else $this->filtre .= " AND dt_admission LIKE '".$dateAdmVal->getDate("Y-m-d")."%' " ;
	}
	if ( $_SESSION['valeurDestConf'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " dest_attendue='".$_SESSION['valeurDestConf']."' " ;
		else $this->filtre .= " AND dest_attendue='".$_SESSION['valeurDestConf']."' " ;
	}
	if ( $_SESSION['valeurFormulaire'] ) {
		if ( $this->filtre == "WHERE " ) $this->filtre .= " idformx='".$_SESSION['valeurFormulaire']."' AND ids=idu " ;
		else $this->filtre .= " AND idformx='".$_SESSION['valeurFormulaire']."' AND ids=idu ";
	}
  if ( $this->filtre == "WHERE " ) {
   	$this->message = "<br /><br /><font color=\"red\">Aucun filtre n'a été saisi. Aucun résultat ne sera renvoyé.</font>" ;
    $this->filtre = "WHERE idpatient<0" ;
    }
      
      eko ( $this->filtre ) ;
    $mod -> MxText ( "message", $this->message ) ;
    // Récupération du code HTML généré.
    $this->af .= $mod -> MxWrite ( "1" ) ;
  }

  // Génération de la liste avec ListMaker
  function genListePatient ( $etape ) {
    global $session ;
    global $options ;
    $uhcd = $options -> getOption ( "FiltreSalleUHCD" ) ;
    $pedi = $options -> getOption ( "FiltreSalleSup" ) ;
    // Préparation de la requête.
    $req = new clResultQuery ;
    $param['table'] = $this->table ;
    if ( $this->equipe ) $eq = " AND traumato LIKE '$this->equipe%' " ; else $eq = '' ;
    // Cas des patients normaux.
    if ( $etape == 1 ) {
      $list = new ListMaker ( "template/ListePatients.html" ) ;
      // Changement de la requête des couleurs de la liste en fonction de la liste affichée (présents, UHCD, attendus ou sortis).
      // Liste des patients présents.
      if ( $this->type == "Presents" ) {
		//$param['cw'] = " p, ".BASEXHAM.".listes l WHERE ((p.dt_examen!='0000-00-00 00:00:00'  AND l.nomliste='Salles d\'examens' AND p.salle_examen NOT LIKE 'UHCD%' AND (p.salle_examen=l.nomitem)) OR ( p.dt_examen!='0000-00-00 00:00:00'  AND p.salle_examen=l.nomitem AND p.salle_examen NOT LIKE 'UHCD%' ) )  AND l.idapplication=".IDAPPLICATION." ORDER BY l.rang" ;
		// Ancienne version avec classement des salles en fonction de leur rang : problème de salle invisible
		//if ( $pedi )
		//	$param['cw'] = " p, ".BASEXHAM.".listes l WHERE ((p.dt_examen!='0000-00-00 00:00:00'  AND l.nomliste='Salles d\'examens' " .
		//			"AND p.salle_examen NOT LIKE '$uhcd%' AND p.salle_examen NOT LIKE '$pedi%' AND (p.salle_examen=l.nomitem)) OR ( p.dt_examen!='0000-00-00 00:00:00'  " .
		//			"AND p.salle_examen=l.nomitem AND p.salle_examen NOT LIKE '$uhcd%' AND p.salle_examen NOT LIKE '$pedi%' ) )  " .
		//			"AND l.idapplication=".IDAPPLICATION." ORDER BY l.rang" ;
		//else
		//	$param['cw'] = " p, ".BASEXHAM.".listes l WHERE ((p.dt_examen!='0000-00-00 00:00:00'  AND l.nomliste='Salles d\'examens' AND p.salle_examen NOT LIKE '$uhcd%' AND (p.salle_examen=l.nomitem)) OR ( p.dt_examen!='0000-00-00 00:00:00'  AND p.salle_examen=l.nomitem AND p.salle_examen NOT LIKE '$uhcd%' ) )  AND l.idapplication=".IDAPPLICATION." ORDER BY l.rang" ;
		
      	if ( $eq ) {
            $param['cw'] = " WHERE dt_examen!='0000-00-00 00:00:00' AND traumato LIKE '$this->equipe%' AND salle_examen NOT LIKE '$uhcd%'" ;
        } elseif ( $pedi )
			$param['cw'] = " p WHERE ((p.dt_examen!='0000-00-00 00:00:00' AND p.salle_examen NOT LIKE '$uhcd%' AND p.salle_examen NOT LIKE '$pedi%') OR ( p.dt_examen!='0000-00-00 00:00:00'  " .
					"AND p.salle_examen NOT LIKE '$uhcd%' AND p.salle_examen NOT LIKE '$pedi%' ) )".$eq ;
		else
			$param['cw'] = " p WHERE ((p.dt_examen!='0000-00-00 00:00:00' AND p.salle_examen NOT LIKE '$uhcd%') OR ( p.dt_examen!='0000-00-00 00:00:00'  AND p.salle_examen NOT LIKE '$uhcd%'))".$eq ;
		$navi = $session -> genNavi ( $session -> getNavi ( 0 ) ) ;
		$list -> addUserVar ( 'navi', $navi ) ;
		$list -> addUrlVar  ( 'navi', $navi ) ;
		$list -> setAlternateColor ( "vuspaire", "vusimpaire" ) ;
		$list -> setSortColumn ( 'col1', 'Patient',        'nomD' ) ;
    	$list -> setSortColumn ( 'col2', 'Age',            'ageD' ) ;
    	$list -> setSortColumn ( 'col3', 'Mode adm.',      'modeAdm' ) ;
    	$list -> setSortColumn ( 'col4', 'Arrivée',        'arriveeD' ) ;
    	$list -> setSortColumn ( 'col5', 'Soignants',      'soignants' ) ;
    	$list -> setSortColumn ( 'col6', 'Salle',          'salle' ) ;
    	$list -> setSortColumn ( 'col7', 'Motif',          'motif' ) ;
    	$list -> setSortColumn ( 'col8', 'Code',           'code' ) ;
    	$list -> setSortColumn ( 'col9', 'Dest. Souhait.', 'destSouhaitee' ) ;
    	$list -> setSortColumn ( 'col0', 'Dest. Confirmée',     'destAttendue' ) ;
		switch ( $options->getOption ( "ClassementPatients" ) ) {
			case 'Salle':
				$list -> setdefaultSort ( 'col6' ) ;
			break ;
			case 'Arrivée':
				$list -> setdefaultSort ( 'col4' ) ;
			break ;
			case 'Nom':
				$list -> setdefaultSort ( 'col1' ) ;
			break ;
		}
		
		/********/
		/* Pour gérer un problème de style. */
    $list -> addUserVar ( '{fin_table}', "</table>" ) ;
    /********/
    
      	// Liste des patients sortis.
      } elseif ( $this->type == "Sortis" ) {
		$list = new ListMaker ( "template/ListePatientsSortis.html" ) ;
		$navi = $session -> genNavi ( $session -> getNavi ( 0 ) ) ;
		$list -> addUserVar ( 'navi', $navi ) ;
		$list -> addUrlVar  ( 'navi', $navi ) ;
		$list -> setSortColumn ( 'col1', 'Patient',         'nomD' ) ;
    	$list -> setSortColumn ( 'col2', 'Arrivée',         'arriveeD' ) ;
    	$list -> setSortColumn ( 'col3', 'Examen',          'examenD' ) ;
    	$list -> setSortColumn ( 'col4', 'Sortie',          'sortieD' ) ;
    	$list -> setSortColumn ( 'col5', 'Dest. souhaitée', 'destSouhaitee' ) ;
    	$list -> setSortColumn ( 'col6', 'Dest. confirmée', 'destAttendue' ) ;
    	$list -> setSortColumn ( 'col7', 'Médecin',         'medecin' ) ;
    	$list -> setSortColumn ( 'col8', 'IDE',             'ide' ) ;
    	$list -> setSortColumn ( 'col9', 'UF',             	'uf' ) ;
    	$list -> setdefaultSort ( '' ) ;
		$param['cw'] = $this->filtre." ORDER BY nom ASC" ;
		$list -> setAlternateColor ( "sortispaire", "sortisimpaire" ) ;
      	// Liste des patients attendus.
    //if ( $this->filtre2 != "" )
      } elseif ( $this->type == "Attendus" ) {
		$list = new ListMaker ( "template/ListePatientsAttendus.html" ) ;
		$param['cw'] = "ORDER BY date" ;
		$list -> setAlternateColor ( "attenduspaire", "attendusimpaire" ) ;
		
    	// Liste des patients UHCD.
      } elseif ( $this->type == "UHCD" ) {
		//$param['cw'] = " p, ".BASEXHAM.".listes l WHERE p.salle_examen LIKE '$uhcd%' AND p.salle_examen=l.nomitem AND l.idapplication=".IDAPPLICATION." AND l.nomliste='Salles d\'examens' ORDER BY l.rang" ;
		$param['cw'] = " p WHERE p.salle_examen LIKE '$uhcd%'" ;
		$list -> setAlternateColor ( "uhcdpaire", "uhcdimpaire" ) ;
		$navi = $session -> genNavi ( $session -> getNavi ( 0 ) ) ;
		$list -> addUserVar ( 'navi', $navi ) ;
		$list -> addUrlVar  ( 'navi', $navi ) ;
		$list -> setSortColumn ( 'col1', 'Patient',        'nomD' ) ;
    	$list -> setSortColumn ( 'col2', 'Age',            'ageD' ) ;
    	$list -> setSortColumn ( 'col3', 'Mode adm.',      'modeAdm' ) ;
    	$list -> setSortColumn ( 'col4', 'Arrivée',        'arriveeD' ) ;
    	$list -> setSortColumn ( 'col5', 'Soignants',      'soignants' ) ;
    	$list -> setSortColumn ( 'col6', 'Salle',          'salle' ) ;
    	$list -> setSortColumn ( 'col7', 'Motif',          'motif' ) ;
    	$list -> setSortColumn ( 'col8', 'Code',           'code' ) ;
    	$list -> setSortColumn ( 'col9', 'Dest. Souhait.', 'destSouhaitee' ) ;
    	$list -> setSortColumn ( 'col0', 'Dest. Confirmée',     'destAttendue' ) ;
		switch ( $options->getOption ( "ClassementPatients" ) ) {
			case 'Salle':
				$list -> setdefaultSort ( 'col6' ) ;
			break ;
			case 'Arrivée':
				$list -> setdefaultSort ( 'col4' ) ;
			break ;
			case 'Nom':
				$list -> setdefaultSort ( 'col1' ) ;
			break ;
		}
		$list -> addUserVar ( 'fin_table', "</table>" ) ;
		  
		
      } else {
      	//if ( $pedi )
      	//	$param['cw'] = " p, ".BASEXHAM.".listes l WHERE p.salle_examen LIKE '$pedi%' AND p.salle_examen=l.nomitem AND l.idapplication=".IDAPPLICATION." AND l.nomliste='Salles d\'examens' ORDER BY l.rang" ;
      	//else
      	//	$param['cw'] = " p, ".BASEXHAM.".listes l WHERE p.salle_examen LIKE 'AUCUNESALLE%' AND p.salle_examen=l.nomitem AND l.idapplication=".IDAPPLICATION." AND l.nomliste='Salles d\'examens' ORDER BY l.rang" ;
      	if ( $this->equipe ) {
      		$param['cw'] = "p WHERE p.traumato LIKE '$this->equipe%'" ;
      	} else {
      		if ( $pedi )
      			$param['cw'] = " p WHERE p.salle_examen LIKE '$pedi%'" ;
      		else
      			$param['cw'] = " p WHERE p.salle_examen LIKE 'AUCUNESALLE%'" ;
      	}
		$list -> setAlternateColor ( "uhcdpaire", "uhcdimpaire" ) ;
		$navi = $session -> genNavi ( $session -> getNavi ( 0 ) ) ;
		$list -> addUserVar ( 'navi', $navi ) ;
		$list -> addUrlVar  ( 'navi', $navi ) ;
		$list -> setSortColumn ( 'col1', 'Patient',        'nomD' ) ;
    	$list -> setSortColumn ( 'col2', 'Age',            'ageD' ) ;
    	$list -> setSortColumn ( 'col3', 'Mode adm.',      'modeAdm' ) ;
    	$list -> setSortColumn ( 'col4', 'Arrivée',        'arriveeD' ) ;
    	$list -> setSortColumn ( 'col5', 'Soignants',      'soignants' ) ;
    	$list -> setSortColumn ( 'col6', 'Salle',          'salle' ) ;
    	$list -> setSortColumn ( 'col7', 'Motif',          'motif' ) ;
    	$list -> setSortColumn ( 'col8', 'Code',           'code' ) ;
    	$list -> setSortColumn ( 'col9', 'Dest. Souhait.', 'destSouhaitee' ) ;
    	$list -> setSortColumn ( 'col0', 'Dest. Confirmée', 'destAttendue' ) ;
		switch ( $options->getOption ( "ClassementPatients" ) ) {
			case 'Salle':
				$list -> setdefaultSort ( 'col6' ) ;
			break ;
			case 'Arrivée':
				$list -> setdefaultSort ( 'col4' ) ;
			break ;
			case 'Nom':
				$list -> setdefaultSort ( 'col1' ) ;
			break ;
		}
		
		$list -> addUserVar ( 'fin_table', "</table>" ) ;
      }
      
      
      // Exécution de la requête.
      if ( $this->type == "Sortis" ) {
        if ( eregi ("idformx",$this->filtre) ) {
          $tab["table"] = BDD.".".PSORTIS.",".FX_BDD.".".TABLEFORMX;
          $tab["cw"] = $param["cw"];
          $res = $req -> Execute ( "Fichier", "getPatients", $tab, "ResultQuery" ) ;
          //eko($res);
        }
        else
          $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
          //eko ( $res['INDIC_SVC'] ) ;
      }
      else
        $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      //eko ( $res['INDIC_SVC'] ) ;
      //eko($res);
      //eko ( $res['INDIC_SVC'] ) ;
      if ( DEBUGLISTESPATIENTS AND $session->getDroit ( "Liste_".$this->type, "a" ) ) newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ;
      // Cas des patients non-vus.
    } else {
      $list = new ListMaker ( "template/ListePatientsBis.html" ) ;
      if ( $eq ) {
          $param['cw'] = "WHERE dt_examen='0000-00-00 00:00:00' AND traumato LIKE '$this->equipe%' " ;
      } elseif ( $pedi )
      	$param['cw'] = "WHERE dt_examen='0000-00-00 00:00:00' AND salle_examen NOT LIKE '$uhcd%' AND salle_examen NOT LIKE '$pedi%'".$eq.' ORDER BY dt_admission' ;
      else
      	$param['cw'] = "WHERE dt_examen='0000-00-00 00:00:00' AND salle_examen NOT LIKE '$uhcd%'".$eq.' ORDER BY dt_admission' ;
      $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    }
    // eko ( $res['INDIC_SVC'] ) ;
    global $tabID ;
    if ( ! is_array ( $tabID ) ) $tabID = array ( ) ;
    // Parcours de la liste des patients récupérés par la requête.
    for ( $i = 0 ; isset ( $res['idpatient'][$i] ) ; $i++ ) {
      
      if ( ! in_array ( $res['idpatient'][$i], $tabID ) ){
      // Calcul du sexe de la personne... (?!).
      switch ( $res['sexe'][$i] ) {
      case 'M': $img = URLIMG."homme.png" ; break ;
      case 'F': $img = URLIMG."femme.png" ; break ;
      default: $img = URLIMG."Indefini.png" ; break ;
      }
      $item['sexe'] = "<img src=\"$img\" alt=\"".$res['sexe'][$i]."\" />" ;
      // Calcul de l'âge.
      $date = new clDate ( $res['dt_naissance'][$i] ) ;
      $age = new clDuree ( $date->getTimestamp ( ) ) ;
      $str = $age -> getAgePrecis ( $date->getTimestamp ( ) ) ;
      if ( $res['dt_naissance'][$i] != "0000-00-00 00:00:00" ) {
		$item['age'] = $str ;
		$item['ageD'] = $date->getTimestamp ( ) ;
      } else {
		$item['age'] = VIDEDEFAUT ;
		$item['ageD'] = VIDEDEFAUT ;
      }
      // Destination souhaitée.
      if ( $res['dest_souhaitee'][$i] ) $item['destSouhaitee'] = $res['dest_souhaitee'][$i] ;
      else $item['destSouhaitee'] = VIDEDEFAUT ;
      // Destination attendue.
      if ( $res['dest_attendue'][$i] ) $item['destAttendue'] = $res['dest_attendue'][$i] ;
      else $item['destAttendue'] = VIDEDEFAUT ;
      
      //////////////////////////////////// Patients Attendus ////////////////////////////////////////
      if ( $this->type == "Attendus" ) {
	$item['urlpatient'] = URLNAVI.$session->genNavi ( $session->getNavi(0), "modPatientAttendu", $res['idpatient'][$i] ) ;
	// Concaténation du nom et du prénom.
	$item['patient'] = strtoupper ( $res['nom'][$i] )." ".ucfirst(strtolower($res['prenom'][$i])) ;
	// Heure de création.
	if ( $res['date'][$i] ) {
	  $dateCreation = new clDate ( $res['date'][$i] ) ;
	  $item['date'] =  $dateCreation -> getDate ( "d-m-Y H:i" ) ;
	} else $item['date'] = VIDEDEFAUT ;
	// Gestion de l'âge.
	if ( $res['dt_naissance'][$i] ) {
	  $item['age'] =  $res['dt_naissance'][$i] ;
	} else $item['age'] = VIDEDEFAUT ;
	// Récupération du mode d'admission.
	if ( $res['adresseur'][$i] ) $item['adresseur'] = $res['adresseur'][$i] ;
	else $item['adresseur'] = VIDEDEFAUT ;
	// Médecin et IDE.
	if ( $res['medecin_urgences'][$i] ) $med = "Dr ".$res['medecin_urgences'][$i] ; else $med = VIDEDEFAUT ;
	$item['medecin'] = $med ;
	// Observations.
	if ( $res['observations'][$i] ) $item['observations'] = nl2br ( $res['observations'][$i] ) ;
	else $item['observations'] = VIDEDEFAUT ;
	// Génération du lien de bascule.
	$imgBasculer = "<img src=\"images/basculer.gif\" name=\"Ajouter\" alt=\"Basculer le patient dans les présents\" /></a>" ;
	$imgSupprimer = "<img src=\"images/Supprimer.gif\" name=\"Supprimer\" alt=\"Supprimer le patient attendu\" /></a>" ;
	$imgModifier = "<img src=\"images/modifier2.gif\" name=\"Modifier\" alt=\"Modifier le patient attendu\" /></a>" ;
	// En fonction des droits et des options, nous affichons les boutons d'actions.
	if ( $session->getDroit ( "Liste_".$this->type, "a" ) AND  $options -> getOption ( "AjoutManuel" ) ) {
	  $item['lienBasculer'] = "<a href=\"".URLNAVI.$session->genNavi ( "Liste_Presents", "basculerPatient", $res['idpatient'][$i] )."\">".$imgBasculer ;
	}
	if ( $session->getDroit ( "Liste_".$this->type, "d" ) ) {
	  $item['lienSupprimer'] = "<a href=\"".URLNAVI.$session->genNavi ( $session->getNavi(0), "delPatient", $res['idpatient'][$i] )."\">".$imgSupprimer ;	  
	}
	if ( $session->getDroit ( "Liste_".$this->type, "d" ) ) {
	  $item['lienModifier'] = "<a href=\"".URLNAVI.$session->genNavi ( $session->getNavi(0), "modPatientAttendu", $res['idpatient'][$i] )."\">".$imgModifier ;	  
	}
      //////////////////////////////////// Patients Sortis ////////////////////////////////////////
      } elseif ( $this->type == "Sortis" ) {
	$item['urlpatient'] = URLNAVI.$session->genNavi ( $session->getNavi(0), "FichePatient", $res['idpatient'][$i] ) ;
	// Concaténation du nom et du prénom.
	if ( $session->getDroit ( "Liste_".$this->type, "m" ) )
	$item['patient'] = "<a href=\"".URLNAVI.$session->genNavi ( $session->getNavi(0), "FichePatient", $res['idpatient'][$i] )."\" ".clPatient::genInfoBulle ($res,$i).">".
	  strtoupper ( $res['nom'][$i] )."</a> ".ucfirst(strtolower($res['prenom'][$i])) ;
	else
	  $item['patient'] = strtoupper ( $res['nom'][$i] )." ".ucfirst(strtolower($res['prenom'][$i])) ;
	$item['nomD'] =  strtoupper ( $res['nom'][$i] ) ;
	// Heure d'arrivée.
	if ( $res['dt_admission'][$i] != "0000-00-00 00:00:00" ) {
	  $dateTemp = new clDate ( $res['dt_admission'][$i] ) ;
	  $item['arrivee'] = $dateTemp -> getDate ( "d-m-Y H:i" ) ;
	  $item['arriveeD'] = $dateTemp -> getTimestamp ( ) ;
	} else { $item['arrivee'] = VIDEDEFAUT ; $item['arriveeD'] = VIDEDEFAUT ; }
	// Heure d'examen.
	if ( $res['dt_examen'][$i] != "0000-00-00 00:00:00" ) {
	  $dateTemp = new clDate ( $res['dt_examen'][$i] ) ;
	  $item['examen'] = $dateTemp -> getDate ( "d-m-Y H:i" ) ;
	  $item['examenD'] = $dateTemp -> getTimestamp ( ) ;
	} else { $item['examen'] = VIDEDEFAUT ; $item['examenD'] = VIDEDEFAUT ; }
	// Heure de sortie.
	if ( $res['dt_sortie'][$i] != "0000-00-00 00:00:00" ) {
	  $dateTemp = new clDate ( $res['dt_sortie'][$i] ) ;
	  $item['sortie'] = $dateTemp -> getDate ( "d-m-Y H:i" ) ;
	  $item['sortieD'] = $dateTemp -> getTimestamp ( ) ;
	} else { $item['sortie'] = VIDEDEFAUT ; $item['sortieD'] = VIDEDEFAUT ; }
	// Médecin.
	if ( $res['medecin_urgences'][$i] ) $med = "Dr ".$res['medecin_urgences'][$i] ; else $med = VIDEDEFAUT ;
	$item['medecin'] = $med ;
	// IDE.
	if ( $res['ide'][$i] ) $ide = $res['ide'][$i] ; else $ide = VIDEDEFAUT ;
	$item['ide'] = $ide ;
	// Affichage des informations sur l'UF
	$ufExec = $options->getOption ( 'numUFexec' ) ;
	$ufUHCD = $options->getOption ( 'numUFUHCD' ) ;
	$ufSC   = $options->getOption ( 'numUFSC' ) ;
	$dtUHCD = new clDate ( $res['dt_UHCD'][$i] ) ;
	if ( $res['uf'][$i] == $ufUHCD ) $sup = $res['uf'][$i].' - UHCD<br/>'.($res['dt_UHCD'][$i]!='0000-00-00 00:00:00'?$dtUHCD->getDate ( 'd/m/Y à H:i'):'').'' ;
	elseif ( $res['uf'][$i] == $ufSC ) $sup = $res['uf'][$i].' - Soins Continus' ;
	else $sup = $res['uf'][$i].' - Urgences' ;
	$item['uf'] = $sup ;
	
	if ( $options  -> getOption ( "Sortis Pagination" ) != "Tous" ) $pagination = $options  -> getOption ( "Sortis Pagination" ) ;
      //////////////////////////////////// Patients Présents  ////////////////////////////////////////
      } else {
	$item['urlpatient'] = URLNAVI.$session->genNavi ( $session->getNavi(0), "FichePatient", $res['idpatient'][$i] ) ;
	// Concaténation du nom et du prénom.
	if ( $session->getDroit ( "Liste_".$this->type, "m" ) )
	  	$item['patient'] = "<a href=\"".URLNAVI.$session->genNavi ( $session->getNavi(0), "FichePatient", 
		$res['idpatient'][$i] )."\" ".clPatient::genInfoBulle ($res,$i).">".strtoupper ( $res['nom'][$i] )."</a><br />".ucfirst(strtolower($res['prenom'][$i])) ;
	else
	  $item['patient'] = strtoupper ( $res['nom'][$i] )."<br />".ucfirst(strtolower($res['prenom'][$i])) ;
	$item['nomD'] = strtoupper ( $res['nom'][$i] ) ;
	// Récupération du mode d'admission.
	if ( $res['mode_admission'][$i] ) $item['modeAdm'] = $res['mode_admission'][$i] ;
	else $item['modeAdm'] = VIDEDEFAUT ;
	
	if ( $res['uf'][$i] == $options->getOption ( 'numUFSC') )
		$item['colorSpe'] = 'style="background-color:#E73E01;"' ;
	else $item['colorSpe'] = '' ;
	
	// Heure d'admission.
	if ( $res['dt_admission'][$i] != "0000-00-00 00:00:00" ) {
	  $datead = new clDate ( $res['dt_admission'][$i] ) ;
	  $dateSimple = $datead -> getDate ( "d-m-Y" ) ;
	  $dureead = new clDuree ( ) ;
	  $duree = $dureead -> getDureeCourte ( $datead -> getDatetime ( ) ) ;
	  $item['arrivee'] = $duree."<br />".$dateSimple ;
	  $item['arriveeD'] = $datead -> getTimestamp ( ) ;
	} else { $item['arrivee'] = VIDEDEFAUT ; $item['arriveeD'] = VIDEDEFAUT ; }
	
	// Observations.
	$item['obs'] = "?" ;
	// Médecin et IDE.
	if ( $res['medecin_urgences'][$i] ) $med = "Dr ".$res['medecin_urgences'][$i] ; else $med = VIDEDEFAUT ;
	if ( $res['ide'][$i] ) $ide = "Ide ".$res['ide'][$i] ; else $ide = VIDEDEFAUT ;
	$item['soignants'] = $med."<br />".$ide ;
	// Salle actuelle.
	$radio = clListeRadios::getEtatSalle ( $res['idpatient'][$i], IDAPPLICATION ) ;
	if ( $res['salle_examen'][$i] ) $item['salle'] = $res['salle_examen'][$i]."$radio<br/>".($res['traumato'][$i]?$res['traumato'][$i]:'') ;
	else  $item['salle'] = VIDEDEFAUT."$radio<br/>".($res['traumato'][$i]?$res['traumato'][$i]:'') ;
	// Motif de recours.
	if ( $res['motif_recours'][$i] ) $item['motif'] = $res['motif_recours'][$i] ;
	else $item['motif'] = VIDEDEFAUT ;
	// Code gravité.
	if ( $res['code_gravite'][$i] ) $item['code'] = $res['code_gravite'][$i] ;
	else $item['code'] = VIDEDEFAUT ;
	// Infos visuelles (colonne Observations).
	$dureeMaxSansUHCD = 3600 * $options -> getOption ( "Présents UHCD" ) ;
	$item['obs'] = "" ;
	
	$uhcd = $options->getOption ( "FiltreSalleUHCD" ) ;
	
	if (  $res['dt_admission'][$i] != "0000-00-00 00:00:00" ) {
	  //if ( $options -> getOption ( "ContrainteHuitUF2702" ) AND $dureead -> getSeconds ( ) > $dureeMaxSansUHCD AND $res['uf'][$i] != "2702" AND ! $res['manuel'][$i] )
	  /*
	  if ( $options -> getOption ( "GestionUHCD" ) AND $res['uf'][$i] != $options->getOption ( "numUFUHCD" ) ) {
	  	
	  	$ufExec = $options->getOption ( 'numUFexec' ) ;
		$ufUHCD = $options->getOption ( 'numUFUHCD' ) ;
		$uf     = $options->getOption ( 'numUFexec' ) ;
		$etat   = $res['etatUHCD'][$i] ;
		$oldUF  = $res['uf'][$i] ;
		
		// Calcul du critère CCMU4 ou CCMU5
		if ( $res['code_gravite'][$i] == 4 OR $res['code_gravite'][$i] == 5 )
		$CCMU45 = 1 ; else $CCMU45 = 0 ;
		
		// Calcul du critère sur la durée
		$datead = new clDate ( $res['dt_admission'][$i] ) ;
	  	$dureead = new clDuree ( ) ;
	  	$duree = $dureead -> getDureeCourte ( $datead -> getDatetime ( ) ) ;
	  	$dureeMaxSansUHCD = 3600 * $options -> getOption ( "Présents UHCD" ) ;
	  	$dureeHeure = $options -> getOption ( "Présents UHCD" ) ;
		if ( $dureead -> getSeconds ( ) > $dureeMaxSansUHCD ) $duree = 1 ;
		else $duree = 0 ;
		
		// Calcul du critère sur le CCMU3
		if ( $res['code_gravite'][$i] == 3 ) $CCMU3 = 1 ; else $CCMU3 = 0 ;
		
		// Vérification des critères directs.
		if ( $CCMU45 ) {
			$rep = 'okCCMU45' ;
			$uf = $ufUHCD ;
		} else {
			if ( $duree ) {
				$rep = 'okDuree' ;
			} else {
				if ( $CCMU3 ) {
					$rep = 'okCCMU3' ;
				} else {
					$rep = 'noCCMU3' ;
					$uf = $ufExec ;
				}
			}
		}
		
		// Vérification de la réponse à la question sur les critères UHCD.
		if ( $rep == 'okDuree' ) {
			if ( $etat != 'okCriteres' AND $etat != 'noCriteres' ) $item['obs'] .= IMGALERTE ;
		}
		
		// Vérification de la réponse à la question sur les actes lourds.
		if ( $rep == 'okCCMU3' ) {
			if ( $etat != 'okActes' AND $etat != 'noActes' ) $item['obs'] .= IMGALERTE ;
		}
	  	
	  }*/
	  if ( $options->getOption ( "ModuleBMR" ) ) {
	  	// On vérifie si le patient est lié à une alerte BMR.
	  	$param['cw'] = "WHERE IDU='".$res['idu'][$i]."'" ;
	  	$ras = $req -> Execute ( "Fichier", "getBMR", $param, "ResultQuery" ) ;
	  	// Si au moins une alerte est liée au patient, on affiche une icone d'alerte.
	  	if ( $ras['INDIC_SVC'][2] )
	    	$item['obs'] .= "<img src=\"".URLIMGRAD."\" alt=\"BMR\" />" ;
	  		//$item['obs'] .= IMGDOCS ;
	  }
	}
	// On vérifie si le patient a une note.
	$param['cw'] = "WHERE ids='".$res['idpatient'][$i]."'" ;
    $param['table'] = TABLENOTES ;
    $ras = $req -> Execute ( "Fichier", "getGenXHAM", $param, "ResultQuery" ) ;
	if ( $ras['INDIC_SVC'][2] AND $ras['note'][0] AND $session->getDroit ( "Presents_Informations" ) ) {
		global $pi ;
		$item['obs'] .= "<img src=\"".URLIMGOBS."\" ".$pi->genInfoBulle ( $ras['note'][0] )." alt=\"Comm.\" />" ;
	}
	$item['obs'] .= clListeRadios::getEtat ( $res['idpatient'][$i], IDAPPLICATION ) ;
	if ( $_POST['showHisto'] ) {
	  // Récupération des passages précédents.
	  $param['IDU'] = $res['idu'][$i] ;
	  $res2 = $req -> Execute ( "Fichier", "getHistorique", $param, "ResultQuery" ) ; 
	  if ( $res2['INDIC_SVC'][2] ) $item['obs'] .= IMGHISTO ;
	  // Récupération des passages précédents.
	  $param['ILP'] = $res['ilp'][$i] ;
	  $res2 = $req -> Execute ( "Fichier", "getHistoriqueDocs", $param, "ResultQuery" ) ;
	  if ( $res2['INDIC_SVC'][2] ) $item['obs'] .= IMGHISTODOCS ;
	}

      }
      //$this->getInfoBulle ( $res, $i ) ;
      $list->addItem ( $item ) ;
      }      
      $tabID[] = $res['idpatient'][$i] ;
    }
    // Récupération du code HTML généré.
    $this->af .= $list->getList ( $pagination ) ;
  }

  // Génération de la barre d'information sur les patients de la journée/présents/sortis/vus/non-vus/UHCD...
  function genInformationsPassages ( ) {
    global $session ;
    global $options ;
    $uhcd = $options->getOption ( "FiltreSalleUHCD" ) ;
    $pedi = $options->getOption ( "FiltreSalleSup" ) ;
    $date = new clDate ( ) ;
    $data = $date -> getDate ( ) ;
    $req = new clResultQuery ;
    // Calcul des patients présents.
    $param['table'] = PPRESENTS ;
    $param['cw'] = "" ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    $nbPresents = $res['INDIC_SVC'][2] ;
    // Calcul des patients vus.
    if ( $pedi )
    	$param['cw'] = "WHERE dt_examen!='0000-00-00 00:00:00' AND salle_examen NOT LIKE '$uhcd%' AND salle_examen NOT LIKE '$pedi%'" ;
    else
    	$param['cw'] = "WHERE dt_examen!='0000-00-00 00:00:00' AND salle_examen NOT LIKE '$uhcd%'" ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    $nbVus = $res['INDIC_SVC'][2] ;
    // Calcul des patients UHCD.
    $param['cw'] = "WHERE salle_examen LIKE '$uhcd%'" ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    $nbUHCD = $res['INDIC_SVC'][2] ;
    // Calcul des patients de pédiatrie
    if ( $pedi ) {
    	$param['cw'] = "WHERE salle_examen LIKE '$pedi%'" ;
    	$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    	$nbPEDI = $res['INDIC_SVC'][2] ;
    } else $nbPEDI = 0 ;
    // Calcul des patients non-vus.
    $nbNonVus = $nbPresents - $nbVus - $nbUHCD - $nbPEDI ;
    // Calcul des patients attendus.
    $param['table'] = PATTENDUS ;
    $param['cw'] = "" ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    $nbAttendus = $res['INDIC_SVC'][2] ;
    // Calcul des patients sortis du jour.
    $param['table'] = PSORTIS ;
    $param['cw'] = "WHERE dt_admission>'$data'" ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    $nbSortis = $res['INDIC_SVC'][2] ;
    // Calcul des passages du jour.
    $param['table'] = PPRESENTS ;
    $param['cw'] = "WHERE dt_admission>'$data'" ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    $nbPresents2= $res['INDIC_SVC'][2] ;
    //newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ;
    $nbPassages = $nbPresents2 + $nbSortis ;
    // Chargement du template de modeliXe.
    $mod = new ModeliXe ( "InformationsPassages.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Affichage du template en fonction du type de liste et des options.
    //if ( $options -> getOption ( "Historique Patient" ) )
    if ( $this->type == "Attendus" OR $options -> getOption ( "ModuleHistorique" ) != 'Oracle' )
      $mod -> MxBloc ( "historique", "modify", " " ) ;
    else {
      //if ( isset ( $_POST['showHisto'] ) ) $_SESSION['showHisto'] = $_POST['showHisto'] ;
      //else $_POST['showHisto'] = $_SESSION['showHisto'] ;
      $mod -> MxCheckerField ( "historique.histo", "checkbox", "showHisto", 1, (($_POST['showHisto'])?true:false) ) ;
    }
    if ( ! $options -> getOption ( "AjoutManuel" ) AND $this->type != "Attendus" )
      $mod -> MxBloc ( "ajouter", "modify", " " ) ;
    // Préparation de l'affichage des informations avec gestion du pluriel et du singulier.
    $mod -> MxText ( "nbPresents", $nbPresents ) ;
    if ( $pedi ) $pediatrie = ", <b>$nbPEDI</b> ".($options->getOption('nomSalleSup')?$options->getOption('nomSalleSup'):'pédiatrie') ; else $pediatrie = '' ;
    if ( $nbPresents > 1 ) $s = "s" ; else $s = '' ;
    $mod -> MxText ( "presents", "présent$s" ) ;
    $mod -> MxText ( "nbVus", $nbVus ) ;
    if ( $nbVus > 1 ) $s = "s" ; else $s = '' ;
    $mod -> MxText ( "vus", "vu$s$pediatrie" ) ;
    $mod -> MxText ( "nbUHCD", $nbUHCD ) ;
    if ( $nbUHCD > 1 ) $s = "s" ; else $s = '' ;
    $mod -> MxText ( "UHCD", "UHCD" ) ;
    $mod -> MxText ( "nbNonVus", $nbNonVus ) ;
    if ( $nbNonVus > 1 ) $s = "s" ; else $s = '' ;
    $mod -> MxText ( "nonVus", "non vu$s" ) ;
    $mod -> MxText ( "nbAttendus", $nbAttendus ) ;
    if ( $nbAttendus > 1 ) $s = "s" ; else $s = '' ;
    $mod -> MxText ( "attendus", "attendu$s" ) ;
    $mod -> MxText ( "nbPassages", $nbPassages ) ;
    if ( $nbPassages > 1 ) $s = "s" ; else $s = '' ;
    $mod -> MxText ( "passages", "entrant$s" ) ;
    // Récupération du code HTML généré par modeliXe.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session -> getNavi ( 0 ) ) ) ;
    $this->af .= $mod -> MxWrite ( "1" ) ;
  }

  // Bascule un patient attendu en patient présent.
  function attenduToPresent ( ) {
    global $session ;
    global $errs ;
    // Récupération des informations du patient attendu.
    $req = new clResultQuery ( ) ;
    $param['table'] = PATTENDUS ;
    $param['cw'] = "WHERE idpatient='".$session->getNavi ( 2 )."'" ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    // On vérifie qu'il est présent dans la table des patients attendus.
    if ( $res['INDIC_SVC'][2] ) {
      // S'il est présent, alors on l'ajoute dans la table des présents.
      $this->addPresent ( $res['sexe'][0], $res['prenom'][0], $res['nom'][0], $res['adresseur'][0], $res['medecin_urgences'][0] ) ;
      // Et nous l'effaçons de la table des attendus.
      $this -> delPatient ( ) ;
    } else {
      // Sinon, nous signalons une erreur.
      $errs -> addErreur ( "clListesPatients : Impossible de trouver le patient dans la table ".PATTENDUS." (idpatient=\"".$session->getNavi ( 2 )."\")" ) ;
    }
  }

  // Cette fonction supprime un patient attendu.
  function delPatient ( ) {
    global $session ;
    global $errs ;
     // Appel de la classe Requete.
    $requete = new clRequete ( BDD, PATTENDUS ) ;
    // Exécution de la requete.
    $res = $requete->delRecord ( "idpatient='".$session->getNavi ( 2 )."'" ) ;
    // Si une erreur est présente, alors on la signale.
    if ( $res['error'] ) $errs -> addErreur ( "clListesPatients : Erreur à la suppression d'un patient (idpatient=\"".$session->getNavi ( 2 )."\").<br />Erreur : ".$res[error] ) ;
  }

  // Cette fonction ajoute un patient présent dont les informations sont passées en paramètres.
  function addPresent ( $sexe, $prenom, $nom, $adresseur, $medecin_urgences ) {
    global $errs ;
    // Préparation de la date d'admission.
    $date = new clDate ( ) ;
    // Remplissage des attributs.
    $data['sexe']              = $sexe ;
    $data['prenom']            = $prenom ;
    $data['nom']               = $nom ;
    $data['dt_admission']      = $date -> getDatetime ( ) ;
    $data['adresseur']         = $adresseur ;
    $data['medecin_urgences']  = $medecin_urgences ;
    $data['manuel']            = 1 ;
    // Préparation de la requête.
    $requete = new clRequete ( BDD, PPRESENTS, $data ) ;
    // Exécution de la requete.
    $res = $requete->addRecord ( ) ;
    // On signale les éventuelles erreurs.
    if ( $res['error'] ) $errs -> addErreur ( "clListesPatients : Erreur lors de la bascule d'un patient attendu en patient présent (".$res[error].")." ) ;
  }
  
  // Cette fonction gère l'ajout d'un nouveau patient attendu.
  function addPatientAttendu ( ) {
    global $session ;
    global $listeMois ;
    // Si la confirmation d'ajout est présente, alors on ajoute le patient dans la table.
    if ( $_POST['ValiderAjouter'] OR $_POST['ValiderAjouter_x'] ) {
      global $errs ;
      // Préparation de la date de création.
      $date = new clDate ( ) ;
      // Remplissage des attributs.
      $data['sexe']              = $_POST['sexe'] ;
      $data['prenom']            = $_POST['prenom'] ;
      $data['nom']               = $_POST['nom'] ;
      $data['date']              = $date -> getDatetime ( ) ;
      $data['dt_naissance']      = $_POST['age'] ;
      $data['adresseur']         = $_POST['adresseur'] ;
      $data['medecin_urgences']  = $_POST['medecin'] ;
      $data['observations']      = $_POST['observations'] ;
      // Préparation de la requête.
      $requete = new clRequete ( BDD, PATTENDUS, $data ) ;
      // Exécution de la requête.
      $res = $requete->addRecord ( ) ;
      // Signalement des éventuelles erreurs rencontrées.
      if ( $res['error'] ) $errs -> addErreur ( "clListesPatients : Erreur lors de la bascule d'un patient attendu en patient présent (".$res[error].")." ) ;
    } else {
      // S'il n'y a pas de confirmation, alors nous affichons le formulaire d'ajout
      // d'un nouveau patient attendu.
      // Chargement du template.
      $mod = new ModeliXe ( "PatientAttendu.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      // Initialisation du titre, des images, des urls...
      $mod -> MxText ( "titre", "Ajouter un nouveau patient attendu" ) ;
      $mod -> MxImage ( "imgClose", URLIMGFER, "Annuler" ) ;
      $mod -> MxUrl  ( "lienClose", URLNAVI.$session->genNavi ( $session->getNavi(0) ) ) ;
      // Préparation du select du sexe du patient.
      $data['M'] = "Homme" ; $data['F'] = "Femme" ; $data['I'] = "Indéterminé" ;
      $mod -> MxSelect ( "sexe", "sexe", $_POST['sexe'], $data, '', '', 'class="w300"' ) ;
      // Champs de saisie du nom et du prénom.
      $mod -> MxFormField ( "prenom", "text", "prenom", $_POST['prenom'], "size=\"47\" maxlength=\"50\"" ) ;
      $mod -> MxFormField ( "nom", "text", "nom", $_POST['nom'], "size=\"47\" maxlength=\"50\"" ) ;
      $mod -> MxFormField ( "age", "text", "age", $_POST['age'], "size=\"47\" maxlength=\"32\"" ) ;
      // Préparation des listes dynamiques.
      $listeCom = new clListes ( "Recours", "recup" ) ;
      $listeGen = new clListesGenerales ( "recup" ) ;
      $listeMedecins          = $listeGen -> getListeItems ( "Médecins", "1", '', '', "1" ) ;
      $listeAdresseurs        = $listeGen -> getListeItems ( "Adresseurs", "1", '', '', "1" ) ;
      $mod -> MxSelect( "medecin", "medecin", $_POST['medecin'], $listeMedecins, '', '','class="w300"' ) ; 
      $mod -> MxSelect( "adresseur", "adresseur", $_POST['adresseur'], $listeAdresseurs, '', '', 'class="w300"' ) ; 
      // Affichage d'un champ texte libre pour les observations.
      $mod -> MxFormField ( "observations", "textarea", "observations", stripslashes($_POST['observations']) ) ;
      // Effacement du bloc modifier qui n'est pas utile ici.
      $mod -> MxBloc ( "modifier", "modify", " " ) ;
      // Variable de navigation.
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session -> getNavi ( 0 ), "addPatientAttendu" ) ) ;
      // Récupération du code HTML généré par ModeliXe.
      $this->af .= $mod -> MxWrite ( "1" ) ;
    }
  }

  // Cette fonction gère la modification des patients attendus.
  function modPatientAttendu ( ) {
    global $session ;
    global $listeMois ;
    global $errs ;

    // On vérifie si la modification est validée.
    if ( $_POST['ValiderModifier'] OR $_POST['ValiderModifier_x'] ) {
      $data['sexe']              = $_POST['sexe'] ;
      $data['prenom']            = $_POST['prenom'] ;
      $data['nom']               = $_POST['nom'] ;
      $data['dt_naissance']      = $_POST['age'] ;
      $data['adresseur']         = $_POST['adresseur'] ;
      $data['medecin_urgences']  = $_POST['medecin'] ;
      $data['observations']      = $_POST['observations'] ;
      // Préparation de la requête.
      $requete = new clRequete ( BDD, PATTENDUS, $data ) ;
      // Exécution de la requête.
      $res = $requete->updRecord ( "idpatient='".$session->getNavi(2)."'" ) ;
      // On signale les éventuelles erreurs rencontrées.
      if ( $res['error'] ) $errs -> addErreur ( "clListesPatients : Erreur lors de la bascule d'un patient attendu en patient présent (".$res[error].")." ) ;
    } else {
      // S'il n'y a pas de confirmation, alors nous affichons les informations actuelles du patient attendu.
      // Récupérations des informations actuelles du patient.
      $param['table'] = PATTENDUS ;
      $param['cw'] = "WHERE idpatient='".$session -> getNavi ( 2 )."' $order" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      // Chargement du template ModeliXe.
      $mod = new ModeliXe ( "PatientAttendu.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      // Préparation des attributs titre, images, liens...
      $mod -> MxText ( "titre", "Modification d'un patient attendu" ) ;
      $mod -> MxImage ( "imgClose", URLIMGFER, "Annuler" ) ;
      $mod -> MxUrl  ( "lienClose", URLNAVI.$session->genNavi ( $session->getNavi(0) ) ) ;
      // Préparation des données actuelles à afficher dans les champs du formulaire.
      $data['M'] = "Homme" ; $data['F'] = "Femme" ; $data['I'] = "Indéterminé" ;
      if ( ! $_POST['sexe'] ) $_POST['sexe'] = $res['sexe'][0] ;
      if ( ! $_POST['prenom'] ) $_POST['prenom'] = $res['prenom'][0] ;
      if ( ! $_POST['nom'] ) $_POST['nom'] = $res['nom'][0] ;
      if ( ! $_POST['medecin'] ) $_POST['medecin'] = $res['medecin_urgences'][0] ;
      if ( ! $_POST['adresseur'] ) $_POST['adresseur'] = $res['adresseur'][0] ;
      if ( ! $_POST['observations'] ) $_POST['observations'] = $res['observations'][0] ;
      if ( ! $_POST['age'] ) $_POST['age'] = $res['dt_naissance'][0] ;

      // Affichage des champs de l'état civil du patient.
	
      $mod -> MxSelect ( "sexe", "sexe", $_POST['sexe'], $data, '', '', 'class="w300"' ) ;
      $mod -> MxFormField ( "prenom", "text", "prenom", $_POST['prenom'], "size=\"47\" maxlength=\"50\"" ) ;
      $mod -> MxFormField ( "nom", "text", "nom", $_POST['nom'], "size=\"47\" maxlength=\"50\"" ) ;
      $mod -> MxFormField ( "age", "text", "age", $_POST['age'], "size=\"47\" maxlength=\"32\"" ) ;

      // Préparation des listes dynamiques.
      $listeCom = new clListes ( "Recours", "recup" ) ;
      $listeGen = new clListesGenerales ( "recup" ) ;
      $listeMedecins          = $listeGen -> getListeItems ( "Médecins", "1", '', '', "1" ) ;
      $listeAdresseurs        = $listeGen -> getListeItems ( "Adresseurs", "1", '', '', "1" ) ;
      
      // Affichage des select des listes dynamiques.
      $mod -> MxSelect( "medecin", "medecin", $_POST['medecin'], $listeMedecins, '', '','class="w300"' ) ; 
      $mod -> MxSelect( "adresseur", "adresseur", $_POST['adresseur'], $listeAdresseurs, '', '', 'class="w300"' ) ; 

      // Affichage du champs des observations.
      $mod -> MxFormField ( "observations", "textarea", "observations", stripslashes($_POST['observations']) ) ;

      // On efface le bloc ajouter du template.
      $mod -> MxBloc ( "ajouter", "modify", " " ) ;

      // Variable de navigation.
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session -> getNavi ( 0 ),  $session -> getNavi ( 1 ), $session -> getNavi ( 2 ) ) ) ;
      
      // Récupération du code HTML généré par ModeliXe.
      $this->af .= $mod -> MxWrite ( "1" ) ;
    }
  }

  // Fonction qui gère l'ajout d'un patient présent.
  function addPatientPresent ( ) {
    global $session ;
    global $listeMois ;
    global $options ;
	
    // Si la confirmation d'ajout est présente, alors on ajoute le nouveau patient dans la table.
    if ( $_POST['ValiderAjouter'] OR $_POST['ValiderAjouter_x']) {
      global $errs ;
      // Préparation de la date d'admission.
      $date = new clDate ( ) ;
      $dateN = new clDate ( $_POST['naissance'] ) ;
      /*if ( $options -> getOption ( "DoubleEtablissement" ) AND ! ( $options -> getOption ( 'DoubleSansCom' ) ) ) {
		  $req = new clResultQuery ;			
      	  $ras = $req -> Execute ( "Fichier", "getMaxIdToulon", array(), "ResultQuery" ) ; 
		  $max = 1 ;
		  for ( $j = 0 ; isset ( $ras['idpatient'][$j] ) ; $j++ )
		  	if ( $ras['idpatient'][$j] > $max ) $max = $ras['idpatient'][$j] ;
		  $max++ ;
		  eko ( "Double Etablissement : Calcul du max ($max)" ) ;
		  $data['idpatient'] = $max ;
      }*/
      
      // Remplissage des champs pour la requête.
      $data['idu']               = "X" ;
      $data['ilp']               = "X" ;
      $data['nsej']              = "X" ;
      $data['uf']                = $options -> getOption ( "AjoutManuelUF" ) ;
      $data['sexe']              = $_POST['sexe'] ;
      $data['prenom']            = $_POST['prenom'] ;
      $data['nom']               = $_POST['nom'] ;
      $data['dt_admission']      = $date -> getDatetime ( ) ;
      $data['dt_naissance']      = $dateN -> getDatetime ( ) ;
      $data['adresse_libre']     = $_POST['adresse'] ;
      $data['adresse_cp']        = $_POST['cp'] ;
      $data['adresse_ville']     = $_POST['ville'] ;
      $data['telephone']         = $_POST['telephone'] ;
      $data['salle_examen']      = $_POST['salle'] ;
      $data['dest_souhaitee']    = $_POST['destSouhaitee'] ;
      $data['dest_attendue']     = $_POST['destAttendue'] ;
      $data['adresseur']         = $_POST['adresseur'] ;
      $data['medecin_urgences']  = $_POST['medecin'] ;
      if ( $_POST['medecin'] )
	$data[dt_examen] = $date -> getDatetime ( ) ;
      $data['recours_categorie'] = $_POST['categorieRecours'] ;
      $data['manuel']            = 1 ;
      // Préparation de la requête.
      $requete = new clRequete ( BDD, PPRESENTS, $data ) ;
      // Exécution de la requête.
      $resu = $requete->addRecord ( ) ;
      // On signale les éventuelles erreurs rencontrées.
      if ( $resu['error'] ) $errs -> addErreur ( "clListesPatients : Erreur lors de la bascule d'un patient attendu en patient présent (".$resu[error].")." ) ;      

      $param['table'] = PPRESENTS ;
      $param['cw'] = "WHERE idu='X'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      for ( $i = 0 ; isset ( $res['idpatient'][$i] ) ; $i++ ) {
	$data2['idu'] = "MANU".$res['idpatient'][$i] ;
	$data2['ilp'] = "MANU".$res['idpatient'][$i] ;
	$data2['nsej'] = "MANU".$res['idpatient'][$i] ;
	// Préparation de la requête.
	$requete = new clRequete ( BDD, PPRESENTS, $data2 ) ;
	// Exécution de la requête.
	$requete->updRecord ( "idpatient='".$res['idpatient'][$i]."'" ) ;
    header ( 'Location:'.URLNAVI.$session->genNavi($session->getNavi(0))) ;
      }

    } else {
      // Sinon, nous affichons le formulaire d'ajout d'un nouveau patient présent.
      // Chargement du template ModeliXe.
      $mod = new ModeliXe ( "addPresent.html" ) ;
      $mod -> SetModeliXe ( ) ;
      // Préparation du titre, des images, des urls...
      $mod -> MxText ( "titre", "Ajouter un nouveau patient présent" ) ;
      $mod -> MxImage ( "imgClose", URLIMGFER, "Annuler" ) ;
      $mod -> MxUrl  ( "lienClose", URLNAVI.$session->genNavi ( $session->getNavi(0) ) ) ;
      // Champs IDU, IPP, et nsej...
      // $mod -> MxFormField ( "idu", "text", "idu", $_POST['idu'], "size=\"47\" maxlength=\"50\"" ) ;
      // $mod -> MxFormField ( "ilp", "text", "ilp", $_POST['ilp'], "size=\"47\" maxlength=\"50\"" ) ;
      // $mod -> MxFormField ( "nsej", "text", "nsej", $_POST['nsej'], "size=\"47\" maxlength=\"50\"" ) ;
      // Champs de l'état civil du patient.
      $data['M'] = "Homme" ; $data['F'] = "Femme" ; $data['I'] = "Indéterminé" ;
      $javascript1  = XhamTools::genAjax ( 'onKeyUp', 'getPatients', 'navi='.$session->genNavi ( 'Ajax', 'getPatientsSortis' ) ) ;
	  $javascript3  = XhamTools::genAjax ( 'onChange', 'getPatients', 'navi='.$session->genNavi ( 'Ajax', 'getPatientsSortis' ) ) ;
	  
	  $javascript1 =  XhamTools::genAjaxWithTempo ( 'getPatients', 'navi='.$session->genNavi ( 'Ajax', 'getPatientsSortis' ) ) ;
	  		
      $mod -> MxSelect ( "sexe", "sexe", $_POST['sexe'], $data, '', '', 'class="w300"  '."$javascript3" ) ;
      $mod -> MxFormField ( "prenom", "text", "prenom", $_POST['prenom'], "size=\"47\" maxlength=\"50\" $javascript1 $javascript3" ) ;
      $mod -> MxFormField ( "nom", "text", "nom", $_POST['nom'], "size=\"47\" maxlength=\"50\"  $javascript1 $javascript3" ) ;
      // Gestion des champs de la date de naissance.
      $_POST['naissance'] = date ( 'd/m/Y' ) ;
      $mod -> MxFormField ( "naissance", "text", "naissance", $_POST['naissance'], "id=\"naissance\"" ) ;
      // Adresse et téléphone du patient.
      $mod -> MxFormField ( "adresse", "text", "adresse", $_POST['adresse'], "size=\"47\" maxlength=\"128\"" ) ;
      $mod -> MxFormField ( "telephone", "text", "telephone", $_POST['telephone'], "size=\"47\" maxlength=\"64\"" ) ;
      $mod -> MxFormField ( "cp", "text", "cp", $_POST['cp'], "size=\"5\" maxlength=\"5\"" ) ;
      $mod -> MxFormField ( "ville", "text", "ville", $_POST['ville'], "size=\"38\" maxlength=\"64\"" ) ;
      // Préparation des listes dynamiques.
      $listeCom = new clListes ( "Recours", "recup" ) ;
      $listeGen = new clListesGenerales ( "recup" ) ;
      $listeMedecins          = $listeGen -> getListeItems ( "Médecins", "1", '', '', "1" ) ;
      $listeSalles            = $listeGen -> getListeItems ( "Salles d'examens", "1", '', '', "1" ) ;
      $listeCategoriesRecours = $listeCom -> getListes     ( "", "1" ) ;
      $listeDestSouhaitees    = $listeGen -> getListeItems ( "Destinations souhaitées", "1", '', '', "1" ) ;
      $listeDestAttendues     = $listeGen -> getListeItems ( "Destinations attendues", "1", '', '', "1" ) ;      
      // Affichage des listes dynamiques.
      $mod -> MxSelect( "medecin", "medecin", $_POST['medecin'], $listeMedecins, '', '','class="w300"' ) ; 
      $mod -> MxSelect( "salle", "salle", $_POST['salle'], $listeSalles, '', '', 'class="w300"' ) ; 
      $mod -> MxSelect( "categorieRecours", "categorieRecours", $_POST['categorieRecours'], $listeCategoriesRecours, '', '', 'class="w300"' ) ; 
      $mod -> MxSelect( "destSouhaitee", "destSouhaitee", $_POST['destSouhaitee'], $listeDestSouhaitees, '', '', 'class="w300"' ) ; 
      $mod -> MxSelect( "destAttendue", "destAttendue", $_POST['destAttendue'], $listeDestAttendues, '', '', 'class="w300"' ) ; 
      // Suppression du bouton de modification inutile ici.
      $mod -> MxBloc ( "modifier", "modify", " " ) ;
      // Variable de navigation.
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session -> getNavi ( 0 ), "addPatientPresent" ) ) ;
      // Récupération du code HTML généré par le template ModeliXe.
      $this->af .= $mod -> MxWrite ( "1" ) ;
    }
  }

  // Récupération du code HTML généré par la classe ListesPatients.
  function getAffichage ( ) {
    return $this->afbulles.$this->af ;
  }

}

?>
