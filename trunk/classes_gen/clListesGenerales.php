<?php

// Titre  : Classe ListesGenerales
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 10 Février 2005

// Description : 
// Cette classe gère les listes générales.
// Elle permet d'afficher une liste seule de différentes façons.

class clListesGenerales {

  // Attribut contenant l'affichage généré par la classe.
  private $af ;
  private $infos ;
  private $erreurs ;
  private $type ;

  // Constructeur de la classe.
  function __construct ( $type='' ) {
    if ( $type ) {
      
    } else {
      
      $this->type = "ListesGenerales" ;
      $this->getListesGenerales ( ) ;
    }
  }

  // Gestion des listes générales.
  function getListesGenerales ( ) {
    global $session ;
    global $options ;
    // Récupération et calcul du ratio pour le nombre de listes
    // affichées par ligne.
    $num = $options->getOption ( "ListesParLigne" ) ;
    $nli = $options->getOption ( "LignesParListe" ) ;
    $options->checkOptionListe ( "ListesParLigne" ) ;
    $options->checkOptionListe ( "LignesParListe" ) ;
    //$options->checkOptionListe ( "LignesParListe" ) ;
    if ( $num ) $ratio = sprintf ( "%d", 90 / $num ) ;
    else $ratio = 1 ;
    // Vérification du droit de lecture.
    if ( $session->getDroit ( "Administration_ListesGenerales", "r" ) ) {
      // Réparation d'une liste d'items.
      if ( $session->getNavi ( 2 ) == "repListeItems" and $session->getDroit ( "Administration_ListesGenerales", "a" ) ) {
	$this->repListe ( $session->getNavi ( 3 ) ) ;
      }
      // Ajout d'un nouvel item à une liste.
      if ( ( isset($_POST['Valider']) or isset($_POST['Valider_x']) ) and $session->getNavi ( 2 ) == "ValiderAjouter" and $session->getDroit ( "Administration_ListesGenerales", "w" ) ) {
	$this->addItem ( $session->getNavi ( 3 ) ) ;
      }
      // Suppression d'un item.
      if ( ( isset($_POST['Supprimer']) or isset($_POST['Supprimer_x']) ) and $session->getNavi ( 2 ) == "ValiderModifier" and $session->getDroit ( "Administration_ListesGenerales", "d" ) ) {
	$this->delItem ( $session->getNavi ( 3 ), $session->getNavi ( 4 ) ) ;
      }
      
      if($session->getDroit ( "Administration_ListesGenerales", "a" ) && isset($_POST['ajouter_liste']) && $_POST['ajouter_liste'] ) { $_POST['nomItemF'] = 'Ici un item de la liste  '.(isset($_POST['ajouter_liste'])?$_POST['ajouter_liste']:'') ;  $this->addItem((isset($_POST['ajouter_liste'])?$_POST['ajouter_liste']:'')) ;}
      
      // Mise à jour d'un item.
      if ( ( isset($_POST['Modifier']) or isset($_POST['Modifier_x']) ) and $session->getNavi ( 2 ) == "ValiderModifier" and $session->getDroit ( "Administration_ListesGenerales", "m" ) ) {
	    $this->modItem ( $session->getNavi ( 3 ), $session->getNavi ( 4 ) ) ;
      }
      // Récupération de toutes les listes.
      $param['cw'] = "WHERE categorie=\"ListesGenerales\" AND idapplication=".IDAPPLICATION." ORDER BY nomliste" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getListes", $param, "ResultQuery" ) ;  
      // Initialisation du template ModeliXe.
      $mod = new ModeliXe ( "GestionDesListesGenerales.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      // Affichage ou non du champs d'informations.
      if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
      else $mod -> MxBloc ( "informations", "modify", " " ) ;
      // Affichage ou non du champs d'erreurs.
      if ( $this->erreurs ) $mod -> MxText ( "erreurs.errs", $this->erreurs ) ;
      else $mod -> MxBloc ( "erreurs", "modify", " " ) ;
      // Parcours des différentes listes.
      
      //debut du template : bouton ajout de liste
      if ( $session->getDroit ( "Administration_ListesGenerales", "a" ) )
      	$mod -> MxHidden ( "ajouterListe.hidden2", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "ajouter_liste" ) ) ;
      else $mod -> MxBloc ( "ajouterListe", "delete" ) ;
      
      for ( $i = 0 ; isset ( $res['nomliste'][$i] ) ; $i++ ) {

	// Affichage ou non d'un tr en fonction de la liste parcourue.
	if ( $i and ( ! ( $i % $num ) ) ) $mod -> MxText ( "liste.tr", "</tr><tr>" ) ;
	else $mod -> MxText ( "liste.tr", "" ) ;
	// Affichage du td à la bonne dimension.
	$mod -> MxText ( "liste.td", "<td width=\"$ratio%\">" ) ;
	// Affichage du nom de la liste.
	$mod -> MxText ( "liste.nomListe", $res['nomliste'][$i] ) ;
	// Création de l'ancre de la liste.
	$mod -> MxText ( "liste.formDeb", '<form method="post" action="index.php?#'.$res['nomliste'][$i].'">' ) ;
	$mod -> MxText ( "liste.ancreListe", '<a name="'.$res['nomliste'][$i].'" />' ) ;
	
	// Si le droit d'écriture est présent, alors on affiche le bouton d'ajout.
	if ( $session->getDroit ( "Administration_ListesGenerales", "w" ) ) {
	  $mod -> MxImage ( "liste.imgAjouter", URLIMGAJO, "Ajouter" ) ;
	  $mod -> MxUrl  ( "liste.lienAjouter", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "Ajouter", $res['nomliste'][$i] ).'#'.$res['nomliste'][$i] ) ;
	}
	// Si le droit d'administration est présent, alors on affiche le bouton de réparation.
	if ( $session->getDroit ( "Administration_ListesGenerales", "a" ) ) {
	  $mod -> MxImage ( "liste.imgReparer", URLIMGREP, "Reparer" ) ;
	  $mod -> MxUrl  ( "liste.lienReparer", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "repListeItems", $res['nomliste'][$i] ) ) ;
	}
	// Génération de la variable de navigation.
	$mod -> MxHidden ( "liste.hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "Modifier", $res['nomliste'][$i] ) ) ;
	// Préparation de la liste des items de la liste parcourue.
	$data = $this->getListeItems ( $res['nomliste'][$i], 1 ) ;
	$mod -> MxSelect( "liste.select", "item", (isset($_POST['item'])?$_POST['item']:''), $data , '', '', "size=\"$nli\" onChange=\"reload(this.form)\"") ; 
	// Si c'est nécessaire, on affiche le formulaire d'ajout d'un nouvel item.
	if ( $session->getNavi ( 2 ) == "Ajouter" and stripslashes ( $session->getNavi ( 3 ) ) == $res['nomliste'][$i]
	     and $session->getDroit ( "Administration_ListesGenerales", "w" ) ) {
	  $mod -> MxText ( "formAjouter", $this->getFormAjouter ( $res['nomliste'][$i] ) ) ;
	  // Si c'est nécessaire, on affiche le formulaire de modification d'un item.
	} elseif ( $session->getNavi ( 2 ) == "Modifier" and stripslashes ( $session->getNavi ( 3 ) ) == $res['nomliste'][$i]  
		   and $session->getDroit ( "Administration_ListesGenerales", "m" ) ) {
	  $mod -> MxText ( "formAjouter", $this->getFormModifier ( $res['nomliste'][$i] ) ) ;
	  // Sinon, on n'affiche pas la partie formulaire.
	} else { $mod -> MxText ( "liste.form", "" ) ; }
	// Boucle sur le bloc liste.
	$mod -> MxBloc ( "liste", "loop" ) ;
      }
      // Récupération de l'affichage généré par le template.
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
      $param['cw'] = "WHERE nomliste='".addslashes(stripslashes($nomListe))."' AND nomitem!='LISTE' AND categorie=\"".$this->type."\" ORDER BY rang" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ; 
      // Si au moins un item est présent, alors on commence la reconstruction.
      if ( $res['INDIC_SVC'][2] ) {
	for ( $i = 0 ; isset ( $res['iditem'][$i] ) ; $i++ ) {
	  $data['rang'] = $i + 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	  $requete->updRecord ( "iditem='".$res['iditem'][$i]."'" ) ;
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
      $param['cw'] = "WHERE nomitem='LISTE' AND categorie=\"".$this->type."\" ORDER BY rang" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;  
      // Si au moins une liste est présente, on commence la reconstruction.
      if ( $res['INDIC_SVC'][2] ) {
	for ( $i = 0 ; isset ( $res['iditem'][$i] ) ; $i++ ) {
	  $data['rang'] = $i + 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	  $requete->updRecord ( "iditem='".$res['iditem'][$i]."'" ) ;
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

  // Modification d'un item d'une liste.
  function modItem ( $nomListe, $idItem ) {
    global $errs ;
    global $options ;
    $req = new clResultQuery ;
    // Récupération des anciennes informations de l'item à modifier.
    $param['cw'] = "WHERE iditem='".$idItem."'" ;
    $res1 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // Récupération de tous les autres items.
    $param['cw'] = "WHERE nomliste='".addslashes($nomListe)."'" ;
    $res2 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // Vérification de la présence d'un item portant le nouveau nom.
    $param['cw'] = "WHERE nomliste='".addslashes($nomListe)."' and nomitem='".addslashes(stripslashes($_POST['nomItemF']))."' and iditem!='$idItem'" ;
    $res3 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // Vérification que l'item existe.
    if ( $res1['INDIC_SVC'] > 0 ) {
      // Vérification d'un changement de nom.
      if ( $res1['nomitem'][0] != $_POST['nomItemF'] ) {
	// Nouveau nom libre ou pas.
	if ( $res3['INDIC_SVC'][2] == 0 ) {
	  // Nouveau nom correct.
	  if ( eregi ( "[0-9a-zA-Z]", $_POST['nomItemF'] ) ) {
	    // Mise à jour du nom de l'item dans la base.
	    $data['nomitem'] = $_POST['nomItemF'] ;
	    $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	    eko ( $requete->updRecord ( "iditem='".$idItem."'" ) ) ;
	    // Message d'information.
	    $this->infos .= "L'item \"".$res1['nomitem'][0]."\" de la liste \"".stripslashes($nomListe)."\" a changé de nom : \"".stripslashes($_POST['nomItemF'])."\".<br />" ;
	  } else {
	    // Message d'erreur.
	    $this->erreurs .= "Le nom choisi ne doit pas être vide." ;
	  }
	} else {
	  // Message d'erreur.
	  $this->erreurs .= "Le nom choisi pour l'item \"".$res1['nomitem'][0]."\" de la liste \"".stripslashes($nomListe)."\" est déjà utilisé. 
                             La modification est annulée.<br />" ;
	}
      }
      // On vérifie si le type de la destination attendue a changé.
      if ( $res1['localisation'][0] != (isset($_POST['typeF'])?$_POST['typeF']:'') ) {
	$data2['localisation'] = (isset($_POST['typeF'])?$_POST['typeF']:'') ;
	$requete = new clRequete ( BASEXHAM, "listes", $data2 ) ;
	$requete->updRecord ( "iditem='".$res1['iditem'][0]."'" ) ;
	// Message d'information.
	$this->infos .= "L'item \"".$res1['nomitem'][0]."\" de la liste \"".stripslashes($nomListe)."\" a changé de type.<br />" ;
      }
      	if ( $nomListe == 'Destinations attendues' ) {
      		$data2['libre'] = $_POST['destpmsi'].$_POST['ori'] ;
      		$requete = new clRequete ( BASEXHAM, "listes", $data2 ) ;
      		$requete->updRecord ( "iditem='".$res1['iditem'][0]."'" ) ;
      	} else {
	      // On vérifie si le champs libre a été changé.
	      if ( strcmp ( $res1['libre'][0] , (isset($_POST['libreF'])?$_POST['libreF']:'') ) ) {
			$data2['libre'] = $_POST['libreF'] ;
			$requete = new clRequete ( BASEXHAM, "listes", $data2 ) ;
			if ( $options -> getOption ( $nomListe." Id" ) OR $nomListe == "Médecins" OR $nomListe == "I.D.E." ) {
			  if ( eregi ( "[0-9a-zA-Z]", $_POST['libreF'] ) ) {
			    $requete->updRecord ( "iditem='".$res1['iditem'][0]."'" ) ;
			    // Message d'information.
			    $this->infos .= "L'item \"".$res1['nomitem'][0]."\" de la liste \"".stripslashes($nomListe)."\" a changé d'identifiant.<br />" ;
			  } else {
			    $this->erreurs .= "L'identifiant ne peut pas être vide. Ce champs ne sera pas modifié.<br />" ;
			  }
			}
		      }
	  }
      // On vérifie si l'item a changé de position ou non.
      if ( $res1['iditem'][0] != $_POST['placerF'] ) {
	// Suppression du rang actuel et décalage du rang des autres items.	
	$rang = $res1['rang'][0] ;
	$param['cw'] = "WHERE rang>'$rang' and nomliste='".addslashes(stripslashes($nomListe))."'" ;
	$res4 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res4['iditem'][$i] ) ; $i++ ) {
	  $data3['rang'] = $res4['rang'][$i] - 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data3 ) ;
	  $requete->updRecord ( "iditem='".$res4['iditem'][$i]."'" ) ;
	}
	// Calcul du rang suivant.
	if ( $_POST['placerF'] ) {
	  $param['cw'] = "WHERE iditem='".$_POST['placerF']."' and nomliste='".addslashes(stripslashes($nomListe))."'" ;
	  $res6 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;	
	  $rang = $res6['rang'][0] + 1 ;
	} else $rang = 1 ;
	// Décalage de tous les items d'un rang.
	$param['cw'] = "WHERE rang>='$rang' and nomliste='".addslashes(stripslashes($nomListe))."'" ;
	$res5 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res5['iditem'][$i] ) ; $i++ ) {
	  $data4['rang'] = $res5['rang'][$i] + 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data4 ) ;
	  $requete->updRecord ( "iditem='".$res5['iditem'][$i]."'" ) ;
	}
	// Mise à jour du rang de l'item.
	if ( $_POST['placerF'] ) $data5['rang'] = $res6['rang'][0] + 1 ;
	else $data5['rang'] = 1 ;
	$requete = new clRequete ( BASEXHAM, "listes", $data5 ) ;
	$requete->updRecord ( "iditem='".$res1['iditem'][0]."'" ) ;
	// Message d'information.
	$this->infos .= "L'item \"".$res1['nomitem'][0]."\" de la liste \"".stripslashes($nomListe)."\" a changé de position.<br />" ;
      }
    } else {
      // Signalement d'une erreur si l'item à modifier n'existe pas.
      $this->erreurs .= "L'item ne peut pas être modifié (id=\"$idItem\") car il n'existe pas." ;
      $errs->addErreur ( "clListesGenerales : L'item ne peut pas être modifié (id=\"$idIditem\") car il n'existe pas." ) ;
    }
  }


  // Suppression d'un item d'une liste.
  function delItem ( $nomListe, $idItem ) {
    global $errs ;
    $req = new clResultQuery ;
    // Récupération des informations actuelles de l'item.
    $param['cw'] = "WHERE iditem='".$idItem."'" ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // Récupération de la liste des items.
    $param['cw'] = "WHERE nomliste='".addslashes(stripslashes($nomListe))."'" ;
    $res2 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // On vérifie qu'on n'est pas en train de supprimer le dernier item.
    if ( $res2['INDIC_SVC'][2] > 1 ) {
      // Vérification que l'item existe.
      if ( $res['INDIC_SVC'][2] > 0 ) {
	// Décalage des rangs des autres items.
	$rang = $res['rang'][0] ;
	$param['cw'] = "WHERE rang>'$rang' and nomliste='".addslashes(stripslashes($nomListe))."'" ;
	$res3 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	for ( $i = 0 ; isset ( $res3['iditem'][$i] ) ; $i++ ) {
	  $data['rang'] = $res3['rang'][$i] - 1 ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	  $requete->updRecord ( "iditem='".$res3['iditem'][$i]."'" ) ;
	}
	// Message d'information.
	$this->infos .= "L'item \"".$res['nomitem'][0]."\" a été supprimé de la liste \"".stripslashes($nomListe)."\"." ;
	// Suppression de l'item.
	$requete = new clRequete ( BASEXHAM, "listes" ) ;
	$requete->delRecord ( "iditem='".$idItem."'" ) ;
      } else {
	// Signalement 
	$this->erreurs .= "L'item ne peut pas être supprimé (id=\"$idItem\") car il n'existe pas." ;
	$errs->addErreur ( "clListesGenerales : L'item ne peut pas être supprimé (id=\"$idIditem\") car il n'existe pas." ) ;
      }
    } else {
      $this->erreurs .= "Impossible de supprimer le dernier item de la liste \"$nomListe\"." ;
    }
  }

  // Modification d'un item d'une liste.
  function getFormModifier ( $nomListe ) {
    global $options ;
    global $session ;
    global $errs ;
    // Récupération des informations de l'item.
    $param['cw'] = "WHERE iditem='".$_POST['item']."'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // Si l'item existe, on affiche le formulaire.
    if ( $res['INDIC_SVC'][2] > 0 ) {
      // Chargement du template ModeliXe.
      $mod = new ModeliXe ( "ModifierListeItem.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      $mod -> MxText ( "formDeb", '<form method="post" action="index.php?#'.$res['nomliste'][0].'">' ) ;
      // Affichage du bouton "Supprimer" si l'utilisateur a les droits.
      if ( ! $session->getDroit ( "Administration_ListesGenerales", "d" ) ) { $mod -> MxBloc ( "supprimer", "modify", " " ) ; }
      // Nom de la liste.
      $mod -> MxText ( "nomListe", $res['nomliste'][0] ) ;
      // Nom actuel de l'item.
      $mod -> MxText ( "oldNomItem", $res['nomitem'][0] ) ;
      // Champs texte de modification du nom de l'item.
      $mod -> MxText ( "nomItem", "Valeur :" ) ;
      $mod -> MxFormField ( "nomItemF", "text", "nomItemF", $res['nomitem'][0], "size=\"31\" maxlength=\"50\"" ) ;
      // Affichage de la liste pour déplacer l'item si on est dans une
      // à classement manuel.
      if ( $options->getOption ( $nomListe ) == "Manuel" ) {
	$mod -> MxText ( "placer", "Placer :" ) ;
	$data = $this->getListeItems ( $nomListe, 1, 1, $res['nomitem'][0] ) ;
	$mod -> MxSelect( "placerF", "placerF", $res['iditem'][0], $data , '', '', "size=\"1\"") ; 
      } else {
	$placerF = "&placerF=".$res['iditem'][0] ;
      }
      // Cas de la liste des destinations attendues.
      if ( $nomListe == "Destinations attendues" OR $nomListe == "Destinations confirmées chir" ) {
		$mod -> MxText ( "formType.type", "Type :" ) ;
		$data2['T'] = "Transfert" ;
		$data2['H'] = "Hospitalisation" ;
		$data2['D'] = "Décès" ;
		$data2['E'] = "Externe" ;
		$data2['6'] = "Hospitalisation à domicile" ;
		$data2['7'] = "Structure d'hébergement médicosociale" ;
		$data2['F'] = "Fugue" ;
		$data2['S'] = "Sortie contre avis médical" ;
		$data2['P'] = "Partie sans attendre prise en charge" ;
		$data2['R'] = "Réorientation directe sans soins" ;
		$data2['X'] = "Erreur (sans ATU)" ;
		$mod -> MxSelect( "formType.typeF", "typeF", $res['localisation'][0], $data2 , '', '', "size=\"1\"") ;
		if ( $nomListe == "Destinations attendues" ) {
			$destpmsi = $res['libre'][0][0] ;
			$tabD['#'] = "--" ;
			$tabD['1'] = "MCO" ;
			$tabD['2'] = "Soins de suite ou réadaptation" ;
			$tabD['3'] = "Soins de longue durée" ;
			$tabD['4'] = "Psychiatrie" ;
			$mod -> MxSelect( "speDestConf.destPMSI", "destpmsi", $destpmsi, $tabD, '', '', "size=\"1\"") ;
			$ori = substr ( $res['libre'][0], 1, strlen ( $res['libre'][0] ) - 1 ) ;
			$tabO['#']	  = '--' ;
			$tabO['HDT']  = "Hosp. sur la demande d'un tiers" ;
			$tabO['HO']   = "Hospitalisation d'office" ;
			$tabO['SC']   = "Surveillance continue" ;
			$tabO['SI']   = "Soins intensifs" ;
			$tabO['REA']  = "Réanimation" ;
			$tabO['UHCD'] = "UHCD" ;
			$tabO['MED']  = "Médecine hors SC, SI, REA" ;
			$tabO['CHIR'] = "Chirurgie hors SC, SI, REA" ;
			$tabO['OBST'] = "Obstétrique hors SC, SI, REA" ;
			$mod -> MxSelect( "speDestConf.Orientation", "ori", $ori, $tabO, '', '', "size=\"1\"") ;
		} else {
			$mod -> MxBloc ( 'speDestConf', 'delete' ) ;
		}
	 } elseif ( $nomListe == "Médecins" ) {
	 	$mod -> MxText ( "formType.type", "Type :" ) ;
		$data2['U'] = "Urgentiste" ;
		$data2['P'] = "Pédiatre" ;
		$data2['F'] = "Sage-femme" ;
		$data2['G'] = "Gynécologie-Obstétrique";
		
		$mod -> MxSelect( "formType.typeF", "typeF", $res['localisation'][0], $data2 , '', '', "size=\"1\"") ;
		$mod -> MxBloc ( 'speDestConf', 'delete' ) ;
	 } else {
		$mod -> MxBloc ( "formType", "modify", " " ) ;
		$mod -> MxBloc ( 'speDestConf', 'delete' ) ;
      }
      if ( $nomListe == "Médecins" ) {
      	$mod -> MxText ( "formLibre.libre", "ADELI :" ) ;
		$mod -> MxFormField ( "formLibre.libreF", "text", "libreF", $res['libre'][0] , "size=\"31\" maxlength=\"50\"" ) ;
      } elseif ( $nomListe == "I.D.E." ) {
      	$mod -> MxText ( "formLibre.libre", "ADELI :" ) ;
		$mod -> MxFormField ( "formLibre.libreF", "text", "libreF", $res['libre'][0] , "size=\"31\" maxlength=\"50\"" ) ;
      } elseif ( $options -> getOption ( $nomListe." Id" ) ) {
	$mod -> MxText ( "formLibre.libre", "Identifiant :" ) ;
	$mod -> MxFormField ( "formLibre.libreF", "text", "libreF", $res['libre'][0] , "size=\"31\" maxlength=\"50\"" ) ; 
      } else {
	$mod -> MxBloc ( "formLibre", "modify", " " ) ;
      }
      // Génération de la variable de navigation.
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "ValiderModifier", $nomListe, $res['iditem'][0] ).(isset($placerF)?$placerF:'') ) ;
        return $mod -> MxWrite ( "1" ) ;
    } else {
      // Envoi d'une erreur si l'item à modifier n'existe pas.
      $errs->addErreur ( "clListesGenerales : L'item (id=".$_POST['item'].") n'existe pas." ) ;
    }
  }

  // Ajout d'un item à une liste.
  function addItem ( $nomListe ) {
    global $options ;
    // On vérifie qu'un item ne porte pas déjà ce nom.
    $param['cw'] = "WHERE nomitem='".addslashes(stripslashes($_POST['nomItemF']))."' AND nomliste='".addslashes(stripslashes($nomListe))."'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // On signale l'erreur si le nom est déjà pris.
    if ( $res['INDIC_SVC'][2] > 0 ) {
      $this->erreurs .= "Dans la liste \"$nomListe\", un item portant ce nom (\"".$_POST['nomItemF']."\") existe déjà. La création est annulée." ;
    } else {
      // Nouveau nom correct.
      if ( eregi ( "[0-9a-zA-Z]", $_POST['nomItemF'] ) ) {
	if ( $options -> getOption ( $nomListe." Id" ) AND ! eregi ( "[0-9a-zA-Z]", $_POST['libreF'] ) ) {
	  $this->erreurs .= "L'identifiant est un champ obligatoire." ;
	} else {
	  // On positionne correctement le nouvel item et on déplace les autres.
	  if ( $options->getOption ( stripslashes($nomListe) ) != "Manuel" ) { $rang = 1 ; } else { 
	    $param['cw'] = "WHERE iditem='".$_POST['placerF']."' and nomliste='".addslashes(stripslashes($nomListe))."'" ;
	    $res2 = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	    $rang = (isset($res2['rang'][0])?$res2['rang'][0]:0) + 1 ; 
	  }
	  $param['cw'] = "WHERE rang>='$rang' AND nomliste='".addslashes($nomListe)."'" ;
	  $req = new clResultQuery ;
	  $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
	  for ( $i = 0 ; isset ( $res['iditem'][$i] ) ; $i++ ) {
	    $data2['rang'] = $res['rang'][$i] + 1 ;
	    $requete = new clRequete ( BASEXHAM, "listes", $data2 ) ;
	    $requete->updRecord ( "iditem='".$res['iditem'][$i]."'" ) ;
	  }
	  // Insertion du nouveau item.
	  $data['categorie']    = "ListesGenerales" ;
	  $data['nomliste']     = $nomListe ;
	  $data['nomitem']      = $_POST['nomItemF'] ;
	  $data['rang']         = $rang ;
	  $data['valide']       = 1 ;
	  $data['localisation'] = (isset($_POST['typeF'])?$_POST['typeF']:'') ;
	  if ( $nomListe == 'Destinations attendues' )  $data['libre'] = $_POST['destpmsi'].$_POST['ori'] ;
	  else $data['libre'] = (isset($_POST['libreF'])?$_POST['libreF']:'') ;
	  $data['idapplication'] = IDAPPLICATION ;
	  $requete = new clRequete ( BASEXHAM, "listes", $data ) ;
	  $requete->addRecord ( ) ;
	  // Message d'information.
	  $this->infos .= "L'item \"".$_POST['nomItemF']."\" a été ajouté dans la liste \"".stripslashes($nomListe)."\"." ;
	} 
      } else {
	$this->erreurs .= "Le nom choisi ne doit pas être vide." ;
      }
    }
  }


  // Retourne le code HTML du formulaire d'ajout d'item.
  function getFormAjouter ( $nomListe ) {
    global $options ;
    global $session ;
    // Chargement du template.
    $mod = new ModeliXe ( "AjouterListeItem.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $mod -> MxText ( "formDeb", '<form method="post" action="index.php?#'.$nomListe.'">' ) ;
    // Nom de la liste.
    $mod -> MxText ( "nomListe", $nomListe ) ;
    // Champs texte pour entrer le nom de l'item.
    $mod -> MxText ( "nomItem", "Valeur :" ) ;
    $mod -> MxFormField ( "nomItemF", "text", "nomItemF", (isset($_POST['valeur'])?$_POST['valeur']:''), "size=\"31\" maxlength=\"50\"" ) ;
    // Si le classement est manuel dans cette liste, alors on affiche une liste de positions possibles.
    if ( $options->getOption ( $nomListe ) == "Manuel" ) {
      $mod -> MxText ( "placer", "Placer :" ) ;
      $data = $this->getListeItems ( $nomListe, 1, 1 ) ;
      $mod -> MxSelect( "placerF", "placerF", '', $data , '', '', "size=\"1\"") ; 
    }
    if ( $nomListe == "Destinations attendues"  OR $nomListe == "Destinations confirmées chir" ) {
      	$mod -> MxText ( "formType.type", "Type :" ) ;
		$data2['T'] = "Transfert" ;
		$data2['H'] = "Hospitalisation" ;
		$data2['D'] = "Décès" ;
		$data2['E'] = "Externe" ;
		$data2['6'] = "Hospitalisation à domicile" ;
		$data2['7'] = "Structure d'hébergement médicosociale" ;
		$data2['F'] = "Fugue" ;
		$data2['S'] = "Sortie contre avis médical" ;
		$data2['P'] = "Partie sans attendre prise en charge" ;
		$data2['R'] = "Réorientation directe sans soins" ;
		$data2['X'] = "Erreur (sans ATU)" ;
		
      	$mod -> MxSelect( "formType.typeF", "typeF", 'H', $data2 , '', '', "size=\"1\"") ; 
      	if ( $nomListe == "Destinations attendues" ) {
			$destpmsi = '' ;
			$tabD['#'] = "--" ;
			$tabD['1'] = "MCO" ;
			$tabD['2'] = "Soins de suite ou réadaptation" ;
			$tabD['3'] = "Soins de longue durée" ;
			$tabD['4'] = "Psychiatrie" ;
			$mod -> MxSelect( "speDestConf.destPMSI", "destpmsi", $destpmsi, $tabD, '', '', "size=\"1\"") ;
			$ori = '' ;
			$tabO['#']	  = '--' ;
			$tabO['HDT']  = "Hosp. sur la demande d'un tiers" ;
			$tabO['HO']   = "Hospitalisation d'office" ;
			$tabO['SC']   = "Surveillance continue" ;
			$tabO['SI']   = "Soins intensifs" ;
			$tabO['REA']  = "Réanimation" ;
			$tabO['UHCD'] = "UHCD" ;
			$tabO['MED']  = "Médecine hors SC, SI, REA" ;
			$tabO['CHIR'] = "Chirurgie hors SC, SI, REA" ;
			$tabO['OBST'] = "Obstétrique hors SC, SI, REA" ;
			$mod -> MxSelect( "speDestConf.Orientation", "ori", $ori, $tabO, '', '', "size=\"1\"") ;
		} else {
			$mod -> MxBloc ( 'speDestConf', 'delete' ) ;
		}
    } elseif ( $nomListe == "Médecins" ) {
	 		$mod -> MxText ( "formType.type", "Type :" ) ;
			$data2['U'] = "Urgentiste" ;
			$data2['P'] = "Pédiatre" ;
			$data2['F'] = "Sage-femme" ;
			$data2['G'] = "Gynécologie-Obstétrique";
		
			$mod -> MxSelect( "formType.typeF", "typeF", '', $data2 , '', '', "size=\"1\"") ;
			$mod -> MxBloc ( 'speDestConf', 'delete' ) ;
	 	} else {
      $mod -> MxBloc ( "formType", "modify", " " ) ;
      $mod -> MxBloc ( 'speDestConf', 'delete' ) ;
    }
    if ( $nomListe == "Médecins" ) {
      	$mod -> MxText ( "formLibre.libre", "ADELI :" ) ;
		$mod -> MxFormField ( "formLibre.libreF", "text", "libreF", $_POST['libreF'] , "size=\"31\" maxlength=\"50\"" ) ;
    } elseif ( $nomListe == "I.D.E." ) {
      	$mod -> MxText ( "formLibre.libre", "ADELI :" ) ;
		$mod -> MxFormField ( "formLibre.libreF", "text", "libreF", $_POST['libreF'] , "size=\"31\" maxlength=\"50\"" ) ;
    } elseif ( $options -> getOption ( $nomListe." Id" ) ) {
      $mod -> MxText ( "formLibre.libre", "Identifiant :" ) ;
      $mod -> MxFormField ( "formLibre.libreF", "text", "libreF", $_POST['libreF'] , "size=\"31\" maxlength=\"50\"" ) ; 
    } else {
      $mod -> MxBloc ( "formLibre", "modify", " " ) ;
    }
    // Génération de la variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), "ValiderAjouter", $nomListe ) ) ;
    return $mod -> MxWrite ( "1" ) ;
  }

  // Retourne la liste des items.
  // $nomListe : Nom de la liste à récupérer.
  // $opt : Met une case vide en début de tableau si vrai.
  // $code : Rempli le tableau avec le code de l'item au lieu de son nom si vrai.
  function getListeItemsV2 ( $nomListe, $opt='', $code='', $libre='' ) {
    global $options ;
    $options -> checkOptionListe ( $nomListe ) ;
    // Préparation du type de classement pour la requête.
    switch ( $options->getOption ( $nomListe ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphabétique': $order = "ORDER BY nomitem" ; break ;
    case 'Alphabétique inversé': $order = "ORDER BY nomitem DESC" ; break ;
    default : $order = "ORDER BY nomitem" ; break ;
    }
    $param['cw'] = "WHERE nomliste='".addslashes($nomListe)."' $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    //eko ( $res ) ;
    if ( $opt ) $tab[] = SELECTLISTE ;
    // Fabrication du tableau.
    for ( $i = 0 ; isset ( $res['iditem'][$i] ) ; $i++ )
      if ( $code ) $tab[$res['codeitem'][$i]] = $res['nomitem'][$i] ;
      elseif ( $libre ) $tab[$res['libre'][$i]] = $res['nomitem'][$i] ;
      else $tab[$res['nomitem'][$i]] = $res['nomitem'][$i] ;
    // Retourne le tableau au format attendu par modelixe.
    return $tab ;
  }


  // Retourne la liste des items.
  function getListeItems ( $nomListe, $modelixe='', $placement='', $nomItem='', $opt='' ) {
    global $options ;
    $options -> checkOptionListe ( $nomListe ) ;
    // Préparation du type de classement pour la requête.
    switch ( $options->getOption ( $nomListe ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphabétique': $order = "ORDER BY nomitem" ; break ;
    case 'Alphabétique inversé': $order = "ORDER BY nomitem DESC" ; break ;
    default : $order = "ORDER BY nomitem" ; break ;
    }
    $param['cw'] = "WHERE nomliste='".addslashes($nomListe)."' $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // Affichage en cas de débugage.
    if ( DEBUGLISTES ) { eko ( $res['INDIC_SVC'] ) ; }
    // Préparation du tableau à retourner pour un select de modelixe.
    if ( $modelixe ) {
      if ( $opt ){ $tab[] = SELECTLISTE ;
      }
      // Placement ou affichage simple.
      if ( $placement ) { 
	$placer = "Après " ; 
	$tab[0] = "En début de liste" ;
	$type = "iditem" ;
	$val = 0 ;
      } else $type = "iditem" ;
      // Fabrication du tableau.
      for ( $i = 0 ; isset ( $res['iditem'][$i] ) ; $i++ )
	if ( $opt ) {
	  $tab[$res['nomitem'][$i]] = $res['nomitem'][$i] ;
	} else {
	  if ( $nomItem == $res['nomitem'][$i] ) {
	    $rang = $res[$type][$i] ;
	    $tab[$rang] = "Position actuelle" ;
	  } else { $tab[($res[$type][$i]+(isset($val)?$val:''))] = (isset($placer)?$placer:'').$res['nomitem'][$i] ; }
	}
      // Retourne le tableau au format attendu par modelixe.
      return $tab ;
    } else {
      // Retourne le tableau au format normal de ResultQuery.
      return $res ;
    }
  }

  function getListeModelixe ( $nomListe, $cle='nomitem', $opt='1' ) {
    global $options ;
    $options -> checkOptionListe ( $nomListe ) ;
    // Préparation du type de classement pour la requête.
    switch ( $options->getOption ( $nomListe ) ) {
    case 'Manuel': $order = "ORDER BY rang" ; break ;
    case 'Alphabétique': $order = "ORDER BY nomitem" ; break ;
    case 'Alphabétique inversé': $order = "ORDER BY nomitem DESC" ; break ;
    default : $order = "ORDER BY nomitem" ; break ;
    }
    $param['cw'] = "WHERE nomliste='".addslashes($nomListe)."' $order" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    // Affichage en cas de débugage.
    if ( DEBUGLISTES ) { newfct ( 'gen_affiche_tableau', $res['INDIC_SVC'] ) ; }
    if ( $opt ) $tab[] = SELECTLISTE ;
    for ( $i = 0 ; isset ( $res['iditem'][$i] ) ; $i++ )
      $tab[$res[$cle][$i]] = $res['nomitem'][$i] ;
    return (is_array($tab)?$tab:array()) ;
  }

  function addListeWithItem($nomListe,$nomItem='') {
  	$_POST['nomItemF'] = "Item de la liste ".addslashes(stripslashes($nomListe)) ;
	//$this->addItem($_POST[ajouter_liste]) ;
	$this->addItem($nomListe) ;
	}

  // Retourne l'affichage généré.
  function getAffichage ( ) {
    return $this->af ;
  }
}
