<?php

// Titre  : Classe Options
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 20 Janvier 2005

// Description : 
// Cette classe récupère les valeurs des options du terminal.
// Ces valeurs sont accessibles via une méthode afin de savoir, 
// par exemple, si un module du terminal est activé.
// Le deuxième rôle de cette classe concerne toute la partie
// administration des options.

class clOptions {

  // Déclaration des attributs de la classe.
  // Tableau associatif contenant toutes les options du terminal.
  private $options ;
  // Attribut contenant l'affichage.
  private $af ;
  private $erreurs2 ;
  private $erreurs ;
  private $infos ;
  private $stop ;

  // Constructeur de la classe.
  function __construct ( $admin="" ) {
    global $session ;
    if ( $admin ) {
      if ( $session ) if ( $session -> getNavi ( 2 ) == "MajOptionsProd" )
	$this->majOptionsProd ( ) ;
      $this->af .= $this->listeCategories ( ) ;
    } else {
      $this->setOptions ( ) ;
    }
  }

  // Retourne la valeur d'une option.
  function getOption ( $nom ) { 
    if ( DEBUGOPTION ) {
      print "<br />" ;
      print "Option appelée : \"$nom\", valeur : \"".$this->options["$nom"]."\"<br />" ;
    }
    return (isset($this->options["$nom"])?$this->options["$nom"]:'') ; 
  }

  // Initialisation des options.
  function setOptions ( ) {
    // Requête pour récupérer les informations actuelles de l'option.
    $param['cw'] = "WHERE idapplication=".IDAPPLICATION ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;  
    // Fabrication du tableau contenant la valeur des options de l'application.
    for ( $i = 0 ; isset ( $res['idoption'][$i] ) ; $i++ ) {
      $this->options[$res['libelle'][$i]] = $res['valeur'][$i] ;
    }
    if ( DEBUGOPTION ) { affTab ( $this->options ) ; }
  }

  // Cette fonction met à jour la valeur d'une option.
  function validerModification ( $idoption ) {
    global $errs ;
    // Requête pour récupérer les informations actuelles de l'option.
    $param['cw'] = "WHERE idoption='$idoption'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;  
    // Si on a un résultat, alors on met à jour.
    if ( $res['INDIC_SVC'][2] > 0 ) {
      // Mise à jour.
      $data['valeur'] = $_POST['option'] ;
      $requete = new clRequete ( BASEXHAM, "options", $data ) ;
      $res = $requete->updRecord ( "idoption='$idoption' AND idapplication=".IDAPPLICATION ) ;
    } else {
      // Sinon, un avertissement d'erreur est envoyé.
      $errs -> addErreur ( "Modification d'une option : Option (idoption=$idoption) inexistante !" ) ;
    }
  }

  // Cette fonction permet de supprimer une option de la base de données.
  function delOption ( $idoption ) {
    global $session ;
    // On récupère les informations sur l'option.
    $param['cw'] = "WHERE idoption=$idoption" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;
    // Si la confirmation de suppression est présente, alors on lance la suppression.
    if ( isset($_POST['Supprimer']) OR isset($_POST['Supprimer_x']) ) {
      // Appel de la classe Requete.
      $requete = new clRequete ( BASEXHAM, TABLEOPTS ) ;
      // Exécution de la requete.
      $requete->delRecord ( "idoption=$idoption AND idapplication=".IDAPPLICATION ) ;
    } else {
      // Sinon, on affiche un formulaire de confirmation (Annuler/Supprimer).
      $mod = new ModeliXe ( "FormConfirmation.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      $mod -> MxText ( "question", "Confirmez-vous la suppression de l'option '".$res['libelle'][0]."' ?" ) ;
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), $session->getNavi(3) ) ) ;
      return $mod -> MxWrite ( "1" ) ;
    }
  }

  function verifForm ( $res='' ) {
    if ( ! $_POST['libelle'] ) $this->erreurs2 .= "Le libellé ne doit pas être vide.<br />" ;

    if ( ! $_POST['description'] ) $this->erreurs2 .= "Le description ne doit pas être vide.<br />" ;
	
    if ( ( $res AND $res['libelle'][0] != stripslashes($_POST['libelle']) ) OR ( ! $res ) ) {
      $param['cw'] = "WHERE libelle='".$_POST['libelle']."' AND idapplication=".IDAPPLICATION ;
      $req = new clResultQuery ;
      $ras = $req -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;
      if ( $ras['INDIC_SVC'][2] ) {
	$this->erreurs2 .= "Le libellé choisi est déjà utilisé.<br />" ;
      }
    }

    if ( ( $res AND (isset($res['cle'][0])?$res['cle'][0]:'') != (isset($_POST['cle'])?$_POST['cle']:'') ) OR ( ! $res ) ) {
      $param['cw'] = "WHERE cle='".(isset($_POST['cle'])?$_POST['cle']:'')."' AND idapplication=".IDAPPLICATION ;
      $req = new clResultQuery ;
      $ras = $req -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      if ( $ras['INDIC_SVC'][2] ) {
	$this->erreurs2 .= "La clé choisie est déjà utilisée.<br />" ;
      }
    }

    if ( ! $this->erreurs2 ) {
      $this->stop = 1 ;
      return 1 ;
    }
  }

  // Mise à jour de l'option.
  function majOption ( $res ) {
    $req = new clResultQuery ;

    $data['libelle']     = stripslashes($_POST['libelle']) ;
    $data['description'] = stripslashes($_POST['description']) ;
    $data['type']        = $_POST['type'] ;
    $data['choix']       = (isset($_POST['choix'])?stripslashes($_POST['choix']):'') ;
    $data['categorie']   = ($_POST['nouvelle']?stripslashes($_POST['nouvelle']):stripslashes($_POST['categorie'])) ;
    $data['administrateur'] = ((isset($_POST['administrateur']) AND $_POST['administrateur'])?1:0) ;

    $req = new clRequete ( BASEXHAM, TABLEOPTS, $data ) ;
    $ris = $req -> updRecord ( "idoption=".$res['idoption'][0]." AND idapplication=".IDAPPLICATION ) ;
    //$ris = $req -> updRecord ( "libelle='".$res['libelle'][0]."' AND idapplication=".IDAPPLICATION.";" ) ;
    eko ( $ris ) ;
  }

  // Modification d'une option.
  function modOption ( ) {
    global $session ;
    if ( $session -> getDroit ( "Configuration_Options", "a" ) ) {

      $param['cw'] = "WHERE idoption=".$session->getNavi ( 3 ) ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;  

      if ( isset ( $_POST['Modifier'] ) ) {
	if ( $this->verifForm ( $res ) ) {
	  $this->majOption ( $res ) ;
	  $this->infos .= "La modification de l'option '".stripslashes($_POST['libelle'])."' a bien été effectuée." ;
	}
      }
      if ( ! $this->stop ) {
	if ( ! isset ( $_POST['libelle'] ) )        $_POST['libelle']        = $res['libelle'][0] ;
	if ( ! isset ( $_POST['description'] ) )    $_POST['description']    = $res['description'][0] ;
	if ( ! isset ( $_POST['categorie'] ) )      $_POST['categorie']      = $res['categorie'][0] ;
	if ( ! isset ( $_POST['type'] ) )           $_POST['type']           = $res['type'][0] ;
	if ( ! isset ( $_POST['choix'] ) )          $_POST['choix']          = $res['choix'][0] ;
	if ( ! isset ( $_POST['administrateur'] ) ) $_POST['administrateur'] = $res['administrateur'][0] ;

	$mod = new ModeliXe ( "GestionOption.mxt" ) ; 
	$mod -> SetModeliXe ( ) ;
	if ( $this->erreurs2 ) $mod -> MxText ( "erreurs.errs", $this->erreurs2 ) ;
	else $mod -> MxBloc ( "erreurs", "delete" ) ;
	$mod -> MxText ( "titre", "Modification de l'option :" ) ;
	$mod -> MxFormField ( "libelle", "text", "libelle", stripslashes($_POST['libelle']), "" ) ;
	$mod -> MxFormField ( "description", "text", "description", stripslashes($_POST['description']), "" ) ;
	$mod -> MxCheckerField ( "administrateur", "checkbox", "administrateur", 1, ($_POST['administrateur']?true:false) ) ;
	$categories = $this->getCategories ( ) ;
	$mod -> MxSelect ( "categories", "categorie", $_POST['categorie'], $categories , '', '', "onChange=reload(this.form)") ;
	$mod -> MxFormField ( "nouvelle.nouvelle", "text", "nouvelle", (isset($_POST['nouvelle'])?stripslashes($_POST['nouvelle']):''), "" ) ;
	$types['text']     = "Champs libre (Texte)" ;
	$types['bool']     = "Booléen (Oui/Non)" ;
	$types['combobox'] = "Liste de valeurs (Combobox)" ;
	$mod -> MxSelect ( "types", "type", $_POST['type'], $types , '', '', "onChange=reload(this.form)") ;
	switch ( $_POST['type'] ) {
	case 'combobox':
	  $mod -> MxFormField ( "choix.tchoix", "text", "choix", ($_POST['choix']?stripslashes($_POST['choix']):"choix1|choix2|choix3..."), "" ) ;
	  break;
	default:
	  $mod -> MxBloc ( "choix", "delete" ) ;
	  break;
	}
	$mod -> MxBloc ( "ajouter", "delete" ) ;
	$mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), $session->getNavi(3) ) ) ;
	return $mod -> MxWrite ( "1" ) ;
      }
    }
  }

  // Ajout de l'option.
  function addNewOption ( ) {
    $req = new clResultQuery ;

    $data['libelle']     = stripslashes($_POST['libelle']) ;
    $data['description'] = stripslashes($_POST['description']) ;
    $data['type']        = $_POST['type'] ;
    $data['choix']       = (isset($_POST['choix'])?stripslashes($_POST['choix']):'') ;
    $data['categorie']   = ($_POST['nouvelle']?stripslashes($_POST['nouvelle']):stripslashes($_POST['categorie'])) ;
    $data['idapplication'] = IDAPPLICATION ;
    $data['administrateur'] = ((isset($_POST['administrateur']) AND $_POST['administrateur'])?1:0) ;

    $req = new clRequete ( BASEXHAM, TABLEOPTS, $data ) ;
    $ris = $req -> addRecord ( ) ;
  }

  // Création d'un nouvelle option.
  function addOption ( ) {
    global $session ;
    if ( $session -> getDroit ( "Configuration_Options", "a" ) ) {
      if ( isset ( $_POST['Ajouter'] ) || isset ( $_POST['Ajouter_x'] ) ) {
	if ( $this->verifForm ( ) ) {
	  $this->addNewOption ( ) ;
	  $this->infos .= "La création de l'option '".stripslashes($_POST['libelle'])."' a bien été effectuée." ;
	}
      }
      if ( ! $this->stop ) {
	$mod = new ModeliXe ( "GestionOption.mxt" ) ; 
	$mod -> SetModeliXe ( ) ;
	if ( $this->erreurs2 ) $mod -> MxText ( "erreurs.errs", $this->erreurs2 ) ;
	else $mod -> MxBloc ( "erreurs", "delete" ) ;
	$mod -> MxText ( "titre", "Ajout d'une nouvelle option :" ) ;
	$mod -> MxFormField ( "libelle", "text", "libelle", (isset($_POST['libelle'])?stripslashes($_POST['libelle']):''), "" ) ;
	$mod -> MxFormField ( "description", "text", "description", (isset($_POST['description'])?stripslashes($_POST['description']):''), "" ) ;
	$mod -> MxCheckerField ( "administrateur", "checkbox", "administrateur", 1, ((isset($_POST['administrateur']) and $_POST['administrateur'])?true:false) ) ;
	$categories = $this->getCategories ( ) ;
	$mod -> MxSelect ( "categories", "categorie", (isset($_POST['categorie'])?$_POST['categorie']:''), $categories , '', '', "onChange=reload(this.form)") ;
	$mod -> MxFormField ( "nouvelle.nouvelle", "text", "nouvelle", (isset($_POST['nouvelle'])?stripslashes($_POST['nouvelle']):''), "" ) ;
	$types['text']     = "Champs libre (Texte)" ;
	$types['bool']     = "Booléen (Oui/Non)" ;
	$types['combobox'] = "Liste de valeurs (Combobox)" ;
	$mod -> MxSelect ( "types", "type", (isset($_POST['type'])?$_POST['type']:''), $types , '', '', "onChange=reload(this.form)") ;
	switch ( (isset($_POST['type'])?$_POST['type']:'') ) {
	case 'combobox':
	  $mod -> MxFormField ( "choix.tchoix", "text", "choix", ((isset($_POST['choix']) and $_POST['choix'])?stripslashes($_POST['choix']):"choix1|choix2|choix3..."), "" ) ;
	  break;
	default:
	  $mod -> MxBloc ( "choix", "delete" ) ;
	  break;
	}
	$mod -> MxBloc ( "modifier", "delete" ) ;
	$mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), $session->getNavi(3) ) ) ;
	return $mod -> MxWrite ( "1" ) ;
      }
    }
  }

  // Vérifie si une option d'une liste (classes clListes et clListesGenerales) existe ou non.
  // Si elle n'existe pas, elle est créée au passage.
  function checkOptionListe ( $option, $complexe='' ) {
    $param['cw'] = "WHERE libelle='".addslashes(stripslashes($option))."' AND idapplication=".IDAPPLICATION ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;  
    if ( ! $res['INDIC_SVC'][2] ) {
      if ( $complexe ) {
	$data['libelle']     = $option ;
	$data['description'] = "Classement de la liste '$option'." ;
	$data['type']        = "combobox" ;
	$data['choix']       = "Manuel|Alphabétique|Alphabétique inversé" ;
	$data['valeur']      = "Manuel" ;
	$data['categorie']   = $option ;
	$data['idapplication'] = IDAPPLICATION ;
	$req = new clRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	$ris = $req -> addRecord ( ) ;

	$data['libelle']     = "Catégories ".$option ;
	$data['description'] = "Classement de la liste des catégories de '$option'." ;
	$data['type']        = "combobox" ;
	$data['choix']       = "Manuel|Alphabétique|Alphabétique inversé" ;
	$data['valeur']      = "Manuel" ;
	$data['categorie']   = $option ;
	$data['idapplication'] = IDAPPLICATION ;
	$req = new clRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	$ris = $req -> addRecord ( ) ;

	$data['libelle']     = "Lignes ".$option ;
	$data['description'] = "Nombre de lignes dans les listes de gestion de '$option'." ;
	$data['type']        = "combobox" ;
	$data['choix']       = "5|10|15|20|25|30" ;
	$data['valeur']      = "15" ;
	$data['categorie']   = $option ;
	$data['idapplication'] = IDAPPLICATION ;
	$req = new clRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	$ris = $req -> addRecord ( ) ;
      } else {
	switch ( $option ) {
	case 'LignesParListe':
	  $data['libelle']     = "LignesParListe" ;
	  $data['description'] = "Nombre de lignes par liste dans la partie administration." ;
	  $data['type']        = "combobox" ;
	  $data['choix']       = "3|4|5|6|7|8|9" ;
	  $data['valeur']      = "8" ;
	  $data['categorie']   = "Listes Générales" ;
	  $data['idapplication'] = IDAPPLICATION ;
	  $req = new clRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	  $ris = $req -> addRecord ( ) ;
	  break;
	case 'ListesParLigne':
	  $data['libelle']     = "ListesParLigne" ;
	  $data['description'] = "Nombre de listes par ligne dans la partie administration." ;
	  $data['type']        = "combobox" ;
	  $data['choix']       = "3|4|5" ;
	  $data['valeur']      = "4" ;
	  $data['categorie']   = "Listes Générales" ;
	  $data['idapplication'] = IDAPPLICATION ;
	  $req = new clRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	  $ris = $req -> addRecord ( ) ;
	  break;
	default:
	  $data['libelle']     = $option ;
	  $data['description'] = "Classement de la liste des '$option'." ;
	  $data['type']        = "combobox" ;
	  $data['choix']       = "Manuel|Alphabétique|Alphabétique inversé" ;
	  $data['valeur']      = "Manuel" ;
	  $data['categorie']   = "Listes Générales" ;
	  $data['idapplication'] = IDAPPLICATION ;
	  $req = new clRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	  $ris = $req -> addRecord ( ) ;
	  $data['libelle']     = $option." Id" ;
	  $data['description'] = "Gestion d'un code rattaché aux items de la liste '$option'." ;
	  $data['type']        = "bool" ;
	  $data['choix']       = "" ;
	  $data['valeur']      = "0" ;
	  $data['categorie']   = "Listes Générales" ;
	  $data['idapplication'] = IDAPPLICATION ;
	  $req = new clRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	  $ris = $req -> addRecord ( ) ;
	  break ;
	}
      }
    }
  }

  // Retourne un tableau contenant les différentes catégories.
  function getCategories ( ) {
    // Récupération de la liste des catégories.
    $param['cw'] = "WHERE idapplication=".IDAPPLICATION." ORDER BY categorie" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getCategoriesOptions", $param, "ResultQuery" ) ;  
    for ( $i = 0 ; isset ( $res['categorie'][$i] ) ; $i++ ) {
      $tab[$res['categorie'][$i]] = $res['categorie'][$i] ;
    }
    if ( is_array ( $tab ) ) return $tab ;
    else return array ( ) ;
  }

  // Affiche la liste des catégories et des options qu'elles contiennent.
  function listeCategories ( ) {
    global $session ;
    // Droit de lecture requis.
    if ( $session->getDroit ( "Configuration_Options", "r" ) ) {
      if ( $session->getNavi ( 2 ) == "Supprimer" and $session->getDroit ( "Configuration_Options", "a" ) and ! isset ( $_POST['Annuler'] ) and ! isset ( $_POST['Annuler_x'] ) ) {
	$this->af .= $this->delOption ( $session->getNavi ( 3 ) ) ; 
      }
      if ( $session->getNavi ( 2 ) == "Ajouter" and $session->getDroit ( "Configuration_Options", "a" ) and ! isset ( $_POST['Annuler'] ) and ! isset ( $_POST['Annuler_x'] ) ) {
	$this->af .= $this->addOption ( ) ; 
      }
      if ( $session->getNavi ( 2 ) == "Administrer" and $session->getDroit ( "Configuration_Options", "a" ) and ! isset ( $_POST['Annuler'] ) and ! isset ( $_POST['Annuler_x'] ) ){
	$this->af .= $this->modOption ( ) ; 
      }

      // Configuration de mise à jour.
      if ( $session->getNavi ( 2 ) == "Valider" and  $session->getDroit ( "Configuration_Options", "m" ) and ( isset ( $_POST['Valider'] ) or isset ( $_POST['Valider_x'] ) ) ) {
	$this->validerModification ( $session->getNavi ( 3 ) ) ;
      }
      if ( $session->getNavi ( 2 ) == "Afficher" ) {
	$_SESSION['voir'.$session->getNavi ( 3 )] = 1 ;
      }
      if ( $session->getNavi ( 2 ) == "Masquer" ) {
	$_SESSION['voir'.$session->getNavi ( 3 )] = 0 ;
      }
      // Récupération de la liste des catégories.
      if ( $session->getDroit ( "Configuration_Options", "a" ) )
      	$param['cw'] = "WHERE idapplication=".IDAPPLICATION." ORDER BY categorie" ;
      else $param['cw'] = "WHERE idapplication=".IDAPPLICATION." AND administrateur=0 ORDER BY categorie" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getCategoriesOptions", $param, "ResultQuery" ) ;  
      // Remplissage du template.
      $mod = new ModeliXe ( "GestionDesOptions.mxt" ) ;
      $mod -> SetModeliXe ( ) ;

      // Si l'utilisateur possède le droit d'administration, alors on affiche
      // un lien pour ajouter des options.
      if ( $session->getDroit ( "Configuration_Options", "a" ) ) {
	$mod -> MxImage ( "imgAjouter", URLIMGAJO, "Ajouter" ) ;
	$mod -> MxUrl  ( "lienAjouter", URLNAVI.$session->genNavi ( "Configuration", "Configuration_Options", "Ajouter" ) ) ;
      }

      // Pour chaque catégorie trouvée, on boucle sur le bloc "categorie"
      for ( $i = 0 ; isset ( $res['categorie'][$i] ) ; $i++ ) {
	// Affichage du titre de la categorie.
	$mod -> MxText ( "categorie.titre", $res['categorie'][$i] ) ;
	if ( isset ( $_SESSION['voir'.$res['categorie'][$i]] ) AND $_SESSION['voir'.$res['categorie'][$i]] ) {
	  $mod -> MxUrl ( "categorie.lienVoir", URLNAVI.$session->genNavi ( "Configuration", "Configuration_Options", "Masquer", $res['categorie'][$i] ) ) ;
	  $mod -> MxImage ( "categorie.imgVoir", URLIMGMAS, "Masquer" ) ;
	  // On recherche les options appartenant à la catégorie.
	  if ( $session->getDroit ( "Configuration_Options", "a" ) )
	  	$param['cw'] = "WHERE categorie='".$res['categorie'][$i]."' AND idapplication=".IDAPPLICATION." ORDER BY libelle" ;
	  else $param['cw'] = "WHERE categorie='".$res['categorie'][$i]."' AND idapplication=".IDAPPLICATION." AND administrateur=0 ORDER BY libelle" ;
	  $req = new clResultQuery ;
	  $ras = $req -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;  
	  // Pour chacune de ces options, on boucle sur le bloc "categorie.option"
	  for ( $j = 0 ; isset ( $ras['idoption'][$j] ) ; $j++ ) {
	    // Alternance des couleurs en fonction du numéro de ligne.
	    if ( $j % 2 ) $mod -> MxText ( "categorie.option.ligne", "<tr class=\"paire\">" ) ;
	    else $mod -> MxText ( "categorie.option.ligne", "<tr class=\"impaire\">" ) ;
	    // Remplissage des informations.
	    $mod -> MxText ( "categorie.option.soustitre", $ras['libelle'][$j] ) ;
	    $mod -> MxText ( "categorie.option.description", $ras['description'][$j] ) ;
	    // Si la modification est en cours et si le droit de modification est présent,
	    // alors on affiche le mini-formulaire de modification.
	    if ( $session->getNavi ( 2 ) == "Modifier" and $session->getNavi ( 3 ) == $ras['idoption'][$j] and $session->getDroit ( "Configuration_Options", "m" ) ){
	      $mod -> MxText ( "categorie.option.ligne", "<tr class=\"modification\">" ) ;
	      // Génération du formulaire.
	      $html = $this -> getModification ( $ras, $j ) ;
	      $mod -> MxBloc ( "categorie.option.form", "modify", $html ) ;
	      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "Valider", $ras['idoption'][$j] ) ) ;
	    } else { 
	      // Sinon, on affiche la valeur de l'option.
	      switch ( $ras['type'][$j] ) {
	      case 'bool':
		if ( $ras['valeur'][$j] ) $valeur = "Oui" ;
		else $valeur = "Non" ;
		break ;
	      default :
		$valeur = $ras['valeur'][$j] ;
		break ;
	      }
	      $mod -> MxText ( "categorie.option.normal.valeur", $valeur ) ;
	      // Si l'utilisateur possède le droit de modification, alors on affiche
	      // un lien pour modifier l'option.
	      if ( ( $session->getDroit ( "Configuration_Options", "m" ) AND ! $ras['administrateur'][$j] ) OR ( $session->getDroit ( "Configuration_Options", "a" ) AND $ras['administrateur'][$j] ) ) {
		$mod -> MxImage ( "categorie.option.normal.imgModifier", URLIMGMOD, "Modifier" ) ;
		$mod -> MxUrl  ( "categorie.option.normal.lien", URLNAVI.$session->genNavi ( "Configuration", "Configuration_Options", 
											   "Modifier", $ras['idoption'][$j] ) ) ;
	      }
	      // Si l'utilisateur possède le droit d'administration, alors on affiche
	      // un lien pour supprimer l'option.
	      if ( $session->getDroit ( "Configuration_Options", "a" ) ) {
		$mod -> MxImage ( "categorie.option.normal.imgModifier2", URLIMGMO3, "Administrer" ) ;
		$mod -> MxUrl  ( "categorie.option.normal.lienModifier2", URLNAVI.$session->genNavi ( "Configuration", "Configuration_Options", 
											   "Administrer", $ras['idoption'][$j] ) ) ;
		$mod -> MxImage ( "categorie.option.normal.imgSupprimer", URLIMGSUP, "Supprimer" ) ;
		$mod -> MxUrl  ( "categorie.option.normal.lienSupprimer", URLNAVI.$session->genNavi ( "Configuration", "Configuration_Options", 
											   "Supprimer", $ras['idoption'][$j] ) ) ;
	      }
	      $mod -> MxBloc ( "categorie.option.form", "modify", " " ) ;
	    }
	    $mod -> MxBloc ( "categorie.option", "loop" ) ;
	  }
	} else {
	  $mod -> MxUrl ( "categorie.lienVoir", URLNAVI.$session->genNavi ( "Configuration", "Configuration_Options", "Afficher", $res['categorie'][$i] ) ) ;
	  $mod -> MxImage ( "categorie.imgVoir", URLIMGAFF, "Afficher" ) ;
	  $mod -> MxBloc ( "categorie.option.form", "modify", " " ) ;
	}
	$mod -> MxBloc ( "categorie", "loop" ) ;
      }
      return $mod -> MxWrite ( "1" ) ;
    }
  }

  // Génération du mini-formulaire.
  function getModification ( $res, $ind ) {
    global $session ;
    if ( ( $session->getDroit ( "Configuration_Options", "m" ) AND ! $res['administrateur'][$ind] ) OR ( $session->getDroit ( "Configuration_Options", "a" ) AND $res['administrateur'][$ind] ) ) {
      // Requête pour récupérer les informations actuelles de l'option.
      $param['cw'] = "WHERE idoption='".$res['idoption'][$ind]."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ; 
      $form = new clForm ;
      $af = "<table class=\"invisible\"><tr><td class=\"colarge\">" ;
      // En fonction du type de l'option le formulaire généré sera différent.
      switch ( $res['type'][0] ) {
      case 'bool':
	$data[0] = "Non" ; $data[1] = "Oui" ;
	$af .= $form -> genSelect ( "option", $res['valeur'][0], $data )."</td><td>" ;
	break ;
      case 'combobox':
	$rus = explode ( "|", $res['choix'][0] ) ;
	for ( $i = 0 ; isset ( $rus[$i] ) ; $i++ ) { $data[$rus[$i]] = $rus[$i] ;  }
	$af .= $form -> genSelect ( "option", $res['valeur'][0], $data )."</td><td>" ;
	break ;
      default:
	$value = (isset($res['valeur'][$ind])?$res['valeur'][$ind]:'') 	;
	$af .= $form -> genText ( "option", $res['valeur'][0] )."</td><td>" ;
	break ;
      }
      // Bouton valider.
      $af .= $form -> genImage ( "Valider", "1", URLIMGVAL )."</td><td>" ;
      // Bouton annuler.
      $af .= $form -> genImage ( "Annuler", "1", URLIMGANU ) ;
      $af .= "</td></tr></table>" ;
      return $af ;
    }
  }



  // Mise à jour des menus de cette application en production.
  function majOptionsProd ( ) {
    // Récupération de la liste des options en dev.
    $req = new clResultQuery ;
    $param['cw'] = "WHERE idapplication=".IDAPPLICATION ;
    $res = $req -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;
    //eko ( $res[INDIC_SVC] ) ;
    // Préparation de la liste des attributs d'une option.
    while ( list ( $key, $val ) = each ( $res ) ) {
      if ( $key != "INDIC_SVC" AND $key != "idoption" ) $keys[] = $key ;
    }
    // Pour chaque option,
    for ( $i = 0 ; isset ( $res['idoption'][$i] ) ; $i++ ) {
      // On regarde si elle existe en production.
      $param['cw'] = "WHERE libelle='".addslashes($res['libelle'][$i])."' AND idapplication=".IDAPPLICATION ;
      $ras = $req -> Execute ( "Fichier", "getOptionsProd", $param, "ResultQuery" ) ;
      //eko ( $ras[INDIC_SVC] ) ;
      // S'il elle n'existe pas,
      if ( ! $ras['INDIC_SVC'][2] ) {
	// On prépare le tableau contenant les données de l'option.
	for ( $j = 0 ; isset ( $keys[$j] ) ; $j++ ) {
	  $data[$keys[$j]] = $res[$keys[$j]][$i] ;
	}
	// Et on crée cette option en production.
	$raq = new clRequete ( BASEXHAM, TABLEOPTS, $data, 'prod' ) ;
	$ris = $raq -> addRecord ( ) ;
	eko ( "insert : ".$res['libelle'][$i] ) ;
	// Si elle existe,
      } else {
	// On crée le tableau contenant les champs à mettre à jour.
	for ( $j = 0 ; isset ( $keys[$j] ) ; $j++ ) {
	  if ( $keys[$j] != "valeur" )
	    $data[$keys[$j]] = $res[$keys[$j]][$i] ;
	}
	// Mise à jour de l'option.
	eko ( "update : ".$res['libelle'][$i] ) ;
	$raq = new clRequete ( BASEXHAM, TABLEOPTS, $data, 'prod' ) ;
	$ris = $raq -> updRecord ( "libelle='".addslashes($res['libelle'][$i])."' AND idapplication=".IDAPPLICATION ) ;
      }

    }
    // Message d'information sur l'opération effectuée.
    $this->infos .= "La liste des droits a été mise à jour en production." ;
  }

  // Retourne l'affichage généré par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}

?>
