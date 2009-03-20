<?php

// Titre  : Classe Listes
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 17 F�vrier 2005

// Description : 
// Cette classe g�re les listes (recours et diagnostics).
// Elle permet d'afficher une liste seule de diff�rentes fa�ons.

class clListes {

  // Attributs de la classe.
  // Contient l'affichage g�n�r� par la classe.
  private $af ;
  // Type (pour l'instant "Recours" ou "Diagnostics").
  private $type ;
  // Contient les messages d'informations.
  private $infos ;
  // Contient les messages d'erreurs.
  private $erreurs ;

  // Constructeur.
  function __construct ( $type, $recup='' ) {
    global $options ;
    $options -> checkOptionListe ( $type, 1 ) ;
    if ( $recup ) {
      $this->type = $type ;
    } else {
      $droits = new clDroits ( "CHECK" ) ;
      $droits -> checkDroit ( "Administration_".$type, "Administration : Gestion de la liste complexe '$type'" ) ;
      $this->type = $type ;
      $this->setAffichage ( ) ;
    }
  }

  // Fonction centrale : Elle va g�n�rer tout l'affichage.
  function setAffichage ( ) {
    global $session ;
    global $options ;
    // R�cup�ration du type dans une variable simple � manipuler.
    $type = $this->type ;
    $nli = $options->getOption ( "Lignes ".$type ) ;
    if ( ! $nli ) $nli = 15 ;
    // Ajout d'une liste.
    if ( ( $_POST['Ajouter'] or $_POST['Ajouter_x'] ) and $session->getNavi ( 3 ) == "validAddListe" and $session->getDroit ( "Administration_".$type, "w" ) ) {
      $form = $this->addListe ( ) ;
    }
    // Modification d'une liste.
    if ( ($_POST['Modifier'] or $_POST['Modifier_x'] ) and $session->getNavi ( 3 ) == "validModListe" and $session->getDroit ( "Administration_".$type, "m" ) ) {
      $form = $this->modListe ( ) ;
    }
    // Suppression d'une liste -> demande de confirmation.
    if ( ( $_POST['Supprimer'] or $_POST['Supprimer_x'] ) and $session->getNavi ( 3 ) == "validModListe" and $session->getDroit ( "Administration_".$type, "d" ) ) {
      $form = $this->delListe ( ) ;
    }
    // Suppression d'une liste -> Suppression r�elle.
    if ( ( $_POST['Supprimer'] or $_POST['Supprimer_x'] ) and $session->getNavi ( 3 ) == "validDelListe" and $session->getDroit ( "Administration_".$type, "d" ) ) {
      $form = $this->delListe ( "1" ) ;
    }
    // Ajout d'un item.
    if ( ( $_POST['Ajouter'] or $_POST['Ajouter_x'] ) and $session->getNavi ( 3 ) == "validAddItem" and $session->getDroit ( "Administration_".$type, "w" ) ) {
      $form = $this->addItem ( ) ;
    }
    // Suppression d'un item.
    if ( ( $_POST['Supprimer'] or $_POST['Supprimer_x'] ) and $session->getNavi ( 3 ) == "validModItem" and $session->getDroit ( "Administration_".$type, "w" ) ) {
      $form = $this->delItem ( ) ;
    }
    // Modification d'un item.
    if ( ( $_POST['Modifier'] or $_POST['Modifier_x'] ) and $session->getNavi ( 3 ) == "validModItem" and $session->getDroit ( "Administration_".$type, "w" ) ) {
      $form = $this->modItem ( ) ;
    }
    // R�paration d'une liste de listes.
    if ( $session->getNavi ( 3 ) == "repListeListes" and $session->getDroit ( "Administration_".$type, "a" ) ) {
      $this->repListe ( ) ;
    }
    // R�paration d'une liste d'items.
    if ( $session->getNavi ( 3 ) == "repListeItems" and $session->getDroit ( "Administration_".$type, "a" ) ) {
      $this->repListe ( $session->getNavi ( 4 ) ) ;
    }
    
    switch ( $options->getOption ( "Cat�gories ".$type ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphab�tique': $order = "ORDER BY nomliste" ; break ;
    case 'Alphab�tique invers�': $order = "ORDER BY nomliste DESC" ; break ;
    default : $order = "" ; break ;
    }
    $param[cw] = "WHERE nomitem='LISTE' AND categorie=\"$type\" $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;  
    
    // Formulaire d'ajout d'une liste.
    if ( $session->getNavi ( 3 ) == "addListe" and $session->getDroit ( "Administration_".$type, "w" ) ) {
      $form = $this->getFormAddListe ( ) ;
    }
    // Formulaire de modification d'une liste.
    if ( $_POST['liste'] and $session->getNavi ( 3 ) == "modListe" and $session->getDroit ( "Administration_".$type, "m" ) ) {
      $form = $this->getFormModListe ( ) ;
    }
    // Formulaire d'ajout d'un item.
    if ( $session->getNavi ( 3 ) == "addItem" and $session->getDroit ( "Administration_".$type, "w" ) ) {
      $form = $this->getFormAddItem ( ) ;
    }
    // Formulaire de modification d'un item.
    if ( $session->getNavi ( 3 ) == "modItem" and $session->getDroit ( "Administration_".$type, "m" ) ) {
      $form = $this->getFormModItem ( ) ;
    }
    
    $mod = new ModeliXe ( "GestionListesComplexes.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
    else $mod -> MxBloc ( "informations", "modify", " " ) ;
    if ( $this->erreurs ) $mod -> MxText ( "erreurs.errs", $this->erreurs ) ;
    else $mod -> MxBloc ( "erreurs", "modify", " " ) ;
    $mod -> MxText ( "formItems", $form ) ;
    $mod -> MxText ( "listeListes.nomListe", "Cat�gories de ".$type ) ;
    if ( $session->getDroit ( "Administration_".$type, "w" ) ) {
      $mod -> MxImage ( "listeListes.imgAjouter", URLIMGAJO, "Ajouter" ) ;
      $mod -> MxUrl  ( "listeListes.lienAjouter", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "addListe" ) ) ;
    }
    if ( $session->getDroit ( "Administration_".$type, "a" ) ) {
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
      if ( $session->getDroit ( "Administration_".$type, "w" ) ) {
	$mod -> MxImage ( "listeItems.imgAjouter", URLIMGAJO, "Ajouter" ) ;
	$mod -> MxUrl  ( "listeItems.lienAjouter", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "addItem", stripslashes($_POST['liste']) ) ) ;
      }
      if ( $session->getDroit ( "Administration_".$type, "a" ) ) {
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

  // R�paration d'une liste (de listes ou d'items).
  function repListe ( $nomListe='' ) {
    global $errs ;
    // R�paration d'une liste d'items.
    if ( $nomListe ) {
      $_POST['liste'] = $nomListe ;
      // R�cup�ration des items de la liste � r�parer.
      $param[cw] = "WHERE nomliste='$nomListe' AND nomitem!='LISTE' AND categorie=\"".$this->type."\" ORDER BY rang" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ; 
      // Si au moins un item est pr�sent, alors on commence la reconstruction.
      if ( $res[INDIC_SVC][2] ) {
	for ( $i = 0 ; isset ( $res[iditem][$i] ) ; $i++ ) {
	  $data[rang] = $i + 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	  $requete->updRecord ( "iditem='".$res[iditem][$i]."'" ) ;
	}
	// Message d'information.
	$this->infos .= "La r�paration de la liste \"".stripslashes($nomListe)."\" a �t� effectu�e." ;
      } else {
	// Signalement des erreurs.
	$errs->addErreur ( "La liste \"".stripslashes($nomListe)."\" n'existe pas ou ne contient aucun item, la r�paration est annul�e." ) ;
	$this->erreurs .= "La liste \"".stripslashes($nomListe)."\" n'existe pas ou ne contient aucun item, la r�paration est annul�e." ;
      }
    // R�paration d'une liste de listes.
    } else {
      // R�cup�ration des diff�rentes listes.
      $param[cw] = "WHERE nomitem='LISTE' AND categorie=\"".$this->type."\" ORDER BY rang" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;  
      // Si au moins une liste est pr�sente, on commence la reconstruction.
      if ( $res[INDIC_SVC][2] ) {
	for ( $i = 0 ; isset ( $res[iditem][$i] ) ; $i++ ) {
	  $data[rang] = $i + 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	  $requete->updRecord ( "iditem='".$res[iditem][$i]."'" ) ;
	}
	// Message d'information.
	$this->infos .= "La r�paration de la liste des cat�gories de ".$this->type." a �t� effectu�e." ;
      } else {
	// Signalement des erreurs.
	$errs->addErreur ( "La liste des listes de cat�gories de ".$this->type." ne contient aucune liste, la r�paration est annul�e." ) ;
	$this->erreurs .= "La liste des listes de cat�gories de ".$this->type." ne contient aucune liste, la r�paration est annul�e." ;
      }
    }
  }

  function getListeItems (  $nomListe, $modelixe='', $placement='', $nomItem='' ) {
    global $options ;
    // Pr�paration du type de classement pour la requ�te.
    
    switch ( $options->getOption ( $this->type ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphab�tique': $order = "ORDER BY nomitem" ; break ;
    case 'Alphab�tique invers�': $order = "ORDER BY nomitem DESC" ; break ;
    default : $order = "" ; break ;
    }
    

    $param[cw] = "WHERE nomliste='".addslashes(stripslashes($nomListe))."' AND categorie=\"".$this->type."\" AND nomitem!='LISTE' $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // Affichage en cas de d�bugage.
    if ( DEBUGLISTES ) { newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ; }
    // Pr�paration du tableau � retourner pour un select de modelixe.
    if ( $modelixe ) {
      // Placement ou affichage simple.
      if ( $placement ) { 
	$placer = "Apr�s " ; 
	$tab[0] = "En d�but de liste" ;
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
	  if ( DEBUGPOSITIONS ) $tab[($res[$type][$i]+$val)] = $placer.$res[rang][$i]." - ".$res[nomitem][$i].$code ; 
	  else $tab[($res[$type][$i]+$val)] = $placer.$res[nomitem][$i].$code ; 
	}
      }
      // Retourne le tableau au format attendu par modelixe.
      return $tab ;
    } else {
      // Retourne le tableau au format normal de ResultQuery.
      return $res ;
    }
  }
  
  /*
    function getListeItems (  $nomListe, $modelixe='', $placement='', $nomItem='', $indexNom='' ) {
    global $options ;
    // Pr�paration du type de classement pour la requ�te.
    switch ( $options->getOption ( $this->type ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphab�tique': $order = "ORDER BY nomitem" ; break ;
    case 'Alphab�tique invers�': $order = "ORDER BY nomitem DESC" ; break ;
    default : $order = "" ; break ;
    }

    $param[cw] = "WHERE idDomaine='".CCAM_IDDOMAINE."' AND nomliste='".addslashes(stripslashes($nomListe))."' AND categorie=\"".$this->type."\" AND nomitem!='LISTE' $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "CCAM_getListesItemsComplexes", $param, "ResultQuery" ) ;
    // Affichage en cas de d�bugage.
    if ( DEBUGLISTES ) { newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ; }
    // Pr�paration du tableau � retourner pour un select de modelixe.
    if ( $modelixe ) {
      // Placement ou affichage simple.
      if ( $placement ) { 
	$placer = "Apr�s " ; 
	$tab[0] = "En d�but de liste" ;
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
  */
  function getCode ( $nomListe, $nomItem ) {
    $param[cw] = "WHERE nomliste='".addslashes(stripslashes($nomListe))."' AND categorie=\"".$this->type."\" AND nomitem='".addslashes(stripslashes($nomItem))."' $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    return $res[code][0] ;
  }

  function getListes ( $nomListe='', $opt='' ) {
    global $options ;
    // Pr�paration du type de classement pour la requ�te.
    switch ( $options->getOption ( $this->type ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphab�tique': $order = "ORDER BY nomliste" ; break ;
    case 'Alphab�tique invers�': $order = "ORDER BY nomliste DESC" ; break ;
    default : $order = "" ; break ;
    }
    if ( $opt ) $tab[] = SELECTLISTE ;
    if ( $nomListe ) {
      $order = str_replace( 'nomliste', 'nomitem', $order ) ;
      $param[cw] = "WHERE nomliste='".addslashes(stripslashes($nomListe))."' AND categorie=\"".$this->type."\" AND nomitem!='LISTE' $order" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
      // Affichage en cas de d�bugage.
      if ( DEBUGLISTES ) { newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ; }
      for ( $i = 0 ; isset ( $res[iditem][$i]  ) ; $i++ ) {
	if ( $opt ) $tab[$res[nomitem][$i]] = $res[nomitem][$i] ;
	else $tab[$res[iditem][$i]] = $res[nomitem][$i] ;
      }
    } else {
      $param[cw] = "WHERE categorie=\"".$this->type."\" and nomitem=\"LISTE\" $order" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
      for ( $i = 0 ; isset ( $res[iditem][$i]  ) ; $i++ ) {
	if ( $opt ) $tab[$res[nomliste][$i]] = $res[nomliste][$i] ;
	else $tab[$res[iditem][$i]] = $res[nomliste][$i] ;
      }
    }
    //eko ( $tab ) ;
    return $tab ;

  }

  function getListeListes (  $nomListe, $modelixe='', $placement='', $nomItem='' ) {
    global $options ;
    // Pr�paration du type de classement pour la requ�te.
    switch ( $options->getOption ( $this->type ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphab�tique': $order = "ORDER BY nomliste" ; break ;
    case 'Alphab�tique invers�': $order = "ORDER BY nomliste DESC" ; break ;
    default : $order = "" ; break ;
    }

    $param[cw] = "WHERE categorie=\"".$this->type."\" and nomitem=\"LISTE\" $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // Affichage en cas de d�bugage.
    if ( DEBUGLISTES ) { newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ; }
    // Pr�paration du tableau � retourner pour un select de modelixe.
    if ( $modelixe ) {
      // Placement ou affichage simple.
      if ( $placement ) { 
	$placer = "Apr�s " ; 
	$tab[0] = "En d�but de liste" ;
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
	  if ( DEBUGPOSITIONS ) $tab[($res[$type][$i]+$val)] = $placer.$res[rang][$i]." - ".$res[nomliste][$i].$code ; 
	  else $tab[($res[$type][$i]+$val)] = $placer.$res[nomliste][$i].$code ; 
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
      // R�cup�ration des informations actuelles de l'item.
      $param[cw] = "WHERE nomliste='".$_POST['liste']."' AND iditem='".$_POST['item']."' AND categorie='".$this->type."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
      // Chargement du template.
      $mod = new ModeliXe ( "GestionItemsComplexes.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      $mod -> MxBloc ( "ajouter", "modify", " " ) ;
      if ( ! $session->getDroit ( "Administration_".$this->type, "m" ) ) {
	$mod -> MxBloc ( "modifier", "modify", " " ) ;
      }
      if ( ! $session->getDroit ( "Administration_".$this->type, "d" ) ) {
	$mod -> MxBloc ( "supprimer", "modify", " " ) ;
      }
      // Nom de la liste.
      $mod -> MxText ( "nomListe", "Modification de l'item \"".$res[nomitem][0]."\" " ) ;
      // Champs texte pour entrer le nom de l'item.
      $mod -> MxText ( "nomItem", "Nom :" ) ;
      $mod -> MxFormField ( "nomItemF", "text", "nomItemF", $res[nomitem][0], "size=\"31\" maxlength=\"50\"" ) ;
      if ( $this->type != "Documents" AND $this->type!='ListesBilans' AND $this->type!='Documents Blopera' AND $this->type!='ListesBilansCmp' ) {
	$mod -> MxText ( "code", "Code :" ) ;
	$mod -> MxFormField ( "codeF", "text", "codeF", $res[code][0], "size=\"31\" maxlength=\"16\"" ) ;
      }
      // Si le classement est manuel dans cette liste, alors on affiche une liste de positions possibles.
      if ( $options->getOption ( $this->type ) == "Manuel" ) {
	$mod -> MxText ( "placer", "Placer :" ) ;
	$data = $this->getListeItems ( $_POST['liste'], 1, 1, $res[nomitem][0] ) ;
	$param[cw] = "WHERE categorie='".$this->type."' AND nomitem!='LISTE' AND nomliste='".$_POST['liste']."'" ;
	$req = new clResultQuery ;
	$res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	$mod -> MxSelect( "placerF", "placerF", $_POST['item'], $data , '', '', "size=\"1\"") ; 
      }
      // G�n�ration de la variable de navigation.
      $liste = "&liste=".stripslashes($_POST['liste'])."&item=".$_POST['item'] ;
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "validModItem" ).$liste ) ;
      return $mod -> MxWrite ( "1" ) ;
    }
  }


  function addItem ( ) {
    global $options ;
    global $session ;
    // On v�rifie qu'un item ne porte pas d�j� ce nom.
    $param[cw] = "WHERE nomliste='".$_POST['liste']."' AND nomitem='".$_POST['nomItemF']."' AND categorie='".$this->type."'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListes", $param, "ResultQuery" ) ;
    // On signale l'erreur si le nom est d�j� pris.
    if ( $res[INDIC_SVC][2] > 0 ) {
      $this->erreurs .= "Un item portant le nom demand� (\"".$_POST['nomItemF']."\") existe d�j� dans cette liste. La cr�ation est annul�e." ;
    } else {
      if ( eregi ( "[0-9a-zA-Z]", $_POST['nomItemF'] ) ) {
	$param[cw] = "WHERE code='".$_POST['codeF']."' AND categorie='".$this->type."' AND nomliste='".$_POST['liste']."'" ;
	$res6 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	if ( eregi ( "[0-9a-zA-Z]", $_POST['codeF'] ) OR $this->type == "Documents" OR $this->type=='Documents Blopera' OR $this->type == "ListesBilans"  OR $this->type=='ListesBilansCmp') {
	  if ( $res6[INDIC_SVC][2] > 0 && $this->type != "Documents" && $this->type!='Documents Blopera' && $this->type != 'ListesBilans' && $this->type!='ListesBilansCmp' ) {
	    $this->erreurs .= "L'item \"".$res6[nomitem][0]."\" a d�j� ce code attribu�. Merci d'en choisir un autre." ;
	  } else {
	    // On positionne correctement le nouvel item et on d�place les autres.
	    if ( $options->getOption ( $this->type ) != "Manuel" ) { $rang = 1 ; } else { 
	      $param[cw] = "WHERE iditem='".$_POST['placerF']."'" ;
	      $res2 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	      $rang = $res2[rang][0] + 1 ; 
	    }
	    $param[cw] = "WHERE rang>='$rang' AND categorie='".$this->type."' AND nomitem!='LISTE' AND nomliste='".$_POST['liste']."'" ;
	    $req = new clResultQuery ;
	    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	    for ( $i = 0 ; isset ( $res[iditem][$i] ) ; $i++ ) {
	      $data2[rang] = $res[rang][$i] + 1 ;
	      $requete = new clRequete ( BASEXHAM, "listes", $data2 ) ;
	      $requete->updRecord ( "iditem='".$res[iditem][$i]."'" ) ;
	    }
	    // Insertion du nouveau item.
	    $data[categorie]    = $this->type ;
	    $data[nomliste]     = stripslashes($session->getNavi(4)) ;
	    $data[code]         = $_POST['codeF'] ;
	    $data[nomitem]      = $_POST['nomItemF'] ;
	    $data[rang]         = $rang ;
	    $data[valide]       = 1 ;
	    $data[idapplication] = IDAPPLICATION ;
	    $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	    $requete->addRecord ( ) ;
	    // Message d'information.
	    $this->infos .= "L'item \"".$_POST['nomItemF']."\" a �t� ajout�e � la cat�gorie \"".stripslashes($_POST['liste'])."\"." ;
	  }
	} else {
	  $this->erreurs = "Le code choisi ne doit pas �tre vide." ;
	}
      } else {
	$this->erreurs = "Le nom choisi ne doit pas �tre vide." ;
      }
    }
  }

  function getFormAddItem ( ) {
    global $options ;
    global $session ;
    $_POST['liste'] = $session->getNavi ( 4 ) ;
    // Chargement du template.
    $mod = new ModeliXe ( "GestionItemsComplexes.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $mod -> MxBloc ( "modifier", "modify", " " ) ;
    $mod -> MxBloc ( "supprimer", "modify", " " ) ;
    // Nom de la liste.
    $mod -> MxText ( "nomListe", "Ajouter un nouveau item � la cat�gorie ".stripslashes($session->getNavi ( 4 ) )) ;
    // Champs texte pour entrer le nom de l'item.
    $mod -> MxText ( "nomItem", "Nom :" ) ;
    $mod -> MxFormField ( "nomItemF", "text", "nomItemF", $_POST['valeur'], "size=\"31\" maxlength=\"50\"" ) ;
    if ( $this->type != "Documents" AND $this->type!='Documents Blopera' AND $this->type != "ListesBilans" AND $this->type!='ListesBilansCmp' ) {
      $mod -> MxText ( "code", "Code :" ) ;
      $mod -> MxFormField ( "codeF", "text", "codeF", '', "size=\"31\" maxlength=\"16\"" ) ;
    }
    // Si le classement est manuel dans cette liste, alors on affiche une liste de positions possibles.
    if ( $options->getOption ( $this->type ) == "Manuel" ) {
      $mod -> MxText ( "placer", "Placer :" ) ;
      $data = $this->getListeItems ( $session->getNavi ( 4 ), 1, 1 ) ;
      if ( ! count ( $data ) ) $data = Array ( ) ;
      $mod -> MxSelect( "placerF", "placerF", '', $data , '', '', "size=\"1\"") ; 
    }
    // G�n�ration de la variable de navigation.
    $liste = "&liste=".stripslashes($_POST['liste']) ;
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "validAddItem", $session->getNavi(4) ).$liste ) ;
    return $mod -> MxWrite ( "1" ) ;
  }

  function getFormAddListe ( ) {
    global $options ;
    global $session ;
    // Chargement du template.
    $mod = new ModeliXe ( "GestionItemsComplexes.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $mod -> MxBloc ( "modifier", "modify", " " ) ;
    $mod -> MxBloc ( "supprimer", "modify", " " ) ;
    // Nom de la liste.
    $mod -> MxText ( "nomListe", "Ajouter une nouvelle cat�gorie de ".$this->type ) ;
    // Champs texte pour entrer le nom de l'item.
    $mod -> MxText ( "nomItem", "Nom :" ) ;
    $mod -> MxFormField ( "nomItemF", "text", "nomItemF", $_POST['valeur'], "size=\"31\" maxlength=\"50\"" ) ;
    // Si le classement est manuel dans cette liste, alors on affiche une liste de positions possibles.
    if ( $options->getOption ( "Cat�gories ".$this->type ) == "Manuel" ) {
      $mod -> MxText ( "placer", "Placer :" ) ;
      $data = $this->getListeListes ( $this->type, 1, 1 ) ;
      $mod -> MxSelect( "placerF", "placerF", '', $data , '', '', "size=\"1\"") ; 
    }
    // G�n�ration de la variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "validAddListe" ) ) ;
    return $mod -> MxWrite ( "1" ) ;
  }

  function addListe ( ) {
    global $options ;
    // On v�rifie qu'un item ne porte pas d�j� ce nom.
    $param[cw] = "WHERE nomliste='".$_POST['nomItemF']."' AND categorie='".$this->type."'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListes", $param, "ResultQuery" ) ;
    // On signale l'erreur si le nom est d�j� pris.
    if ( $res[INDIC_SVC][2] > 0 ) {
      $this->erreurs .= "Une cat�gorie portant le nom demand� (\"".$_POST['nomItemF']."\") existe d�j�. La cr�ation est annul�e." ;
    } else {
      if ( eregi ( "[0-9a-zA-Z]", $_POST['nomItemF'] ) ) {
	// On positionne correctement le nouvel item et on d�place les autres.
	if ( $options->getOption ( "Cat�gories ".$this->type ) != "Manuel" ) { $rang = 1 ; } else { 
	  $param[cw] = "WHERE iditem='".$_POST['placerF']."'" ;
	  $res2 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	  $rang = $res2[rang][0] + 1 ; 
	}
	$param[cw] = "WHERE rang>='$rang' AND categorie='".$this->type."' AND nomitem='LISTE'" ;
	$req = new clResultQuery ;
	$res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res[iditem][$i] ) ; $i++ ) {
	  $data2[rang] = $res[rang][$i] + 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data2 ) ;
	  $requete->updRecord ( "iditem='".$res[iditem][$i]."'" ) ;
	}
	// Insertion du nouveau item.
	$data[categorie]    = $this->type ;
	$data[nomliste]     = $_POST['nomItemF'] ;
	$data[nomitem]      = "LISTE" ;
	$data[rang]         = $rang ;
	$data[valide]       = 1 ;
	$requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	$requete->addRecord ( ) ;
	// Message d'information.
	$this->infos .= "La cat�gorie de ".$this->type." \"".stripslashes($_POST['nomItemF'])."\" a �t� ajout�e." ;
      } else {
	$this->erreurs = "Le nom choisi ne doit pas �tre vide." ;
      }
    }
  }

  function modListe ( ) {
    global $session ;
    global $options ;
    global $errs ;
    $req = new clResultQuery ;
    // R�cup�ration des anciennes informations de l'item � modifier.
    $param[cw] = "WHERE nomliste='".$_POST['liste']."' AND nomitem='LISTE' AND categorie='".$this->type."'" ;
    $res1 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // R�cup�ration de tous les autres items.
    $param[cw] = "WHERE categorie='".$this->type."'" ;
    $res2 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // V�rification de la pr�sence d'une liste portant le nouveau nom.
    $param[cw] = "WHERE nomitem='LISTE' and nomliste='".$_POST['nomItemF']."' and iditem!='".$res1[iditem][0]."'" ;
    $res3 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;

    // V�rification que l'item existe.
    if ( $res1[INDIC_SVC] > 0 ) {
      // V�rification d'un changement de nom.
      if ( $res1[nomliste][0] != stripslashes($_POST['nomItemF']) ) {
	// Nouveau nom libre ou pas.
	if ( $res3[INDIC_SVC][2] == 0 ) {
	  // Nouveau nom correct.
	  if ( eregi ( "[0-9a-zA-Z]", $_POST['nomItemF'] ) ) {
	    // Mise � jour du nom de l'item dans la base.
	    $data[nomliste] = $_POST['nomItemF'] ;
	    $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	    $requete->updRecord ( "nomliste='".addslashes($res1[nomliste][0])."'" ) ;
	    // Message d'information.
	    $this->infos .= "L'item \"".$res1[nomliste][0]."\" de la liste des cat�gories de \"".$this->type."\" a chang� de nom : 
                             \"".stripslashes($_POST['nomItemF'])."\".<br />" ;
	    $_POST['liste'] = $_POST['nomItemF'] ;
	  } else {
	    // Message d'erreur.
	    $this->erreurs .= "Le nom choisi ne doit pas �tre vide." ;
	  }
	} else {
	  // Message d'erreur.
	  $this->erreurs .= "Le nom choisi pour la liste \"".$res1[nomliste][0]."\" est d�j� utilis�. La modification est annul�e.<br />" ;
	}
      }
      // On v�rifie si l'item a chang� de position ou non.
      if ( $res1[iditem][0] != $_POST['placerF'] ) {
	// Suppression du rang actuel et d�calage du rang des autres items.	
	$rang = $res1[rang][0] ;
	$param[cw] = "WHERE rang>'$rang' and categorie='".$this->type."' AND nomitem='LISTE'" ;
	$res4 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res4[iditem][$i] ) ; $i++ ) {
	  $data3[rang] = $res4[rang][$i] - 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data3 ) ;
	  $requete->updRecord ( "iditem='".$res4[iditem][$i]."'" ) ;
	}
	// Calcul du rang suivant.
	if ( $_POST['placerF'] ) {
	  $param[cw] = "WHERE iditem='".$_POST['placerF']."' and categorie='".$this->type."' AND nomitem='LISTE'" ;
	  $res6 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;	
	  $rang = $res6[rang][0] + 1 ;
	} else $rang = 1 ;
	// D�calage de tous les items d'un rang.
	$param[cw] = "WHERE rang>='$rang' and categorie='".$this->type."' AND nomitem='LISTE'" ;
	$res5 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res5[iditem][$i] ) ; $i++ ) {
	  $data4[rang] = $res5[rang][$i] + 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data4 ) ;
	  $requete->updRecord ( "iditem='".$res5[iditem][$i]."'" ) ;
	}
	// Mise � jour du rang de l'item.
	if ( $_POST['placerF'] ) $data5[rang] = $res6[rang][0] + 1 ;
	else $data5[rang] = 1 ;
	$requete = new clRequete ( BASEXHAM, "listes", $data5 ) ;
	$requete->updRecord ( "iditem='".$res1[iditem][0]."'" ) ;
	// Message d'information.
	$this->infos .= "La cat�gorie de ".$this->type." \"".$res1[nomliste][0]."\" a chang� de position.<br />" ;
      }
    } else {
      // Signalement d'une erreur si l'item � modifier n'existe pas.
      $this->erreurs .= "La liste ne peut pas �tre modifi�e (id=\"$idItem\") car elle n'existe pas." ;
      $errs->addErreur ( "clListesGenerales : La liste ne peut pas �tre modifi�e (id=\"$idIditem\") car elle n'existe pas." ) ;
    }
  }

  function getFormModListe ( ) {
    global $session ;
    global $options ;
    // Chargement du template.
    $mod = new ModeliXe ( "GestionItemsComplexes.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $mod -> MxBloc ( "ajouter", "modify", " " ) ;
    if ( ! $session->getDroit ( "Administration_".$this->type, "m" ) ) {
      $mod -> MxBloc ( "modifier", "modify", " " ) ;
    }
    if ( ! $session->getDroit ( "Administration_".$this->type, "d" ) ) {
      $mod -> MxBloc ( "supprimer", "modify", " " ) ;
    }
    
    // Nom de la liste.
    $mod -> MxText ( "nomListe", "Modifier la cat�gorie \"".stripslashes($_POST['liste'])."\" " ) ;
    // Champs texte pour entrer le nom de l'item.
    $mod -> MxText ( "nomItem", "Nom :" ) ;
    $mod -> MxFormField ( "nomItemF", "text", "nomItemF", stripslashes($_POST['liste']), "size=\"31\" maxlength=\"50\"" ) ;
    // Si le classement est manuel dans cette liste, alors on affiche une liste de positions possibles.
    if ( $options->getOption ( "Cat�gories ".$this->type ) == "Manuel" ) {
      $mod -> MxText ( "placer", "Placer :" ) ;
      $data = $this->getListeListes ( $this->type, 1, 1, $_POST['liste'] ) ;
      $param[cw] = "WHERE categorie='".$this->type."' AND nomitem='LISTE' AND nomliste='".$_POST['liste']."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
      $mod -> MxSelect( "placerF", "placerF", $res[iditem][0], $data , '', '', "size=\"1\"") ; 
    }
    // G�n�ration de la variable de navigation.
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
      $mod = new ModeliXe ( "FormConfirmation.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      // Phrase de confirmation.
      $mod -> MxText ( "question", "Etes vous certain de vouloir supprimer toute cette cat�gorie de ".$this->type." ?" ) ;
      // G�n�ration de la variable de navigation.
      $liste = "&liste=".stripslashes($_POST['liste']) ;
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "gestListes", "validDelListe" ).$liste ) ;
      return $mod -> MxWrite ( "1" ) ;
    } else {
      $req = new clResultQuery ;
      // R�cup�ration des informations actuelles de l'item.
      $param[cw] = "WHERE nomliste='".$_POST['liste']."' AND categorie='".$this->type."'" ;
      $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
      // R�cup�ration de la liste des items.
      $param[cw] = "WHERE categorie='".$this->type."'" ;
      $res2 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
      // On v�rifie qu'on n'est pas en train de supprimer le dernier item.
      if ( $res2[INDIC_SVC][2] > 1 ) {
	// V�rification que l'item existe.
	if ( $res[INDIC_SVC][2] > 0 ) {
	  // D�calage des rangs des autres items.
	  $rang = $res[rang][0] ;
	  $param[cw] = "WHERE rang>'$rang' AND categorie='".$this->type."' AND nomitem='LISTE'" ;
	  $res3 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	  for ( $i = 0 ; isset ( $res3[iditem][$i] ) ; $i++ ) {
	    $data[rang] = $res3[rang][$i] - 1 ;
	    $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	    $requete->updRecord ( "iditem='".$res3[iditem][$i]."'" ) ;
	  }
	  // Message d'information.
	  $this->infos .= "La liste \"".$res[nomliste][0]."\" a �t� supprim�e de la liste des cat�gories de ".$this->type ;
	  // Suppression de l'item.
	  $requete = new clRequete ( BASEXHAM, "listes" ) ;
	  $requete->delRecord ( "nomListe='".$_POST['liste']."' AND nomitem='LISTE' AND categorie='".$this->type."'" ) ;
	  $_POST['liste'] = '' ;
	} else {
	  // Signalement 
	  $this->erreurs .= "La liste ne peut pas �tre supprim�e (id=\"$idItem\") car elle n'existe pas." ;
	  $errs->addErreur ( "clListesGenerales : La liste ne peut pas �tre supprim�e (id=\"$idItem\") car elle n'existe pas." ) ;
	}
      } else {
	$this->erreurs .= "Impossible de supprimer la derni�re liste \"".stripslashes($_POST['liste'])."\"." ;
      }
    }
  }

  function delItem ( ) {
    global $session ;
    global $options ;
    global $errs ;
    $req = new clResultQuery ;
    // R�cup�ration des informations actuelles de l'item.
    $param[cw] = "WHERE nomliste='".$_POST['liste']."' AND categorie='".$this->type."' AND iditem='".$_POST['item']."'" ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // R�cup�ration de la liste des items.
    $param[cw] = "WHERE categorie='".$this->type."' AND nomliste='".$_POST['liste']."'" ;
    $res2 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // V�rification que l'item existe.
    if ( $res[INDIC_SVC][2] > 0 ) {
      // D�calage des rangs des autres items.
      $rang = $res[rang][0] ;
      $param[cw] = "WHERE rang>'$rang' AND categorie='".$this->type."' AND nomitem!='LISTE' AND nomliste='".$_POST['liste']."'" ;
      $res3 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
      //newfct ( gen_affiche_tableau, $res3[INDIC_SVC] ) ;
      for ( $i = 0 ; isset ( $res3[iditem][$i] ) ; $i++ ) {
	$data[rang] = $res3[rang][$i] - 1 ;
	$requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	$requete->updRecord ( "iditem='".$res3[iditem][$i]."'" ) ;
      }
      // Message d'information.
      $this->infos .= "L'item \"".$res[nomitem][0]."\" a �t� supprim� de la liste \"".$res[nomliste][0]."\"" ;
      // Suppression de l'item.
      $requete = new clRequete ( BASEXHAM, "listes" ) ;
      $requete->delRecord ( "nomListe='".$_POST['liste']."' AND iditem='".$_POST['item']."'" ) ;
      $_POST['item'] = '' ;
    } else {
      // Signalement 
      $this->erreurs .= "L'item ne peut pas �tre supprim� (id=\"$idItem\") car il n'existe pas." ;
      $errs->addErreur ( "clListesGenerales : L'item ne peut pas �tre supprim� (id=\"$idItem\") car il n'existe pas." ) ;
    }
  }

  function modItem ( ) {
    global $session ;
    global $options ;
    global $errs ;
    $req = new clResultQuery ;
    // R�cup�ration des anciennes informations de l'item � modifier.
    $param[cw] = "WHERE nomliste='".$_POST['liste']."' AND iditem='".$_POST['item']."'" ;
    $res1 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // R�cup�ration de tous les autres items.
    $param[cw] = "WHERE categorie='".$this->type."' AND nomliste='".$_POST['liste']."'" ;
    $res2 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // V�rification de la pr�sence d'une liste portant le nouveau nom.
    $param[cw] = "WHERE nomitem='".$_POST['nomItemF']."' and iditem!='".$res1[iditem][0]."' AND nomliste='".$_POST['liste']."'" ;
    $res3 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;

    // V�rification que l'item existe.
    if ( $res1[INDIC_SVC] > 0 ) {
      // V�rification d'un changement de nom.
      if ( $res1[nomitem][0] != stripslashes($_POST['nomItemF']) ) {
	// Nouveau nom libre ou pas.
	if ( $res3[INDIC_SVC][2] == 0 ) {
	  // Nouveau nom correct.
	  if ( eregi ( "[0-9a-zA-Z]", $_POST['nomItemF'] ) ) {
	    // Mise � jour du nom de l'item dans la base.
	    $data[nomitem] = $_POST['nomItemF'] ;
	    $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	    $requete->updRecord ( "iditem='".$_POST['item']."'" ) ;
	    // Message d'information.
	    $this->infos .= "L'item \"".$res1[nomitem][0]."\" de la liste \"".stripslashes($_POST['liste'])."\" a chang� de nom : 
                             \"".stripslashes($_POST['nomItemF'])."\".<br />" ;
	  } else {
	    // Message d'erreur.
	    $this->erreurs .= "Le nom choisi ne doit pas �tre vide." ;
	  }
	} else {
	  // Message d'erreur.
	  $this->erreurs .= "Le nom choisi pour l'item \"".$res1[nomitem][0]."\" est d�j� utilis�. La modification est annul�e.<br />" ;
	}
      }
      // On v�rifie si l'item a chang� de position ou non.
      if ( $_POST['item'] != $_POST['placerF'] ) {
	// Suppression du rang actuel et d�calage du rang des autres items.	
	$rang = $res1[rang][0] ;
	$param[cw] = "WHERE rang>'$rang' AND categorie='".$this->type."' AND nomitem!='LISTE' AND nomliste='".$_POST['liste']."'" ;
	$res4 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res4[iditem][$i] ) ; $i++ ) {
	  $data3[rang] = $res4[rang][$i] - 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data3 ) ;
	  $requete->updRecord ( "iditem='".$res4[iditem][$i]."'" ) ;
	}
	// Calcul du rang suivant.
	if ( $_POST['placerF'] ) {
	  $param[cw] = "WHERE iditem='".$_POST['placerF']."' AND categorie='".$this->type."'" ;
	  $res6 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;	
	  $rang = $res6[rang][0] + 1 ;
	} else $rang = 1 ;
	// D�calage de tous les items d'un rang.
	$param[cw] = "WHERE rang>='$rang' AND categorie='".$this->type."' AND nomitem!='LISTE' AND nomliste='".$_POST['liste']."'" ;
	$res5 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res5[iditem][$i] ) ; $i++ ) {
	  $data4[rang] = $res5[rang][$i] + 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data4 ) ;
	  $requete->updRecord ( "iditem='".$res5[iditem][$i]."'" ) ;
	}
	// Mise � jour du rang de l'item.
	if ( $_POST['placerF'] ) $data5[rang] = $res6[rang][0] + 1 ;
	else $data5[rang] = 1 ;
	$requete = new clRequete ( BASEXHAM, "listes", $data5 ) ;
	$requete->updRecord ( "iditem='".$res1[iditem][0]."'" ) ;
	// Message d'information.
	$this->infos .= "L'item \"".$res1[nomitem][0]."\" a chang� de position.<br />" ;
      }
      
      // On v�rifie si le code a chang�.
      if ( $res1[code][0] != $_POST['codeF'] ) {
	$param[cw] = "WHERE code='".$_POST['codeF']."' AND categorie='".$this->type."' AND nomliste='".$_POST['liste']."'" ;
	$res6 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	if ( eregi ( "[0-9a-zA-Z]", $_POST['codeF'] ) && $this->type != "Documents" && $this->type!='Documents Blopera' && $this->type != 'ListesBilans' && $this->type!='ListesBilansCmp') {
	  if ( $res6[INDIC_SVC][2] > 0 && $this->type != "Documents" && $this->type!='Documents Blopera' && $this->type != 'ListesBilans' && $this->type!='ListesBilansCmp' ) {
	    $this->erreurs .= "L'item \"".$res6[nomitem][0]."\" a d�j� ce code attribu�. Merci d'en choisir un autre." ;
	  } else {
	    // Mise � jour du rang de l'item.
	    if ( $_POST['codeF'] ) $data6[code] = $_POST['codeF'] ;
	    else $data6[code] = '_' ;
	    $requete = new clRequete ( BASEXHAM, "listes", $data6 ) ;
	    $requete->updRecord ( "iditem='".$res1[iditem][0]."'" ) ;
	    $this->infos .= "Le code \"".$res1[code][0]."\" de l'item \"".$res1[nomitem][0]."\" a chang� : \"".stripslashes($_POST['codeF'])."\"<br />" ;
	  }
	} else {
	  $this->erreurs = "Le code ne doit pas �tre vide." ;
	}
      }
    } else {
      // Signalement d'une erreur si l'item � modifier n'existe pas.
      $this->erreurs .= "L'item ne peut pas �tre modifi� (id=\"$idItem\") car il n'existe pas." ;
      $errs->addErreur ( "clListesGenerales : L'item ne peut pas �tre modifi� (id=\"$idIditem\") car il n'existe pas." ) ;
    }
  }

  function getAffichage ( ) {
    return $this->af ;
  }  

}
