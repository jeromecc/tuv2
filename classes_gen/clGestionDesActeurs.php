<?php

// Titre  : Classe GestionDesActeurs
// Auteur : Damien BOREL <dborel@ch-hyeres.fr>
// Date   : 25 Octobre 2005

// Description : 
// Cette classe gère les acteurs et leur affectation à des groupes.

if ( ! function_exists ( "post" ) ) {
  function post ( $name ) {
    if ( isset ( $_POST[$name] ) ) return $_POST[$name] ;
    else return false ;
  }
}

class clGestionDesActeurs {

  public $infos ;
  public $erreurs ;
  private $af ;

  // Constructeur de la classe.
  function __construct ( ) {
    $this->genAffichage ( ) ;
  }

  // Ajout d'acteurs à un groupe.
  function ajouterActeurs ( ) {
    $liste = $_POST['idActeursNonGroupes'] ;
    for ( $i = 0 ; isset ( $liste[$i] ) ; $i++ ) {
      $param['nomBase'] = cfg_db ;
      $param['aw'] = "AND r.idgroupe=".$_POST['idGroupe']." AND r.idacteur=".$liste[$i]." ORDER BY a.nomActeur ASC" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getActeursGroupes", $param, "ResultQuery" ) ;
      if ( ! $res['INDIC_SVC'][2] ) {
	$data['idgroupe']      = $_POST['idGroupe'] ;
	$data['idacteur']      = $liste[$i] ;
	$data['idutilisateur'] = "0" ;
	$req = new clRequete ( BASEXHAM, TABLERELUG, $data ) ;
	$ris = $req -> addRecord ( ) ;
      }
    }
  }

  // Suppression d'acteurs à un groupe.
  function enleverActeurs ( ) {
    $liste = $_POST['idActeursGroupes'] ;
    for ( $i = 0 ; isset ( $liste[$i] ) ; $i++ ) {
      $req = new clRequete ( BASEXHAM, TABLERELUG, $data ) ;
      $ris = $req -> delRecord ( "idacteur=".$liste[$i]." AND idgroupe=".$_POST['idGroupe'] ) ;
    }
  }

  // Génération de l'affichage.
  function genAffichage ( ) {
    global $session ;
    if ( ( post('Ajouter') OR post('Ajouter_x') ) AND $session -> getDroit ( "Administration_Acteurs", "w" ) ) $this->ajouterActeurs ( ) ;
    if ( ( post('Enlever') OR post('Enlever_x') ) AND $session -> getDroit ( "Administration_Acteurs", "d" ) ) $this->enleverActeurs ( ) ;
    if ( ( post('ModifierActeur') OR post('ModifierActeur_x') ) AND $session -> getDroit ( "Administration_Acteurs", "m" ) ) $this->modifierActeur ( ) ;
    if ( ( post('AjouterActeur') OR post('AjouterActeur_x') ) AND $session -> getDroit ( "Administration_Acteurs", "w" ) ) $this->ajouterActeur ( ) ;
    if ( ( post('SupprimerActeur') OR post('SupprimerActeur_x') OR post('Supprimer') OR post('Supprimer_x') ) AND $session -> getDroit ( "Administration_Acteurs", "d" ) ) $this->supprimerActeur ( ) ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "GestionDesActeurs.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $formulaireActeur = $this->genFormulaireActeur ( ) ;
    if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
    else $mod -> MxBloc ( "informations", "modify", " " ) ;
    if ( $this->erreurs ) $mod -> MxText ( "erreurs.errs", $this->erreurs ) ;
    else $mod -> MxBloc ( "erreurs", "modify", " " ) ;
    $mod -> MxText ( "formulaireActeur", $formulaireActeur ) ;
    $listeActeurs = $this -> getActeurs ( ) ;
    $listeGroupes = $this -> getGroupes ( ) ;
    $mod -> MxSelect ( "listeActeurs", "idActeur", post('idActeur'), $listeActeurs, '', '', "size=\"15\" onChange=reload(this.form)" ) ;
    if ( ! post('idGroupe')) $_POST['idGroupe'] = '1' ;
    $mod -> MxSelect ( "listeGroupes", "idGroupe", $_POST['idGroupe'], $listeGroupes, '', '', "size=\"1\" onChange=reload(this.form)" ) ;
    $listeActeursGroupes = $this -> getActeursGroupes ( $_POST['idGroupe'] ) ;
    $listeActeursNonGroupes = $this -> getActeursNonGroupes ( $listeActeursGroupes ) ;
    $mod -> MxSelect ( "nonGroupes", "idActeursNonGroupes[]", '', $listeActeursNonGroupes, '', '', "size=\"15\" multiple=\"yes\"" ) ;
    $mod -> MxSelect ( "Groupes", "idActeursGroupes[]", '', $listeActeursGroupes, '', '', "size=\"15\" multiple=\"yes\"" ) ;
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi ( 0 ), $session->getNavi ( 1 ) ) ) ;
    // On récupère le code HTML généré.
    $this->af .= $mod -> MxWrite ( "1" ) ;
  }

  // Modification d'un acteur.
  function modifierActeur ( ) {
    $param['ob'] = '' ;
    $param['cw'] = "WHERE idActeur=".$_POST['idActeur'] ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "authCodeAcces", $param, "ResultQuery" ) ;
    //print affTab ( $res['INDIC_SVC'] ) ;
    if ( ! $res['INDIC_SVC'][2] ) {
      $this->erreur .= "L'acteur demandé est introuvable. Aucune modification n'a été effectuée." ;
      $errs -> addErreur ( "L'acteur demandé est introuvable. Aucune modification n'a été effectuée.".affTab ( $res['INDIC_SVC'] ) ) ;
    }
    $param['ob'] = '' ;
    $param['cw'] = "WHERE password='".addslashes(stripslashes($_POST['password']))."' AND idActeur!=".$_POST['idActeur'] ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "authCodeAcces", $param, "ResultQuery" ) ;
    if ( $res['INDIC_SVC'][2] ) $this->erreurs .= "Ce mot de passe est déjà utilisé. La modification est annulée.<br/>" ;
    if ( ! $_POST['nomActeur'] ) $this->erreurs .= "Le nom ne doit pas être vide. La modification est annulée.<br/>" ;
    if ( ! $this->erreurs ) {
      $data['nomActeur']  = stripslashes ( $_POST['nomActeur'] ) ;
      $data['mailActeur'] = stripslashes ( $_POST['mailActeur'] ) ;
      $data['password']   = stripslashes ( $_POST['password'] ) ;
      $req = new clRequete ( BASEXHAM, TABLEACTEURS, $data ) ;
      $ris = $req -> updRecord ( "idActeur=".$_POST['idActeur'] ) ;
      $this->infos .= "La modification a réussi." ;
      unset ( $_POST['idActeur'] ) ;
    }
  }

  // Création d'un nouveau acteur.
  function ajouterActeur ( ) {
    $param['cw'] = "WHERE password='".addslashes(stripslashes($_POST['password']))."'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "authCodeAcces", $param, "ResultQuery" ) ;
    if ( ! $_POST['nomActeur'] ) $this->erreurs .= "Le nom ne doit pas être vide. La création est annulée.<br/>" ;
    if ( $res['INDIC_SVC'][2] ) $this->erreurs .= "Ce mot de passe est déjà utilisé. La création est annulée.<br/>" ;
    if ( ! $this->erreurs ) {
      $data['nomActeur']  = stripslashes ( $_POST['nomActeur'] ) ;
      $data['mailActeur'] = stripslashes ( $_POST['mailActeur'] ) ;
      $data['password']   = stripslashes ( $_POST['password'] ) ;
      $req = new clRequete ( BASEXHAM, TABLEACTEURS, $data ) ;
      $ris = $req -> addRecord ( ) ;
      $this->infos .= "La création a réussi." ;
      unset ( $_POST['idActeur'] ) ;
    }
  }

  // Suppression d'un acteur.
  function supprimerActeur ( ) {
    global $session ;
    $param['ob'] = '' ;
    $param['cw'] = "WHERE idActeur=".$_POST['idActeur'] ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "authCodeAcces", $param, "ResultQuery" ) ;
    if ( post('Supprimer') OR post('Supprimer_x') ) {
      // Appel de la classe Requete.
      $requete = new clRequete ( BASEXHAM, TABLEACTEURS ) ;
      // Exécution de la requete.
      $rs = $requete->delRecord ( "idActeur=".$_POST['idActeur'] ) ;
      if ( ! $rs[1] )  {
	$this->infos .= "L'acteur '".$res['nomActeur'][0]."' a bien été supprimé.<br/>" ;
	unset ( $_POST['idActeur'] ) ;
      } else $this->erreurs .= "Une erreur inconnue s'est produite lors de la suppression de l'acteur.<br/>" ;
    } else {
      $mod = new ModeliXe ( "FormConfirmation.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      $mod -> MxText ( "question", "Confirmez-vous la suppression de l'acteur '".$res['nomActeur'][0]."' ?" ) ;
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1) )."&idActeur=".$_POST['idActeur'] ) ;
      $this->af .= $mod -> MxWrite ( "1" ) ;
    }
  }

  // Formulaire acteur
  function genFormulaireActeur ( ) {
    global $session ;
    if ( post('AnnulerActeur_x') ) unset ( $_POST['idActeur'] ) ;
    if ( isset ( $_POST['idActeur'] ) ) {
      $mod = new ModeliXe ( "FormulaireActeur.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      $param['ob'] = '' ;
      $param['cw'] = "WHERE idActeur=".$_POST['idActeur'] ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "authCodeAcces", $param, "ResultQuery" ) ;
      if ( post('nomActeur') ) $nom = $_POST['nomActeur'] ; else $nom  = $res['nomActeur'][0] ;
      if ( post('mailActeur') ) $mail = $_POST['mailActeur'] ; else $mail = $res['mailActeur'][0] ;
      if ( post('password') ) $pwd = $_POST['password'] ; else $pwd  = $res['password'][0] ;
      if ( post('idActeur') )	{
	$mod -> MxText ( "titre", "Modification de l'acteur \"".$res['nomActeur'][0]."\" :" ) ;
	$mod -> MxBloc ( "ajouter", "delete" ) ;
      } else {
	$mod -> MxText ( "titre", "Création d'un nouveau acteur :" ) ;
	$mod -> MxBloc ( "modifier", "delete" ) ;
	$mod -> MxBloc ( "supprimer", "delete" ) ;
      }
      $mod -> MxFormField ( "nomActeur", "text", "nomActeur", stripslashes($nom), "size=\"64\" maxlength=\"64\"" ) ;
      $mod -> MxFormField ( "mailActeur", "text", "mailActeur", stripslashes($mail), "size=\"64\" maxlength=\"64\"" ) ;
      $mod -> MxFormField ( "pwdActeur", "password", "password", stripslashes($pwd), "size=\"64\" maxlength=\"64\"" ) ;
      $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi ( 0 ), $session->getNavi ( 1 ) )."&idActeur=".$_POST['idActeur'] ) ;
      $mod -> MxHidden ( "agirETQ.hiddenIdActeur", "idActeur=".$_POST['idActeur'] ) ;
      if ( ! $session -> getDroit ( "Administration_Acteurs", "w" ) ) $mod -> MxBloc ( "ajouter", "delete" ) ;
      if ( ! $session -> getDroit ( "Administration_Acteurs", "m" ) ) $mod -> MxBloc ( "modifier", "delete" ) ;
      if ( ! $session -> getDroit ( "Administration_Acteurs", "d" ) ) $mod -> MxBloc ( "supprimer", "delete" ) ;
      if ( ! $session -> getDroit ( "AgirETQ", "r" ) ) $mod -> MxBloc ( "agirETQ", "delete" ) ;
      // On récupère le code HTML généré.
      return $mod -> MxWrite ( "1" ) ;
    }
  }

  // Affichage des acteurs.
  function getActeurs ( ) {
    // Récupération de la liste des acteurs dans ce groupe.
    $tab[] = "Nouveau acteur" ;
    $param['cw'] = "WHERE 1=1 " ;
    $param['ob'] = "ORDER BY nomActeur" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "authCodeAcces", $param, "ResultQuery" ) ;
    //print affTab ( $res ) ;
    //ob_flush ( ) ; flush ( ) ;
    for ( $i = 0 ; isset ( $res['idActeur'][$i] ) ; $i++ ) { $tab[$res['idActeur'][$i]] = $res['nomActeur'][$i] ; }
    if ( is_array ( $tab ) ) return $tab ;
    else return array ( ) ;
  }

  // Retourne la liste des acteurs d'un groupe donné.
  function getActeursGroupes ( $idGroupe='0' ) {
    // Récupération de la liste des acteurs dans ce groupe.
    $param['nomBase'] = cfg_db ;
    $param['aw'] = "AND r.idgroupe=$idGroupe ORDER BY a.nomActeur ASC" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getActeursGroupes", $param, "ResultQuery" ) ;
   // eko ( $res['INDIC_SVC'] ) ;
    for ( $i = 0 ; isset ( $res['idActeur'][$i] ) ; $i++ ) { $tab[$res['idActeur'][$i]] = $res['nomActeur'][$i] ; }
    if ( isset ( $tab ) AND is_array ( $tab ) ) return $tab ;
    else return array ( ) ;
  }

  // Retourne la liste des acteurs ne faisant pas partie de la liste d'acteur donnée.
  function getActeursNonGroupes ( $listeActeurs=array ( ) ) {
    while ( list ( $key, $val ) = each ( $listeActeurs ) ) {
      $liste[$key] = $key ;
    }
    if ( ! isset ( $liste ) OR ! is_array ( $liste ) ) $liste = array ( ) ;
    // Récupération de la liste des acteurs dans ce groupe.
    $param['cw'] = "WHERE 1=1 " ;
    $param['ob'] = "ORDER BY nomActeur" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "authCodeAcces", $param, "ResultQuery" ) ;
    for ( $i = 0 ; isset ( $res['idActeur'][$i] ) ; $i++ ) {
      if ( ! in_array ( $res['idActeur'][$i], $liste ) ) $tab[$res['idActeur'][$i]] = $res['nomActeur'][$i] ;
    }
    if ( is_array ( $tab ) ) return $tab ;
    else return array ( ) ;
  }

  // Renvoie la liste des groupes dans un tableau (idgroupe/nomgroupe).
  function getGroupes ( ) {
    // Récupération de la liste des groupes.
    $param['nomBase'] = cfg_db ;
    $param['aw'] = " AND idapplication=".IDAPPLICATION." ORDER BY nomgroupe ASC" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getGroupesApplication", $param, "ResultQuery" ) ;
    // Formatage de cette liste pour l'insérer directement dans ModeliXe.
    for ( $i = 0 ; isset ( $res['idgroupe'][$i] ) ; $i++ ) { 
      // Récupération de la liste des groupes.
      $param['idgroupe'] = $res['idgroupe'][$i] ;
      $req = new clResultQuery ;
      $ras = $req -> Execute ( "Fichier", "getNombreActeurs", $param, "ResultQuery" ) ;
      $tab[$res['idgroupe'][$i]] = $res['nomgroupe'][$i]." (".(isset($ras['nb'][0])?$ras['nb'][0]:0).")" ; 
    }
    if ( is_array ( $tab ) )
      // On renvoie la liste.
      return $tab ;
    else return array ( ) ;
  }

  // Retourne l'affichage généré par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }

}
?>