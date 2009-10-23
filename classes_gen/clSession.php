<?php

// Titre  : Classe Session
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 18 Janvier 2005

// Description : 
// Cette classe gère tous ce qui a rapport avec la session en cours. Elle 
// permet de générer les liens, les variables hidden. Elle fait appel à la 
// class Authentification pour récupérer les informations et les droits de
// l'utilisateur actuellement connecté.

class clSession {

  // Déclaration des attributs de la classe.
  // Contient les différents niveaux de la navigation en cours.
  private $navi ;
  // Contient la navigation complète.
  private $navifull ;
  // Contient les informations de l'utilisateur connecté.
  public $user ;
  // Contient tous les droits de l'utilisateur.
  private $droits ;
  // Déclaration des fonctions d'accès aux attributs de la classe.
  // Permet de se situer dans la navigation...
  function getNavi ( $lvl ) { if ( isset ( $this->navi[$lvl] ) ) return $this->navi[$lvl] ; }
  // Renvoie la navigation complète pour débugage.
  function getNaviFull ( ) { $navi = '' ; for ( $i = 0 ; isset ( $this->navi[$i] ) ; $i++ ) $navi .= $this->navi[$i]."|" ; return rtrim($navi,'|') ; }
  // Retourne la navigation actuelle.
  function genNaviFull ( ) { 
    if ( isset ( $_POST['idSessionATQ'] ) ) $sess = $_POST['idSessionATQ'] ;
    if ( isset ( $_GET['idSessionATQ'] ) ) $sess = $_GET['idSessionATQ'] ;
    if ( isset ( $sess ) ) $lienSessionATQ = "&idSessionATQ=".$sess ;
    if ( isset ( $_POST['noMenu'] ) ) $nomenu = $_POST['noMenu'] ;
    if ( isset ( $_GET['noMenu'] ) ) $nomenu = $_GET['noMenu'] ;
    if ( isset ( $nomenu ) ) $lienNoMenu = "&noMenu=".$nomenu ;
    return $this->navifull.(isset($lienSessionATQ)?$lienSessionATQ:'').(isset($lienNoMenu)?$lienNoMenu:'') ; 
  }
  
  // Renvoie vrai si l'utilisateur a accès à la page avec le droit demandé.
  //poil au nez
  function getDroit ( $page, $type='r' ) { 
    global $options ;
    if ( $this->user['superadmin'] ) return 1 ; 
    else return ( ( isset ( $this->droits[$page] ) ? $this->droits[$page][$type] : '' ) ) ; 
  }
  //ligne qui sert à rien
  
  // Récupération d'informations de l'utilisateur.
  function getUF        (          ) { return $this->user['uf']              ; } //separés par des virgule et entre accolades
  function getUid       (          ) { return $this->user['iduser']          ; }
  function getGrp       (          ) { return $this->user['idgroupe']        ; }
  function getNom       (          ) { return $this->user['nom']             ; }
  function getTel       ( $ind='0' ) { if (isset($this->user['tel'])) return $this->user['tel'][$ind] ; }
  function getTels      (          ) { return $this->user['tel']             ; }
  function getUser      (          ) { return $this->user['pseudo']          ; }
  function getMail      (          ) { return $this->user['mail']            ; }
  function getType      (          ) { return $this->user['type']            ; }
  function getGroupes	(		   ) { return $this->user['groupes']         ; } //tableau des groupes au sens ldap strict
  function getOrgs      (          ) { return $this->user['org']             ; }
  function getPrenom    (          ) { return $this->user['prenom']          ; }
  function getService   ( $ind='0' ) { return $this->user['service'][$ind]   ; }
  function getServices  (          ) { return $this->user['service']         ; }
  function getFonction  ( $ind='0' ) { return $this->user['fonctions'][$ind] ; }
  function getFonctions (          ) { return $this->user['fonctions']       ; }
  function getAttribute ( $var     ) { return $this->user[$var]              ; }
  function getIP        (          ) { return (isset($this->user['ip'])?$this->user['ip']:'') ; }
  function getPassword  (          ) { return XhamTools::dechiffre ( $this->user['password'] ) ; }

  // Retourne '1' si l'utilisateur fait partie de l'UF passée en argument.
  function isInUF ( $uf ) {
    for ( $i = 0 ; isset ( $this->user['uf'][$i] ) ; $i++ )
      if ( $uf == $this->user['uf'][$i] ) return 1 ;
  }

  // Constructeur du bonheur.
  function __construct ( $noLog='', $logSup='' ) {
    // Récupération de la navigation.
    $this->setNavi   ( ) ;
    // Initialisation (et authentification) de l'utilisateur.
    $this->setUser   ( ) ;
    // Initialisation des droits de l'utilisateur connecté.
    $this->setDroits ( ) ;
    // Variable qui indique si l'information est loguée ou non.
    $this->noLog = $noLog ;
    $this->logSup = $logSup ;
    // Débugage
    if ( DEBUGSESSION ) { print "<br />".$this->getNaviFull ( ) ; }
    //    echo affTab ( $this->user['fonctions'] ) ;
       
       
        //logue tout ce qui se passe
     if(defined("DEBUGALLNAVI") ) {
   		if($fp = fopen(DEBUGALLNAVI,"a")){
			fputs($fp, "<br/>");
			fputs($fp, "--------------------------------------------------------");
			fputs($fp, "<br/>");
			$rep = date("y.d.m G:i:s");
			$rep .= "<br/>";
			$mess= affTab($_SESSION);
			$mess.="<br/>".affTab($_POST);
			$mess.="<br/>".affTab($_GET);
			fputs($fp, $rep.$mess);
			fclose($fp);
			}
      }
  }

  // Nolog le petit martien.
  function noLog ( ) {
    $this->noLog = 1 ;
  }

  // Destructeur
  function destruct ( ) {
    global $logs ;
    if ( ! $this->noLog )
      $logs -> addLog ( "navi", $this->getNaviFull ( ), $this->logSup ) ;
  }

  // Change le log supplémentaire.
  function setLogSup ( $logSup='' ) {
    $this->logSup = $logSup ;
  }

  // Ajout des statistiques dans la base de données.
  function setStats ( ) {
    global $logs ;
    if ( $this->navi[0] != 'Importation' ) {
    $data['nombre'] = "nombre+1" ;
    // Appel de la classe Requete.
    $requete = new clRequete ( BASEXHAM, TABLESTATS, $data ) ;
    // Exécution de la requete.
    $res = $requete->updRecord ( "loc1='".addslashes(stripslashes((isset($this->navi[0])?$this->navi[0]:'')))."' AND loc2='".addslashes(stripslashes((isset($this->navi[1])?$this->navi[1]:'')))."' AND uid='".addslashes(stripslashes($this->getUid()))."' AND idapplication=".IDAPPLICATION ) ;
    //eko ( $res ) ;
    if ( ! $res['affected_rows'] ) {
      $data['nombre'] = "1" ;
      $data['loc1']   = (isset($this->navi[0])?$this->navi[0]:'') ;
      $data['loc2']   = (isset($this->navi[1])?$this->navi[1]:'') ;
      $data['uid']    = $this->getUid ( ) ;
      $data['idapplication'] = IDAPPLICATION ;
      
      // Appel de la classe Requete.
      $requete = new clRequete ( BASEXHAM, TABLESTATS, $data ) ;
      // Exécution de la requete.
      $res = $requete->addRecord ( ) ;
    }
    }
  }

  // Initialisation de la navigation.
  function setNavi ( $naviforce='') {
    // Elle est transmise soit dans une variable de type GET (lien),
    // soit dans une variable de type POST (formulaire).
    if ( isset($_GET['navi']) && $_GET['navi'] ) $navi = $_GET['navi'] ;
    if ( isset($_POST['navi']) && $_POST['navi'] ) $navi = $_POST['navi'] ;
    $this->navifull = $navi ;
    if ( isset($_POST['Déconnexion']) && $_POST['Déconnexion'] ) { $this->navifull = '' ; $navi = '' ; }
    if ( $naviforce ) {
      $navi = $naviforce;
      $this->navifull = $navi ;
    }
   // print "<br/><br/>on est dans setnavi. navifull=".$this->navifull.'<br/>';
    // Si la navigation a bien été transmise, alors on récupère les différents
    // niveaux dans un tableau.
    if ( ENCODERURL ) { 
      // NOTE : un petit str_replace histoire que base64_encode('FABIENLAFOUINE') soit un peu partout dans les URL.
      if ( $navi ) { $this->navi = explode ( "|", base64_decode ( str_replace ( "RkFCSUVOTEFGT1VJTkU", "+", $navi )))   ;
      } else { $this->navi[0] = "Accueil" ; } 
    }  else { 
      if ( $navi ) { $this->navi = explode ( "|", $navi ) ; 
      // Sinon, on initialise le premier niveau à la page d'accueil du terminal.
      } else { $this->navi[0] = "Accueil" ; } 
    }
    
    $_SESSION['XHAM_veryOldNavi']=$_SESSION['XHAM_oldNavi'];    
    $_SESSION['XHAM_oldNavi']=$_SESSION['XHAM_Navi'];
    $_SESSION['XHAM_Navi']=$this->navi;
  }
  
  
  

  // Même que ça fait un truc cool il parait !
  //ok tu veux des commentaires ?
  //declaration et nom de la methode
  function unsetOldNavi() 
  //debut d'accolade de la definition de la methode
  {
  	//affecte la valeur de la variable $_SESSION['XHAM_oldNavi'] à la variable $_SESSION['XHAM_Navi'
    $_SESSION['XHAM_Navi']=$_SESSION['XHAM_oldNavi'];
    //affecte la valeur de la variable $_SESSION['XHAM_veryOldNavi'] à la variable $_SESSION['XHAM_oldNavi'
    $_SESSION['XHAM_oldNavi']=$_SESSION['XHAM_veryOldNavi'];
    //accolade de fermeture de la methode
  }


  // Cette fonction est rigolote !!! Mais c'est mal ! Bouh, la honte...
  
  //je le crois pas ! quelle fonction de naze !!
  
  // Non mais oh ! T'es qui toi pour juger cette fonction ?! Grrr !

  function setMiniNavi ( $lvl, $valeur ) {
    $this->navi[$lvl] = $valeur ;
  }

  // Génération de la valeur de la variable navigation à transmettre.
  function genNavi ( ) {
    // Récupération du nombre d'arguments de la fonction.
    $n = func_num_args ( ) ;
    // Pour chaque argument, on le concatène au précédent avec le séparateur |.
    for ( $i = 0 ; $i < $n ; $i++ ) {
      if ( isset ( $lien ) ) $lien .= "|".func_get_arg ( $i ) ;
      else $lien = func_get_arg ( $i ) ;
    }
    // Si aucun lien n'est défini, on renvoie vers la page d'accueil.
    if ( ! isset ( $lien ) ) $lien = "Accueil" ;
    if ( isset ( $_POST['idSessionATQ'] ) ) $sess = $_POST['idSessionATQ'] ;
    if ( isset ( $_GET['idSessionATQ'] ) ) $sess = $_GET['idSessionATQ'] ;
    if ( isset ( $sess ) ) $lienSessionATQ = "&idSessionATQ=".$sess ;
    else $lienSessionATQ = "" ;
    if ( isset ( $_POST['noMenu'] ) ) $nomenu = $_POST['noMenu'] ;
    if ( isset ( $_GET['noMenu'] ) ) $nomenu = $_GET['noMenu'] ;
    if ( isset ( $nomenu ) ) $lienNoMenu = "&noMenu=".$nomenu ;
    else $lienNoMenu = '' ;
    // On renvoie la chaîne ainsi construite. (Et on remplace les '+' par le résultat 
    // de base64_encode ( "FABIENLAFOUINE" ) : vrai mais peu probable dans une url...)
    if ( ENCODERURL ) return str_replace ( "+", "RkFCSUVOTEFGT1VJTkU", base64_encode ( $lien ) ).$lienSessionATQ.$lienNoMenu ;
    else return $lien.$lienSessionATQ.$lienNoMenu ;
  }

  // Commande une pizza royale directement livrée au CH-Hyères, bureau des développeurs.
  function getPizzaRoyale ( ) {
    eko ( base64_decode ( "RmF1dCB2cmFpbWVudCDqdHJlIHRvcmR1IHBvdXIgcGVuc2VyIHF1ZSBjZXR0ZSBmb25jdGlvbiBmYWl0IHZyYWltZW50IOdhLCBqJ3kgY3JvaXMgcGFzICEgUG91ciBsYSBwZWluZSwgdHUgbidlbiBhdXJhcyBwYXMgY2V0dGUgZm9pcy4=" ) ) ;
    if ( isset ( $_SESSION['creditCardOK'] ) AND $_SESSION['creditCardOK'] ) {
      $ch = curl_init ( "http://www.allopizza.com/" ) ;
      $fp = fopen ( "../configAlloPizza.txt", "w" ) ;
      curl_setopt ( $ch, CURLOPT_FILE, $fp);
      curl_setopt ( $ch, CURLOPT_HEADER, 0) ;
      curl_exec ( $ch ) ;
      curl_close ( $ch );
      fclose ( $fp ) ;
    }
  }
  
  
  // récupération de l'ancienne variable de Navigation
  function getOldNaviFull() {
  return implode('|',$_SESSION['XHAM_oldNavi']);
  }
  function getOldNavi() {
  return $_SESSION['XHAM_oldNavi'];
  }
  

  function crypt ( $val ) {
    $cle = CRYPTKEY ;
    $lenCle = strlen ( $cle ) ;
    $lenVal = strlen ( $val ) ;
    $crypt = '';
    for ( $i = 0 ; $i < $lenVal ; $i++ ) {
      $crypt .= substr ( $val, $i, 1 ) ^ substr ( $cle, $i % $lenCle, 1 ) ;
    }
    return $val ;
  }
  
  // Récupération des droits de l'utilisateur connecté.
  function setDroits ( ) {
    $droits = new clDroits ( $this->user['idgroupe'] ) ;
    $this->droits = $droits->getDroits ( ) ;

  }
  
  function isUser(){
  	if($this->user['type']=='Echec')
  		return false;
  	return true;
  }

  // Récupération des informations (et authentification) de l'utilisateur connecté.
  function setUser ( ) {
    global $superAdmin ;
    // On appelle la classe authentification qui se charge d'afficher les informations de connexion.
    if ( $this->navifull ) $init = $this->navifull ; else $init = "Accueil" ;
    $user = new clAuthentification ( $init ) ;
    $this->user = $user->getInformations ( ) ;
    $superAdmin = (isset($this->user['superadmin'])?$this->user['superadmin']:'') ;
  }

  

}

?>
