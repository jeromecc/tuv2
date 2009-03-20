<?php

// Titre  : Classe Droits
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 28 Janvier 2005

// Description : Gestion et accès aux droits.
// Cette classe a plusieurs rôles possibles :
// Tout d'abord, elle permet de récupérer les droits d'un utilisateur à sa connexion.
// Elle permet aussi de configurer les droits des groupes.

//cervetti genJavaTitles 12/05
//javascript ajoutant description des droits dans le select d'attribution

class clDroits {

  // Attribut contenant l'affichage.
  private $af ;
  // Attribut contenant les informations de l'utilisateur.
  private $idgroupe ;
  // Attribut contenant les droits de l'utilisateur.
  private $droits ;

  private $infos ;
  private $erreurs ;

  function __construct ( $idgroupe='' ) {
    global $session ;
    // Mise à jour des droits en production.
    if ( $session ) if ( $session->getNavi ( 2 ) == "MajDroitsProd" ) $this->majDroitsProd ( ) ;
    // Si un utilisateur est transmis, alors on est en mode récupération et accès aux droits.
    if ( $idgroupe == "CHECK" ) {
      
    } elseif ( $idgroupe ) {
      $this->idgroupe = $idgroupe ;
      $this->setDroits ( ) ;
      // Sinon, nous sommes dans la partie gestion des droits.
    } else { 
      $this->gestionDroits ( ) ;
    }
  }

  // Vérifie si un droit passé en argument existe. Il est créé s'il n'existe pas.
  function checkDroit ( $droit, $description ) {
    // Récupération de tous les droits existants.
    $param['cw'] = "WHERE libelle='$droit' AND idapplication='".IDAPPLICATION."'" ;
    $req = new clResultQuery ;
    $restous = $req -> Execute ( "Fichier", "getDroitsTous", $param, "ResultQuery" ) ;
    if ( ! $restous['INDIC_SVC'][2] ) {
      $param2['idgroupe'] = 0 ;
      $param2['idapplication'] = IDAPPLICATION ;
      $param2['libelle'] = $droit ;
      $param2['descriptiondroit'] = addslashes ( $description ) ;
      $requete = new clRequete ( BASEXHAM, TABLEDROITS, $param2 ) ;
      $sql = $requete->addRecord ( ) ;
    }
  }

function getDroitExplication($droit) {
	$param['cw'] = "WHERE libelle='$droit' AND idapplication='".IDAPPLICATION."'" ;
    	$req = new clResultQuery ;
    	$restous = $req -> Execute ( "Fichier", "getDroitsTous", $param, "ResultQuery" ) ;
    	return $restous['descriptiondroit'][0];
	}


  // Récupère et calcule les droits de l'utilisateur.
  function setDroits ( ) {
    global $options ;
    global $superAdmin ;
    global $pi ;
    // On récupère la liste de tous les droits en rapport avec l'utilisateur.
    $param['idgroupe'] = $this->idgroupe ;
    $param['idapplication'] = IDAPPLICATION ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getDroitsGroupe", $param, "ResultQuery" ) ;
    // Pour chaque droit trouvé, on le décompose et on calcule ses valeurs.
    for ( $i = 0 ; isset ( $res['iddroit'][$i] ) ; $i++ ) {
      // Décomposition en binaire.
      $bin = sprintf ( "%05b", $res['valeur'][$i] ) ;
      $lib = $res['libelle'][$i] ;
      if ( ! isset ( $this->droits[$lib]['r'] ) ) $this->droits[$lib]['r'] = 0 ;
      if ( ! isset ( $this->droits[$lib]['w'] ) ) $this->droits[$lib]['w'] = 0 ;
      if ( ! isset ( $this->droits[$lib]['m'] ) ) $this->droits[$lib]['m'] = 0 ;
      if ( ! isset ( $this->droits[$lib]['d'] ) ) $this->droits[$lib]['d'] = 0 ;
      if ( ! isset ( $this->droits[$lib]['a'] ) ) $this->droits[$lib]['a'] = 0 ;
      // Application du XOR.
      if ( ! $this->droits[$lib]['r'] ) $this->droits[$lib]['r'] = $bin[4] ;
      if ( ! $this->droits[$lib]['w'] ) $this->droits[$lib]['w'] = $bin[3] ;
      if ( ! $this->droits[$lib]['m'] ) $this->droits[$lib]['m'] = $bin[2] ;
      if ( ! $this->droits[$lib]['d'] ) $this->droits[$lib]['d'] = $bin[1] ;
      if ( ! $this->droits[$lib]['a'] ) $this->droits[$lib]['a'] = $bin[0] ;
    }
    if ( $options -> getOption ( "Indisponible" ) ) {
      if ( ! $this->droits['Configuration_Options'][a] AND ! $superAdmin ) {
	$pi -> addPostIt ( "Attention", "Une opération de maintenance est actuellement en cours. L'application sera disponible aux alentours de ".$options->getOption ( "HeureDisponibilite" ).".", "alerte", "1" ) ;
	$this->droits = '' ;
	$this->droits['Accueil']['r'] = 1 ;
      }
    }
  }

  // Renvoie le tableau contenant tous les droits relatifs à un utilisateur.
  function getDroits ( ) {
    return $this->droits ;
  }

  // Mise à jour des droits d'un groupe.
  function majDroits ( ) {
    global $session ;
    global $errs ;
    if ( $session->getDroit ( "Configuration_Droits", "m" ) ) {
      // Récupération des droits actuels du groupe.
      $dro = $this->listeDroits ( "format" ) ;
      // Test de la présence d'au moins un droit.
      if ( is_array ( $dro ) ) {
	// On parcourt tous les droits.
	while ( list ( $key, $val ) = each ( $dro ) ) {
	  // On fabrique le nom de chaque droit pour la récupération des valeurs
	  // transmises par le formulaire.
	  $r = "R".$key ; $w = "W".$key ; $m = "M".$key ; $d = "D".$key ; $a = "A".$key ;
	  // Fabrication de la valeur du droit.
	  $data['valeur'] = (isset($_POST[$r])?$_POST[$r]:0) + 2 *  (isset($_POST[$w])?$_POST[$w]:0) + 4 *  (isset($_POST[$m])?$_POST[$m]:0) + 8 *  (isset($_POST[$d])?$_POST[$d]:0) + 16 * (isset($_POST[$a])?$_POST[$a]:0) ;
	  // Mise à jour dans la table droit.
	  $requete = new clRequete ( BASEXHAM, TABLEDROITS, $data ) ;
	  $sql = $requete->updRecord ( "iddroit='$key' AND idapplication='".IDAPPLICATION."'" ) ;
	}
      } else { $errs->addErreur ( "majDroits : ce groupe n'a aucun droit, cette opération est impossible." ) ; }
    }
  }
  
  // Suppression d'un droit à un groupe
  function delDroits ( ) {
    global $session ;
    if ( $session->getDroit ( "Configuration_Droits", "d" ) ) {
      // On vérifie d'un tableau a bien été transmis.
      if ( is_array ( $_POST['droits'] ) ) {
	// On parcourt ce tableau.
	while ( list ( $key, $val ) = each ( $_POST['droits'] ) ) { 
	  // Si un iddroit est présent, alors on supprime ce droit.
	  if ( $val ) {
	    $requete = new clRequete ( BASEXHAM, TABLEDROITS ) ;
	    $requete->delRecord ( "iddroit='$val' AND idapplication='".IDAPPLICATION."'" ) ;
	  }
	}
      }
    }
  }


  // Ajout des droits à un groupe.
  function addDroits ( ) {
    global $session ;
    if ( $session->getDroit ( "Configuration_Droits", "w" ) ) {
      // On vérifie qu'un tableau a bien été transmis.
      if ( is_array ( $_POST['tous'] ) ) {
	// Parcours de ce tableau.
	while ( list ( $key, $val ) = each ( $_POST['tous'] ) ) { 
	  // On récupère les informations de ce droit.
	  $param1['cw'] = "WHERE iddroit='$val' AND idapplication='".IDAPPLICATION."'" ;
	  $req1 = new clResultQuery ;
	  $res1 = $req1 -> Execute ( "Fichier", "getDroits", $param1, "ResultQuery" ) ;  
	  // Si les informations sont valide, alors on affecte ce droit au groupe.
	  if ( $res1['INDIC_SVC'][2] ) {
	    $param2['idgroupe'] = $_POST['idgroupe'] ;
	    $param2['idapplication'] = IDAPPLICATION ;
	    $param2['libelle'] = $res1['libelle'][0] ;
	    $param2['descriptiondroit'] = addslashes ( $res1['descriptiondroit'][0] ) ;
	    $param2['valeur'] = 1 ;
	    $requete = new clRequete ( BASEXHAM, TABLEDROITS, $param2 ) ;
	    $sql = $requete->addRecord ( ) ;
	  }
	}
      }
    }
  }

  function addNewDroit ( ) {
    global $session ;
    if ( $session -> getDroit ( "Configuration_Droits", "a" ) ) {
      $param['cw'] = "WHERE idapplication='".IDAPPLICATION."' AND libelle='".$_POST['libelle']."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getDroitsTous", $param, "ResultQuery" ) ;
      if ( $res['INDIC_SVC'][2] ) {
	$this->erreurs .= "Ce libellé est déjà utilisé. La création est annulée.<br/>" ;
      } elseif ( ! $_POST['libelle'] ) {
	$this->erreurs .= "Le libellé ne peut pas être vide. La création est annulée.<br/>" ;
      } else {
	$data['idapplication']    = IDAPPLICATION ;
	$data['libelle']          = stripslashes ( $_POST['libelle'] ) ;
	$data['descriptiondroit'] = stripslashes ( $_POST['description'] ) ;
	$data['idgroupe']         = 0 ;
	$requete = new clRequete ( BASEXHAM, TABLEDROITS, $data ) ;
	$sql = $requete->addRecord ( ) ;
	if ( ! isset ( $sql[1] ) OR ! $sql[1] ) $this->infos .= "Le droit '".stripslashes($_POST['libelle'])."' a été créé.<br>" ;
      }
    }
  }


  function getTousDroits ( ) {
    global $session ;
    $param['cw'] = "WHERE idapplication='".IDAPPLICATION."' ORDER BY libelle" ;
    $req = new clResultQuery ;
    $javascript = "<script language=\"javascript\">";
    $javascript .= "var tabexplic = new Array();";
    $res = $req -> Execute ( "Fichier", "getDroitsTous", $param, "ResultQuery" ) ;
    for ( $i = 0 ; isset ( $res['iddroit'][$i] ) ; $i++ ) {
      $tab[$res['libelle'][$i]] = $res['libelle'][$i]." (".$res['descriptiondroit'][$i].")" ;
	$javascript .= "tab['".$res['libelle'][$i]."']='".$res['descriptiondroit'][$i]."';";
    }
    if ( is_array ( $tab ) ) return $tab ;
    else return array ( ) ;
  }

//ajout cervetti : construction tableau javascript des explications;
function genJavaTitles ( ) {
    global $session ;
    $param['cw'] = "WHERE idapplication='".IDAPPLICATION."' ORDER BY libelle" ;
    $req = new clResultQuery ;
    $javascript = "<script type=\"text/javascript\">\n";
    $javascript .= "var tabexplic = new Array();\n";
    $res = $req -> Execute ( "Fichier", "getDroitsTous", $param, "ResultQuery" ) ;
    for ( $i = 0 ; isset ( $res['iddroit'][$i] ) ; $i++ ) {
	if( ! isset ($jeton[$res['libelle'][$i]]) OR ! $jeton[$res['libelle'][$i]] ) {
	 $javascript .= "tabexplic['".$res['libelle'][$i]."']=\"".addslashes($res['descriptiondroit'][$i])."\";\n";
	$jeton[$res['libelle'][$i]]=true;}
    }
	$javascript .= "function genJolieBulle() {return overlib(tabexplic[this.text],STICKY, MOUSEOFF);}";

	$javascript .= "var nav = document.getElementById('touslesdroitsdispos');\n";
	$javascript .= "if (nav) for (var i = 0; i < nav.options.length; i++) {\n";
	$javascript .= "if(window.overlib) nav.options[i].onmouseover=genJolieBulle;";
	$javascript .= "else nav.options[i].title=tabexplic[nav.options[i].text];\n";
	$javascript .= "}\n";

	$javascript .= "var nav = document.getElementById('touslesdroitsgroupe');\n";
	$javascript .= "if (nav) for (var i = 0; i < nav.options.length; i++) {\n";
	$javascript .= "if(window.overlib) nav.options[i].onmouseover=genJolieBulle;";
	$javascript .= "else nav.options[i].title=tabexplic[nav.options[i].text];\n";
	$javascript .= "}\n";


	$javascript .= "</script>\n";
	
    	return $javascript;
  }


  function delDroit ( $libelle ) {
    global $session ;
    if ( $session -> getDroit ( "Configuration_Droits", "a" ) ) {
      $param['cw'] = "WHERE idapplication='".IDAPPLICATION."' AND idgroupe=0 AND libelle='".$libelle."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getDroitsTous", $param, "ResultQuery" ) ;
      if ( $res['lectureseule'][0] ) {
	$this->erreurs .= "C'est un droit système, il est impossible de le supprimer." ;      
      } else {
	if ( isset ( $_POST['Supprimer'] ) OR isset ( $_POST['Supprimer_x'] ) ) {
	  // Appel de la classe Requete.
	  $requete = new clRequete ( BASEXHAM, TABLEDROITS ) ;
	  // Exécution de la requete.
	  $rs = $requete->delRecord ( "libelle='$libelle' AND idapplication=".IDAPPLICATION ) ;
	  if ( ! isset ( $rs[1] ) OR ! $rs[1] )  $this->infos .= "Le droit '".$res['libelle'][0]."' a bien été supprimé." ;
	} else {
	  $mod = new ModeliXe ( "FormConfirmation.mxt" ) ;
	  $mod -> SetModeliXe ( ) ;
	  $param['cw'] = "WHERE d.idgroupe=g.idgroupe AND d.libelle='$libelle' AND idapplication='".IDAPPLICATION."' ORDER BY g.nomgroupe" ;
	  $ras = $req -> Execute ( "Fichier", "getDroits", $param, "ResultQuery" ) ;
	  for ( $i = 0 ; isset ( $ras['nomgroupe'][$i] ) ; $i++ ) {
	    if ( isset ( $groupes ) AND $ras['nomgroupe'][$i] ) $groupes .= ", ".$ras['nomgroupe'][$i] ;
	    elseif ( $ras['nomgroupe'][$i] ) $groupes = $ras['nomgroupe'][$i] ;
	  }
	  if ( ! $ras['INDIC_SVC'][2] ) $groupes = "Aucun groupe" ;
	  $mod -> MxText ( "question", "Confirmez-vous la suppression du droit '".$res['libelle'][0]."' ?<br/>(Ce droit est utilisé par : $groupes)" ) ;
	  $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), $session->getNavi(3), stripslashes($libelle) )."&idgroupe=".$_POST['idgroupe']."&action=".$_POST['action'] ) ;
	  return $mod -> MxWrite ( "1" ) ;
	}
      }
    }
  }


  // Mise à jour des menus de cette application en production.
  function majDroitsProd ( ) {
  	$data = array ( ) ;
    // On efface les droits de base (idgroupe=0) sur le serveur de production.
    $raq = new clRequete ( BASEXHAM, TABLEDROITS, $data, 'prod' ) ;
    if ( $raq -> getConn ( ) ) {
      $ris = $raq -> delRecord ( "idgroupe=0 AND idapplication=".IDAPPLICATION ) ;
      // On récupère la liste des droits.
      $req = new clResultQuery ;
      $param[cw] = "WHERE idgroupe=0 AND idapplication=".IDAPPLICATION ;
      $res = $req -> Execute ( "Fichier", "getDroitsTous", $param, "ResultQuery" ) ;
      //eko ( $res[INDIC_SVC] ) ;
      // Récupération du nom des attributs d'un droit.
      while ( list ( $key, $val ) = each ( $res ) ) {
	if ( $key != "INDIC_SVC" AND $key != "iddroit" ) $keys[] = $key ;
      }
      // Pacours des droits existants.
      for ( $i = 0 ; isset ( $res['iddroit'][$i] ) ; $i++ ) {
	// Pour chaque droit on vérifie s'il existe en production.
	$param['cw'] = "WHERE idgroupe=0 AND libelle='".addslashes($res['libelle'][$i])."' AND idapplication=".IDAPPLICATION ;
	$ras = $req -> Execute ( "Fichier", "getDroitsProd", $param, "ResultQuery" ) ;
	//eko ( $ras[INDIC_SVC] ) ;
	// S'il n'existe pas,
	if ( ! $ras['INDIC_SVC'][2] ) {
	  // On prépare le tableau contenant toutes les données du droit.
	  for ( $j = 0 ; isset ( $keys[$j] ) ; $j++ ) {
	    $data[$keys[$j]] = $res[$keys[$j]][$i] ;
	  }
	  // On ajoute le droit en production.
	  $raq = new clRequete ( BASEXHAM, TABLEDROITS, $data, 'prod' ) ;
	  $ris = $raq -> addRecord ( ) ;
	  eko ( "insert : ".$res['libelle'][$i] ) ;
	  // S'il existe déjà,
	} else {
	  // On prépare le tableau contenant les champs à mettre à jour.
	  for ( $j = 0 ; isset ( $keys[$j] ) ; $j++ ) {
	    if ( $keys[$j] != "valeur" AND $keys[$j] != "idgroupe" )
	      $data[$keys[$j]] = $res[$keys[$j]][$i] ;
	  }
	  // On met à jour le droit en production.
	  $raq = new clRequete ( BASEXHAM, TABLEDROITS, $data, 'prod' ) ;
	  $ris = $raq -> updRecord ( "libelle='".addslashes($res['libelle'][$i])."' AND idapplication=".IDAPPLICATION ) ;
	  eko ( "update : ".$res['libelle'][$i] ) ;
	  //eko ( $ris ) ;
	}
      }
      // Affichage d'un message d'information.
      $this->infos .= "La liste des droits a été mise à jour en production." ;
    }
  }

  // Gestion des droits d'un groupe.
  function gestionDroits ( ) {
    global $session ;
    if ( $session->getDroit ( "Configuration_Droits", "r" ) ) {
      // Si idgroupe est transmis, alors on affiche les opérations sur
      // les droits du groupe.
      if ( isset ( $_POST['idgroupe'] ) AND $_POST['idgroupe'] ) {
	// Si action est tranmis, on affiche la gestion des privilèges.
	if ( isset ( $_POST['action'] ) AND $_POST['action'] ) {
	  // Si Enlever est transmis, on lance la suppression des droits sélectionnés.
	  if ( isset ( $_POST['Enlever'] ) or isset ( $_POST['Enlever_x'] ) ) $this->delDroits ( ) ;
	  // Si Ajouter est transmis, on lance l'ajout des droits sélectionnés.
	  if ( isset ( $_POST['Ajouter'] ) or isset ( $_POST['Ajouter_x'] ) ) $this->addDroits ( ) ;
	  // Création d'un nouveau droit.
	  if ( isset ( $_POST['CreerDroit'] ) or isset ( $_POST['CreerDroit_x'] ) ) $this->addNewDroit ( ) ;
	  // Suppression d'un droit.
	  if ( ( isset ( $_POST['SupprimerDroit'] ) or isset ( $_POST['SupprimerDroit_x'] ) or $session->getNavi ( 3 ) == "Suppression" ) AND ! isset ( $_POST['Annuler'] ) ) $bonus = $this->delDroit ( (isset($_POST['libelle'])?$_POST['libelle']:$session->getNavi(4)) ) ;
	  // Récupération de la liste des groupes.
	  $tab = $this->listeGroupes ( ) ;
	  // Récupération des droits du groupe.
	  $dro = $this->listeDroits ( "format" ) ;
	  // Récupération des droits attribuables.
	  $tous = $this->listeDroits ( "format", $_POST['idgroupe'] ) ;
	  // Appel du template.
	  $mod = new ModeliXe ( "AttributionDeDroits.mxt" ) ;
	  $mod -> SetModeliXe ( ) ;
	  if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
	  else $mod -> MxBloc ( "informations", "modify", " " ) ;
	  if ( $this->erreurs ) $mod -> MxText ( "erreurs.errs", $this->erreurs ) ;
	  else $mod -> MxBloc ( "erreurs", "modify", " " ) ;
	  $act[0] = "Gestion des privilèges" ;
	  $act[1] = "Attribution des droits" ;
	  // Remplissage des différents champs.
	  $mod -> MxSelect( "action", "action", $_POST['action'], $act , '', '', "onChange=reload(this.form)") ; 
	  $mod -> MxSelect( "groupe", "idgroupe", $_POST['idgroupe'], $tab , '', '', "onChange=reload(this.form)") ; 
	  $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "AttributionDroits" ) ) ;
	  $mod -> MxSelect( "tous", "tous[]", '', $tous, '', '', "size=\"15\" multiple=\"yes\" id=\"touslesdroitsdispos\"") ; 
	  $mod -> MxSelect( "droits", "droits[]", '', $dro , '', '', "size=\"15\" multiple=\"yes\" id=\"touslesdroitsgroupe\"") ; 
	  $mod -> MxFormField ( "creation.libelle", "text", "libelle", (isset($_POST['libelle'])?stripslashes($_POST['libelle']):''), "" ) ;
	  $mod -> MxFormField ( "creation.description", "text", "description", (isset($_POST['description'])?stripslashes($_POST['description']):''), "" ) ;
	  $mod -> MxHidden ( "creation.hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "AttributionDroit", "Creation" )."&idgroupe=".$_POST['idgroupe']."&action=".$_POST['action'] ) ;
	  $listeDroits = $this->getTousDroits ( ) ;
	  $mod -> MxSelect( "suppression.libelles", "libelle", (isset($_POST['libelle'])?stripslashes($_POST['libelle']):''), $listeDroits, '', '', 'size="15"') ; 
	  $mod -> MxHidden ( "suppression.hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "AttributionDroit", "Suppression" )."&idgroupe=".$_POST['idgroupe']."&action=".$_POST['action'] ) ;
	  $mod -> MxText ( "bonus", (isset($bonus)?$bonus:'') ) ;
	  if ( ! $session->getDroit ( "Configuration_Droits", "w" ) ) { $mod -> MxBloc ( "boutonsajouter", "modify", "" ) ; }
	  if ( ! $session->getDroit ( "Configuration_Droits", "d" ) ) { $mod -> MxBloc ( "boutonsenlever", "modify", "" ) ; }
	  if ( ! $session->getDroit ( "Configuration_Droits", "a" ) ) { $mod -> MxBloc ( "creation", "delete" ) ; }
	  if ( ! $session->getDroit ( "Configuration_Droits", "a" ) ) { $mod -> MxBloc ( "suppression", "delete" ) ; }
		
	} else {
	  // Si MajDroits est tranmis, alors on lance la mise à jour du droit.
	  if ( isset ( $_POST['MajDroits'] ) || isset ( $_POST['MajDroits_x'] ) ) $this->majDroits ( ) ;
	  // Récupération de la liste des groupes.
	  $tab = $this->listeGroupes ( ) ;
	  // Récupération de la liste des droits du groupe.
	  $dro = $this->listeDroits ( ) ;
	  // Chargement du template.
	  $mod = new ModeliXe ( "GestionDesDroits.mxt" ) ;
	  $mod -> SetModeliXe ( ) ;
	  $act[0] = "Gestion des privilèges" ;
	  $act[1] = "Attribution des droits" ;
	  // Remplissage des champs.
	  $mod -> MxSelect( "action", "action", (isset($_POST['action'])?$_POST['action']:''), $act , '', '', "onChange=reload(this.form)") ; 
	  $mod -> MxSelect( "groupe", "idgroupe", $_POST['idgroupe'], $tab , '', '', "onChange=reload(this.form)") ; 
	  $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "MajDroits" ) ) ;
	  if ( count ( $dro['iddroit'] ) > 0 ) $mod -> MxText ( "titres", "<tr><th>Libellé</th><th>Description</th><th title='lecture'>R</th><th title='écriture'>W</th><th title='modifier'>M</th><th title='effacer'>D</th><th title='administrer'>A</th>" ) ;
	  else $mod -> MxText ( "titres", "<tr><th>Ce groupe ne possède aucun droit dans cette application.</th></tr>" ) ;
	  // On parcourt chaque droit attribué au groupe, et on ajoute 
	  // une ligne au template pour gérer les privilèges.
	  for ( $i = 0 ; isset ( $dro['iddroit'][$i] ) ; $i++ ) {
	    $bin = sprintf ( "%05b", $dro['valeur'][$i] ) ;
	    $mod -> MxText ( "listedroits.libelle", $dro['libelle'][$i] ) ;
	    $mod -> MxText ( "listedroits.description", $dro['descriptiondroit'][$i] ) ;
	    $mod -> MxCheckerField ( "listedroits.R", "checkbox", "R".$dro['iddroit'][$i], 1, (($bin[4])?true:false) ,"title='Lecture'") ;
	    $mod -> MxCheckerField ( "listedroits.W", "checkbox", "W".$dro['iddroit'][$i], 1, (($bin[3])?true:false) ,"title='Ecriture'") ;
	    $mod -> MxCheckerField ( "listedroits.M", "checkbox", "M".$dro['iddroit'][$i], 1, (($bin[2])?true:false) ,"title='Modification'") ;
	    $mod -> MxCheckerField ( "listedroits.D", "checkbox", "D".$dro['iddroit'][$i], 1, (($bin[1])?true:false) ,"title='Effacement'") ;
	    $mod -> MxCheckerField ( "listedroits.A", "checkbox", "A".$dro['iddroit'][$i], 1, (($bin[0])?true:false), "title='Administration'") ;
	    $mod -> MxBloc ( "listedroits", "loop" ) ;
	  }

	  $mod -> MxHidden ( "boutons.hidden2", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "" ) ) ;
	  if ( ! $session->getDroit ( "Configuration_Droits", "m" ) ) { $mod -> MxBloc ( "boutons", "modify", "" ) ; }
	}
      } else {
	// Dans ce cas, on affiche seulement la liste des groupes disponibles.
	$tab = $this->listeGroupes ( ) ;
	$mod = new ModeliXe ( "ListeGroupes.mxt" ) ;
	$mod -> SetModeliXe ( ) ;
	$mod -> MxSelect( "groupe", "idgroupe", (isset($_POST['idgroupe'])?$_POST['idgroupe']:''), $tab , '', '', "onChange=reload(this.form)") ; 
	$mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1) ) ) ;
      }
      /*
      if ( ! ereg ( ".*".PRODKEYW.".*", URL ) AND URL ) {
	$mod -> MxUrl ( "lienMaj", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "MajDroitsProd" ) ) ;
	$mod -> MxImage ( "imgMaj", URLIMGMAJ, "Envoyer en production" ) ;
      }
      */
      $this->af .= $mod -> MxWrite ( "1" ) ;

	//ajout cervetti, javascript descriptions droits
	$this->af.=$this->genJavaTitles();
    }
  }

  // Fabrication de la liste des groupes.
  function listeGroupes ( ) {
    // Récupération de la liste des groupes.
    $param['aw'] = " AND idapplication=".IDAPPLICATION." ORDER BY nomgroupe" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getGroupesApplication", $param, "ResultQuery" ) ;
    $tab[0] = "Choisir un groupe" ;
    // Formatage de cette liste pour l'insérer directement dans ModeliXe.
    $param['idapplication'] = IDAPPLICATION ;
    for ( $i = 0 ; isset ( $res['idgroupe'][$i] ) ; $i++ ) { 
      $param['idgroupe'] = $res['idgroupe'][$i] ;
      $ras = $req -> Execute ( "Fichier", "getDroitsSomme", $param, "ResultQuery" ) ;
      $tab[$res['idgroupe'][$i]] = $res['nomgroupe'][$i]." (".($ras['somme'][0]?$ras['somme'][0]:0).")" ; 
    }
    // On renvoie la liste.
    return $tab ;
  }

  // Récupère la liste des droits d'un groupe.
  function listeDroits ( $format='', $idgroupe='' ) {
    // Si un idgroupe est transmis, alors on fabrique la liste des droits
    // que ne possède pas ce groupe.
    if ( $idgroupe ) {
      // Récupération des droits du groupe.
      $param['cw'] = "WHERE d.idgroupe=g.idgroupe AND g.idgroupe='".$_POST['idgroupe']."' AND idapplication='".IDAPPLICATION."' ORDER BY libelle" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getDroits", $param, "ResultQuery" ) ;
      
      // Récupération de tous les droits existants.
      $param['cw'] = "WHERE idapplication='".IDAPPLICATION."' ORDER BY libelle" ;
      $req = new clResultQuery ;
      $restous = $req -> Execute ( "Fichier", "getDroitsTous", $param, "ResultQuery" ) ;
      
      // On repère les droits que ne possède pas le groupe.
      for ( $i = 0 ; isset ( $restous['iddroit'][$i] ) ; $i++ ) {
	if ( ! contient ( $res['libelle'], $restous['libelle'][$i] ) && !contient((isset($tab)?$tab:array()),$restous['libelle'][$i]) ) $tab[$restous['iddroit'][$i]] = $restous['libelle'][$i] ;
      }
      // Si le tableau est vide, on affiche "Aucun droit".
      if ( ! isset ( $tab ) OR count ( $tab ) == 0 ) $tab[0] = "Aucun droit" ;
      $res = $tab ;
    } else {
      // Sinon, on fabrique un tableau contenant la liste des droits que possède
      // le groupe identifié par l'idgroupe transmis par formulaire.
      $param['cw'] = "WHERE d.idgroupe=g.idgroupe AND g.idgroupe='".$_POST['idgroupe']."' AND idapplication='".IDAPPLICATION."' ORDER BY descriptiondroit" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getDroits", $param, "ResultQuery" ) ;
      // Si l'argument $format est "vrai", alors on formate le tableau pour
      // le passer directement à Modelixe.
      if ( $format ) {
	for ( $i = 0 ; isset ( $res['iddroit'][$i] ) ; $i++ ) {
	  $tab[$res['iddroit'][$i]] = $res['libelle'][$i] ;
	}
	// Si le groupe n'a pas de droit, on affiche "Aucun droit".
	if ( $res['INDIC_SVC'][2] == 0 ) $tab[0] = "Aucun droit" ;
	$res = $tab ;
      }
    }
    return $res ;
  }

  // Retourne l'affichage de la classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}
