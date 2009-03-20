<?php

// Titre  : Classe Groupes
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 27 Avril 2005

// Description : 
// Cette classe permet de g�rer les diff�rents groupes utilis�s dans le terminal.


class clGroupes {

  // Attribut contenant l'affichage.
  private $af ;
  private $infos ;
  private $erreurs ;

  // Constructeur.
  function __construct ( ) {
    global $session ;
    $this->af .= $this->genAffichage ( ) ;
  }

  // Affectation de groupes � une application.
  function affecterGroupes ( ) {
    $liste = $_POST['idGroupesNonAffectes'] ;
    for ( $i = 0 ; isset ( $liste[$i] ) ; $i++ ) {
      $param['aw'] = "AND r.idapplication=".IDAPPLICATION." AND r.idGroupe=".$liste[$i] ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getGroupesApplication", $param, "ResultQuery" ) ;
      if ( ! $res['INDIC_SVC'][2] ) {
	$data['idGroupe']      = $liste[$i] ;
	$data['idapplication'] = IDAPPLICATION ;
	$req = new clRequete ( BASEXHAM, TABLERELAG, $data ) ;
	$ris = $req -> addRecord ( ) ;
      }
    }
  }

  // D�saffectation de groupes � une application.
  function enleverGroupes ( ) {
    $liste = $_POST['idGroupesAffectes'] ;
    for ( $i = 0 ; isset ( $liste[$i] ) ; $i++ ) {
      $req = new clRequete ( BASEXHAM, TABLERELAG, (isset($data)?$data:array()) ) ;
      $ris = $req -> delRecord ( "idGroupe=".$liste[$i]." AND idapplication=".IDAPPLICATION ) ;
    }
  }

  // Affichage g�n�ral.
  function genAffichage ( ) {
    global $session ;
    if ( isset ( $_POST['Annuler'] ) OR isset ( $_POST['Annuler_x'] ) ) {
      $_POST['idGroupe'] = '' ;
    } elseif ( $session -> getDroit ( "Configuration_Groupes", "w" ) AND ( isset ( $_POST['Ajouter'] ) OR isset ( $_POST['Ajouter_x'] ) ) ) {
      $text = $this->addGroupe ( ) ;
    } elseif ( $session -> getDroit ( "Configuration_Groupes", "m" ) AND ( isset ( $_POST['Modifier'] ) OR isset ( $_POST['Modifier_x'] ) ) ) {
      $text = $this->modGroupeV ( ) ;
      $_POST['idGroupe'] = '' ;
    } elseif ( $session -> getDroit ( "Configuration_Groupes", "d" ) AND ( isset ( $_POST['Supprimer'] ) OR isset ( $_POST['Supprimer_x'] ) ) ) {
      $text = $this->delGroupe ( ) ;
      $_POST['idGroupe'] = '' ;
    } elseif ( $session -> getDroit ( "Configuration_Groupes", "m" ) AND isset ( $_POST['idGroupe'] ) ) {
      $text = $this->modGroupe ( ) ;
    }
    if ( ( isset ( $_POST['Affecter'] ) OR isset ( $_POST['Affecter_x'] ) ) AND $session -> getDroit ( "Configuration_Groupes", "w" ) ) $this->affecterGroupes ( ) ;
    if ( ( isset ( $_POST['Enlever'] ) OR isset ( $_POST['Enlever_x'] ) ) AND $session -> getDroit ( "Configuration_Groupes", "d" ) ) $this->enleverGroupes ( ) ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "GestionDesGroupes.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Affichage ou non du champs d'informations.
    if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
    else $mod -> MxBloc ( "informations", "modify", " " ) ;
    // Affichage ou non du champs d'erreurs.
    if ( $this->erreurs ) $mod -> MxText ( "erreurs.erreurs", $this->erreurs ) ;
    else $mod -> MxBloc ( "erreurs", "modify", " " ) ;

    $listeGroupesAffectes = $this->getGroupesAffectes ( ) ;
    $listeGroupesNonAffectes = $this->getGroupesNonAffectes ( $listeGroupesAffectes ) ;
    $mod -> MxSelect ( "nonAffectes", "idGroupesNonAffectes[]", '', $listeGroupesNonAffectes, '', '', "size=\"15\" multiple=\"yes\"" ) ; 
    $mod -> MxSelect ( "Affectes", "idGroupesAffectes[]", '', $listeGroupesAffectes, '', '', "size=\"15\" multiple=\"yes\"" ) ; 

    $tab = $this -> listeGroupes ( ) ;
    $mod -> MxText ( "gestiongroupe", (isset($text)?$text:'') ) ;
    $mod -> MxSelect ( "listeGroupes", "idGroupe", (isset($_POST['idGroupe'])?$_POST['idGroupe']:''), $tab, '', '', "size=\"15\" onclick=reload(this.form)" ) ; 
    // G�n�ration de la variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1) ) ) ;
    if ( ! $session -> getDroit ( "Configuration_Groupes", "w" ) ) $mod -> MxBloc ( "nouveau", "modify", " " ) ;
    return $mod -> MxWrite ( "1" ) ;
  }

    // Retourne la liste des groupes affect�s � l'application.
  function getGroupesAffectes ( ) {
    // R�cup�ration de la liste des acteurs dans ce groupe.
    $param['aw'] = "AND r.idapplication=".IDAPPLICATION." ORDER BY g.nomgroupe ASC" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getGroupesApplication", $param, "ResultQuery" ) ;
    for ( $i = 0 ; isset ( $res['idgroupe'][$i] ) ; $i++ ) { 
      $tab[$res['idgroupe'][$i]] = $res['nomgroupe'][$i] ; }
    if ( is_array ( $tab ) ) return $tab ;
    else return array ( ) ;
  }

  // Retourne la liste des acteurs ne faisant pas partie de la liste d'acteur donn�e.
  function getGroupesNonAffectes ( $listeGroupes=array ( ) ) {
    while ( list ( $key, $val ) = each ( $listeGroupes ) ) {
      $liste[$key] = $key ;
    }
    if ( ! is_array ( $liste ) ) $liste = array ( ) ;
    // R�cup�ration de la liste des acteurs dans ce groupe.
    $param['cw'] = "ORDER BY nomGroupe" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getGroupes", $param, "ResultQuery" ) ;
    for ( $i = 0 ; isset ( $res['idgroupe'][$i] ) ; $i++ ) {
      // eko (  $res[nomgroupe][$i] ) ;
      if ( ! in_array ( $res['idgroupe'][$i], $liste ) ) $tab[$res['idgroupe'][$i]] = $res['nomgroupe'][$i] ;
    }
    if ( isset ( $tab ) AND is_array ( $tab ) ) return $tab ;
    else return array ( ) ;
  }
  
  // Fabrication de la liste des groupes.
  function listeGroupes ( ) {
    // R�cup�ration de la liste des groupes.
    $param['cw'] = "ORDER BY nomgroupe ASC" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getGroupes", $param, "ResultQuery" ) ;
    // Formatage de cette liste pour l'ins�rer directement dans ModeliXe.
    for ( $i = 0 ; isset ( $res['idgroupe'][$i] ) ; $i++ ) { $tab[$res['nomgroupe'][$i]] = $res['nomgroupe'][$i] ; }
    // On renvoie la liste.
    return $tab ;
  }

  // G�n�re l'affichage pour la modification d'un groupe.
  function modGroupe ( ) {
    global $session ;
    $mod = new ModeliXe ( "GestionGroupe.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $mod -> MxText ( "nomGroupe", stripslashes($_POST['idGroupe']) ) ;
    if ( ! $session -> getDroit ( "Configuration_Groupes", "m" ) ) $mod -> MxBloc ( "modifier", "modify", " " ) ;
    if ( ! $session -> getDroit ( "Configuration_Groupes", "d" ) ) $mod -> MxBloc ( "supprimer", "modify", " " ) ;
    return $mod -> MxWrite ( "1" ) ;
  }

  // G�n�re l'affichage pour l'ajout d'un groupe.
  function addGroupe ( ) {
    // R�cup�ration de la liste des groupes.
    $param['cw'] = "WHERE nomgroupe=\"".$_POST['newgroupe']."\"" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getGroupes", $param, "ResultQuery" ) ;
    if ( $res['INDIC_SVC'][2] ) {
      $this->erreurs .= "Le groupe \"".stripslashes($_POST['newgroupe'])."\" existe d�j�, la cr�ation est annul�e." ;
    } else {
      $data['nomgroupe'] = $_POST['newgroupe'] ;
      // Pr�paration de la requ�te.
      $requete = new clRequete ( BASEXHAM, "groupes", $data ) ;
      // Ex�cution de la requ�te.
      $res = $requete->addRecord ( ) ;
    }
  }

  // Mise � jour d'un groupe.
  function modGroupeV ( ) {
    if ( stripslashes ( $_POST['idGroupe'] ) == "Invit�" OR stripslashes ( $_POST['idGroupe'] ) == "HOPI" ) {
      $this->erreurs .= "Ce groupe ne peut �tre modifi�, il est n�cessaire au fonctionnement de l'application." ;
    } else {
      // R�cup�ration de la liste des groupes.
      $param['cw'] = "WHERE nomgroupe=\"".$_POST['nomGroupe']."\"" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getGroupes", $param, "ResultQuery" ) ;
      if ( $res['INDIC_SVC'][2] ) {
	$this->erreurs .= "Le groupe \"".stripslashes($_POST['nomGroupe'])."\" existe d�j�. Le changement de nom du groupe \"".stripslashes($_POST['idGroupe'])."\" est annul�." ;
      } else {
	// Pr�paration du tableau de donn�es.
	$data['nomgroupe'] = $_POST['nomGroupe'] ;
	// Appel de la classe Requete.
	$requete = new clRequete ( BASEXHAM, "groupes", $data ) ;
	// Ex�cution de la requete.
	$res = $requete->updRecord ( "nomgroupe=\"".$_POST['idGroupe']."\"" ) ;
	if ( $res['error'] ) $this->erreurs .= "Une erreur a �t� rencontr� lors de la tentative de modification du groupe \"".stripslashes($_POST['idGroupe'])."\".<br/>Le probl�me a �t� signal� � l'administrateur." ;
	else $this->infos .= "Le groupe \"".stripslashes($_POST['idGroupe'])."\" a chang� de nom : \"".stripslashes($_POST['nomGroupe'])."\"" ;
      }
    }
  }

  // Suppression d'un groupe.
  function delGroupe ( ) {
    if ( stripslashes ( $_POST['idGroupe'] ) == "Invit�" OR stripslashes ( $_POST['idGroupe'] ) == "HOPI" ) {
      $this->erreurs .= "Ce groupe ne peut �tre supprim�, il est n�cessaire au fonctionnement de l'application." ;
    } else {
      // Appel de la classe Requete.
      $requete = new clRequete ( BASEXHAM, "groupes" ) ;
      // Ex�cution de la requete.
      $res = $requete->delRecord ( "nomgroupe=\"".$_POST['idGroupe']."\"" ) ;
      // Gestion des messages d'erreurs ou d'informations.
      if ( $res['error'] ) $this->erreurs .= "Une erreur a �t� rencontr� lors de la tentative de suppression du groupe \"".stripslashes($_POST['idGroupe'])."\".<br/>Le probl�me a �t� signal� � l'administrateur." ;
      else $this->infos .= "Le groupe \"".stripslashes($_POST['idGroupe'])."\" a �t� supprim�." ;
    }
  }

  // Retourne l'affichage g�n�r� par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}
