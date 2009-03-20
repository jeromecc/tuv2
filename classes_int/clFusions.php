<?php

// Titre  : Classe Fusions
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 06 Mai 2005

// Description : 
// Gestion de la fusion des patients entr�s manuellement et les patients
// import�s automatiquement (en cas de panne g�n�ralement).

class clFusions {

  // Attributs de la classe.
  // Contient l'affichage g�n�r� par la classe.
  private $af ;
  // Contient les messages d'informations
  private $infos ;
  // Contient les messages d'erreurs.
  private $erreurs ;

  // Constructeur.
  function __construct ( ) {
    global $session ;
    if ( $session -> getDroit ( "Administration_Fusions", "w" ) AND ( $_POST['Fusionner'] OR $_POST['Fusionner_x'] ) ) $this->genFusion ( ) ;
    if ( $session -> getDroit ( "Administration_Fusions", "r" ) ) $this->genListes ( ) ;
  }

  // G�n�re l'affichage des deux listes (patients manuels et automatiques).
  function genListes ( ) {
    global $session ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "FusionPatients.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Affichage ou non du champs d'informations.
    if ( $this->infos ) $mod -> MxText ( "informations.infos", $this->infos ) ;
    else $mod -> MxBloc ( "informations", "modify", " " ) ;
    // Affichage ou non du champs d'erreurs.
    if ( $this->erreurs ) $mod -> MxText ( "erreurs.erreurs", $this->erreurs ) ;
    else $mod -> MxBloc ( "erreurs", "modify", " " ) ;

    // Initialisation des variables.
    if ( isset ( $_POST['valeurNom'] ) ) $_SESSION['valeurNom'] = $_POST['valeurNom'] ;
    if ( isset ( $_POST['valeurPrenom'] ) ) $_SESSION['valeurPrenom'] = $_POST['valeurPrenom'] ;
    if ( isset ( $_POST['valeurAnnee'] ) ) $_SESSION['valeurAnnee'] = $_POST['valeurAnnee'] ;
    // G�n�ration des champs du formulaire.
    $mod -> MxFormField ( "nom", "text", "valeurNom", stripslashes($_SESSION['valeurNom']) ) ;
    $mod -> MxFormField ( "prenom", "text", "valeurPrenom", stripslashes($_SESSION['valeurPrenom']) ) ;
    // Fabrication du filtre pour la requ�te.
    $dateVal = new clDate ( $_POST['filtreDate'] ) ;
    if ( ( $_SESSION['valeurNom'] OR $_SESSION['valeurPrenom'] ) AND $_POST['valeurDate'] ) {
      $this->filtre = "AND nom LIKE '".$_SESSION['valeurNom']."%' ".($_SESSION['valeurPrenom']?" AND prenom LIKE '".$_SESSION['valeurPrenom']."%'":'')." AND dt_admission LIKE '".$dateVal->getDate("Y-m-d")."%'" ;
    } elseif ( $_SESSION['valeurNom'] OR $_SESSION['valeurPrenom'] ) {
      $this->filtre = "AND nom LIKE '".$_SESSION['valeurNom']."%'".($_SESSION['valeurPrenom']?" AND prenom LIKE '".$_SESSION['valeurPrenom']."%'":'') ;
    } elseif ( $_POST['filtreDate'] ) {
      $this->filtre = "AND dt_admission LIKE '".$dateVal->getDate("Y-m-d")."%'" ;
    } elseif ( ! isset ( $_POST['filtreDate'] ) ) {
      $this->filtre = "AND dt_admission LIKE '".date("Y-m-d")."%'" ;
    }
    // G�n�ration des dates possibles.
    //$dt = new clDate ( DATELANCEMENT ) ;
    $dt = new clDate ( ) ;
    $dt -> addWeeks ( -13 ) ;
    $this->filtre .= " AND dt_admission>='".$dt->getDate ( 'Y-m-d' )."%'" ;
    $listeDates[] = "Pas de filtre" ;
    // Calcul de la liste des valeurs possibles pour la date.
    $dta = new clDate ( date ( "Y-m-d 00:00:00" ) ) ;
    if ( ! isset ( $_POST['filtreDate'] ) ) $_POST['filtreDate'] = $dta -> getTimestamp ( ) ;
    for ( ; $dt->getTimestamp ( ) <= $dta -> getTimestamp ( ) ; $dta -> addDays ( -1 ) )
      $listeDates[$dta->getTimestamp()] = $dta->getDate ( "d-m-Y" ) ;
    // Si on n'a aucune date, on initialise la variable avec un tableau vide (pour ModeliXe).
    if ( ! is_array ( $listeDates ) ) $listeDates = Array ( ) ;
    // Passage du tableau de dates � ModeliXe.
    $mod -> MxSelect( "date", "filtreDate", $_POST['filtreDate'], $listeDates, '', '', "onChange=\"reload(this.form)\"" ) ; 
    // R�cup�ration et ajout dans ModeliXe de la liste des patients entr�s manuellement.
    $manuels = $this -> getPatientsManuels ( ) ;
    $mod -> MxSelect ( "manuels", "manuel", (isset($_POST['manuel'])?$_POST['manuel']:''), $manuels, '', '', "size=\"25\"" ) ; 
    // R�cup�ration et ajout dans ModeliXe de la liste des patients entr�s automatiquement.
    $automatiques = $this -> getPatientsAutomatiques ( ) ;
    $mod -> MxSelect ( "automatiques", "automatique", (isset($_POST['automatique'])?$_POST['automatique']:''), $automatiques, '', '', "size=\"25\"" ) ; 
    // G�n�ration de la variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1) ) ) ;
    // Affichage du bouton "Fusionner" en fonction des droits.
    if ( ! $session -> getDroit ( "Administration_Fusions", "w" ) ) $mod -> MxBloc ( "fusionner", "modify", " " ) ;
    // On enl�ve le bouton "Supprimer" pour le moment.
    $mod -> MxBloc ( "supprimer", "modify", " " ) ;
    // R�cup�ration du code g�n�r� par ModeliXe.
    $this->af .= $mod -> MxWrite ( "1" ) ;
  }

  // Cette fonction lance le processus de fusion des patients.
  function genFusion ( ) {
    global $errs ;
    global $options ;
    
    global $fusion;
    global $table_patient_manuel;
    global $table_patient_automatique;
    
    // On v�rifie qu'un patient manuel et un patient automatique ont bien �t� s�lectionn�.
    if ( ! $_POST['manuel'] OR ! $_POST['automatique'] ) 
      // Affichage d'un message d'erreur si ce n'est pas bon.
      $this->erreurs .= "Deux patients doivent �tre s�lectionn�s pour lancer le processus de fusion des patients." ;
    else {
      // On r�cup�re l'idpatient et la table actuelle du patient automatique s�lectionn�.
      $auto = explode ( "|", $_POST['automatique'] ) ;
      // En fonction de son �tat (Presents ou Sortis), on en d�duit sa table.
      if ( $auto[1] == "Presents" ) $param['table'] = PPRESENTS ;
      else $param['table'] = PSORTIS ;
      $param['cw'] = "WHERE idpatient='".$auto[0]."'" ;
      // Lancement de la requ�te pour r�cup�rer toutes ses informations.
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      $table_patient_automatique = $param['table']; 
      
      // On r�cup�re l'idpatient et la table actuelle du patient manuel s�lectionn�.
      $manu = explode ( "|", $_POST['manuel'] ) ;
      // En fonction de son �tat (Presents ou Sortis), on en d�duit sa table.
      if ( $manu[1] == "Presents" ) $param2['table'] = PPRESENTS ;
      else $param2['table'] = PSORTIS ;
      $param2['cw'] = "WHERE idpatient='".$manu[0]."'" ;
      // Lancement de la requ�te pour r�cup�rer toutes ses informations.
      $req2 = new clResultQuery ;
      $ras = $req2 -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
      $table_patient_manuel = $param2['table'];
      
      
      // On v�rifie que le patient automatique existe.
      if ( $res['INDIC_SVC'][2] < 1 ) {
	$this->erreurs .= "Le patient automatique (idpatient=\"".$_POST['automatique']."\") est introuvable dans la table des patients ".$auto[1].". Probl�me signal�." ;
	$errs -> addErreur ( "clFusion : Le patient automatique (idpatient=\"".$_POST['automatique']."\") est introuvable dans la table des patients ".$auto[1]."." ) ;
      // On v�rifie que le patient manuel existe.
      } elseif ( $ras['INDIC_SVC'][2] < 1 ) {
	$this->erreurs .= "Le patient manuel (idpatient=\"".$manu[0]."\") est introuvable dans la table des patients ".$manu[1].". Probl�me signal�." ;
	$errs -> addErreur ( "clFusion : Le patient manuel (idpatient=\"".$manu[0]."\") est introuvable dans la table des patients ".$manu[1]."." ) ;
      } else {
	$data['idu']                  = $res['idu'][0] ;	
	$data['ilp']                  = $res['ilp'][0] ;	
	$data['nsej']                 = $res['nsej'][0] ;	
	$data['uf']                   = $res['uf'][0] ;	
	$data['nom']                  = $res['nom'][0] ;	
	$data['prenom']               = $res['prenom'][0] ;	
	$data['sexe']                 = $res['sexe'][0] ;	
	$data['dt_naissance']         = $res['dt_naissance'][0] ;	
	$data['adresse_libre']        = $res['adresse_libre'][0] ;	
	$data['adresse_cp']           = $res['adresse_cp'][0] ;	
	$data['adresse_ville']        = $res['adresse_ville'][0] ;	
	$data['telephone']            = $res['telephone'][0] ;	
	$data['prevenir']             = $res['prevenir'][0] ;	
	$data['medecin_traitant']     = $res['medecin_nom'][0] ;	
	$data['dt_admission']         = $res['dt_admission'][0] ;
	if ( $res['mode_admission'][0] ) 
		$data['mode_admission'] = $res['mode_admission'][0] ;	
	$data['iduser']               = "FUSION" ;
	$data['manuel']               = 0 ;
	// Appel de la classe Requete.
	$requete = new clRequete ( BDD, $param2['table'], $data ) ;
	// Ex�cution de la requete.
	$requete->updRecord ( "idpatient='".$manu[0]."'" ) ;
	
	// Appel de la classe Requete.
	$requete = new clRequete ( BDD, $param['table'] ) ;
	// Ex�cution de la requete.
	$requete->delRecord ( "idpatient='".$auto[0]."'" ) ;
	$this->infos .= "Fusion du patient (".$res['sexe'][0].") ".ucfirst(strtolower($res['prenom'][0]))." ".strtoupper($res['nom'][0])." effectu�e.<br />" ;
	
	if ( $options -> getOption ( "Module_CCAM" ) ) {
	
    $fusion = 1;
    
    $ccam = new clCCAMCotationActesDiags ( Array ( ) ) ;
    
	  $ccam -> writeBALall ( Array ( $auto[0] , $manu[0] ) ) ;
	}
      }
    }
  }

  // Retourne la liste des patients manuels au format attendu par ModeliXe.
  function getPatientsManuels ( ) {
    // On cherche dans la table des pr�sents tous les patients entr�s manuellement.
    $param['table'] = PPRESENTS ;
    $param['cw'] = "WHERE manuel='1' ORDER BY nom" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    // On parcourt les patients trouv�s.
    for ( $i = 0 ; isset ( $res['idpatient'][$i] ) ; $i++ ) {
      // Ajout du patient au tableau.
      $identifiant=$res['idpatient'][$i]."|Presents\" ".clPatient::genInfoBulle ( $res, $i ) ;
      if ( $res['sexe'][$i] != "M" ) $e = "e" ; else $e = '' ;
      $tab[$identifiant] = "(".$res['sexe'][$i].") ".ucfirst(strtolower($res['prenom'][$i]))." ".strtoupper($res['nom'][$i])." (Pr�sent$e)" ;
    }

    // On cherche dans la table des sortis tous les patients entr�s manuellement.
    $param['table'] = PSORTIS ;
    $param['cw'] = "WHERE manuel='1' ORDER BY nom" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    // On parcourt les patients trouv�s.
    for ( $i = 0 ; isset ( $res['idpatient'][$i] ) ; $i++ ) {
      // Ajout du patient au tableau.
      $identifiant=$res['idpatient'][$i]."|Sortis\" ".clPatient::genInfoBulle ( $res, $i ) ;
      if ( $res['sexe'][$i] != "M" ) $e = "e" ; else $e = '' ;
      $tab[$identifiant] = "(".$res['sexe'][$i].") ".ucfirst(strtolower($res['prenom'][$i]))." ".strtoupper($res['nom'][$i])." (Sorti$e)" ;
    }

    // On fait en sorte de retourner un tableau.
    if ( is_array ( $tab ) )
      return $tab ;
    else return array ( ) ;
  }

  // Retourne la liste des patients automatiques au format attendu par ModeliXe.
  function getPatientsAutomatiques ( ) {
    // On cherche dans la table des pr�sents tous les patients entr�s automatiquement.
    $param['table'] = PPRESENTS ;
    $param['cw'] = "WHERE manuel='0' ".$this->filtre." ORDER BY nom" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    //newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ;
    // On parcourt les patients trouv�s.
    for ( $i = 0 ; isset ( $res['idpatient'][$i] ) ; $i++ ) {
      // Ajout du patient au tableau.
      $identifiant=$res['idpatient'][$i]."|Presents\" ".clPatient::genInfoBulle ( $res, $i ) ;
      if ( $res['sexe'][$i] != "M" ) $e = "e" ; else $e = '' ;
      $tab[$identifiant] = "(".$res['sexe'][$i].") ".ucfirst(strtolower($res['prenom'][$i]))." ".strtoupper($res['nom'][$i])." (Pr�sent$e)" ;
    }

    // On cherche dans la table des sortis tous les patients entr�s automatiquement.
    $param['table'] = PSORTIS ;
    $param['cw'] = "WHERE manuel='0' ".$this->filtre." ORDER BY nom" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    //newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ;
    // On parcourt les patients trouv�s.
    for ( $i = 0 ; isset ( $res['idpatient'][$i] ) ; $i++ ) {
      // Ajout du patient au tableau.
      $identifiant=$res['idpatient'][$i]."|Sortis\" ".clPatient::genInfoBulle ( $res, $i ) ;
      if ( $res['sexe'][$i] != "M" ) $e = "e" ; else $e = '' ;
      $tab[$identifiant] = "(".$res['sexe'][$i].") ".ucfirst(strtolower($res['prenom'][$i]))." ".strtoupper($res['nom'][$i])." (Sorti$e)" ;
    }

    // On fait en sorte de retourner un tableau.
    if ( is_array ( $tab ) )
      return $tab ;
    else return array ( ) ;
  }


  // Renvoie l'affichage g�n�r� par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}

?>
