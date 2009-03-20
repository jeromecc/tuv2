<?php

// Titre  : Classe Listes
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 20 Avril 2005

// Description : 
// Cette classe gère les listes (recours et diagnostics).
// Elle permet d'afficher une liste seule de différentes façons.

class clCCAMListesComplexes {

  // Attributs de la classe.
  // Contient l'affichage généré par la classe.
  private $af ;
  // Type (pour l'instant "Recours" ou "Diagnostics").
  private $type ;
  // Contient les messages d'informations.
  private $infos ;
  // Contient les messages d'erreurs.
  private $erreurs ;

  // Constructeur.
  function __construct ( $type, $recup='' ) {
    if ( $recup ) {
      $this->type = $type ;
    } else {
      $this->type = $type ;
      $this->setAffichage ( ) ;
    }
  }

  // Fonction centrale : Elle va générer tout l'affichage.
  function setAffichage ( ) {
    global $session ;
    global $options ;
    // Récupération du type dans une variable simple à manipuler.
    $type = $this->type ;
    $nli = $options->getOption ( "Lignes ".$type ) ;
    if ( ! $nli ) $nli = 15 ;
    if ( $session->getDroit ( "CCAM_".$type, "r" ) ) {

      // Ajout d'une liste.
      if ( ( $_POST['Ajouter'] or $_POST['Ajouter_x'] ) and $session->getNavi ( 3 ) == "validAddListe" and $session->getDroit ( "CCAM_".$type, "w" ) ) {
	$form = $this->addListe ( ) ;
      }
      // Modification d'une liste.
      if ( ($_POST['Modifier'] or $_POST['Modifier_x'] ) and $session->getNavi ( 3 ) == "validModListe" and $session->getDroit ( "CCAM_".$type, "m" ) ) {
	$form = $this->modListe ( ) ;
      }
      // Suppression d'une liste -> demande de confirmation.
      if ( ( $_POST['Supprimer'] or $_POST['Supprimer_x'] ) and $session->getNavi ( 3 ) == "validModListe" and $session->getDroit ( "CCAM_".$type, "d" ) ) {
	$form = $this->delListe ( ) ;
      }
      // Suppression d'une liste -> Suppression réelle.
      if ( ( $_POST['Supprimer'] or $_POST['Supprimer_x'] ) and $session->getNavi ( 3 ) == "validDelListe" and $session->getDroit ( "CCAM_".$type, "d" ) ) {
	$form = $this->delListe ( "1" ) ;
      }
      // Ajout d'un item.
      if ( ( $_POST['Ajouter'] or $_POST['Ajouter_x'] ) and $session->getNavi ( 3 ) == "validAddItem" and $session->getDroit ( "CCAM_".$type, "w" ) ) {
	$form = $this->addItem ( ) ;
      }
      // Suppression d'un item.
      if ( ( $_POST['Supprimer'] or $_POST['Supprimer_x'] ) and $session->getNavi ( 3 ) == "validModItem" and $session->getDroit ( "CCAM_".$type, "w" ) ) {
	$form = $this->delItem ( ) ;
      }
      // Modification d'un item.
      if ( ( $_POST['Modifier'] or $_POST['Modifier_x'] ) and $session->getNavi ( 3 ) == "validModItem" and $session->getDroit ( "CCAM_".$type, "w" ) ) {
	$form = $this->modItem ( ) ;
      }
      // Réparation d'une liste de listes.
      if ( $session->getNavi ( 3 ) == "repListeListes" and $session->getDroit ( "CCAM_".$type, "a" ) ) {
	$this->repListe ( ) ;
      }
      // Réparation d'une liste d'items.
      if ( $session->getNavi ( 3 ) == "repListeItems" and $session->getDroit ( "CCAM_".$type, "a" ) ) {
	$this->repListe ( $session->getNavi ( 4 ) ) ;
      }

      switch ( $options->getOption ( "Catégories ".$type ) ) {
      case 'Manuel': $order = "ORDER BY rang" ; break ;
      case 'Alphabétique': $order = "ORDER BY nomliste" ; break ;
      case 'Alphabétique inversé': $order = "ORDER BY nomliste DESC" ; break ;
      default : $order = "" ; break ;
      }
      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomitem='LISTE' AND categorie=\"$type\" $order" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;  
      // newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ;
      // Formulaire d'ajout d'une liste.
      if ( $session->getNavi ( 3 ) == "addListe" and $session->getDroit ( "CCAM_".$type, "w" ) ) {
	$form = $this->getFormAddListe ( ) ;
      }
      // Formulaire de modification d'une liste.
      if ( $_POST['liste'] and $session->getNavi ( 3 ) == "modListe" and $session->getDroit ( "CCAM_".$type, "m" ) ) {
	$form = $this->getFormModListe ( ) ;
      }
      // Formulaire d'ajout d'un item.
      if ( $session->getNavi ( 3 ) == "addItem" and $session->getDroit ( "CCAM_".$type, "w" ) ) {
	$form = $this->getFormAddItem ( ) ;
      }
      // Formulaire de modification d'un item.
      if ( $session->getNavi ( 3 ) == "modItem" and $session->getDroit ( "CCAM_".$type, "m" ) ) {
	$form = $this->getFormModItem ( ) ;
      }

      $mod = new ModeliXe ( "CCAM_GestionListesComplexes.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
      else $mod -> MxBloc ( "informations", "modify", " " ) ;
      if ( $this->erreurs ) $mod -> MxText ( "erreurs.errs", $this->erreurs ) ;
      else $mod -> MxBloc ( "erreurs", "modify", " " ) ;
      $mod -> MxText ( "formItems", $form ) ;
      $mod -> MxText ( "listeListes.nomListe", "Catégories de ".$type ) ;
      if ( $session->getDroit ( "CCAM_".$type, "w" ) ) {
	$mod -> MxImage ( "listeListes.imgAjouter", URLIMGAJO, "Ajouter" ) ;
	$mod -> MxUrl  ( "listeListes.lienAjouter", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "addListe" ) ) ;
      }
      if ( $session->getDroit ( "CCAM_".$type, "a" ) ) {
	$mod -> MxImage ( "listeListes.imgReparer", URLIMGREP, "Reparer" ) ;
	$mod -> MxUrl  ( "listeListes.lienReparer", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "repListeListes" ) ) ;
      }
      for ( $i = 0 ; isset ( $res[nomliste][$i] ) ; $i++ ) {
	if ( DEBUGPOSITIONS ) $data[$res[nomliste][$i]] = $res[rang][$i]." - ".$res[nomliste][$i] ;
	else $data[$res[nomliste][$i]] = $res[nomliste][$i] ;
      }
      if ( ! count ( $data ) ) $data = Array ( ) ;
      $mod -> MxSelect( "listeListes.select", "liste", stripslashes($_POST['liste']), $data , '', '', "size=\"$nli\" onClick=\"reload(this.form)\"") ; 
      $mod -> MxHidden ( "listeListes.hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "modListe", $res[nomliste][$i] ) ) ;
      if ( $_POST['liste'] ) {
	$mod -> MxText ( "listeItems.nomListe", stripslashes($_POST['liste']) ) ;
	if ( $session->getDroit ( "CCAM_".$type, "w" ) ) {
	  $mod -> MxImage ( "listeItems.imgAjouter", URLIMGAJO, "Ajouter" ) ;
	  $mod -> MxUrl  ( "listeItems.lienAjouter", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "addItem", stripslashes($_POST['liste']) ) ) ;
	}
	if ( $session->getDroit ( "CCAM_".$type, "a" ) ) {
	  $mod -> MxImage ( "listeItems.imgReparer", URLIMGREP, "Reparer" ) ;
	  $mod -> MxUrl  ( "listeItems.lienReparer", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "repListeItems", stripslashes($_POST['liste']) ) ) ;
	}
	$listes = "&liste=".stripslashes($_POST['liste']) ;
	$mod -> MxHidden ( "listeItems.hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "modItem", stripslashes($_POST['liste']) ).$listes ) ;
	$data = $this->getListeItems ( $_POST['liste'], 1 ) ;
	if ( ! is_array ( $data ) ) $data = Array ( ) ;
	$mod -> MxSelect( "listeItems.select", "item", $_POST['item'], $data , '', '', "size=\"$nli\" onClick=\"reload(this.form)\"") ; 
      } else {
	$mod -> MxBloc ( "listeItems", "modify", " " ) ;
      }
      $this->af .= $mod -> MxWrite ( "1" ) ;
    }
  }

  // Réparation d'une liste (de listes ou d'items).
  function repListe ( $nomListe='' ) {
    global $errs ;
    // Réparation d'une liste d'items.
    if ( $nomListe ) {
      $_POST['liste'] = $nomListe ;
      // Récupération des items de la liste à réparer.
      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='$nomListe' AND nomitem!='LISTE' AND categorie=\"".$this->type."\" ORDER BY rang" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ; 
      // Si au moins un item est présent, alors on commence la reconstruction.
      if ( $res[INDIC_SVC][2] ) {
	for ( $i = 0 ; isset ( $res[iditem][$i] ) ; $i++ ) {
	  $data[rang] = $i + 1 ;
	  $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
	  $requete->updRecord ( "iditem='".$res[iditem][$i]."'" ) ;
	}
	// Message d'information.
	$this->infos .= "La réparation de la liste \"".stripslashes($nomListe)."\" a été effectuée." ;
      } else {
	// Signalement des erreurs.
	$errs->addErreur ( "La liste \"".stripslashes($nomListe)."\" n'existe pas ou ne contient aucun item, la réparation est annulée." ) ;
	$this->erreurs .= "La liste \"".stripslashes($nomListe)."\" n'existe pas ou ne contient aucun item, la réparation est annulée." ;
      }
    // Réparation d'une liste de listes.
    } else {
      // Récupération des différentes listes.
      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomitem='LISTE' AND categorie=\"".$this->type."\" ORDER BY rang" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;  
      // Si au moins une liste est présente, on commence la reconstruction.
      if ( $res[INDIC_SVC][2] ) {
	for ( $i = 0 ; isset ( $res[iditem][$i] ) ; $i++ ) {
	  $data[rang] = $i + 1 ;
	  $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
	  $requete->updRecord ( "iditem='".$res[iditem][$i]."'" ) ;
	}
	// Message d'information.
	$this->infos .= "La réparation de la liste des catégories de ".$this->type." a été effectuée." ;
      } else {
	// Signalement des erreurs.
	$errs->addErreur ( "La liste des listes de catégories de ".$this->type." ne contient aucune liste, la réparation est annulée." ) ;
	$this->erreurs .= "La liste des listes de catégories de ".$this->type." ne contient aucune liste, la réparation est annulée." ;
      }
    }
  }

  function getListeItems (  $nomListe, $modelixe='', $placement='', $nomItem='', $indexNom='' ) {
    global $options ;
    // Préparation du type de classement pour la requête.
    switch ( $options->getOption ( $this->type ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphabétique': $order = "ORDER BY nomitem" ; break ;
    case 'Alphabétique inversé': $order = "ORDER BY nomitem DESC" ; break ;
    default : $order = "" ; break ;
    }

    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".addslashes(stripslashes($nomListe))."' AND categorie=\"".$this->type."\" AND nomitem!='LISTE' $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
    // Affichage en cas de débugage.
    if ( DEBUGLISTES ) { newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ; }
    // Préparation du tableau à retourner pour un select de modelixe.
    if ( $modelixe ) {
      // Placement ou affichage simple.
      if ( $placement ) { 
	$placer = "Après " ; 
	$tab[0] = "En début de liste" ;
	$type = "iditem" ;
	$val = 0 ;
      } else $type = "iditem" ;
      // Fabrication du tableau.
      for ( $i = 0 ; isset ( $res[iditem][$i] ) ; $i++ ) {
	if ( $res[code][$i] ) $code = " (".$res[code][$i].")" ; else $code = "" ;
	if ( $nomItem == $res[nomitem][$i] ) {
	  if ( $res[nomitem][$i] ) {
	    $rang = $res[$type][$i] ;
	    $tab[$rang] = "Position actuelle" ;
	  }
	} else { 
	  if ( $indexNom ) {
	  	$tab[$res[nomitem][$i].$code] = $placer.$res[nomitem][$i].$code ;
	  } else {
	  	if ( DEBUGPOSITIONS ) $tab[($res[$type][$i]+$val)] = $placer.$res[rang][$i]." - ".$res[nomitem][$i].$code ; 
	  	else $tab[($res[$type][$i]+$val)] = $placer.$res[nomitem][$i].$code ;
	  } 
	}
      }
      // Retourne le tableau au format attendu par modelixe.
      return $tab ;
    } else {
      // Retourne le tableau au format normal de ResultQuery.
      return $res ;
    }
  }
  
  function getCode ( $nomListe, $nomItem ) {
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".addslashes(stripslashes($nomListe))."' AND categorie=\"".$this->type."\" AND nomitem='".addslashes(stripslashes($nomItem))."' $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
    return $res[code][0] ;
  }

  function getListes ( $nomListe='', $opt='' ) {
    global $options ;
    // Préparation du type de classement pour la requête.
    switch ( $options->getOption ( $this->type ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphabétique': $order = "ORDER BY nomliste" ; break ;
    case 'Alphabétique inversé': $order = "ORDER BY nomliste DESC" ; break ;
    default : $order = "" ; break ;
    }
    if ( $opt ) $tab[] = SELECTLISTE ;
    if ( $nomListe ) {
      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".addslashes(stripslashes($nomListe))."' AND categorie=\"".$this->type."\" AND nomitem!='LISTE' $order" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
      // Affichage en cas de débugage.
      if ( DEBUGLISTES ) { newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ; }
      for ( $i = 0 ; isset ( $res[iditem][$i]  ) ; $i++ ) {
	if ( $opt ) $tab[$res[nomitem][$i]] = $res[nomitem][$i] ;
	else $tab[$res[iditem][$i]] = $res[nomitem][$i] ;
      }
    } else {
      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND categorie=\"".$this->type."\" and nomitem=\"LISTE\" $order" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
      for ( $i = 0 ; isset ( $res[iditem][$i]  ) ; $i++ ) {
	if ( $opt ) $tab[$res[nomliste][$i]] = $res[nomliste][$i] ;
	else $tab[$res[iditem][$i]] = $res[nomliste][$i] ;
      }
    }
    return $tab ;
  }

  function getListeListes (  $nomListe, $modelixe='', $placement='', $nomItem='', $indexNom='' ) {
    global $options ;
    // Préparation du type de classement pour la requête.
    switch ( $options->getOption ( $this->type ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphabétique': $order = "ORDER BY nomliste" ; break ;
    case 'Alphabétique inversé': $order = "ORDER BY nomliste DESC" ; break ;
    default : $order = "" ; break ;
    }

    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND categorie=\"".$this->type."\" and nomitem=\"LISTE\" $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
    // Affichage en cas de débugage.
    if ( DEBUGLISTES ) { newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ; }
    // Préparation du tableau à retourner pour un select de modelixe.
    if ( $modelixe ) {
      // Placement ou affichage simple.
      if ( $placement ) { 
	$placer = "Après " ; 
	$tab[0] = "En début de liste" ;
	$type = "iditem" ;
	$val = 0 ;
      } else $type = "iditem" ;
      // Fabrication du tableau.
      for ( $i = 0 ; isset ( $res[iditem][$i] ) ; $i++ ) {
	if ( $res[code][$i] ) $code = " (".$res[code][$i].")" ; else $code = "" ;
	if ( stripslashes($nomItem) == stripslashes($res[nomliste][$i]) ) {
	  if ( $res[nomliste][$i] ) {
	    $rang = $res[$type][$i] ;
	    $tab[$rang] = "Position actuelle" ;
	  }
	} else { 
	  if ( $indexNom ) {
	  	$tab[$res[nomliste][$i].$code] = $placer.$res[nomliste][$i].$code ;
	  } else {
	  	if ( DEBUGPOSITIONS ) $tab[($res[$type][$i]+$val)] = $placer.$res[rang][$i]." - ".$res[nomliste][$i].$code ; 
	  	else $tab[($res[$type][$i]+$val)] = $placer.$res[nomliste][$i].$code ;
	  } 
	}
      }
      // Retourne le tableau au format attendu par modelixe.
      return $tab ;
    } else {
      // Retourne le tableau au format normal de ResultQuery.
      return $res ;
    }
  }



  function getFormModItem ( ) {
    global $session ;
    global $options ;
    if ( $_POST['item'] ) {
      // Récupération des informations actuelles de l'item.
      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".$_POST['liste']."' AND iditem='".$_POST['item']."' AND categorie='".$this->type."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
      // Chargement du template.
      $mod = new ModeliXe ( "CCAM_GestionItemsComplexes.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      $mod -> MxBloc ( "ajouter", "modify", " " ) ;
      if ( ! $session->getDroit ( "CCAM_".$this->type, "m" ) ) {
	$mod -> MxBloc ( "modifier", "modify", " " ) ;
      }
      if ( ! $session->getDroit ( "CCAM_".$this->type, "d" ) ) {
	$mod -> MxBloc ( "supprimer", "modify", " " ) ;
      }
      // Nom de la liste.
      $mod -> MxText ( "nomListe", "Modification de l'item \"".$res[nomitem][0]."\" " ) ;
      // Champs texte pour entrer le nom de l'item.
      $mod -> MxText ( "nomItem", "Nom :" ) ;
      $mod -> MxFormField ( "nomItemF", "text", "nomItemF", $res[nomitem][0], "size=\"31\" maxlength=\"50\"" ) ;
      if ( $this->type != "Documents" ) {
	$mod -> MxText ( "code", "Code :" ) ;
	$mod -> MxFormField ( "codeF", "text", "codeF", $res[code][0], "size=\"31\" maxlength=\"16\"" ) ;
      }
      if ( $this->type == "ListeMédecins" ) {
        // Recherche de l'uf de la categorie stripslashes($session->getNavi ( 4 ) )
      $paramuf[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomitem='LISTE' AND nomliste='".stripslashes($session->getNavi ( 4 ) )."' AND categorie='".$this->type."'" ;
      $requf       = new clResultQuery ;
      $resuf       = $requf -> Execute ( "Fichier", "CCAM_getListesComplexes2", $paramuf, "ResultQuery" ) ;
      $mod -> MxText ( "uf", "Uf :" ) ;
      $mod -> MxText ( "ufcode",$resuf["code"][0] ) ;
    }
      // Si le classement est manuel dans cette liste, alors on affiche une liste de positions possibles.
      if ( $options->getOption ( $this->type ) == "Manuel" ) {
	$mod -> MxText ( "placer", "Placer :" ) ;
	$data = $this->getListeItems ( $_POST['liste'], 1, 1, $res[nomitem][0] ) ;
	$param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND categorie='".$this->type."' AND nomitem!='LISTE' AND nomliste='".$_POST['liste']."'" ;
	$req = new clResultQuery ;
	$res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	$mod -> MxSelect( "placerF", "placerF", $_POST['item'], $data , '', '', "size=\"1\"") ; 
      }
      // Génération de la variable de navigation.
      $liste = "&liste=".stripslashes($_POST['liste'])."&item=".$_POST['item'] ;
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "validModItem" ).$liste ) ;
      return $mod -> MxWrite ( "1" ) ;
    }
  }


  function addItem ( ) {
    global $options ;
    global $session ;
    // On vérifie qu'un item ne porte pas déjà ce nom.
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".$_POST['liste']."' AND nomitem='".$_POST['nomItemF']."' AND categorie='".$this->type."'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "CCAM_getListesComplexes", $param, "ResultQuery" ) ;
    // On signale l'erreur si le nom est déjà pris.
    if ( $res[INDIC_SVC][2] > 0 ) {
      $this->erreurs .= "Un item portant le nom demandé (\"".$_POST['nomItemF']."\") existe déjà dans cette liste. La création est annulée." ;
    } else {
      if ( eregi ( "[0-9a-zA-Z]", $_POST['nomItemF'] ) ) {
	$param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND code='".$_POST['codeF']."' AND categorie='".$this->type."' AND nomliste='".$_POST['liste']."'" ;
	$res6 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	if ( eregi ( "[0-9a-zA-Z]", $_POST['codeF'] ) OR $this->type == "Documents" ) {
    // code modifié pour permettre de saisir des codes ADELI non unique
    if ( $res6[INDIC_SVC][2] > 0 && $this->type != "Documents" ) {
      $this->erreurs .= "L'item \"".$res6[nomitem][0]."\" a déjà ce code attribué. Merci d'en choisir un autre." ;
	  } else {
	    // On positionne correctement le nouvel item et on déplace les autres.
	    if ( $options->getOption ( $this->type ) != "Manuel" ) { $rang = 1 ; } else { 
	      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND iditem='".$_POST['placerF']."'" ;
	      $res2 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	      $rang = $res2[rang][0] + 1 ; 
	    }
	    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND rang>='$rang' AND categorie='".$this->type."' AND nomitem!='LISTE' AND nomliste='".$_POST['liste']."'" ;
	    $req = new clResultQuery ;
	    $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	    for ( $i = 0 ; isset ( $res[iditem][$i] ) ; $i++ ) {
	      $data2[rang] = $res[rang][$i] + 1 ;
	      $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data2 ) ;
	      $requete->updRecord ( "iditem='".$res[iditem][$i]."'" ) ;
	    }
	    // Insertion du nouveau item.
	    $data[categorie]    = $this->type ;
	    $data[nomliste]     = stripslashes($session->getNavi(4)) ;
      
      $data[code]         = $_POST['codeF'] ;
	    $data[nomitem]      = $_POST['nomItemF'] ;
	    $data[rang]         = $rang ;
	    $data[idDomaine]    = CCAM_IDDOMAINE ;
	    $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
	    $requete->addRecord ( ) ;
	    
	    
	    /*// Mise à jour de l'Uf de la spécialité dans la table ccam_liste
      if ($this->type == "ListeMédecins" ) {
        $dataUf[code] = $_POST['ufF'] ;
        $requete = new clRequete ( CCAM_BDD, "ccam_liste", $dataUf ) ;
        $requete->updRecord ( "nomitem='LISTE' and nomliste='".stripslashes($session->getNavi(4))."'" ) ;
      
      // Mise à jour de l'Uf dans la table ccam_uf_spe
        $dataSpe[numeroUF] = $_POST['ufF'] ;
        $requete = new clRequete ( CCAM_BDD, "ccam_uf_spe", $dataSpe ) ;
        $requete->updRecord ( "libelleSpecialite='".stripslashes($session->getNavi(4))."'" ) ;  
      
      }
      */
        
        
	    // Message d'information.
	    $this->infos .= "L'item \"".$_POST['nomItemF']."\" a été ajoutée à la catégorie \"".stripslashes($_POST['liste'])."\"." ;
	  }
	} else {
	  $this->erreurs = "Le code choisi ne doit pas être vide." ;
	}
      } else {
	$this->erreurs = "Le nom choisi ne doit pas être vide." ;
      }
    }
  }

  function getFormAddItem ( ) {
    global $options ;
    global $session ;
    $_POST['liste'] = $session->getNavi ( 4 ) ;
    // Chargement du template.
    $mod = new ModeliXe ( "CCAM_GestionItemsComplexes.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $mod -> MxBloc ( "modifier", "modify", " " ) ;
    $mod -> MxBloc ( "supprimer", "modify", " " ) ;
    // Nom de la liste.
    $mod -> MxText ( "nomListe", "Ajouter un nouveau item à la catégorie ".stripslashes($session->getNavi ( 4 ) )) ;
    // Champs texte pour entrer le nom de l'item.
    $mod -> MxText ( "nomItem", "Nom :" ) ;
    $mod -> MxFormField ( "nomItemF", "text", "nomItemF", $_POST['valeur'], "size=\"31\" maxlength=\"50\"" ) ;
    if ( $this->type != "Documents" ) {
      $mod -> MxText ( "code", "Code :" ) ;
      $mod -> MxFormField ( "codeF", "text", "codeF", '', "size=\"31\" maxlength=\"16\"" ) ;
    }
    if ( $this->type == "ListeMédecins" ) {
      
      // Recherche de l'uf de la categorie stripslashes($session->getNavi ( 4 ) )
      $paramuf[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomitem='LISTE' AND nomliste='".stripslashes($session->getNavi ( 4 ) )."' AND categorie='".$this->type."'" ;
      $requf       = new clResultQuery ;
      $resuf       = $requf -> Execute ( "Fichier", "CCAM_getListesComplexes2", $paramuf, "ResultQuery" ) ;
      //eko($resuf);
      $mod -> MxText ( "uf", "Uf :" ) ;
      $mod -> MxText ( "ufcode", $resuf["code"][0] ) ;
    }
    // Si le classement est manuel dans cette liste, alors on affiche une liste de positions possibles.
    if ( $options->getOption ( $this->type ) == "Manuel" ) {
      $mod -> MxText ( "placer", "Placer :" ) ;
      $data = $this->getListeItems ( $session->getNavi ( 4 ), 1, 1 ) ;
      if ( ! count ( $data ) ) $data = Array ( ) ;
      $mod -> MxSelect( "placerF", "placerF", '', $data , '', '', "size=\"1\"") ; 
    }
    // Génération de la variable de navigation.
    $liste = "&liste=".stripslashes($_POST['liste']) ;
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "validAddItem", $session->getNavi(4) ).$liste ) ;
    return $mod -> MxWrite ( "1" ) ;
  }

  function getFormAddListe ( ) {
    global $options ;
    global $session ;
    // Chargement du template.
    
    $mod = new ModeliXe ( "CCAM_GestionItemsComplexes2.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $mod -> MxBloc ( "modifier", "modify", " " ) ;
    $mod -> MxBloc ( "supprimer", "modify", " " ) ;
    // Nom de la liste.
    $mod -> MxText ( "nomListe", "Ajouter une nouvelle catégorie de ".$this->type ) ;
    // Champs texte pour entrer le nom de l'item.
    $mod -> MxText ( "nomItem", "Nom :" ) ;
    $mod -> MxFormField ( "nomItemF", "text", "nomItemF", $_POST['valeur'], "size=\"31\" maxlength=\"50\"" ) ;
    if ( $this->type == "ListeMédecins" ) {
      $mod -> MxText ( "uf", "Uf :" ) ;
      $mod -> MxFormField ( "ufF", "text", "ufF", $resuf["code"][0], "size=\"31\" maxlength=\"16\"" ) ;
    }
    // Si le classement est manuel dans cette liste, alors on affiche une liste de positions possibles.
    if ( $options->getOption ( "Catégories ".$this->type ) == "Manuel" ) {
      $mod -> MxText ( "placer", "Placer :" ) ;
      $data = $this->getListeListes ( $this->type, 1, 1 ) ;
      $mod -> MxSelect( "placerF", "placerF", '', $data , '', '', "size=\"1\"") ; 
    }
    // Génération de la variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "validAddListe" ) ) ;
    return $mod -> MxWrite ( "1" ) ;
  }

  function addListe ( ) {
    global $options ;
    // On vérifie qu'un item ne porte pas déjà ce nom.
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".$_POST['nomItemF']."' AND categorie='".$this->type."'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "CCAM_getListesComplexes", $param, "ResultQuery" ) ;
    // On signale l'erreur si le nom est déjà pris.
    if ( $res[INDIC_SVC][2] > 0 ) {
      $this->erreurs .= "Une catégorie portant le nom demandé (\"".$_POST['nomItemF']."\") existe déjà. La création est annulée." ;
    } else {
      if ( eregi ( "[0-9a-zA-Z]", $_POST['nomItemF'] ) ) {
	// On positionne correctement le nouvel item et on déplace les autres.
	if ( $options->getOption ( "Catégories ".$this->type ) != "Manuel" ) { $rang = 1 ; } else { 
	  $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND iditem='".$_POST['placerF']."'" ;
	  $res2 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	  $rang = $res2[rang][0] + 1 ; 
	}
	$param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND rang>='$rang' AND categorie='".$this->type."' AND nomitem='LISTE'" ;
	$req = new clResultQuery ;
	$res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res[iditem][$i] ) ; $i++ ) {
	  $data2[rang] = $res[rang][$i] + 1 ;
	  $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data2 ) ;
	  $requete->updRecord ( "iditem='".$res[iditem][$i]."'" ) ;
	}
	
  if ($this->type == "ListeMédecins" ) {
    $data[code] = $_POST['ufF'] ;

    // On regarde si l'uf existe déjà dans la table ccam_uf_spe
    $req = new clResultQuery ;
      
    // Récupération des informations de la table ccam_uf_spe 
    $param_ccam_uf_spe["cw"] = "numeroUF ='".$_POST['ufF']."' and libelleSpecialite!='".$_REQUEST["liste"]."'" ; 
    $resTable_ccam_uf_spe   = $req -> Execute ( "Fichier", "CCAM_getUFspe", $param_ccam_uf_spe, "ResultQuery" ) ;
      
    if ( $resTable_ccam_uf_spe["INDIC_SVC"]["2"] == 0 ) {
      
      // (Insertion) Mise à jour de l'Uf dans la table ccam_uf_spe
      $dataSpe[numeroUF]          = $_POST['ufF'] ;
      $dataSpe[libelleSpecialite] = stripslashes($_POST['nomItemF']);
      $requete = new clRequete ( CCAM_BDD, "ccam_uf_spe", $dataSpe ) ;
      $requete->addRecord ( ) ; 
    
      // Insertion du nouveau item.
      $data[categorie]    = $this->type ;
      $data[nomliste]     = $_POST['nomItemF'] ;
      $data[nomitem]      = "LISTE" ;
      $data[rang]         = $rang ;
      $data[idDomaine]    = CCAM_IDDOMAINE ;
      $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
      $requete->addRecord ( ) ;
      // Message d'information.
      $this->infos .= "La catégorie de ".$this->type." \"".stripslashes($_POST['nomItemF'])."\" a été ajoutée." ;
      }
    else
      // Signalement d'une erreur si l'uf à modifier existe.
      $this->erreurs .= "La liste ne peut pas être modifiée car l'uf ".$_POST['ufF']." existe déjà." ;
      
  }
  else {
  // Insertion du nouveau item.
	$data[categorie]    = $this->type ;
	$data[nomliste]     = $_POST['nomItemF'] ;
	$data[nomitem]      = "LISTE" ;
	$data[rang]         = $rang ;
	$data[idDomaine]    = CCAM_IDDOMAINE ;
	$requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
	$requete->addRecord ( ) ;
	// Message d'information.
	$this->infos .= "La catégorie de ".$this->type." \"".stripslashes($_POST['nomItemF'])."\" a été ajoutée." ;
  
  }
  
  
  } 
  
  else {
  $this->erreurs = "Le nom choisi ne doit pas être vide." ;
      }
      
    }
  }

  function modListe ( ) {
    global $session ;
    global $options ;
    global $errs ;
    $req = new clResultQuery ;
    // Récupération des anciennes informations de l'item à modifier.
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".$_POST['liste']."' AND nomitem='LISTE' AND categorie='".$this->type."'" ;
    $res1 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
    // Récupération de tous les autres items.
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND categorie='".$this->type."'" ;
    $res2 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
    // Vérification de la présence d'une liste portant le nouveau nom.
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomitem='LISTE' and nomliste='".$_POST['nomItemF']."' and iditem!='".$res1[iditem][0]."'" ;
    $res3 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;

    // Vérification que l'item existe.
    if ( $res1[INDIC_SVC] > 0 ) {
      // Vérification d'un changement de nom.
      if ( $res1[nomliste][0] != stripslashes($_POST['nomItemF']) ) {
	// Nouveau nom libre ou pas.
	if ( $res3[INDIC_SVC][2] == 0 ) {
	  // Nouveau nom correct.
	  if ( eregi ( "[0-9a-zA-Z]", $_POST['nomItemF'] ) ) {
	    // Mise à jour du nom de l'item dans la base.
	    $data[nomliste] = $_POST['nomItemF'] ;
	    $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
	    $requete->updRecord ( "nomliste='".addslashes($res1[nomliste][0])."'" ) ;
	    // Message d'information.
	    $this->infos .= "L'item \"".$res1[nomliste][0]."\" de la liste des catégories de \"".$this->type."\" a changé de nom : 
                             \"".stripslashes($_POST['nomItemF'])."\".<br />" ;
	    $_POST['liste'] = $_POST['nomItemF'] ;
	  } else {
	    // Message d'erreur.
	    $this->erreurs .= "Le nom choisi ne doit pas être vide." ;
	  }
	} else {
	  // Message d'erreur.
	  $this->erreurs .= "Le nom choisi pour la liste \"".$res1[nomliste][0]."\" est déjà utilisé. La modification est annulée.<br />" ;
	}
      }
      // On vérifie si l'item a changé de position ou non.
      if ( $res1[iditem][0] != $_POST['placerF'] ) {
	// Suppression du rang actuel et décalage du rang des autres items.	
	$rang = $res1[rang][0] ;
	$param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND rang>'$rang' and categorie='".$this->type."' AND nomitem='LISTE'" ;
	$res4 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res4[iditem][$i] ) ; $i++ ) {
	  $data3[rang] = $res4[rang][$i] - 1 ;
	  $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data3 ) ;
	  $requete->updRecord ( "iditem='".$res4[iditem][$i]."'" ) ;
	}
	// Calcul du rang suivant.
	if ( $_POST['placerF'] ) {
	  $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND iditem='".$_POST['placerF']."' and categorie='".$this->type."' AND nomitem='LISTE'" ;
	  $res6 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;	
	  $rang = $res6[rang][0] + 1 ;
	} else $rang = 1 ;
	// Décalage de tous les items d'un rang.
	$param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND rang>='$rang' and categorie='".$this->type."' AND nomitem='LISTE'" ;
	$res5 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res5[iditem][$i] ) ; $i++ ) {
	  $data4[rang] = $res5[rang][$i] + 1 ;
	  $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data4 ) ;
	  $requete->updRecord ( "iditem='".$res5[iditem][$i]."'" ) ;
	}
	// Mise à jour du rang de l'item.
	if ( $_POST['placerF'] ) $data5[rang] = $res6[rang][0] + 1 ;
	else $data5[rang] = 1 ;
	$requete = new clRequete ( CCAM_BDD, "ccam_liste", $data5 ) ;
	$requete->updRecord ( "iditem='".$res1[iditem][0]."'" ) ;
	// Message d'information.
	$this->infos .= "La catégorie de recours \"".$res1[nomliste][0]."\" a changé de position.<br />" ;
  if ($this->type == "ListeMédecins" ) {
      $dataUf[code] = $_POST['ufF'] ;
        
      // On regarde si l'uf existe déjà dans la table ccam_uf_spe
      $req = new clResultQuery ;
      
      // Récupération des informations de la table ccam_uf_spe 
      $param_ccam_uf_spe["cw"] = "numeroUF ='".$_POST['ufF']."' and libelleSpecialite!='".$_REQUEST["liste"]."'" ; 
      $resTable_ccam_uf_spe   = $req -> Execute ( "Fichier", "CCAM_getUFspe", $param_ccam_uf_spe, "ResultQuery" ) ;
      
      if ( $resTable_ccam_uf_spe["INDIC_SVC"]["2"] == 0 ) {
        $requete = new clRequete ( CCAM_BDD, "ccam_liste", $dataUf ) ;
        $requete->updRecord ( "nomitem='LISTE' and nomliste='".$_REQUEST["liste"]."'" ) ;
      
        // Mise à jour de l'Uf dans la table ccam_uf_spe
        $dataSpe[numeroUF] = $_POST['ufF'] ;
        $requete = new clRequete ( CCAM_BDD, "ccam_uf_spe", $dataSpe ) ;
        $requete->updRecord ( "libelleSpecialite='".$_REQUEST["liste"]."'" ) ;  
      }
      else
        // Signalement d'une erreur si l'uf à modifier existe.
        $this->erreurs .= "La liste ne peut pas être modifiée car l'uf ".$_POST['ufF']." existe déjà." ;
      
      }
  }
  
  } else {
      // Signalement d'une erreur si l'item à modifier n'existe pas.
      $this->erreurs .= "La liste ne peut pas être modifiée (id=\"$idItem\") car elle n'existe pas." ;
      $errs->addErreur ( "clListesGenerales : La liste ne peut pas être modifiée (id=\"$idIditem\") car elle n'existe pas." ) ;
    }
  }

  function getFormModListe ( ) {
    global $session ;
    global $options ;
    // Chargement du template.
    $mod = new ModeliXe ( "CCAM_GestionItemsComplexes2.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $mod -> MxBloc ( "ajouter", "modify", " " ) ;
    if ( ! $session->getDroit ( "CCAM_".$this->type, "m" ) ) {
      $mod -> MxBloc ( "modifier", "modify", " " ) ;
    }
    if ( ! $session->getDroit ( "CCAM_".$this->type, "d" ) ) {
      $mod -> MxBloc ( "supprimer", "modify", " " ) ;
    }
    
    // Nom de la liste.
    $mod -> MxText ( "nomListe", "Modifier la catégorie \"".stripslashes($_POST['liste'])."\" " ) ;
    
    if ( $this->type == "ListeMédecins" ) {
        // Recherche de l'uf de la categorie stripslashes($session->getNavi ( 4 ) )
      $paramuf[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomitem='LISTE' AND nomliste='".$_REQUEST["liste"]."' AND categorie='".$this->type."'" ;
      $requf       = new clResultQuery ;
      $resuf       = $requf -> Execute ( "Fichier", "CCAM_getListesComplexes2", $paramuf, "ResultQuery" ) ;
      $mod -> MxText ( "uf", "Uf :" ) ;
      $mod -> MxFormField ( "ufF", "text", "ufF", $resuf["code"][0], "size=\"31\" maxlength=\"16\"" ) ;
    }
    // Champs texte pour entrer le nom de l'item.
    $mod -> MxText ( "nomItem", "Nom :" ) ;
    $mod -> MxFormField ( "nomItemF", "text", "nomItemF", stripslashes($_POST['liste']), "size=\"31\" maxlength=\"50\"" ) ;
    // Si le classement est manuel dans cette liste, alors on affiche une liste de positions possibles.
    if ( $options->getOption ( "Catégories ".$this->type ) == "Manuel" ) {
      $mod -> MxText ( "placer", "Placer :" ) ;
      $data = $this->getListeListes ( $this->type, 1, 1, $_POST['liste'] ) ;
      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND categorie='".$this->type."' AND nomitem='LISTE' AND nomliste='".$_POST['liste']."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
      $mod -> MxSelect( "placerF", "placerF", $res[iditem][0], $data , '', '', "size=\"1\"") ; 
    }
    // Génération de la variable de navigation.
    $liste = "&liste=".stripslashes($_POST['liste']) ;
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "validModListe" ).$liste ) ;
    return $mod -> MxWrite ( "1" ) ;
  }

  function delListe ( $confirmation='' ) {
    global $session ;
    global $options ;
    global $errs ;
    if ( ! $confirmation ) {
      // Chargement du template.
      $mod = new ModeliXe ( "CCAM_FormConfirmation.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      // Phrase de confirmation.
      $mod -> MxText ( "question", "Etes vous certain de vouloir supprimer toute cette catégorie de ".$this->type." ?" ) ;
      // Génération de la variable de navigation.
      $liste = "&liste=".stripslashes($_POST['liste']) ;
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "validDelListe" ).$liste ) ;
      return $mod -> MxWrite ( "1" ) ;
    } else {
      $req = new clResultQuery ;
      // Récupération des informations actuelles de l'item.
      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".$_POST['liste']."' AND categorie='".$this->type."'" ;
      $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
      // Récupération de la liste des items.
      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND categorie='".$this->type."'" ;
      $res2 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
      // On vérifie qu'on n'est pas en train de supprimer le dernier item.
      if ( $res2[INDIC_SVC][2] > 1 ) {
	// Vérification que l'item existe.
	if ( $res[INDIC_SVC][2] > 0 ) {
	  // Décalage des rangs des autres items.
	  $rang = $res[rang][0] ;
	  $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND rang>'$rang' AND categorie='".$this->type."' AND nomitem='LISTE'" ;
	  $res3 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	  for ( $i = 0 ; isset ( $res3[iditem][$i] ) ; $i++ ) {
	    $data[rang] = $res3[rang][$i] - 1 ;
	    $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
	    $requete->updRecord ( "iditem='".$res3[iditem][$i]."'" ) ;
	  }
	  // Message d'information.
	  $this->infos .= "La liste \"".$res[nomliste][0]."\" a été supprimée de la liste des catégories de ".$this->type ;
	  // Suppression de l'item.
	  $requete = new clRequete ( CCAM_BDD, "ccam_liste" ) ;
	  $requete->delRecord ( "nomListe='".$_POST['liste']."' AND nomitem='LISTE' AND categorie='".$this->type."'" ) ;
	  $_POST['liste'] = '' ;
	} else {
	  // Signalement 
	  $this->erreurs .= "La liste ne peut pas être supprimée (id=\"$idItem\") car elle n'existe pas." ;
	  $errs->addErreur ( "clListesGenerales : La liste ne peut pas être supprimée (id=\"$idItem\") car elle n'existe pas." ) ;
	}
      } else {
	$this->erreurs .= "Impossible de supprimer la dernière liste \"".stripslashes($_POST['liste'])."\"." ;
      }
    }
  }

  function delItem ( ) {
    global $session ;
    global $options ;
    global $errs ;
    $req = new clResultQuery ;
    // Récupération des informations actuelles de l'item.
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".$_POST['liste']."' AND categorie='".$this->type."' AND iditem='".$_POST['item']."'" ;
    $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
    // Récupération de la liste des items.
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND categorie='".$this->type."' AND nomliste='".$_POST['liste']."'" ;
    $res2 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
    // Vérification que l'item existe.
    if ( $res[INDIC_SVC][2] > 0 ) {
      // Décalage des rangs des autres items.
      $rang = $res[rang][0] ;
      $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND rang>'$rang' AND categorie='".$this->type."' AND nomitem!='LISTE' AND nomliste='".$_POST['liste']."'" ;
      $res3 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
      //newfct ( gen_affiche_tableau, $res3[INDIC_SVC] ) ;
      for ( $i = 0 ; isset ( $res3[iditem][$i] ) ; $i++ ) {
	$data[rang] = $res3[rang][$i] - 1 ;
	$requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
	$requete->updRecord ( "iditem='".$res3[iditem][$i]."'" ) ;
      }
      // Message d'information.
      $this->infos .= "L'item \"".$res[nomitem][0]."\" a été supprimé de la liste \"".$res[nomliste][0]."\"" ;
      // Suppression de l'item.
      $requete = new clRequete ( CCAM_BDD, "ccam_liste" ) ;
      $requete->delRecord ( "nomListe='".$_POST['liste']."' AND iditem='".$_POST['item']."'" ) ;
      $_POST['item'] = '' ;
    } else {
      // Signalement 
      $this->erreurs .= "L'item ne peut pas être supprimé (id=\"$idItem\") car il n'existe pas." ;
      $errs->addErreur ( "clListesGenerales : L'item ne peut pas être supprimé (id=\"$idItem\") car il n'existe pas." ) ;
    }
  }

  function modItem ( ) {
    global $session ;
    global $options ;
    global $errs ;
    $req = new clResultQuery ;
    // Récupération des anciennes informations de l'item à modifier.
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".$_POST['liste']."' AND iditem='".$_POST['item']."'" ;
    $res1 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
    // Récupération de tous les autres items.
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND categorie='".$this->type."' AND nomliste='".$_POST['liste']."'" ;
    $res2 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
    // Vérification de la présence d'une liste portant le nouveau nom.
    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomitem='".$_POST['nomItemF']."' and iditem!='".$res1[iditem][0]."' AND nomliste='".$_POST['liste']."'" ;
    $res3 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;

    // Vérification que l'item existe.
    if ( $res1[INDIC_SVC] > 0 ) {
      // Vérification d'un changement de nom.
      if ( $res1[nomitem][0] != stripslashes($_POST['nomItemF']) ) {
	// Nouveau nom libre ou pas.
	if ( $res3[INDIC_SVC][2] == 0 ) {
	  // Nouveau nom correct.
	  if ( eregi ( "[0-9a-zA-Z]", $_POST['nomItemF'] ) ) {
	    // Mise à jour du nom de l'item dans la base.
	    $data[nomitem] = $_POST['nomItemF'] ;
	    $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
	    $requete->updRecord ( "iditem='".$_POST['item']."'" ) ;
	    // Message d'information.
	    $this->infos .= "L'item \"".$res1[nomitem][0]."\" de la liste \"".stripslashes($_POST['liste'])."\" a changé de nom : 
                             \"".stripslashes($_POST['nomItemF'])."\".<br />" ;
	  } else {
	    // Message d'erreur.
	    $this->erreurs .= "Le nom choisi ne doit pas être vide." ;
	  }
	} else {
	  // Message d'erreur.
	  $this->erreurs .= "Le nom choisi pour l'item \"".$res1[nomitem][0]."\" est déjà utilisé. La modification est annulée.<br />" ;
	}
      }
      // On vérifie si l'item a changé de position ou non.
      if ( $_POST['item'] != $_POST['placerF'] ) {
	// Suppression du rang actuel et décalage du rang des autres items.	
	$rang = $res1[rang][0] ;
	$param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND rang>'$rang' AND categorie='".$this->type."' AND nomitem!='LISTE' AND nomliste='".$_POST['liste']."'" ;
	$res4 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res4[iditem][$i] ) ; $i++ ) {
	  $data3[rang] = $res4[rang][$i] - 1 ;
	  $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data3 ) ;
	  $requete->updRecord ( "iditem='".$res4[iditem][$i]."'" ) ;
	}
	// Calcul du rang suivant.
	if ( $_POST['placerF'] ) {
	  $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND iditem='".$_POST['placerF']."' AND categorie='".$this->type."'" ;
	  $res6 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;	
	  $rang = $res6[rang][0] + 1 ;
	} else $rang = 1 ;
	// Décalage de tous les items d'un rang.
	$param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND rang>='$rang' AND categorie='".$this->type."' AND nomitem!='LISTE' AND nomliste='".$_POST['liste']."'" ;
	$res5 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res5[iditem][$i] ) ; $i++ ) {
	  $data4[rang] = $res5[rang][$i] + 1 ;
	  $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data4 ) ;
	  $requete->updRecord ( "iditem='".$res5[iditem][$i]."'" ) ;
	}
	// Mise à jour du rang de l'item.
	if ( $_POST['placerF'] ) $data5[rang] = $res6[rang][0] + 1 ;
	else $data5[rang] = 1 ;
	$requete = new clRequete ( CCAM_BDD, "ccam_liste", $data5 ) ;
	$requete->updRecord ( "iditem='".$res1[iditem][0]."'" ) ;
	// Message d'information.
	$this->infos .= "L'item \"".$res1[nomitem][0]."\" a changé de position.<br />" ;


      /*if ($this->type == "ListeMédecins" ) {
        $dataUf[code] = $_POST['ufF'] ;
        $requete = new clRequete ( CCAM_BDD, "ccam_liste", $dataUf ) ;
        $requete->updRecord ( "nomitem='LISTE' and nomliste='".$_REQUEST["liste"]."'" ) ;
      
      // Mise à jour de l'Uf dans la table ccam_uf_spe
        $dataSpe[numeroUF] = $_POST['ufF'] ;
        $requete = new clRequete ( CCAM_BDD, "ccam_uf_spe", $dataSpe ) ;
        $requete->updRecord ( "libelleSpecialite='".$_REQUEST["liste"]."'" ) ;  
      
      }*/
      }
      
      // On vérifie si le code a changé.
      if ( $res1[code][0] != $_POST['codeF'] ) {
	$param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND code='".$_POST['codeF']."' AND categorie='".$this->type."' AND nomliste='".$_POST['liste']."'" ;
	$res6 = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
	if ( eregi ( "[0-9a-zA-Z]", $_POST['codeF'] ) && $this->type != "Documents"  ) {
	  // code modifié pour permettre de saisir des codes ADELI non unique
    if ( $res6[INDIC_SVC][2] > 0 && $this->type != "Documents" ) {
	    $this->erreurs .= "L'item \"".$res6[nomitem][0]."\" a déjà ce code attribué. Merci d'en choisir un autre." ;
	  } else {
	    // Mise à jour du rang de l'item.
	    if ( $_POST['codeF'] ) $data6[code] = $_POST['codeF'] ;
	    else $data6[code] = '_' ;
	    $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data6 ) ;
	    $requete->updRecord ( "iditem='".$res1[iditem][0]."'" ) ;
	    $this->infos .= "Le code \"".$res1[code][0]."\" de l'item \"".$res1[nomitem][0]."\" a changé : \"".stripslashes($_POST['codeF'])."\"<br />" ;
	  }
	} else {
	  $this->erreurs = "Le code ne doit pas être vide." ;
	}
      }
    } else {
      // Signalement d'une erreur si l'item à modifier n'existe pas.
      $this->erreurs .= "L'item ne peut pas être modifié (id=\"$idItem\") car il n'existe pas." ;
      $errs->addErreur ( "clListesGenerales : L'item ne peut pas être modifié (id=\"$idIditem\") car il n'existe pas." ) ;
    }
  }

  function getAffichage ( ) {
    return $this->af ;
  }  

}
