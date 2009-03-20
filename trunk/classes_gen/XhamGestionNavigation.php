<?php

// Titre  : Classe GestionNavigation
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 13 Juin 2005

// Description : 
// Permet la gestion des menus
// et de la navigation. 


class XhamGestionNavigation {

  // Attribut contenant l'affichage.
  private $af ;
  private $erreurs ;
  private $erreurs2 ;
  private $infos ;
  private $stop ;

  // Constructeur de la classE.
  function __construct ( $xham ) {
  	$this -> xham = $xham ;
    // V�rification du droit de lecture sur ce module.
    if ( $this->xham -> getDroit ( "Configuration_Navigation", "r" ) ) {
      // Si le bouton "annuler" n'a pas �t� press�...
      if ( ! isset ( $_POST['Annuler'] ) AND ! isset ( $_POST['Annuler_x'] ) ) {
	// En fonction de l'action � effectuer, on appelle les bonnes m�thodes.
	switch ( $this->xham -> getNavi ( 2 ) ) {
	case 'MajMenuProd':
	  // On met � jour les menus en production.
	  $action = $this->MajMenuProd ( ) ;
	  break ;
	case 'MonterMenu':
	  // On monte le menu pass� en argument d'une case.
	  $action = $this->modRang ( $this->xham -> getNavi ( 3 ), "menu", "-1" ) ;
	  break ;
	case 'DescendreMenu':
	  // On descend le menu pass� en argument d'une case.
	  $action = $this->modRang ( $this->xham -> getNavi ( 3 ), "menu", "1" ) ;
	  break ;
	case 'Monter':
	  // On monte le sous-menu pass� en argument d'une case.
	  $action = $this->modRang ( $this->xham -> getNavi ( 3 ), "item", "-1" ) ;
	  break ;
	case 'Descendre':
	  // On descend le sous-menu pass� en argument d'une case.
	  $action = $this->modRang ( $this->xham -> getNavi ( 3 ), "item", "1" ) ;
	  break ;
	case 'ModifierMenu':
	  // On lance la modification du menu.
	  $action = $this->modMenu ( $this->xham -> getNavi ( 3 ), "menu" ) ;
	  break ;
	case 'Modifier':
	  // On lance la modification du sous-menu.
	  $action = $this->modMenu ( $this->xham -> getNavi ( 3 ), "item" ) ;
	  break ;
	case 'SupprimerMenu':
	  // Lancement de la suppression d'un menu.
	  $action = $this->delMenu ( $this->xham -> getNavi ( 3 ), "menu" ) ;
	  break ;
	case 'Supprimer':
	  // Lancement de la suppression d'un sous-menu.
	  $action = $this->delMenu ( $this->xham -> getNavi ( 3 ), "item" ) ;
	  break ;
	case 'AjouterMenu':
	  // Formulaire de cr�ation de menu/sous-menu.
	  $action = $this->addMenu ( ) ;
	  break ;
	default:
	  $action = '' ;
	  break ;
	}
      } else { $action = '' ; }
      // On lance la g�n�ration de l'affichage.
      $this->af .= $this->genAffichage ( $action ) ;
    }
  }
  
  // V�rification de la validit� des informations transmises.
  function verifForm ( $res='' ) {
    // V�rification de la pr�sence d'un libell�.
    if ( ! $_POST['libelle'] ) $this->erreurs2 .= "Le libell� ne doit pas �tre vide.<br />" ;
    // V�rification de la pr�sence de la cl�.
    if ( ! $_POST['cle'] ) $this->erreurs2 .= "Le cl� ne doit pas �tre vide.<br />" ;
    // Si le libell� a �t� chang�,

    // On v�rifie que la nouvelle cl� n'est pas d�j� utilis�e.
    if ( ( $res AND $res['cle'][0] != $_POST['cle'] ) OR ( ! $res ) ) {
      $param['cw'] = "WHERE cle='".$_POST['cle']."' AND idapplication=".IDAPPLICATION ;
      $ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      if ( $ras['INDIC_SVC'][2] ) {
	// Si elle est d�j� utilis�e, on signale l'erreur.
	$this->erreurs2 .= "La cl� choisie est d�j� utilis�e.<br />" ;
      }
    }
    // S'il n'y a pas d'erreur, on retourne "vrai".
    if ( ! $this->erreurs2 ) {
      $this->stop = 1 ;
      return 1 ;
    }
  }

  // Mise � jour d'un menu dont l'identifiant est pass� en argument.
  function majMenu ( $res ) {

    // Si on passe d'un menu � un sous-menu...
    if ( $res['type'][0] == "menu" AND $_POST['type'] ) {
      // On met � jour les rangs des menus.
      $this->majRangs ( $res['rang'][0] ) ;
      // On r�cup�re la nouvelle valeur du rang de menu mis � jour.
      $param['cw'] = "WHERE menuparent='".$_POST['type']."' AND idapplication=".IDAPPLICATION ;
      $ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      $data['rang'] = $ras['INDIC_SVC'][2] + 1 ;
      // Si on passe d'un sous-menu � un menu...
    } else if ( $res['type'][0] == "item" AND ! $_POST['type'] ) {
      // On met � jour les rangs au sein de ses anciens potes sous-menu.
      $this->majRangs ( $res['rang'][0], $res['menuparent'][0] ) ;
      // On r�cup�re le nouveau rang.
      $param['cw'] = "WHERE type='menu' AND idapplication=".IDAPPLICATION ;
      $ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      $data['rang'] = $ras['INDIC_SVC'][2] + 1 ;
    }
    
    // Si on est dans le cas d'un sous-menu, on calcule sa cl� totale.
    if ( $_POST['type'] ) {
      $param['cw'] = "WHERE idunique='".$_POST['type']."' AND idapplication=".IDAPPLICATION ;
      $rus = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      $data['cletotale'] = $rus['cle'][0]."|".$_POST['cle'] ;
    } else {
      // Sinon, la cl� totale est la m�me que la cl� du menu.
      $data['cletotale'] = $_POST['cle'] ;
    }
    
    // Pr�paration des informations � mettre � jour.
    $data['libelle']    = stripslashes($_POST['libelle']) ;
    $data['type']       = ($_POST['type']?"item":"menu") ;
    $data['menuparent'] = $_POST['type'] ;
    $data['cle']        = stripslashes($_POST['cle']) ;
    $data['noption']    = $_POST['option'] ;
    $data['valeur']     = (isset($_POST['valeur'])?$_POST['valeur']:'') ;
    $data['droit']      = $_POST['droit'] ;
    $data['etat']       = $_POST['etat'] ;
    $data['classe']     = $_POST['classe'] ;
    $data['arguments']  = (isset($_POST['arguments'])?stripslashes($_POST['arguments']):'') ;
    $data['code']       = (isset($_POST['code'])?stripslashes($_POST['code']):'') ;

    // Mise � jour de l'enregistrement.
    $req = new XhamRequete ( BASEXHAM, TABLENAVI, $data ) ;
    $ris = $req -> updRecord ( "idmenu=".$res['idmenu'][0] ) ;
  }

  // G�n�ration des menus de base pour une nouvelle application.
  function genNewMenus ( ) {
  	$idunique = array ( ) ;
    // Tableau contenant les menus.
    $type = Array ( 'menu', 'menu', 'item', 'item', 'item', 'item', 'item', 'item', 'item', 'menu', 'item', 'menu', 'item' ) ;
    $rang = Array ( 1, 2, 1, 2, 3, 4, 5, 6, 7, 3, 1, 4, 8 ) ;
    $libelle = Array ( 'Accueil', 'Configuration', 'Utilisateurs', 'Groupes', 'Droits', 'Options', 'Navigation', 'Sessions', 'Version', 'Administration', 'Listes G�n�rales', 'Changement Password', 'Logs' ) ;
    $cle = Array ( 'Accueil', 'Configuration', 'Configuration_Utilisateurs', 'Configuration_Groupes', 'Configuration_Droits', 'Configuration_Options', 'Configuration_Navigation', 'Configuration_Sessions', 'Configuration_Version', 'Administration', 'Administration_ListesGenerales', 'ChangementPassword', 'Logs' ) ;
    $cletotale = Array ( 'Accueil', 'Configuration', 'Configuration|Configuration_Utilisateurs', 'Configuration|Configuration_Groupes', 'Configuration|Configuration_Droits', 'Configuration|Configuration_Options', 'Configuration|Configuration_Navigation', 'Configuration|Configuration_Sessions', 'Configuration|Configuration_Version', 'Administration', 'Administration|Administration_ListesGenerales', 'ChangementPassword', 'Configuration|Logs' ); 
    $droit = Array ( 'Accueil', 'Configuration', 'Configuration_Utilisateurs', 'Configuration_Groupes', 'Configuration_Droits', 'Configuration_Options',  'Configuration_Navigation', 'Configuration_Sessions', 'Configuration_Version', 'Administration', 'Administration_ListesGenerales', 'Accueil', 'Configuration_Logs' ) ;
    $option = Array ( '', '', 'GestionMySQL', '', '', '', '', '', '', '', '', '' ) ;
    $valeur = Array ( '', '', '1', '', '', '', '', '', '', '', '', '' ) ;
    $etat = Array ( 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1 ) ;
    $classe = Array ( '', '', 'XhamGestionUtilisateurs', 'XhamGestionGroupes', 'XhamGestionDroits', 'XhamGestionOptions', 'XhamGestionNavigation', 'XhamStatsSessions', '', '', 
		      'XhamListesGenerales', 'XhamUtilisateurs', 'XhamAffichageLogs' ) ;
    $arg = Array ( '$this->xham', '$this->xham', '$this->xham', '$this->xham', '$this->xham', '$this->xham', '$this->xham', '$this->xham', '$this->xham', '$this->xham', '$this->xham', '$this->xham', '$this->xham' ) ;
    $code = Array ( '$mod = new ModeliXe ( "Accueil.mxt" ) ; $mod -> SetModeliXe ( ) ; $this->af .= $mod -> MxWrite ( "1" ) ;',  '', '', '', '', '', '', '',
		    '$this->af .= "<pre>".file_get_contents(URLPATCHS)."</pre>";', '', '', '', '' ) ;
    $lectureseule = Array ( 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1 ) ;
    $sousmenu = Array ( '', '', 1, 1, 1, 1, 1, 1, 1,'', 9, '', 1 ) ;

    // On parcourt les diff�rents items / sous-menus.
    for ( $i = 0 ; isset ( $libelle[$i] ) ; $i++ ) {
      // G�n�ration d'un identifiant de menu unique.
      do {
	$idUnique = $this->genIdentifiantUnique ( ) ;
	$param['cw'] = "WHERE idunique='$idUnique'" ;
	$ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      } while ( $ras['INDIC_SVC'][2] ) ;
      // R�cup�ration de l'identifiant du menu parent si c'est un sous-menu.
      if ( $sousmenu[$i] ) $data['menuparent'] = $idunique[$sousmenu[$i]] ;
      else
	$data['menuparent']  = '' ;
      // Pr�paration de champs du nouveau menu.
      $idunique[$i]        = $idUnique ;
      $data['idunique']      = $idUnique ;
      $data['idapplication'] = IDAPPLICATION ;
      $data['type']          = $type[$i] ;
      $data['rang']          = $rang[$i] ;
      $data['libelle']       = $libelle[$i] ;
      $data['cle']           = $cle[$i] ;
      $data['cletotale']     = $cletotale[$i] ;
      $data['noption']       = $option[$i] ;
      $data['valeur']        = $valeur[$i] ;
      $data['droit']         = $droit[$i] ;
      $data['etat']          = $etat[$i] ;
      $data['classe']        = $classe[$i] ;
      $data['arguments']     = $arg[$i] ;
      $data['code']          = $code[$i] ;
      $data['lectureseule']  = $lectureseule[$i] ;
      // Ajout du nouveau menu.
      $req = new XhamRequete ( BASEXHAM, TABLENAVI, $data ) ;
      $ris = $req -> addRecord ( ) ;
    }
  }

  // Cr�ation du nouveau menu ou sous-menu.
  function addNewMenu ( ) {
    
    // Cas d'un sous-menu.
    if ( $_POST['type'] ) {
      // Calcul du rang.
      $param['cw'] = "WHERE menuparent='".$_POST['type']."' AND idapplication=".IDAPPLICATION ;
      $ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      $data['rang'] = $ras['INDIC_SVC'][2] + 1 ;
      // Calcul de la cl� totale.
      $param['cw'] = "WHERE idunique='".$_POST['type']."' AND idapplication=".IDAPPLICATION ;
      $rus = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      $data['cletotale'] = $rus['cle'][0]."|".$_POST['cle'] ;
      // Cas d'un menu.
    } else {
      // Calcul du rang.
      $param['cw'] = "WHERE type='menu'"." AND idapplication=".IDAPPLICATION ;
      $ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      $data['rang'] = $ras['INDIC_SVC'][2] + 1 ;
      // Calcul de la cl�.
      $data['cletotale'] = $_POST['cle'] ;
    }

    // Pr�paration des informations du nouveau menu/sous-menu.
    $data['libelle']    = stripslashes($_POST['libelle']) ;
    do {
      $idUnique = $this->genIdentifiantUnique ( ) ;
      $param['cw'] = "WHERE idunique='$idUnique'" ;
      $ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
    } while ( $ras['INDIC_SVC'][2] ) ;
    $data['idunique']   = $idUnique ;
    $data['type']       = ($_POST['type']?"item":"menu") ;
    $data['menuparent'] = $_POST['type'] ;
    $data['cle']        = stripslashes($_POST['cle']) ;
    $data['noption']    = $_POST['option'] ;
    $data['valeur']     = (isset($_POST['valeur'])?$_POST['valeur']:'') ;
    $data['droit']      = $_POST['droit'] ;
    $data['etat']       = $_POST['etat'] ;
    $data['classe']     = $_POST['classe'] ;
    $data['arguments']  = (isset($_POST['arguments'])?stripslashes($_POST['arguments']):'') ;
    $data['code']       = (isset($_POST['code'])?stripslashes($_POST['code']):'') ;
    $data['idapplication'] = IDAPPLICATION ;

    // Insertion du nouvel enregistrement.
    $req = new XhamRequete ( BASEXHAM, TABLENAVI, $data ) ;
    $ris = $req -> addRecord ( ) ;
  }

  // Formulaire de modification d'un menu.
  function modMenu ( $idmenu, $type ) {
    // V�rification de la pr�sence du droit de modification.
    if ( $this->xham -> getDroit ( "Configuration_Navigation", "m" ) ) {    
      // R�cup�ration des informations actuelles.
      $param['cw'] = "WHERE idunique='$idmenu'"." AND idapplication=".IDAPPLICATION ;
      $res = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      // On lance la modification si "Modifier" a �t� press�.
      if ( isset ( $_POST['Modifier'] ) OR isset ( $_POST['Modifier_x'] ) ) {
	// V�rification de la validit� des informations saisies.
	if ( $this->verifForm ( $res ) ) {
	  // Mise � jour du menu.
	  $this->majMenu ( $res ) ;
	  $this->infos .= "La modification du menu '".$res['libelle'][0]."' a bien �t� effectu�e." ;
	}
      }
      // Si aucune mise � jour n'a �t� faite.
      if ( ! $this->stop ) {
	// On r�cup�re les informations de la base.
	if ( ! isset ( $_POST['libelle'] ) )   $_POST['libelle']   = $res['libelle'][0] ;
	if ( ! isset ( $_POST['type'] ) )      $_POST['type']      = $res['menuparent'][0] ;
	if ( ! isset ( $_POST['cle'] ) )       $_POST['cle']       = $res['cle'][0] ;
	if ( ! isset ( $_POST['option'] ) )    $_POST['option']    = $res['noption'][0] ;
	if ( ! isset ( $_POST['valeur'] ) )    $_POST['valeur']    = $res['valeur'][0] ;
	if ( ! isset ( $_POST['droit'] ) )     $_POST['droit']     = $res['droit'][0] ;
	if ( ! isset ( $_POST['etat'] ) )      $_POST['etat']      = $res['etat'][0] ;
	if ( ! isset ( $_POST['classe'] ) )    $_POST['classe']    = $res['classe'][0] ;
	if ( ! isset ( $_POST['arguments'] ) ) $_POST['arguments'] = $res['arguments'][0] ;

	if ( ! isset ( $_POST['code'] ) )      { 
	  if ( $res['code'][0] ) $_POST['code'] = $res['code'][0] ;
	  else $_POST['code'] = '$this->af .= "Affichage renvoy� par le code" ;' ;
	}
	
	// Chargement du template ModeliXe.
	$mod = new ModeliXe ( "GestionMenu.mxt" ) ; 
	$mod -> SetModeliXe ( ) ;
	// Remplissage des champs "texte".
	$mod -> MxText ( "titre", "Modification du menu '".$res['libelle'][0]."' :" ) ;
	$mod -> MxFormField ( "libelle", "text", "libelle", stripslashes($_POST['libelle']), "" ) ;
	$mod -> MxFormField ( "cle", "text", "cle", stripslashes($_POST['cle']), "" ) ;
	// Affichage des �ventuels messages d'erreurs.
	if ( $this->erreurs2 ) $mod -> MxText ( "erreurs.errs", $this->erreurs2 ) ;
	else $mod -> MxBloc ( "erreurs", "delete" ) ;
	// Fabrication de la liste des "sous-menu de..." possibles.
	$types = $this->getMenus ( $idmenu, $type ) ;
	$mod -> MxSelect ( "types", "type", $_POST['type'], $types , '', '', "onChange=reload(this.form)") ; 
	// R�cup�ration des options disponibles.
	$options = $this->getOptions ( ) ;
	$mod -> MxSelect ( "options", "option", $_POST['option'], $options , '', '', "onChange=reload(this.form)") ;
	// R�cup�ration des valeurs possibles de l'option.
	$valeurs = $this->valeurOption ( $_POST['option'] ) ;
	if ( is_array ( $valeurs ) ) {
	  $mod -> MxSelect ( "valeur.svaleurs", "valeur", $_POST['valeur'], $valeurs , '', '', "onChange=reload(this.form)") ;
	} elseif ( $_POST['option'] ) {
	  $mod -> MxFormField ( "valeur.tvaleurs", "text", "valeur", $_POST['valeur'], "" ) ;
	} else $mod -> MxBloc ( "valeur", "delete" ) ;
	// R�cup�ration de la liste des droits disponibles.
	$droits = $this->getDroits ( ) ;
	$mod -> MxSelect ( "droits", "droit", $_POST['droit'], $droits , '', '', "onChange=reload(this.form)") ;
	// Les �tats possibles.
	$etats[1] = "Visible" ;
	$etats[0] = "Invisible" ;
	$mod -> MxSelect ( "etats", "etat", $_POST['etat'], $etats , '', '', "onChange=reload(this.form)") ;
	// Les classes disponibles.
	$classes = $this->getClasses ( ) ;
	$mod -> MxSelect ( "classes", "classe", $_POST['classe'], $classes , '', '', "onChange=reload(this.form)") ;
	if ( $_POST['classe'] ) {
	  $mod -> MxBloc ( "code", "delete" ) ;
	  $mod -> MxFormField ( "arguments.arguments", "text", "arguments", str_replace ( '"', '&quot;', stripslashes($_POST['arguments']) ), "" ) ;
	} else {
	  $mod -> MxBloc ( "arguments", "delete" ) ;
	  $mod -> MxText ( "code.code", stripslashes($_POST['code']) ) ;
	}
	$mod -> MxBloc ( "ajouter", "delete" ) ;
	$mod -> MxHidden ( "hidden", "navi=".$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), $this->xham->getNavi(2), $this->xham->getNavi(3) ) ) ;
	return $mod -> MxWrite ( "1" ) ;
      }
    }
  }

  // Gestion de l'ajout d'un nouveau menu.
  function addMenu ( ) {
    if ( $this->xham -> getDroit ( "Configuration_Navigation", "w" ) ) {
      if ( isset ( $_POST['Ajouter'] ) OR isset ( $_POST['Ajouter_x'] ) ) {
	if ( $this->verifForm ( ) ) {
	  $this->addNewMenu ( ) ;
	  $this->infos .= "La cr�ation du menu '".$_POST['libelle']."' a bien �t� effectu�e." ;
	}
      }
      if ( ! $this->stop ) {
	if ( ! isset ( $_POST['code'] ) ) $_POST['code'] = '$this->af .= "Affichage renvoy� par le code" ;' ;
	$mod = new ModeliXe ( "GestionMenu.mxt" ) ; 
	$mod -> SetModeliXe ( ) ;
	$mod -> MxText ( "titre", "Ajout d'un nouveau menu :" ) ;
	$mod -> MxFormField ( "libelle", "text", "libelle", (isset($_POST['libelle'])?stripslashes($_POST['libelle']):''), "" ) ;
	$mod -> MxFormField ( "cle", "text", "cle", (isset($_POST['cle'])?stripslashes($_POST['cle']):''), "" ) ;
	if ( $this->erreurs2 ) $mod -> MxText ( "erreurs.errs", $this->erreurs2 ) ;
	else $mod -> MxBloc ( "erreurs", "delete" ) ;
	$types = $this->getMenus ( (isset($idmenu)?$idmenu:''), (isset($type)?$type:'') ) ;
	$mod -> MxSelect ( "types", "type", (isset($_POST['type'])?$_POST['type']:''), $types , '', '', "onChange=reload(this.form)") ; 
	$options = $this->getOptions ( ) ;
	$mod -> MxSelect ( "options", "option", (isset($_POST['option'])?$_POST['option']:''), $options , '', '', "onChange=reload(this.form)") ;
	$valeurs = $this->valeurOption ( (isset($_POST['option'])?$_POST['option']:'') ) ;
	if ( is_array ( $valeurs ) ) {
	$mod -> MxSelect ( "valeur.svaleurs", "valeur", (isset($_POST['valeur'])?$_POST['valeur']:''), $valeurs , '', '', "onChange=reload(this.form)") ;
	} elseif ( (isset($_POST['option'])?$_POST['option']:'') ) {
	  $mod -> MxFormField ( "valeur.tvaleurs", "text", "valeur", $_POST['valeur'], "" ) ;
	} else $mod -> MxBloc ( "valeur", "delete" ) ;
	$droits = $this->getDroits ( ) ;
	$mod -> MxSelect ( "droits", "droit", (isset($_POST['droit'])?$_POST['droit']:''), $droits , '', '', "onChange=reload(this.form)") ;
	$etats[1] = "Visible" ;
	$etats[0] = "Invisible" ;
	$mod -> MxSelect ( "etats", "etat", (isset($_POST['etat'])?$_POST['etat']:''), $etats , '', '', "onChange=reload(this.form)") ;
	$classes = $this->getClasses ( ) ;
	$mod -> MxSelect ( "classes", "classe", (isset($_POST['classe'])?$_POST['classe']:''), $classes , '', '', "onChange=reload(this.form)") ;
	if ( (isset($_POST['classe'])?$_POST['classe']:'') ) {
	  $mod -> MxBloc ( "code", "delete" ) ;
	  $mod -> MxFormField ( "arguments.arguments", "text", "arguments", (isset($_POST['arguments'])?$_POST['arguments']:''), "" ) ;
	} else {
	  $mod -> MxBloc ( "arguments", "delete" ) ;
	  $mod -> MxText ( "code.code", stripslashes($_POST['code']) ) ;
	}
	$mod -> MxBloc ( "modifier", "delete" ) ;
	$mod -> MxHidden ( "hidden", "navi=".$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), $this->xham->getNavi(2), $this->xham->getNavi(3) ) ) ;
	return $mod -> MxWrite ( "1" ) ;
      }
    }
  }

  // R�cup�ration de la liste des classes.
  function getClasses ( ) {
    $tab[] = "Pas de classe � appeler." ;
    $reps = Array ( "classes_int", "classes_gen", "modules" ) ;
    $rep = $reps ;
    while ( list ( $key, $val ) = each ( $rep ) ) {
      $dh = opendir ( $val ) ;
      while ( false !== ( $filename = readdir ( $dh ) ) ) {
	$files[] = $filename ;
	if ( ereg ( '.php$', $filename ) and ( ereg ( '^cl', $filename ) || ($val=='classes_int' || $val=='classes_gen') )) {
	  $file = explode ( ".", $filename ) ;
	  $tab[$file[0]] = "$val -> $filename" ;
	} else if ( is_dir ( "$val/$filename/classes_int/" ) AND $filename != '.' AND $filename != '..' ) {
	  $dh2 = opendir ( "$val/$filename/classes_int/" ) ;
	  while ( false !== ( $filename2 = readdir ( $dh2 ) ) ) {
	    $files[] = $filename2 ;
	    if ( ereg ( '.php$', $filename2 ) and ereg ( '^cl', $filename2 ) ) {
	      $file = explode ( ".", $filename2 ) ;
	      $tab[$file[0]] = "$filename -> $filename2" ;
	    }
	  }
	}
      }
    }
    return $tab ;
  }

  // R�cup�ration de la liste des droits.
  function getDroits ( ) {
    $param['cw'] = "WHERE idapplication=".IDAPPLICATION." ORDER BY libelle" ;
    $res = $this->xham -> Execute ( "Fichier", "getDroitsTous", $param, "ResultQuery" ) ;
    for ( $i = 0 ; isset ( $res['iddroit'][$i] ) ; $i++ ) {
      $tab[$res['libelle'][$i]] = $res['libelle'][$i] ;
    }
    return $tab ;
  }

  // R�cup�ration de la liste des options.
  function getOptions ( ) {
    $param['cw'] = "WHERE idapplication=".IDAPPLICATION." ORDER BY categorie" ;
    $res = $this->xham -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;
    $tab[] = "Pas d'option associ�e" ;
    for ( $i = 0 ; isset ( $res['idoption'][$i] ) ; $i++ ) {
      $tab[$res['libelle'][$i]] = $res['categorie'][$i]." -> ".$res['libelle'][$i] ;
    }
    return $tab ;
  }

  // R�cup�ration des valeurs possibles d'une option.
  function valeurOption ( $libelle ) {
    $param['cw'] = "WHERE libelle='$libelle'"." AND idapplication=".IDAPPLICATION ;
    $res = $this->xham -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;
    if ( $res['INDIC_SVC'][2] ) {
      switch ( $res['type'][0] ) {
      case 'bool':
	$tab[1] = "Oui" ;
	$tab[0] = "Non" ;
	break ;
      case 'combobox':
	$choix = explode ( "|", $res['choix'][0] ) ;
	if ( is_array ( $choix ) )
	  while ( list ( $key, $val ) = each ( $choix ) )
	    $tab[$val] = $val ;
	break ;
      default:
	break ;
      } 
      return $tab ;
    } else return '' ;

  }

  // R�cup�ration de la liste des menus (ou des sous-menu d'un menu).
  function getMenus ( $idmenu, $type ) {
    if ( $type == "menu" ) {
      $param['cw'] = "WHERE menuparent='$idmenu'"." AND idapplication=".IDAPPLICATION ;
      $ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
    }
    $param['cw'] = "WHERE type='menu' AND idapplication=".IDAPPLICATION." ORDER BY rang" ;
    $res = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
    $tab[] = "Menu" ;
    if ( ! isset ( $ras ) OR ! $ras['INDIC_SVC'][2] ) {
      for ( $i = 0 ; isset ( $res['idmenu'][$i] ) ; $i++ ) {
	if ( $res['libelle'][$i] != "Accueil" )
	  $tab[$res['idunique'][$i]] = "Sous-menu de ".$res['libelle'][$i] ;
      }
    }
    return $tab ;
  }

  // Modification de la position (rang) d'un menu.
  function modRang ( $idmenu, $type, $action ) {
    if ( $this->xham -> getDroit ( "Configuration_Navigation", "m" ) ) {
      $param['cw'] = "WHERE idunique='$idmenu'"." AND idapplication=".IDAPPLICATION ;
      $res = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      if ( $type == "menu" ) {
	$param['cw'] = "WHERE type='menu'"." AND idapplication=".IDAPPLICATION ;
	$ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      } else {
	$param['cw'] = "WHERE menuparent='".$res['menuparent'][0]."' AND idapplication=".IDAPPLICATION ;
	$ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      }
      if ( $res['rang'][0] == 1 AND $action < 0 ) $this->erreurs .= "Impossible d'augmenter le rang de cet �l�ment." ;
      if ( $res['rang'][0] == $ras['INDIC_SVC'][2] AND $action > 0 ) $this->erreurs .= "Impossible de descendre le rang de cet �l�ment." ;
      
      $param['cw'] .= " AND rang=".($res['rang'][0]+$action) ;
      $rus = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      
      // Pr�paration du tableau de donn�es.
      $data['rang'] = ($res['rang'][0]+$action) ;
      // Appel de la classe Requete.
      $requete = new XhamRequete ( BASEXHAM, TABLENAVI, $data ) ;
      // Ex�cution de la requete.
      $requete->updRecord ( "idmenu=".$res['idmenu'][0] ) ;
      
      // Pr�paration du tableau de donn�es.
      $data['rang'] = $res['rang'][0] ;
      // Appel de la classe Requete.
      $requete = new XhamRequete ( BASEXHAM, TABLENAVI, $data ) ;
      // Ex�cution de la requete.
      $requete->updRecord ( "idmenu=".$rus['idmenu'][0] ) ;

      return (isset($af)?$af:'') ;
    }
  }

  // Supression d'un menu.
  function delMenu ( $idmenu, $type ) {
    if ( $this->xham -> getDroit ( "Configuration_Navigation", "d" ) ) {
      $param['cw'] = "WHERE idunique='$idmenu'"." AND idapplication=".IDAPPLICATION ;
      $res = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      if ( $type == "menu" ) {
	$param['cw'] = "WHERE menuparent='$idmenu'"." AND idapplication=".IDAPPLICATION ;
	$ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
      }
      if ( $res['lectureseule'][0] ) {
	$this->erreurs .= "C'est un menu syst�me, il est impossible de le supprimer." ;      
      } elseif ( isset ( $ras ) AND $ras['INDIC_SVC'][2] ) {
	$this->erreurs .= "Ce menu contient des items, il est impossible de le supprimer." ;            
      } else {
	if ( isset ( $_POST['Supprimer'] ) OR isset ( $_POST['Supprimer_x'] ) ) {
	  // Appel de la classe Requete.
	  $requete = new XhamRequete ( BASEXHAM, TABLENAVI ) ;
	  // Ex�cution de la requete.
	  $requete->delRecord ( "idunique='$idmenu'" ) ;
	  $this->infos .= "Le menu '".$res['libelle'][0]."' a bien �t� supprim�." ;
	  if ( $type == "menu" ) $this->majRangs ( $res['rang'][0] ) ;
	  else $this->majRangs ( $res['rang'][0], $res['menuparent'][0] ) ;
	} else {
	  $mod = new ModeliXe ( "FormConfirmation.mxt" ) ;
	  $mod -> SetModeliXe ( ) ;
	  $mod -> MxText ( "question", "Confirmez-vous la suppression du menu '".$res['libelle'][0]."' ?" ) ;
	  $mod -> MxHidden ( "hidden", "navi=".$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), $this->xham->getNavi(2), $this->xham->getNavi(3) ) ) ;
	  return $mod -> MxWrite ( "1" ) ;
	}
      }
    }
  }

  // Mise � jour des rangs des menus.
  function majRangs ( $rang, $menuparent='' ) {
    if ( $menuparent )
      $param['cw']  = "rang>=$rang AND menuparent='$menuparent'"." AND idapplication=".IDAPPLICATION ;
    else
      $param['cw']  = "rang>=$rang AND type='menu'"." AND idapplication=".IDAPPLICATION ;
    $param['set'] = "rang=rang-1" ;
    $res = $this->xham -> Execute ( "Fichier", "updateRangs", $param, "ResultQuery" ) ;
  }

  // G�n�ration d'un identifiant de menu. L'unicit� n'est pas v�rifi�e � ce niveau.
  function genIdentifiantUnique ( $taille='16' ) {
    $lettres = "0123456789abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $sid = '';
    for ( $i = 0 ; $i < $taille ; $i++ ) {
      $sid .= substr ( $lettres, ( rand ( ) % ( strlen ( $lettres ) ) ), 1 ) ;      
    }
    return $sid ;
  }

  // G�n�ration de l'affichage de la gestion des menus.
  function genAffichage ( $action ) {
    $mod = new ModeliXe ( "GestionDesMenus.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    
    if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
    else $mod -> MxBloc ( "informations", "modify", " " ) ;
    if ( $this->erreurs ) $mod -> MxText ( "erreurs.errs", $this->erreurs ) ;
    else $mod -> MxBloc ( "erreurs", "modify", " " ) ;

    $mod -> MxText ( "action", $action ) ;

    $param['cw'] = "WHERE type='menu' AND idapplication=".IDAPPLICATION." ORDER BY rang" ;
    $res = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;

    if ( $this->xham->getNavi ( 2 ) == "Afficher" ) {
      $_SESSION['voir'.$this->xham->getNavi ( 3 )] = 1 ;
    }
    if ( $this->xham->getNavi ( 2 ) == "Masquer" ) {
      $_SESSION['voir'.$this->xham->getNavi ( 3 )] = 0 ;
    }

    for ( $i = 0 ; isset ( $res['idmenu'][$i] ) ; $i++ ) {

      $mod -> MxText ( "menu.nom", $res['libelle'][$i] ) ;

      $param['cw'] = "WHERE type='item' AND menuparent='".$res['idunique'][$i]."' AND idapplication=".IDAPPLICATION." ORDER BY rang" ;
      $ras = $this->xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;

      if ( isset ( $_SESSION['voir'.$res['idunique'][$i]] ) AND $_SESSION['voir'.$res['idunique'][$i]] ) {
	$mod -> MxUrl ( "menu.lien", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "Masquer", $res['idunique'][$i] ) ) ;
	$mod -> MxImage ( "menu.img", URLIMGMAS, "Masquer" ) ;

	
	for ( $j = 0 ; isset ( $ras['idmenu'][$j] ) ; $j++ ) {
	  $mod -> MxText ( "menu.item.nom", "&nbsp;- ".$ras['libelle'][$j] ) ;

	  if ( $this->xham -> getDroit ( "Configuration_Navigation", "m" ) ) {
	    if ( $ras['rang'][$j] > 1 ) {
	      $mod -> MxUrl ( "menu.item.lienMonter", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "Monter", $ras['idunique'][$j] ) ) ;
	      $mod -> MxImage ( "menu.item.imgMonter", URLIMGFH2, "Monter" ) ;
	    }
	    
	    if ( isset ( $ras['rang'][$j+1] ) ) {
	      $mod -> MxUrl ( "menu.item.lienDescendre", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "Descendre", $ras['idunique'][$j] ) ) ;
	      $mod -> MxImage ( "menu.item.imgDescendre", URLIMGFB2, "Descendre" ) ;
	    }
	    
	    if ( ! $ras['lectureseule'][$j] ) {
	      $mod -> MxUrl ( "menu.item.lienModifier", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "Modifier", $ras['idunique'][$j] ) ) ;
	      $mod -> MxImage ( "menu.item.imgModifier", URLIMGMOD, "Modifier" ) ;
	      if ( $this->xham -> getDroit ( "Configuration_Navigation", "d" ) ) {
		$mod -> MxUrl ( "menu.item.lienSupprimer", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "Supprimer", $ras['idunique'][$j] ) ) ;
		$mod -> MxImage ( "menu.item.imgSupprimer", URLIMGSUP, "Supprimer" ) ;
	      }
	    }
	  }

	  $mod -> MxBloc ( "menu.item", "loop" ) ;
	}
      } else {
	if ( $ras['INDIC_SVC'][2] ) {
	  $mod -> MxUrl ( "menu.lien", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "Afficher", $res['idunique'][$i] ) ) ;
	  $mod -> MxImage ( "menu.img", URLIMGAFF, "Afficher" ) ;
	}
      }

    if ( $this->xham -> getDroit ( "Configuration_Navigation", "m" ) ) {
      if ( $res['rang'][$i] > 1 ) {
	$mod -> MxUrl ( "menu.lienMonter", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "MonterMenu", $res['idunique'][$i] ) ) ;
	$mod -> MxImage ( "menu.imgMonter", URLIMGFH1, "Monter" ) ;
      }
      
      if ( isset ( $res['rang'][$i+1] ) ) {
	$mod -> MxUrl ( "menu.lienDescendre", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "DescendreMenu", $res['idunique'][$i] ) ) ;
	$mod -> MxImage ( "menu.imgDescendre", URLIMGFB1, "Descendre" ) ;
      }
      
      if ( ! $res['lectureseule'][$i] ) {
	$mod -> MxUrl ( "menu.lienModifier", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "ModifierMenu", $res['idunique'][$i] ) ) ;
	$mod -> MxImage ( "menu.imgModifier", URLIMGMOD, "Modifier" ) ;
	if ( $this->xham -> getDroit ( "Configuration_Navigation", "d" ) ) {
	  $mod -> MxUrl ( "menu.lienSupprimer", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "SupprimerMenu", $res['idunique'][$i] ) ) ;
	  $mod -> MxImage ( "menu.imgSupprimer", URLIMGSUP, "Supprimer" ) ;
	}
      }
    }

    $mod -> MxBloc ( "menu", "loop" ) ;
    }

    $mod -> MxUrl ( "lienAjouter", URLNAVI.$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1), "AjouterMenu" ) ) ;
    $mod -> MxImage ( "imgAjouter", URLIMGAJ2, "Ajouter" ) ;

  

    return $mod -> MxWrite ( "1" ) ;
  }

  // On renvoie la variable contenant l'affichage de la page.
  function getAffichage ( ) {
    return $this->af ;
  }

}

?>
