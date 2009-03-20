<?php

// Titre  : Classe Utilisateurs
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 05 Juin 2005

// Description : 
// Gestion des utilisateurs dans la base MySQL interne.


class XhamGestionUtilisateurs {

  // Attribut contenant l'affichage.
  private $af ;
  private $infos ;
  private $erreurs ;

  // Constructeur.
  function __construct ( $xham ) {
    $this->xham = $xham ;
    if ( $xham -> getNavi ( 2 ) == "ChangementPassword" ) {
      $this->af .= $this -> changerPassword ( ) ;
    } else {
    if( ! isset($_POST['Enlever'])) $_POST['Enlever'] = '';
    if( ! isset($_POST['Ajouter_x'])) $_POST['Ajouter_x'] = '';
    if( ! isset($_POST['Modifier_x'])) $_POST['Modifier_x'] = '';
    if( ! isset($_POST['Supprimer_x'])) $_POST['Supprimer_x'] = '';
    if( ! isset($_POST['AjouterUtilisateur_x'])) $_POST['AjouterUtilisateur_x'] = '';
    if( ! isset($_POST['Enlever_x'])) $_POST['Enlever_x'] = '' ;
      if ( ( $_POST['Enlever'] OR $_POST['Enlever_x'] ) AND $xham -> getDroit ( "Configuration_Utilisateurs", "m" ) ) {
		$this -> enleverGroupes ( ) ;
      } elseif ( ( $_POST['Ajouter'] OR $_POST['Ajouter_x'] ) AND $xham -> getDroit ( "Configuration_Utilisateurs", "m" ) ) {
		$this -> ajouterGroupes ( ) ;
      } elseif ( ( $_POST['Modifier'] OR $_POST['Modifier_x'] ) AND $xham -> getDroit ( "Configuration_Utilisateurs", "m" ) ) {
		$this -> modifierUtilisateur ( ) ;
      } elseif ( ( $_POST['Supprimer'] OR $_POST['Supprimer_x'] ) AND $xham -> getDroit ( "Configuration_Utilisateurs", "d" ) ) {
		$this -> supprimerUtilisateur ( ) ;
      }  elseif ( ( $_POST['AjouterUtilisateur'] OR $_POST['AjouterUtilisateur_x'] ) AND $xham -> getDroit ( "Configuration_Utilisateurs", "w" ) ) {
		$this->ajouterUtilisateur ( ) ;
      }
      $this->af .= $this -> genAffichage ( ) ;
    }
  }

  function changerPassword ( ) {
    if ( $_POST['Modifier'] OR $_POST['Modifier_x'] ) {
      $param[cw] = "WHERE uid='".$this->xham->user -> getLogin ( )."' AND password=MD5('".$_POST['pwd']."')" ;
      $res = $this->xham -> Execute ( "Fichier", "getUtilisateurs", $param, "ResultQuery" ) ;
      if ( $res[INDIC_SVC][2] ) {
	if ( $_POST['pwd1'] AND ( $_POST['pwd1'] == $_POST['pwd2'] ) ) {
	  $p .= "password=MD5('".$_POST['pwd1']."') " ; 
	  $param[idutilisateur] = $res[idutilisateur][0] ;
	  $param[set] = $p ;
	  $res = $this->xham -> Execute ( "Fichier", "updateUtilisateur", $param, "ResultQuery" ) ;
	  $this->infos .= "Le nouveau mot de passe a bien été enregistré." ;
	} else {
	  $this->erreurs .= "Les deux mots de passe entrés ne sont pas identiques. La modification est annulée.<br />" ;
	}
      } else {
	$this->erreurs .= "L'ancien mot de passe entré n'est pas valide. La modification est annulée." ;
      }
    }
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "ChangementPassword.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    
    if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
    else $mod -> MxBloc ( "informations", "modify", " " ) ;
    if ( $this->erreurs ) $mod -> MxText ( "erreurs.errs", $this->erreurs ) ;
    else $mod -> MxBloc ( "erreurs", "modify", " " ) ;
    
    $listeUsers = $this -> getUtilisateurs ( "Choisissez un utilisateur", "Création d'un utilisateur", "CREATION" ) ;
    $mod -> MxSelect  ( "iduser", "iduser", $_POST['iduser'], $listeUsers , '', '', "onChange=reload(this.form)") ; 
    
    $mod -> MxText ( "uid", $this->xham->user->getLogin ( ) ) ;
    $mod -> MxFormField ( "pwd", "password", "pwd", "", "size=\"24\" maxlength=\"16\"" ) ;
    $mod -> MxFormField ( "pwd1", "password", "pwd1", "", "size=\"24\" maxlength=\"16\"" ) ;
    $mod -> MxFormField ( "pwd2", "password", "pwd2", "", "size=\"24\" maxlength=\"16\"" ) ;
    // Variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$this->xham->genNavi ( $this->xham->getNavi ( 0 ), $this->xham->getNavi ( 1 ), $this->xham->getNavi ( 2 ) ) ) ;
    // On retourne le code HTML généré.
    return $mod -> MxWrite ( "1" ) ;	
  }
    
  function isUnique ( $uid ) {
    $param[cw] = "WHERE uid='$uid'" ;
    $res = $this->xham -> Execute ( "Fichier", "getUtilisateurs", $param, "ResultQuery" ) ;
    if ( ! $res[INDIC_SVC][2] ) return 1 ;
  }

  function getUniqueUID ( $nom, $prenom ) {
    $uid = strtolower ( substr ( $prenom, 0, 1 ).$nom ) ;
    if ( $this->isUnique ( $uid ) ) return $uid ;
    for ( $i = 2 ; 1 ; $i++ ) {
      if ( $this->isUnique ( $uid.$i ) ) return $uid.$i ;
    }    
  }

  function ajouterUtilisateur ( ) {
    if ( ! $_POST['uid'] ) $this->erreurs .= "L'uid est obligatoire, la création est annulée.<br />" ;
    if ( ! $_POST['nom'] ) $this->erreurs .= "Le nom est obligatoire, la création est annulée.<br />" ;
    if ( ! $_POST['prenom'] ) $this->erreurs .= "Le prénom est obligatoire, la création est annulée.<br />" ;
    if ( ! $_POST['mail'] ) $this->erreurs .= "Le mail est obligatoire, la création est annulée.<br />" ;
    if ( ( ! $_POST['pwd1'] ) OR ( ! $_POST['pwd2'] ) OR ( $_POST['pwd1'] != $_POST['pwd2'] ) ) $this->erreurs .= "Les deux mots de passe entrés ne sont pas identiques. La création est annulée.<br />" ;
	if ( ! $this->isUnique ( $_POST['uid'] ) ) $this->erreurs .= "L'uid choisi est déjà utilisé.<br />" ;
	
    //$uid = $this -> getUniqueUID ( $_POST['nom'], $_POST['prenom'] ) ;

    if ( ! $this->erreurs ) {
      $param[uid] = $_POST['uid'] ;
      $param[nom] = $_POST['nom'] ;
      $param[prenom] = $_POST['prenom'] ;
      $param[mail] = $_POST['mail'] ;
      $param[password] = "MD5('".$_POST['pwd1']."')" ;
      $res = $this->xham -> Execute ( "Fichier", "addUtilisateur", $param, "ResultQuery" ) ;
      $_POST['iduser'] = mysql_insert_id ( ) ;
    }
  }

  function enleverGroupes ( ) {
	$groupes = '' ;
    // On vérifie qu'un tableau a bien été transmis.
    if ( is_array ( $_POST['groupesaffect'] ) ) {
      // Parcours de ce tableau.
      while ( list ( $key, $val ) = each ( $_POST['groupesaffect'] ) ) { 
	// On récupère les informations de ce droit.
	if ( $groupes ) $groupes .= ",".$val ;
	else $groupes = $val ;
      }
    
      $requete = new XhamRequete ( BASEXHAM, TABLERELUG ) ;
      $requete->delRecord ( "idutilisateur=".$_POST['iduser']." AND idgroupe IN ($groupes)" ) ;
    }
  }

  function ajouterGroupes ( ) {
    // On vérifie qu'un tableau a bien été transmis.
    if ( is_array ( $_POST['groupesdispos'] ) ) {
      // Parcours de ce tableau.
      while ( list ( $key, $val ) = each ( $_POST['groupesdispos'] ) ) { 
	$param[cw] = "WHERE idutilisateur=".$_POST['iduser']." AND idgroupe=$val" ;
	$res = $this->xham -> Execute ( "Fichier", "getGroupesUtilisateur", $param, "ResultQuery" ) ;
	if ( ! $res[INDIC_SVC][2] ) {
	  // On récupère les informations de ce droit.
	  $data[idgroupe] = $val ;
	  $data[idutilisateur] = $_POST['iduser'] ;
	  $requete = new XhamRequete ( BASEXHAM, TABLERELUG, $data ) ;
	  $requete->addRecord ( ) ;
	}
      }
    }
  }

  function modifierUtilisateur ( ) {
    if ( ! $_POST['nom'] ) $this->erreurs .= "Le nom est obligatoire, la modification ne sera pas effectuée sur le nom.<br />" ;
    else { $p = "nom='".$_POST['nom']."' " ; }
    if ( ! $_POST['prenom'] ) $this->erreurs .= "Le prénom est obligatoire, la modification ne sera pas effectuée sur le prénom.<br />" ;
    else { if ( $p ) $p .= ", " ; $p .= "prenom='".$_POST['prenom']."' " ; }
    if ( ! $_POST['mail'] ) $this->erreurs .= "Le mail est obligatoire, la modification ne sera pas effectuée sur le mail.<br />" ;
    else { if ( $p ) $p .= ", " ; $p .= "mail='".$_POST['mail']."' " ; }
    if ( ( $_POST['pwd1'] OR $_POST['pwd2'] ) AND ( $_POST['pwd1'] != $_POST['pwd2'] ) ) $this->erreurs .= "Les deux mots de passe entrés ne sont pas identiques. Le mot de passe ne sera pas modifié.<br />" ;    
    elseif ( $_POST['pwd1'] AND $_POST['pwd2'] ) { if ( $p ) $p .= ", " ; $p .= "password=MD5('".$_POST['pwd1']."') " ; }
    $param[idutilisateur] = $_POST['iduser'] ;
    $param[set] = $p ;
    $res = $this->xham -> Execute ( "Fichier", "updateUtilisateur", $param, "ResultQuery" ) ;
  }

  function supprimerUtilisateur ( ) {
    if ( $this->xham -> getNavi ( 2 ) == "ValiderSupprimer" ) {
      $requete = new XhamRequete ( BASEXHAM, TABLEUSERS ) ;
      $requete->delRecord ( "idutilisateur=".$_POST['iduser'] ) ;
      $_POST['iduser'] = "" ;
    } else {
      // Chargement du template ModeliXe.
      $mod = new ModeliXe ( "FormConfirmation.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      $mod -> MxText ( "question", "Cet utilisateur sera définitivement supprimé. Etes vous certain de vouloir continuer ?" ) ;
      // Variable de navigation.
      $mod -> MxHidden ( "hidden", "navi=".$this->xham->genNavi ( $this->xham->getNavi ( 0 ), $this->xham->getNavi ( 1 ), "ValiderSupprimer" )."&iduser=".$_POST['iduser'] ) ;
      // On retourne le code HTML généré.
      $this->confirmation = $mod -> MxWrite ( "1" ) ;
    }
  }

  // Génération de l'affichage.
  function genAffichage ( ) {
    if ( $this->xham -> getDroit ( "Configuration_Utilisateurs", "r" ) ) {
      if ( $_POST['iduser'] == "CREATION" ) {
	// Chargement du template ModeliXe.
	$mod = new ModeliXe ( "CreationUtilisateur.mxt" ) ;
      } else {
	// Chargement du template ModeliXe.
	$mod = new ModeliXe ( "GestionDesUtilisateurs.mxt" ) ;
      }

      $mod -> SetModeliXe ( ) ;

      if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
      else $mod -> MxBloc ( "informations", "modify", " " ) ;
      if ( $this->erreurs ) $mod -> MxText ( "erreurs.errs", $this->erreurs ) ;
      else $mod -> MxBloc ( "erreurs", "modify", " " ) ;
	
      $listeUsers = $this -> getUtilisateurs ( "Choisissez un utilisateur", "Création d'un utilisateur", "CREATION" ) ;
      $mod -> MxSelect  ( "iduser", "iduser", $_POST['iduser'], $listeUsers , '', '', "onChange=reload(this.form)") ; 

      if ( $_POST['iduser'] == "CREATION" ) {

	//$mod -> MxText ( "iduserselect.uid", "Généré automatiquement" ) ;
	$mod -> MxFormField ( "iduserselect.uid", "text", "uid", $_POST['uid'], "size=\"24\" maxlength=\"32\"" ) ;
	$mod -> MxFormField ( "iduserselect.fnom", "text", "nom", $_POST['nom'], "size=\"24\" maxlength=\"32\"" ) ;
	$mod -> MxFormField ( "iduserselect.fprenom", "text", "prenom", $_POST['prenom'], "size=\"24\" maxlength=\"32\"" ) ;
	$mod -> MxFormField ( "iduserselect.fmail", "text", "mail", $_POST['mail'], "size=\"24\" maxlength=\"48\"" ) ;
	$mod -> MxFormField ( "iduserselect.password.pwd1", "password", "pwd1", "", "size=\"24\" maxlength=\"16\"" ) ;
	$mod -> MxFormField ( "iduserselect.password.pwd2", "password", "pwd2", "", "size=\"24\" maxlength=\"16\"" ) ;
	
      } else {
	$mod -> MxText ( "confirmation", $this->confirmation ) ;	
	if ( isset ($_POST['iduser']) &&  $_POST['iduser'] ) {
	  $param[cw] = "WHERE idutilisateur='".$_POST['iduser']."'" ;
	  $res = $this->xham -> Execute ( "Fichier", "getUtilisateurs", $param, "ResultQuery" ) ;
	  
	  if ( $res[INDIC_SVC][2] ) {
	    
	    if ( ! $_POST['uid'] ) $_POST['uid'] = $res[uid][0] ;
	    if ( ! $_POST['nom'] ) $_POST['nom'] = $res[nom][0] ;
	    if ( ! $_POST['prenom'] ) $_POST['prenom'] = $res[prenom][0] ;
	    if ( ! $_POST['mail'] ) $_POST['mail'] = $res[mail][0] ;
	    
	    // Remplissage des champs.
	    $mod -> MxText ( "iduserselect.uid", $res[uid][0] ) ;
	    $mod -> MxFormField ( "iduserselect.fnom", "text", "nom", $res[nom][0], "size=\"24\" maxlength=\"32\"" ) ;
	    $mod -> MxFormField ( "iduserselect.fprenom", "text", "prenom", $res[prenom][0], "size=\"24\" maxlength=\"32\"" ) ;
	    $mod -> MxFormField ( "iduserselect.fmail", "text", "mail", $res[mail][0], "size=\"24\" maxlength=\"48\"" ) ;
	    $mod -> MxFormField ( "iduserselect.password.pwd1", "password", "pwd1", "", "size=\"24\" maxlength=\"16\"" ) ;
	    $mod -> MxFormField ( "iduserselect.password.pwd2", "password", "pwd2", "", "size=\"24\" maxlength=\"16\"" ) ;
	    
	    $groupes = $this -> getGroupesAffectes ( ) ;
	    $mod -> MxSelect( "iduserselect.groupesdispos", "groupesdispos[]", $_POST['groupesdispos'], $this -> getGroupesDisponibles ( $groupes ) , '', '', "size=\"11\" multiple=\"yes\"") ; 
	    $mod -> MxSelect( "iduserselect.groupesaffect", "groupesaffect[]", $_POST['groupesaffect'], $this -> getGroupesAffectes2   ( $groupes ) , '', '', "size=\"11\" multiple=\"yes\"") ; 
	    
	    if ( ! $this->xham -> getDroit ( "Configuration_Utilisateurs", "m" ) ) {
	      $mod -> MxBloc ( "iduserselect.boutonannuler", "modify", " " ) ;
	      $mod -> MxBloc ( "iduserselect.boutonenlever", "modify", " " ) ;
	      $mod -> MxBloc ( "iduserselect.boutonajouter", "modify", " " ) ;
	      $mod -> MxBloc ( "iduserselect.boutonmodifier", "modify", " " ) ;
	    }      
	    if ( ! $this->xham -> getDroit ( "AgirETQ", "r" ) ) {
	      $mod -> MxBloc ( "boutonagirETQ", "delete" ) ;
	    } else {
	      $mod -> MxHidden ( "boutonagirETQ.hidden", "iduser=".$_POST['iduser'] ) ;
	    }
	    if ( ! $this->xham -> getDroit ( "Configuration_Utilisateurs", "d" ) ) {
	      $mod -> MxBloc ( "iduserselect.boutonsupprimer", "modify", " " ) ;
	    }
	  } else {
	    $mod -> MxBloc ( "iduserselect", "modify", " " ) ;
	    $mod -> MxBloc ( "boutonagirETQ", "delete" ) ;
	  }
	} else {
	  $mod -> MxBloc ( "iduserselect", "modify", " " ) ;
	  $mod -> MxBloc ( "boutonagirETQ", "delete" ) ;
	}
      }
      
       // Variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$this->xham->genNavi ( $this->xham->getNavi ( 0 ), $this->xham->getNavi ( 1 ) ) ) ;
    // On retourne le code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
      
    }
   
  }


  function getGroupesDisponibles ( $groupes ) {
    if ( $groupes ) $param[cw] = "WHERE idgroupe NOT IN ($groupes)" ;
    else $param[cw] = "" ;
    $res = $this->xham -> Execute ( "Fichier", "getGroupes", $param, "ResultQuery" ) ;
     for ( $i = 0 ; isset ( $res[idgroupe][$i] ) ; $i++ ) {
       $tab[$res[idgroupe][$i]] = $res[nomgroupe][$i] ;
     }
     if ( is_array ( $tab ) ) return $tab ;
     else return Array ( ) ;
  }

  function getGroupesAffectes ( ) {
    $param['cw'] = " WHERE idutilisateur = ".$_POST['iduser'] ;
    $res = $this->xham -> Execute ( "Fichier", "getGroupesUtilisateur", $param, "ResultQuery" ) ;
    $s = $res[idgroupe][0] ;
    for ( $i = 1 ; isset ( $res[idgroupe][$i] ) ; $i++ ) {
      $s .= ",".$res[idgroupe][$i] ;
    }
    return $s ;
  }

  function getGroupesAffectes2 ( $groupes ) {
    if ( $groupes ) {
      $param[cw] = "WHERE idgroupe IN ($groupes)" ;
      $res = $this->xham -> Execute ( "Fichier", "getGroupes", $param, "ResultQuery" ) ;
      for ( $i = 0 ; isset ( $res[idgroupe][$i] ) ; $i++ ) {
	$tab[$res[idgroupe][$i]] = $res[nomgroupe][$i] ;
      }
    }
    if ( is_array ( $tab ) ) return $tab ;
     else return Array ( ) ;
  }

  function getUtilisateurs ( $phrase='', $creation='', $creationKey='' ) {
    $param[cw] = "ORDER BY uid" ;
    $res = $this->xham -> Execute ( "Fichier", "getUtilisateurs", $param, "ResultQuery" ) ;
    if ( $phrase ) $tab[] = $phrase ;
    if ( $creation AND $creationKey ) $tab[$creationKey] = $creation ;
    for ( $i = 0 ; isset ( $res['idutilisateur'][$i] ) ; $i++ ) {
      $tab[$res['idutilisateur'][$i]] = $res['uid'][$i]." (".$res['prenom'][$i]." ".$res['nom'][$i].")" ;
    }
    if ( is_array ( $tab ) ) return $tab ;
    else return Array ( ) ;
  }

  function getAffichage ( ) {
    return $this->af ;
  }
  
}

?>
