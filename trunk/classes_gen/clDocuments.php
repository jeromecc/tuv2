<?php

// Titre  : Classe Documents
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 25 Février 2005

// Description : 
// Cette classe gère les documents : Création, édition, impression,
// suppression, attribution... etc.


class clDocuments {
  
  // Déclaration des attributs de la classe Documents.
  // Contient l'affichage généré par la classe.
  private $af ;
  // Contient les informations à afficher.
  private $infos ;
  // Contient les erreurs à afficher.
  private $errs ;
  private $DocumentsNameList;

  // Constructeur.
  function __construct ( $action='' ,$dnl='Documents' ) {
  global $errs;
  $this->DocumentsNameList=$dnl ;
     if ( $action ) {
      
    } else {
      // Si aucun argument, nous sommes dans la partie administration.
      $this->setAffichage ( ) ;
    }
  }

  // Génération de l'affichage de l'administration des documents.
  function setAffichage ( ) {
    global $session ;
    global $options ;
    // Vérification du droit de lecture sur les documents.
    if ( $session->getDroit ( "Administration_Documents", "r" ) AND $options->getOption ( "ModuleDocuments" ) ) {
      // Génération du mini-menu.
      $this->af .= $this->getMenuDocs ( ) ;
      // Si le document est en cours de modification et que le droit est présent, on invoque le formulaire de modification.
      if ( $session->getDroit ( "Administration_Documents", "m" ) AND $session->getNavi ( 2 ) == "modDoc" ) {
	$this->af .= $this->modDocForm ( ) ;
	// Si une création est demandée et le droit présent, alors on invoque le formulaire d'ajout de document.
      } elseif ( $_POST['addDoc'] AND $session->getDroit ( "Administration_Documents", "w" ) ) {
	$this->af .= $this->addDocForm ( ) ;
	// Si nous voulons modifier les bannières du document, alors invoque la gestion des listes pour les documents.
      } elseif ( ( $session->getNavi(2) == "gestListe" OR $_POST['gestDoc'] OR $_POST['gestDoc_x'] ) AND $session->getDroit ( "Administration_Documents", "m" ) ) {
	// Génération de la gestion des listes.
	$liste = new clListes ( $this->DocumentsNameList ) ;
	$this->af .= $liste -> getAffichage ( ) ;
	// Génération du document au format pdf.
      } elseif ( $session->getNavi(2) == "voirDoc" ) {
      if (IDAPPLICATION == '2') {
      	   global $pas_daffichage;
	   $pas_daffichage = '1';
	   }
	$this->genDoc ( Array ( $session->getNavi(3) ) ) ;
	
	
      } else {
	// Ajout réel d'un nouveau document.
	if ( $session->getDroit ( "Administration_Documents", "w" ) AND $session->getNavi ( 2 ) == "validAddDoc" AND ( $_POST['Ajouter'] OR $_POST['Ajouter_x'] ) ) {
	  $this->addDoc ( ) ;
	}
	// Mise à jour réelle d'un document.
	if ( $session->getDroit ( "Administration_Documents", "m" ) AND $session->getNavi ( 2 ) == "validModDoc"  AND ( $_POST['Modifier'] OR $_POST['Modifier_x'] ) ) {
	  $this->modDoc ( ) ;
	}
	// Suppression d'un document.
	if ( $session->getDroit ( "Administration_Documents", "d" ) AND $session->getNavi ( 2 ) == "validModDoc"  AND ( $_POST['Supprimer'] OR $_POST['Supprimer_x'] ) ) {
	  $this->delDoc ( ) ;
	}
	// Récupération de la liste des catégories de documents.
	$param[cw] = " ORDER BY categorie" ;
	$req = new clResultQuery ;
	$res = $req -> Execute ( "Fichier", "getCategoriesDocuments", $param, "ResultQuery" ) ;  
	// Remplissage du template.
	$mod = new ModeliXe ( "GestionDocuments.mxt" ) ;
	$mod -> SetModeliXe ( ) ;
	// Affichage des messages d'informations.
	if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
	else $mod -> MxBloc ( "informations", "modify", " " ) ;
	// Affichage des messages d'erreurs.
	if ( $this->erreurs ) $mod -> MxText ( "erreurs.errs", $this->erreurs ) ;
	else $mod -> MxBloc ( "erreurs", "modify", " " ) ;
	// Parcours des différentes catégories de documents.
	for ( $i = 0 ; isset ( $res[categorie][$i] ) ; $i++ ) {
	  // Recherche des dernières versions des documents de la catégorie parcourue.
	  $param[cw] = "WHERE categorie='".$res[categorie][$i]."' AND fin_validite='0000-00-00 00:00:00' ORDER BY nom" ;
	  $req = new clResultQuery ;
	  $ras = $req -> Execute ( "Fichier", "getDocuments", $param, "ResultQuery" ) ;  
	  // Si on a un résultat, alors on parcourt ces documents.
	  if ( $ras[INDIC_SVC][2] ) {
	    // Et on affiche le titre de la catégorie.
	    $mod -> MxText ( "categorie.titre", $res[categorie][$i] ) ;
	    for ( $j = 0 ; isset ( $ras[iddocument][$j] ) ; $j++ ) {
	      //Pour chaque document, on génère une ligne.
	      $date = new clDate ( $ras[deb_validite][$j] ) ;
	      // Gestion des lignes paires et impaires pour la mise en page...
	      if ( $j % 2 ) $mod -> MxText ( "categorie.document.ligne", "<tr class=\"paire\">" ) ;
	      else $mod -> MxText ( "categorie.document.ligne", "<tr class=\"impaire\">" ) ;
	      // Nom du document.
	      $mod -> MxText ( "categorie.document.nomDocument", $ras[nom][$j] ) ;
	      // Date de la dernière modification.
	      $mod -> MxText ( "categorie.document.dateModification", $date -> getDateTextFull ( )." (v".$ras[version][$j].")" ) ;
	      // Si le droit de modification est présent, un lien est généré.
	      if ( $session->getDroit ( "Administration_Documents", "m" ) ) {
		$mod -> MxImage ( "categorie.document.imgMod", URLIMGMOD, "Modifier" ) ;
		$mod -> MxUrl  ( "categorie.document.modDoc", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), 
											  "modDoc", $ras[iddocument][$j] ) ) ;
	      }
	      $mod -> MxImage ( "categorie.document.imgVoir", URLIMGPDF, "Afficher" ) ;
	      $mod -> MxUrl  ( "categorie.document.voirDoc", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), 
											 "voirDoc", $ras[iddocument][$j] ) ) ;
	      $mod -> MxBloc ( "categorie.document", "loop" ) ;
	    }
	    $mod -> MxBloc ( "categorie", "loop" ) ;
	  }
	}
	// Récupération du code HTML généré par ModeliXe.
	$this->af .= $mod -> MxWrite ( "1" ) ;
      }
    }
  }

  // Récupération des informations pour les documents.
  function genTab ( ) {
    global $errs;
    $liste = new clListes ( $this->DocumentsNameList, "1" ) ;
    $tab = $liste -> getListes ( ) ;
    //newfct ( gen_affiche_tableau, $tab ) ;
    while ( list ( $key, $val ) = each ( $tab ) ) { 
      $tub[$val] = $liste -> getListes ( $val ) ;
      //newfct ( gen_affiche_tableau, $tab[$val] ) ;
    }
    return $tub ;
  }

  // Récupération des informations d'un patient pour l'intégration dans le document
  // à générer.
  function genPat ( $idpatient ) {
    global $session ;
    global $options ;
    // Si un idpatient est tranmis, alors on récupère ses informations
    // à l'aide de la classe Patient.
    if ( $idpatient ) {
    
      //concerne la recuperation d'une instance de la classe FoRmX gerant les actions
      if ($options->getOption ( "use_formx" )) {
      global $actions;
      $actions = new clFoRmX($idpatient,'NO_POST_THREAT');
      }
    
      $patient = new clPatient ( $idpatient, $session -> getNavi ( 5 ) ) ;
      $pat[NMA] = $patient -> getNom ( ) ;
      $pat[PRE] = $patient -> getPrenom ( ) ;
      $pat[DNA] = $patient -> getDateNaissance ( ) ;
      $pat[SEX] = $patient -> getSexe ( ) ;
      $date = new clDate ( $patient -> getDateAdmission ( ) ) ;
      $pat[DTA] = $date -> getDate ( $options->getOption ( "Documents Date" ) ) ;
      $pat[HEA] = $date -> getDate ( "H:i" ) ;
      $pat[MED] = $patient -> getMedecin ( ) ;
      $pat[DHS] = $patient -> getDateSortie ( ) ;
      $pat[DHE] = $patient -> getDateExamen ( ) ;
      // Sinon, nous renseignons avec les valeurs par défaut.
    } else {
      $pat[NMA] = "Bon" ;
      $pat[PRE] = "Jean" ;
      $date = new clDate ( "1981-12-24 22:30:00" ) ;
      $pat[DNA] = $date -> getDate ( $options->getOption ( "Documents Date" ) ) ;
      $pat[SEX] = "1" ;
      $date = new clDate ( ) ;
      $pat[DTA] = $date -> getDate ( $options->getOption ( "Documents Date" ) ) ;
      $pat[HEA] = $date -> getDate ( "H:i" ) ;
      $pat[MED] = "Testeur" ;
      $pat[DHS] = $date -> getDate ( $options->getOption ( "Documents Date" ) ) ;
      $pat[DHE] = $date -> getDate ( $options->getOption ( "Documents Date" ) ) ;
    }
    // On renvoie le tableau généré.
    return $pat ;
  }

  // Génération du document au format PDF.
  // $docs : Contient la liste des documents à générer.
  // $idpatient : idpatient transmis ou non.
  function genDoc ( $docs, $idpatient='', $output='', $rep='' ) {
    global $options ;
    global $tab ;
    global $errs;
    global $session;
    global $pas_daffichage;
    
    // Récupération des données.
    $pat = $this->genPat ( $idpatient ) ;
    $tab = $this->genTab ( ) ;
    
    if ( $output ) {
      // Parcours des différents documents à générer.
      for ( $i = 0 ; isset ( $docs[$i] ) ; $i++ ) {
	// Préparation du document PDF.
	$pdf = new clFPDF ( ) ;
	$pdf -> AliasNbPages ( ) ;
	$pdf -> SetFont ( 'Times', '', 12 ) ;
	// Récupération du contenu du document parcouru.
	$param[cw] = "WHERE iddocument='".$docs[$i]."' ORDER BY nom" ;
	$req = new clResultQuery ;
	$ras = $req -> Execute ( "Fichier", "getDocuments", $param, "ResultQuery" ) ;
	// Saut de page.
	$pdf -> AddPage ( ) ;
	$string = $ras[contenu][0] ;
	// On remplace les balises par leurs valeurs réelles.
	while ( list ( $key, $val ) = each ( $pat ) ) {
	  $searched_string = "<**".$key.">" ;
	  $string = str_replace ( $searched_string, $pat[$key], $string ) ;
	}
	reset ( $pat ) ;
	$pdf -> SetFont ( 'times', '', 10 ) ;
	// Génération du contenu.
	$pdf -> writehtml ( $string ) ;
	
	if ( IDAPPLICATION == '1' ) {
	
	// Signature du médecin.
	$pdf -> Cell ( 80, 10, "", 0, 1 ) ;
	$pdf -> Cell ( 80, 25, "Dr $pat[MED]", 0, 1, R ) ;
	}
	
	if ( ! file_exists ( $rep ) )
	  system ( "mkdir -p ".$rep ) ;
	$pdf -> Output ( $rep.$output[$i], "F" ) ;

	//if ( IDAPPLICATION == '2' ) return ;
      }
    } 
    // Préparation du document PDF.
    $pdf = new clFPDF ( ) ;
    $pdf -> AliasNbPages ( ) ;
    $pdf -> SetFont ( 'Times', '', 12 ) ;
    // Parcours des différents documents à générer.
    for ( $i = 0 ; isset ( $docs[$i] ) ; $i++ ) {
      // Récupération du contenu du document parcouru.
      $param[cw] = "WHERE iddocument='".$docs[$i]."' ORDER BY nom" ;
      $req = new clResultQuery ;
      $ras = $req -> Execute ( "Fichier", "getDocuments", $param, "ResultQuery" ) ;
      // Saut de page.
      $pdf -> AddPage ( ) ;
      $string = $ras[contenu][0] ;
      // On remplace les balises par leurs valeurs réelles.
      while ( list ( $key, $val ) = each ( $pat ) ) {
	$searched_string = "<**".$key.">" ;
	$string = str_replace ( $searched_string, $pat[$key], $string ) ;
      }
      reset ( $pat ) ;
      $pdf -> SetFont ( 'times', '', 10 ) ;
      // Génération du contenu.
      $pdf -> writehtml ( $string ) ;
      if ( IDAPPLICATION == '1' ) {
      // Signature du médecin.
      $pdf -> Cell ( 80, 10, "", 0, 1 ) ;
      $pdf -> Cell ( 80, 25, "Dr $pat[MED]", 0, 1, R ) ;
      }
    }
    if (IDAPPLICATION != '2' || $pas_daffichage == '1') {
    	$pdf -> Output ( ) ;
    } else {
    	$fic='buff_impr_'.rand().'.pdf';
	$pdf -> Output (URLCACHE.$fic) ;
	return $fic;
    	}
    }

  // Génération du menu de navigation/
  function getMenuDocs ( ) {
    global $session ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "MenuDocuments.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Calcul de la variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1) ) ) ;
    // En fonction de la navigation actuelle, on affiche les boutons.
    if ( $session->getNavi(2) == "gestListes" OR  $session->getNavi ( 2 ) == "gestDoc" ) $_POST['gestDoc'] = 1 ;
    if ( $_POST['addDoc'] OR $_POST['addDoc_x'] ) {
      $mod -> MxBloc ( "addDoc", "modify", " " ) ;
    } elseif ( $_POST['gestDoc'] OR $_POST['gestDoc_x'] ) {
      $mod -> MxBloc ( "gestPersonnel", "modify", " " ) ;
    } else {
      $mod -> MxBloc ( "listDocs", "modify", " " ) ;
    }
    // Récupération du code HTML généré par le template.
    $this->af .= $mod -> MxWrite ( "1" ) ;
  }

  // Suppression d'un document.
  function delDoc ( ) {
    global $session ;
    global $errs ;
    $date = new clDate ( ) ;
    // On récupère les informations du document à supprimer (désactiver plutôt...).
    $param[cw] = "WHERE iddocument='".$session->getNavi ( 3 )."' AND  fin_validite='0000-00-00 00:00:00'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getDocuments", $param, "ResultQuery" ) ;
    // Si on a un résultat alors on désactive le document en renseignant sa 
    // date de fin de validité.
    if ( $res[INDIC_SVC][2] ) {
      $data1[fin_validite] = $date -> getDatetime ( ) ;
      $requete = new clRequete ( BDD, "documents", $data1 ) ;
      $requete->updRecord ( "iddocument='".$res[iddocument][0]."'" ) ;
      $this->infos .= "Suppression du document \"".$res[nom][0]."\" effectuée." ;
    } else {
      // Génération d'une erreur et d'un message si le document n'existe pas.
      $this->erreurs .= "Le document à supprimer n'existe pas ou n'est plus actif. Le problème a été transmis, l'action est annulée." ;
      $errs -> addErreur ( "Le document à supprimer (id=\"".$session->getNavi(3)."\") n'existe pas ou n'est plus actif. Action annulée." ) ;
    }
  }

  // Modification d'un document.
  function modDoc ( ) {
    global $session ;
    global $errs ;
    $date = new clDate ( ) ;
    // Récupération des informations du document.
    $param[cw] = "WHERE iddocument='".$session->getNavi ( 3 )."' AND  fin_validite='0000-00-00 00:00:00'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getDocuments", $param, "ResultQuery" ) ;
    // Si le document existe on le met à jour :
    // Désactivation de la version précédente en renseignant une date de fin de validité,
    // puis création de la nouvelle version (en incrémentant le numéro de version).
    if ( $res[INDIC_SVC][2] ) {
      // Message d'information.
      $this->infos .= "Modification du document \"".$res[nom][0]."\" effectuée." ;
      // Fin de validité de la version actuelle.
      $data1[fin_validite] = $date -> getDatetime ( ) ;
      $requete = new clRequete ( BDD, "documents", $data1 ) ;
      $requete->updRecord ( "iddocument='".$res[iddocument][0]."'" ) ;
      // Création de la nouvelle version du document.
      if ( $_POST['newCategorie'] ) $data2[categorie] = $_POST['newCategorie'] ;
      else if ( $_POST['listeCategories'] ) $data2[categorie] = $_POST['listeCategories'] ;
      else $data2[categorie] = $res[categorie][0] ;
      if ( eregi ( "[0-9a-zA-Z]", $_POST['nomDoc'] ) ) $data2[nom] = stripslashes($_POST['nomDoc']) ;
      else $data2[nom] = $res[nom][0] ;
      $data2[contenu] = $_POST['contenu'] ;
      $data2[deb_validite] = $date -> getDatetime ( ) ;
      $data2[version] = $res[version][0] + 1 ;
      $requete = new clRequete ( BDD, "documents", $data2 ) ;
      $res = $requete->addRecord ( ) ;
      //newfct ( gen_affiche_tableau, $res ) ;
    } else {
      // Génération d'une erreur et d'un affichage si le document à modifier n'existe pas.
      $this->erreurs .= "Le document à modifier n'existe pas ou n'est plus actif. Le problème a été transmis, l'action est annulée." ;
      $errs -> addErreur ( "Le document à modifier (id=\"".$session->getNavi(3)."\") n'existe pas ou n'est plus actif. Action annulée." ) ;
    }
  }

  // Génération du formulaire de modification d'un document.
  function modDocForm ( ) {
    global $session ;
    global $errs ;
    // Récupération de la liste des catégories de documents.
    $param[cw] = "" ;
    $req = new clResultQuery ;
    $cat = $req -> Execute ( "Fichier", "getCategoriesDocuments", $param, "ResultQuery" ) ; 
    for ( $i = 0 ; isset ( $cat[categorie][$i] ) ; $i++ ) { $tab[$cat[categorie][$i]] = $cat[categorie][$i] ; }
    // Récupération des informations du document.
    $param[cw] = "WHERE iddocument='".$session->getNavi ( 3 )."' AND  fin_validite='0000-00-00 00:00:00'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getDocuments", $param, "ResultQuery" ) ;
    // Si le document existe, on génère le formulaire.
    if ( $res[INDIC_SVC][2] ) {
      $mod = new ModeliXe ( "EditionDocument.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      $mod -> MxBloc ( "addText", "modify", " " ) ;
      $mod -> MxBloc ( "Ajouter", "modify", " " ) ;
      $mod -> MxText ( "modText.nomDocument", $res[nom][0] ) ;
      $mod -> MxText ( "contenu", $res[contenu][0] ) ;
      $mod -> MxSelect( "listeCategories", "listeCategories", $res[categorie][0], $tab , '', '', "size=\"1\"") ;
      $mod -> MxFormField ( "nomDoc", "text", "nomDoc", $res[nom][0], "size=\"31\" maxlength=\"50\"" ) ;
      $mod -> MxFormField ( "newCategorie", "text", "newCategorie", '', "size=\"31\" maxlength=\"50\"" ) ;
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "validModDoc", $res[iddocument][0] ) ) ;
      // Le bouton "Modifier" n'est présent que si le droit l'est aussi.
      if ( ! $session->getDroit ( "Administration_Documents", "m" ) ) $mod -> MxBloc ( "Modifier", "modify", " " ) ;
      // Le bouton "Supprimer" n'est présent que si le droit l'est aussi.
      if ( ! $session->getDroit ( "Administration_Documents", "d" ) ) $mod -> MxBloc ( "Supprimer", "modify", " " ) ;
      return $mod -> MxWrite ( "1" ) ;
    } else {
      // Génération d'une erreur et d'un affichage si le document à modifier n'existe pas.
      $mod = new ModeliXe ( "RetourErreur.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      $mod -> MxText ( "erreur", "Le document demandé n'existe pas ou n'est plus actif. Le problème a été signalé." ) ;
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "retourErreur" ) ) ;
      $errs -> addErreur ( "clDocuments : Le document demandé (id=\"".$session->getNavi(3)."\") n'existe pas ou n'est plus actif." ) ;
      return $mod -> MxWrite ( "1" ) ; 
    }
  }

  // Création d'un document.
  function addDoc ( ) {
    global $session ;
    global $errs ;
    $date = new clDate ( ) ;
    // Message d'information.
    $this->infos .= "Création du document \"".stripslashes($_POST['nomDoc'])."\" effectuée." ;
    if ( $_POST['newCategorie'] ) $data[categorie] = $_POST['newCategorie'] ;
    else if ( $_POST['listeCategories'] ) $data[categorie] = $_POST['listeCategories'] ;
    else $data[categorie] = "Non classés" ;
    // Vérification de la validité du nom du document.
    if ( eregi ( "[0-9a-zA-Z]", $_POST['nomDoc'] ) ) $data[nom] = stripslashes($_POST['nomDoc']) ;
    // Sinon, on génère le nom avec la date de publication.
    else $data[nom] = $date->getDatetime ;
    $data[contenu] = stripslashes($_POST['contenu']) ;
    $data[deb_validite] = $date -> getDatetime ( ) ;
    $data[version] = 1 ;
    $requete = new clRequete ( BDD, "documents", $data ) ;
    $requete->addRecord ( ) ;
  }

  // Génération du formulaire d'ajout de document.
  function addDocForm ( ) {
    global $session ;
    global $errs ;
    // Récupération des catégories existantes.
    $param[cw] = "" ;
    $req = new clResultQuery ;
    $cat = $req -> Execute ( "Fichier", "getCategoriesDocuments", $param, "ResultQuery" ) ; 
    for ( $i = 0 ; isset ( $cat[categorie][$i] ) ; $i++ ) { $tab[$cat[categorie][$i]] = $cat[categorie][$i] ; }
    // Chargement du template.
    $mod = new ModeliXe ( "EditionDocument.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $mod -> MxBloc ( "modText", "modify", " " ) ;
    $mod -> MxBloc ( "Modifier", "modify", " " ) ;
    $mod -> MxBloc ( "Supprimer", "modify", " " ) ;
    $mod -> MxText ( "contenu", '' ) ;
    $mod -> MxSelect( "listeCategories", "listeCategories", '', $tab , '', '', "size=\"1\"") ;
    $mod -> MxFormField ( "nomDoc", "text", "nomDoc", '', "size=\"31\" maxlength=\"50\"" ) ;
    $mod -> MxFormField ( "newCategorie", "text", "newCategorie", '', "size=\"31\" maxlength=\"50\"" ) ;
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "validAddDoc" ) ) ;
    // Si le droit d'écriture est présent, on affiche le bouton "Ajouter".
    if ( ! $session->getDroit ( "Administration_Documents", "w" ) ) $mod -> MxBloc ( "Ajouter", "modify", " " ) ;
    return $mod -> MxWrite ( "1" ) ;
  }

  // Récupération de l'affichage généré par la classe Document.
  function getAffichage ( ) {
    return $this->af ;
  }

}

?>
