<?php

// Titre  : Classe Authentification
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 20 Janvier 2005

// Description : 
// Toute l'authentification est effectuée dans cette classe.
// Les informations concernant l'utilisateur connecté sont récupérées ici.


class clAuthentification {

  // Declaration des attributs de la classe.
  // Contient les informations de l'utilisateur connecté.
  private $informations ;

  // Constructeur de la classe.
  function __construct ( $init='' ) {
    if ( $init ) { $this->navi = $init ; $this->setInformations ( ) ; }
  }

  // Génération de la petite fenêtre de connexion.
  function getFormulaire ( ) {
    global $session ;
    global $options ;

    $type = $options -> getOption ( "TypeAuth" ) ;

    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "Authentification.mxt" ) ;
    $mod -> SetModeliXe ( ) ;

    switch ( $type ) {
    case 'CodeAcces':
      if ( $session -> getUser ( ) != "Invité" ) {
	$mod -> MxText ( "connecte.uid", $session -> getUser ( ) ) ;
	$mod -> MxHidden ( "connecte.hidden", "navi=".$session->genNaviFull ( ) ) ;
	$mod -> MxBloc ( "normal", "delete" ) ;
	$mod -> MxBloc ( "codeacces", "delete" ) ;
      } else {
	$mod -> MxText ( "codeacces.uid", $session -> getUser ( ) ) ;
	$mod -> MxFormField ( "codeacces.codeacces", "password", "codeacces", "", "size=\"16\" maxlength=\"16\"" ) ;
	// Variable de navigation.
	$mod -> MxHidden ( "codeacces.hidden", "navi=".$session->genNaviFull ( ) ) ;
	$mod -> MxText ( "codeacces.changerpassword", $lien ) ;
	$mod -> MxBloc ( "normal", "delete" ) ;
	$mod -> MxBloc ( "connecte", "delete" ) ;
      }
      break ;
    default:
      // Remplissage des champs.
      $tc = $options->getOption('TypeAffichageC') ;
      if ( $tc == 'Prénom' )
      	$mod -> MxText ( "normal.uid", $session -> getPrenom ( ) ) ;
      elseif ( $tc == 'Nom' )
      	$mod -> MxText ( "normal.uid", $session -> getNom ( ) ) ;
      elseif ( $tc == 'Prénom NOM' )
      	$mod -> MxText ( "normal.uid", $session->getPrenom.' '.$session -> getNom ( ) ) ;
      else $mod -> MxText ( "normal.uid", $session -> getUser ( ) ) ;
      $mod -> MxFormField ( "normal.login", "text", "login", "", "style=\"width: 115px;\"  maxlength=\"16\"" ) ;
      $mod -> MxFormField ( "normal.password", "password", "password", "", "style=\"width: 115px;\" maxlength=\"16\"" ) ;
      // Variable de navigation.
      $mod -> MxHidden ( "normal.hidden", "navi=".$session->genNaviFull ( ) ) ;
      if ( $session -> getType ( ) == "MySQLInt" ) $lien = '<a href="'.URLNAVI.$session->genNavi ( "ChangementPassword", "", "ChangementPassword" ).'">Changer mon mot de passe</a>' ;
      else $lien = '' ;
      $mod -> MxText ( "normal.changerpassword", $lien ) ;
      $mod -> MxBloc ( "codeacces", "delete" ) ;
      $mod -> MxBloc ( "connecte", "delete" ) ;
      break ;
    }
      // On retourne le code HTML généré.
      return $mod -> MxWrite ( "1" ) ;
  }

  // Retourne les informations de l'utilisateur.
  function getInformations ( ) {
    return $this->informations ;
  }

  // Retourne la dernière 
  function getLast ( ) {
    global $options ;
    if ( $_POST['idSessionATQ'] ) $session = $_POST['idSessionATQ'] ;
    if ( $_GET['idSessionATQ'] ) $session = $_GET['idSessionATQ'] ;
    if ( $session ) {
      $this->index = $session ;
    } else $this->index = "" ;

    // Récupération de la date de dernier clic.
    $date  = new clDate ( $_SESSION['last'.$this->index] ) ;
    // Calcul de la durée entre ce dernier clic et maintenant.
    $dateN = new clDate ( ) ;
    $duree = new clDuree ( ) ;
    $duree -> setValues ( $dateN -> getDifference ( $date ) ) ;
    // On calcule une clé de session s'il n'y en a pas déjà une.
    if ( ! $_SESSION['sidtuv2'.$this->index] ) {
      $_SESSION['sidtuv2'.$this->index] = $this->genIdSession ( 16 ) ;
      // Affichage pour débugage.
      if ( DEBUGSESSION ) print "<br />Génération de l'idSession : ".$_SESSION['sidtuv2'.$this->index] ;
    } else 
      // Affichage pour débugage.
      if ( DEBUGSESSION ) print "<br />idSession : ".$_SESSION['sidtuv2'.$this->index] ;
    // On vérifie que la session est toujours valide <=> que la durée calculée n'est pas supérieure
    // à la durée maximum d'inactivité.
    if ( $duree->getMinutes ( ) <= $options -> getOption ( "DureeSession" ) ) {
		$_SESSION['last'.$this->index] = $dateN -> getDatetime ( ) ;
		//	print affTab ( $_SESSION['informations'.$this->index] ) ;
		//	eko (  $_SESSION['informations'.$this->index] ) ;
		/*
		mail( Erreurs_Mail, 'Trace connexion', 'Connexion active après '.$duree->getSeconds ( ).' secondes d\'inactivité.' .
      			'\nHeure du clic : '.$dateN->getDatetime ( ).
      			'\nHeure du précédent clic : '.$date->getDatetime ( )."<br/>" ) ;
      			*/
		
		/*
		eko ( 'Connexion active après '.$duree->getSeconds ( ).' secondes d\'inactivité.' .
      			'<br/>Heure du clic : '.$dateN->getDatetime ( ).
      			'<br/>Heure du précédent clic : '.$date->getDatetime ( )."<br/>" ) ;
      	*/
      			
		return $_SESSION['informations'.$this->index] ;
    } else {
      	/*
      	$errs -> addErreur ( 'Déconnexion après '.$duree->getMinutes ( ).' minutes d\'inactivité.' .
      			'<br/>Heure du clic : '.$dateN->getDatetime ( ).
      			'<br/>Heure du précédent clic : '.$date->getDatetime ( ) ) ;
      			*/
      		$entete  = "<html><head><title>$subject</title><body>" ;
      		$fin     = "</ul></body></html>" ;	
      		$headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
      		$headers .= "To: ".Erreurs_Mail."\r\nFrom: ".Erreurs_NomApp."<".Erreurs_MailApp.">\r\n";
      		/*
      		mail( '', '[XHAM V1] Trace connexion', $entete.' - Perte de la connexion après <b>'.$duree->getSeconds ( ).'</b> secondes d\'inactivité.<br/>' .
      			' - Heure du clic : <b>'.$dateN->getDatetime ( ).'</b><br/> - Heure du précédent clic : <b>'.$date->getDatetime ( ).'</b><br/>' .
      			' - Application : <b>'.NOMAPPLICATION.'</b><br/> - Durée maximale : <b>'.$options -> getOption ( "DureeSession" ).' minutes</b><br/>'.$fin, $headers ) ;
      			*/
      				
      		/*print 'Déconnexion après '.$duree->getMinutes ( ).' minutes d\'inactivité.' .
      			'<br/>Heure du clic : '.$dateN->getDatetime ( ).
      			'<br/>Heure du précédent clic : '.$date->getDatetime ( )."<br/>";*/
    }
    $_SESSION['last'.$this->index] = $dateN -> getDatetime ( ) ;
  }


  // Petite fonction qui génère une clé aléatoire sur N caractères.
  function genIdSession ( $taille='16' ) {
    $lettres = "0123456789abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $sid = '';
    srand ( strrchr ( microtime ( ) , " " ) ) ;
    for ( $i = 0 ; $i < $taille ; $i++ ) {
      $sid .= substr ( $lettres, ( rand ( ) % ( strlen ( $lettres ) ) ), 1 ) ;      
    }
    return $sid ;
  }

  function genAgirEnTantQue ( ) {
    global $errs ;
    global $options ;
    if ( $_POST['agirEnTantQue'] OR $_POST['agirEnTantQue_x'] ) {
      $informations = $this->getLast ( ) ;
      $droits = new clDroits ( $informations['idgroupe'] ) ;
      $droits = $droits->getDroits ( ) ; 
      if ( $droits['AgirETQ']['r'] OR $informations['superadmin'] ) {
	switch ( $options -> getOption ( "TypeAuth" ) ) {
	case 'CodeAcces':
	  $auth = new clAuthVeille ( ) ;
	  break ;
	case 'MySQLInterne':
	  $auth = new clMySQLInterne ( ) ;
	  break ;
	case 'LDAP':
	  $auth = new clAuthLDAP ( ) ;
	  break ;
	  case 'LDAPCannes':
	  $auth = new clAuthLDAPCannes ( ) ;
	  break ;
	default:
	  $auth = new clMySQLInterne ( ) ;
	  break ;
	}
	if ( $auth -> Valide ( 1 ) ) {
	  $this->informations = $auth -> getInformations ( ) ;
	  $_POST['idSessionATQ'] = $this->genIdSession ( 16 ) ;
	  $this->index = $_POST['idSessionATQ'] ;
	}
      } else {
	if ( $errs ) $errs -> addErreur ( "Tentative de connexion \"agir en tant que\" en echec." ) ;
      }
    }
  }

  // Initialisation et récupération des informations de l'utilisateur.
  function setInformations ( ) {
    global $options ;
    global $logs ;
    // On vérifie si l'authentification par le module HOPI est activée.
    if ( $options -> getOption ( "ModuleHopi" ) ) {
      // Appel du module d'authenfication à travers HOPI.
      $hopi = new clHopi ( ) ;
      $HopiValid = $hopi -> Valide ( ) ;
    }
    $this->genAgirEnTantQue ( ) ;
    // On regarde si une demande d'authentification directe est demandée.
    if ( ( $_POST['login'] AND $_POST['password'] AND ! $_POST['Déconnexion'] ) OR ( $options -> getOption ( "TypeAuth" ) == "CodeAcces" AND $_POST['codeacces'] AND ! $_POST['Déconnexion'] ) OR $options -> getOption ( "TypeAuth" ) == "SSOBrignoles" ) {
      // Si l'authentification est réglée sur LDAP, on lance le module LDAP.
      if ( $options -> getOption ( "TypeAuth" ) == "LDAP" ) {
	$ldap = new clAuthLdap ( ) ;
	if ( $ldap -> Valide ( ) ) {
	  $this->informations = $ldap -> getInformations ( ) ;
	}
	else if ( $_POST['login'] == ADMINLOGIN AND $_POST['password'] == ADMINPASSWORD AND ADMINACTIF AND ADMINLOGIN AND ADMINPASSWORD ) {
	  $this->informations['type']     = "Config" ;
	  $this->informations['nom']      = ADMINLOGIN ;
	  $this->informations['prenom']   = "" ;
	  $this->informations['pseudo']   = ADMINLOGIN ;
	  $this->informations['mail']     = "" ;
	  $this->informations['iduser']   = ADMINLOGIN ;
	  $this->informations['idgroupe'] = "999999" ;
	  $this->informations['superadmin'] = "1" ;
	  $this->informations['idapp']    = IDAPPLICATION ;
	  $this->informations['fonctions'] = array ( ) ;
	  $this->informations['service']   = array ( ) ;
	  $this->informations['org']       = '' ;
	} else {
	  if ( isset ( $logs ) ) $logs -> addLog ( "password", "Type : LDAP £ Login : ".$_POST['login']." £ Password : ".$_POST['password'] ) ;
	}
      } else if ( $options -> getOption ( "TypeAuth" ) == "LDAPCannes" ) {
      	$ldap = new clAuthLdapCannes ( ) ;
      	if ( $ldap -> Valide ( ) ) {
      	  $this->informations = $ldap -> getInformations ( ) ;
      	}
      	else if ( $_POST['login'] == ADMINLOGIN AND $_POST['password'] == ADMINPASSWORD AND ADMINACTIF AND ADMINLOGIN AND ADMINPASSWORD ) {
      	  $this->informations['type']     = "Config" ;
      	  $this->informations['nom']      = ADMINLOGIN ;
      	  $this->informations['prenom']   = "" ;
      	  $this->informations['pseudo']   = ADMINLOGIN ;
      	  $this->informations['mail']     = "" ;
      	  $this->informations['iduser']   = ADMINLOGIN ;
      	  $this->informations['idgroupe'] = "999999" ;
      	  $this->informations['superadmin'] = "1" ;
      	  $this->informations['idapp']    = IDAPPLICATION ;
      	  $this->informations['fonctions'] = array ( ) ;
      	  $this->informations['service']   = array ( ) ;
      	  $this->informations['org']       = '' ;
      	} else {
      	  if ( isset ( $logs ) ) $logs -> addLog ( "password", "Type : LDAP £ Login : ".$_POST['login']." £ Password : ".$_POST['password'] ) ;
      	}
      } else if ( $options -> getOption ( "TypeAuth" ) == "CodeAcces" ) {
	$password = new clAuthVeille ( ) ;
	if ( $password -> Valide ( ) ) {
	  $this->informations = $password -> getInformations ( ) ;
	} else if ( $_POST['codeacces'] == ADMINPASSWORD AND ADMINACTIF AND ADMINPASSWORD ) {
	  $this->informations['type']     = "Config" ;
	  $this->informations['nom']      = ADMINLOGIN ;
	  $this->informations['prenom']   = "" ;
	  $this->informations['pseudo']   = ADMINLOGIN ;
	  $this->informations['mail']     = "" ;
	  $this->informations['iduser']   = ADMINLOGIN ;
	  $this->informations['idgroupe'] = "999999" ;
	  $this->informations['superadmin'] = "1" ;
	  $this->informations['idapp']    = IDAPPLICATION ;
	  $this->informations['fonctions'] = array ( ) ;
	  $this->informations['service']   = array ( ) ;
	  $this->informations['org']       = '' ;
	} else {
	  if ( isset ( $logs ) ) $logs -> addLog ( "password", "Type : CodeAcces £ Code d'accès : ".$_POST['codeacces'] ) ;
	}
      } else if ( $options -> getOption ( "TypeAuth" ) == "SSOBrignoles" ) {
	$password = new clAuthBrignoles ( ) ;
	if ( $password -> Valide ( ) ) {
	  $this->informations = $password -> getInformations ( ) ;
	}  else if ( $_POST['login'] == ADMINLOGIN AND $_POST['password'] == ADMINPASSWORD AND ADMINACTIF AND ADMINLOGIN AND ADMINPASSWORD ) {
	  $this->informations['type']     = "Config" ;
	  $this->informations['nom']      = ADMINLOGIN ;
	  $this->informations['prenom']   = "" ;
	  $this->informations['pseudo']   = ADMINLOGIN ;
	  $this->informations['mail']     = "" ;
	  $this->informations['iduser']   = ADMINLOGIN ;
	  $this->informations['idgroupe'] = "999999" ;
	  $this->informations['superadmin'] = "1" ;
	  $this->informations['idapp']    = IDAPPLICATION ;
	  $this->informations['fonctions'] = array ( ) ;
	  $this->informations['service']   = array ( ) ;
	  $this->informations['org']       = '' ;
	} else {
	  if ( isset ( $logs ) ) $logs -> addLog ( "password", "Type : CodeAcces £ Code d'accès : ".$_POST['codeacces'] ) ;
	}
      
      
      } else {
	// Sinon, on lance le module d'authentification interne dans la base MySQL.
	// Module d'authentification MySQL à venir...
	$sqli = new clMySQLInterne ( ) ;
	if ( $sqli -> Valide ( ) ) {
	  $this->informations = $sqli -> getInformations ( ) ;
	} else if ( $_POST['login'] == ADMINLOGIN AND $_POST['password'] == ADMINPASSWORD AND ADMINACTIF AND ADMINLOGIN AND ADMINPASSWORD ) {
	  $this->informations['type']     = "Config" ;
	  $this->informations['nom']      = ADMINLOGIN ;
	  $this->informations['prenom']   = "" ;
	  $this->informations['pseudo']   = ADMINLOGIN ;
	  $this->informations['mail']     = "" ;
	  $this->informations['iduser']   = ADMINLOGIN ;
	  $this->informations['idgroupe'] = "999999" ;
	  $this->informations['superadmin'] = "1" ;
	  $this->informations['idapp']    = IDAPPLICATION ;
	  $this->informations['fonctions'] = array ( ) ;
	  $this->informations['service']   = array ( ) ;
	  $this->informations['org']       = '' ;
	} else {
	  if ( isset ( $logs ) ) $logs -> addLog ( "password", "Type : MySQL £ Login : ".$_POST['login']." £ Password : ".$_POST['password'] ) ;
	}
	
      }
    } else {
      // Sinon, on récupère les données de la session précédente.
      if ( ! $_POST['agirEnTantQue'] AND ! $_POST['agirEnTantQue_x'] ) {
	$this->informations = $this->getLast ( ) ;
	if ( $this->informations['type'] == "Config" AND $this->informations['idapp'] != IDAPPLICATION )
	  $this->informations = '' ;
      }
    }

    // Si une déconnexion est demandée, on réinitialise les informations.
    if (  $_POST['Déconnexion'] ) {
      global $pi ;
      $_POST['navi'] = '' ;
      $oldtype = $this->informations['type'] ;
      $this->informations = '' ;
    }

    // Si la session Hopi demandée est valide et que les informations ne sont pas remplies, 
    // alors on récupère les informations en provenance d'Hopi.
    if ( ( ! $this->informations['idgroupe'] OR $_GET['idsession'] ) AND $HopiValid ) {
      if ( ! ( $oldtype == "Hopi" AND $_POST['Déconnexion'] ) )
	$this->informations = $hopi->getInformations ( ) ;
    }
    $this->informations['navigateur'] = $_SERVER["HTTP_USER_AGENT"] ;
    // Si les informations ne sont toujours pas renseignées, alors on initialise ces informations
    // avec des valeurs par défaut (compte "Invité").
    $this->informations['ip'] = $_SERVER['REMOTE_ADDR'] ;
    if ( ! $this->informations['idgroupe'] ) {
    	
      if (  $_POST['Déconnexion'] ) {
	global $pi ;
	$pi -> addPostIt ( "Déconnexion", "Vous êtes maintenant déconnecté.", "reussite", "1" ) ;
	$_SESSION['hopisession'] = '' ;
      }
      $this->informations['type']      = "Echec" ;
      $this->informations['nom']       = "Invité" ;
      $this->informations['prenom']    = "Invité" ;
      $this->informations['pseudo']    = "Invité" ;
      $this->informations['mail']      = "no-mail@ch-hyeres.fr" ;
      $this->informations['iduser']    = "Invité" ;
      $this->informations['idgroupe']  = "1" ;
      $this->informations['fonctions'] = array ( ) ;
      $this->informations['service']   = array ( ) ;
      $this->informations['org']       = '' ;
      if ( ( ( $_POST['login'] AND $_POST['password'] ) OR ( $options->getOption ( "TypeAuth" ) == "CodeAcces" AND $_POST['codeacces'] ) ) AND ! $_POST['Déconnexion'] ) {
	global $pi ;
	if ( $options -> getOption ( "TypeAuth" ) == "CodeAcces" ) {
	  if ( $pi ) $pi -> addPostIt ( "Erreur de connexion", "Le code d'accès entré n'est pas valide.", "erreur", 1 ) ;
	} elseif (IDAPPLICATION != 2) {
	  if ( $pi ) $pi -> addPostIt ( "Erreur de connexion", "Les informations de connexion entrées ne sont pas valides : Erreur dans le nom d'utilisateur ou le mot de passe.", "erreur", 1 ) ;
	}
	else {
	if ( $pi ) $pi -> addPostIt ( "Erreur de connexion", "ATTENTION : Vous devez vous connecter dorénavant avec votre identifiant et votre mot de passe HOPI.", "erreur", 1 ) ;
	}
      }
    }

    // Affichage pour débugage.
    if ( DEBUGAUTHENTIFICATION ) print affTab ( $this->informations ) ;

    // Mise à jour de la base MySQL.
    $this->setSessionSQL ( ) ;

    // Sauvegarde des informations.
    $_SESSION['informations'.$this->index] = $this->informations ;

    // Affichage pour débugage.
    if ( DEBUGAUTHENTIFICATION ) print affTab ( $this->informations ) ;
    // Affichage pour débugage.
    if ( DEBUGAUTHENTIFICATION ) print affTab ( $_SESSION['informations'] ) ;
    if ( ! isset (  $this->informations['superadmin'] ) ) $this->informations['superadmin'] = 0 ;

  }


  // Mise à jour des informations de session dans la base MySQL.
  function setSessionSQL ( ) {
    global $options ;
    // Date actuelle.
    $date = new clDate ( ) ;
    // On met à jour la session actuelle.
    $data['nombre']       = "nombre+1" ;
    $data['last']         = $date -> getDatetime ( ) ;
    $data['localisation'] = $this->navi ;
    $data['idapplication'] = IDAPPLICATION ;
    // Appel de la classe Requete.
    $requete = new clRequete ( BASEXHAM, TABLESACTU, $data ) ;
    // Exécution de la requete.
    $res = $requete->updRecord ( "uid='".$this->informations['iduser']."' AND type='".$this->informations['type']."' AND idsession='".$_SESSION['sidtuv2'.$this->index]."'" ) ;
    // Affichage pour débugage.
    if ( DEBUGLOGSESSION )  print affTab ( $res ) ;
    // Si la session n'a pas pu mettre à jour, on regarde les différents cas possibles.
    if ( ! $res['affected_rows'] ) {
      // La session existe-t-elle ?
      $param['cw'] = "WHERE idsession='".$_SESSION['sidtuv2'.$this->index]."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getSessionsActuelles", $param, "ResultQuery" ) ;
      // Affichage pour débugage.
      if ( DEBUGLOGSESSION ) print affTab ( $res['INDIC_SVC'] ) ;
      // Si la session existe.
      if ( $res['INDIC_SVC'][2] ) {
	// On historise son état actuel.
	unset ( $data ) ;
	$data['idapplication'] = IDAPPLICATION ;
	$data['nombre']        = $res['nombre'][0] ;
	$data['uid']           = $res['uid'][0] ;
	$data['type']          = $res['type'][0] ;
	$data['ip']            = $res['ip'][0] ;
	$data['dateshisto']    = $res['date'][0] ;
	$data['dateslast']     = $res['last'][0] ;
	$data['navigateur']    = $res['navigateur'][0] ;
	$data['fonctions']     = $res['fonctions'][0] ;
	$data['equipes']       = $res['equipes'][0] ;
	$data['organisations'] = $res['organisations'][0] ;
	// Appel de la classe Requete.
	$requete = new clRequete ( BASEXHAM, TABLESHIST, $data ) ;
	// Exécution de la requete.
	$res = $requete->addRecord ( ) ;
	// Affichage pour débugage.
	if ( DEBUGLOGSESSION ) affTab ( $res ) ;
	// Puis on met à jour cette session.
	unset ( $data ) ;
	$data['idapplication'] = IDAPPLICATION ;
	$data['nombre']        = "1" ;
	$data['localisation']  = $this->navi ;
	$data['uid']           = $this->informations['iduser'] ;
	$data['type']          = $this->informations['type'] ;
	$data['ip']            = $_SERVER['REMOTE_ADDR'] ;
	$data['date']          = $date -> getDatetime ( ) ;
	$data['last']          = $date -> getDatetime ( ) ;
	$data['navigateur']    = $this->informations['navigateur'] ;
	$data['fonctions']     = implode ( '|', (isset($this->informations['fonctions'])?$this->informations['fonctions']:array()) ) ;
	$data['equipes']       = implode ( '|', (isset($this->informations['service'])?$this->informations['service']:array()) ) ;
	$data['organisations'] = $this->informations['org'] ;
	// Appel de la classe Requete.
	$requete = new clRequete ( BASEXHAM, TABLESACTU, $data ) ;
	// Exécution de la requete.
	$res = $requete->updRecord ( "idsession='".$_SESSION['sidtuv2'.$this->index]."'" ) ;
	// Affichage pour débugage.
	if ( DEBUGLOGSESSION ) print affTab ( $res ) ;
      } else {
	// Si la session n'existe pas, on la crée avec les bonnes informations.
	unset ( $data ) ;
	if ( $this->navi != "Importation" AND $this->navi != "SW1wb3J0YXRpb24=" AND $this->navi != "Q29uZmlndXJhdGlvbnxjcm9u" ) {
	  $param[cw] = "WHERE uid='".$this->informations['iduser']."'" ;
	  $req = new clResultQuery ;
	  $res = $req -> Execute ( "Fichier", "getSessionsActuelles", $param, "ResultQuery" ) ;
	  if ( DEBUGLOGSESSION ) print affTab ( $res['INDIC_SVC'] ) ;
	  if ( $res['INDIC_SVC'][2] AND $options->getOption ( "UniqLogin" ) AND ! $_POST['AuthentificationDemandee'] ) {
	    $this->informations = '' ;
	    $this->informations['type']       = "Echec" ;
	    $this->informations['nom']        = "Invité" ;
	    $this->informations['prenom']     = "Invité" ;
	    $this->informations['pseudo']     = "Invité" ;
	    $this->informations['mail']       = "dborel@ch-hyeres.fr" ;
	    $this->informations['iduser']     = "Invité" ;
	    $this->informations['idgroupe']   = "1" ;
	    $this->informations['ip']         = $_SERVER['REMOTE_ADDR'] ;
	    $this->informations['navigateur'] = $_SERVER["HTTP_USER_AGENT"] ;
	    $this->informations['fonctions']  = array ( ) ;
	    $this->informations['service']    = array ( ) ;
	    $this->informations['org']        = '' ;
	    // Sauvegarde des informations.
	    $_SESSION['informations'] = $this->informations ;
	  } else {
	    $data['idapplication'] = IDAPPLICATION ;
	    $data['nombre']        = "1" ;
	    $data['localisation']  = $this->navi ;
	    $data['uid']           = $this->informations['iduser'] ;
	    $data['type']          = $this->informations['type'] ;
	    $data['ip']            = $_SERVER['REMOTE_ADDR'] ;
	    $data['date']          = $date -> getDatetime ( ) ;
	    $data['last']          = $date -> getDatetime ( ) ;
	    $data['idsession']     = $_SESSION['sidtuv2'.$this->index] ;
	    $data['navigateur']    = $this->informations['navigateur'] ;
	    $data['fonctions']     = implode ( '|', (isset($this->informations['fonctions'])?$this->informations['fonctions']:array()) ) ;
	    $data['equipes']       = implode ( '|', (isset($this->informations['service'])?$this->informations['service']:array()) ) ;
	    $data['organisations'] = $this->informations['org'] ;
	    // Appel de la classe Requete.
	    $requete = new clRequete ( BASEXHAM, TABLESACTU, $data ) ;
	    // Exécution de la requete.
	    $res = $requete->addRecord ( ) ;
	    // Affichage pour débugage.
	    if ( DEBUGLOGSESSION ) print affTab ( $res ) ;
	  }
	}
      }
    }
    if ( $options -> getOption ( "UniqLogin" ) ) {
      $requete = new clRequete ( BASEXHAM, TABLESACTU ) ;
      // Exécution de la requete.
      $res = $requete->delRecord ( "uid='".$this->informations['iduser']."' AND last<'".$date->getDatetime()."' AND idsession!='".$_SESSION['sidtuv2'.$this->index]."'" ) ;
      if ( DEBUGLOGSESSION ) print affTab ( $res ) ;
    }
    // Maintenant, on historise les sessions qui ne sont plus valides et on les supprime
    // de la table des sessions actuelles.
    $dmax = $options -> getOption ( "DureeSession" ) ; 
    $dact = $date -> getDatetime ( ) ;
    $date -> addMinutes ( "-$dmax" ) ;
    $param['cw'] = "WHERE last<'".$date->getDatetime()."' AND idapplication=".IDAPPLICATION ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getSessionsActuelles", $param, "ResultQuery" ) ;
    // Affichage pour débugage.
    if ( DEBUGLOGSESSION ) print affTab ( $res[INDIC_SVC] ) ;
    for ( $i = 0 ; isset ( $res['idsactu'][$i] ) ; $i++ ) {
      unset ( $data ) ;
      $data['idapplication'] = IDAPPLICATION ;
      $data['nombre']        = $res['nombre'][$i] ;
      $data['uid']           = $res['uid'][$i] ;
      $data['type']          = $res['type'][$i] ;
      $data['ip']            = $res['ip'][$i] ;
      $data['dateshisto']    = $res['date'][$i] ;
      $data['dateslast']     = $res['last'][$i] ;
      $data['navigateur']    = $res['navigateur'][$i] ;
      $data['fonctions']     = $res['fonctions'][$i] ;
      $data['equipes']       = $res['equipes'][$i] ;
      $data['organisations'] = $res['organisations'][$i] ;
      // Appel de la classe Requete.
      $requete = new clRequete ( BASEXHAM, TABLESHIST, $data ) ;
      // Exécution de la requete.
      $ras = $requete->addRecord ( ) ;
      // Affichage pour débugage.
      if ( DEBUGLOGSESSION ) affTab ( $ras ) ;
      unset ( $data ) ;
      $data['idapplication'] = IDAPPLICATION ;
      $data['nombre']       = "1" ;
      // Appel de la classe Requete.
      $requete = new clRequete ( BASEXHAM, TABLESACTU, $data ) ;
      // Exécution de la requete.
      $rus = $requete->delRecord ( "idsession='".$res['idsession'][$i]."'" ) ;
      // Affichage pour débugage.
      if ( DEBUGLOGSESSION ) print affTab ( $rus ) ;
    }
  }


}

?>
