<?php
/*
 * Created on 9 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
class XhamStats {
	
	function __construct ( $xhamInstance, $noLog="" ) {
 		$this->xham = $xhamInstance;	
 		//$this -> setSessionSQL ( ) ;
 		// $this -> updateSessions ( ) ;
		// $this -> setStats  ( ) ;
 	}
	  	
  	// Ajout des statistiques dans la base de données.
  	function setStats ( ) {
    	$data['nombre'] = "nombre+1" ;
    	// Appel de la classe Requete.
    	$this->xham->newRequete ( BASEXHAM, TABLESTATS, $data ) ;
    	// Exécution de la requete.
    	$res = $this->xham->updRecord ( "loc1='".addslashes(stripslashes($this->xham->getNavi(0)))."' AND loc2='".addslashes(stripslashes($this->xham->getNavi(1)))."' AND uid='".addslashes(stripslashes($this->xham->user->getLogin()))."' AND idapplication=".IDAPPLICATION ) ;
    	//print "ouuuuuuu"; die ;
    	if ( ! $res['affected_rows'] ) {
      		$data['nombre'] = "1" ;
      		$data['loc1']   = $this->xham->getNavi(0) ;
      		$data['loc2']   = $this->xham->getNavi(1) ;
      		$data['uid']    = $this->xham->user->getLogin ( ) ;
      		$data['idapplication'] = IDAPPLICATION ;
      		// Appel de la classe Requete.
      		$this->xham->newRequete ( BASEXHAM, TABLESTATS, $data ) ;
      		// Exécution de la requete.
      		$res = $this->xham->addRecord ( ) ;
    }
    $this -> updateSessions ( ) ;
  }
  
  // Sauvegarde d'une session passée en paramètre.
  function saveOld ( $res, $i ) {
    // Si la session existe.
    if ( $res['INDIC_SVC'][2] ) {
		// On historise son état actuel.
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
		$requete = new XhamRequete ( BASEXHAM, TABLESHIST, $data ) ;
		// Exécution de la requete.
		$ras = $requete->addRecord ( ) ;
		// Appel de la classe Requete.
  		$requete = new XhamRequete ( BASEXHAM, TABLESACTU, $data ) ;
  		// Exécution de la requete.
  		$rus = $requete->delRecord ( "idsession='".$res['idsession'][$i]."'" ) ;
  	}
  }
  
  // Création d'une nouvelle session dans la table.
  function setNew ( $user, $clesession ) {
  	$date = new clDate ( ) ;
	$data['idapplication'] = IDAPPLICATION ;
	$data['nombre']        = "1" ;
	$data['localisation']  = $this->xham->genNaviFull ( ) ;
	$data['uid']           = $user->getLogin ( ) ;
	$data['type']          = $user->getType ( ) ;
	$data['ip']            = $_SERVER['REMOTE_ADDR'] ;
	$data['date']          = $date -> getDatetime ( ) ;
	$data['last']          = $date -> getDatetime ( ) ;
	$data['idsession']     = $clesession ;
	$data['navigateur']    = $user->getNavigateur ( ) ;
	if(is_array($user->getFonctions()))
		$data['fonctions']     = implode ( '|', $user->getFonctions ( ) ) ;
	else
		$data['fonctions']     =   $user->getFonctions ( ) ;
	if(is_array($user->getServices ( )))
		$data['equipes']       = implode ( '|', $user->getServices ( ) ) ;
	else
		$data['equipes']  = "" ;
	$data['organisations'] = $user->getOrgs ( ) ;
	// Appel de la classe Requete.
	$requete = new XhamRequete ( BASEXHAM, TABLESACTU, $data ) ;
	// Exécution de la requete.
	$res = $requete->addRecord ( ) ;
  }
  
  // Mise à jour d'une session passée en paramètre.
  function updateSession ( $idSession='' ) {
  	$date = new clDate ( ) ;
	// On met à jour la session actuelle.
    $data['nombre']       = "nombre+1" ;
    $data['last']         = $date -> getDatetime ( ) ;
    $data['localisation'] = $this->xham->genNaviFull ( ) ;
    $data['idapplication'] = IDAPPLICATION ;
    // Appel de la classe Requete.
    $requete = new XhamRequete ( BASEXHAM, TABLESACTU, $data ) ;
    // Exécution de la requete.
    $res = $requete->updRecord ( "idsession='".$idSession."'" ) ;
    return $res['affected_rows'] ;
  }
  
  // Mise à jour de toutes les sessions.
  function updateSessions ( ) {
  	//eko ( $_SERVER ) ;
  	$cook = session_id ( ) ;
  	//eko ( $cook ) ;
  	if ( $this->xham->session->isInfo ( "clesession" ) )
  		$cleS = $this->xham->getInfo ( "clesession" ) ;
  	else $cleS = $cook ;
	$user = $this->xham->user ;
	if ( ! $this->updateSession ( $cleS ) ) {
		$this->setNew ( $user, $cleS ) ;
	}
	if ( $cleS != $cook )	$this->delete ( $cook ) ;
	$this->delete ( ) ;
  } 
  
  // Effacement des sessions.
  function delete ( $cleS='' ) {
  	// Si une clé est transmise, alors on supprime cette session.
  	if ( $cleS ) {
  		$param['cw'] = "WHERE idsession='$cleS' AND idapplication=".IDAPPLICATION ;
    	$res = $this -> xham -> Execute ( "Fichier", "getSessionsActuelles", $param, "ResultQuery" ) ;
    	//eko ( $res['INDIC_SVC'] ) ;
  	} else {
  		// Sinon, on supprime seulement les sessions expirées.
  		$date = new clDate ( ) ;
  		$date -> addMinutes ( -$this->xham->getOption ( "DureeSession" ) ) ;
  		$param['cw'] = "WHERE last<'".$date->getDatetime()."' AND idapplication=".IDAPPLICATION ;
   		$res = $this -> xham -> Execute ( "Fichier", "getSessionsActuelles", $param, "ResultQuery" ) ;
   		//eko ( $res['INDIC_SVC'] ) ;
  	}
    for ( $i = 0 ; isset ( $res['idsession'][$i] ) ; $i++ ) $this->saveOld ( $res, $i ) ; 
  }
  

  
  
  
  //Renoie vraie si la derniere ip de connexion du login est l'ip courante
  function isSameIpThanLast($login) {
  	$currentIp = $_SERVER['REMOTE_ADDR'];
  	//Recuperation d'une ligne de session valide avec l'user courant
  	$date = new clDate ( ) ;
  	$date -> addMinutes ( - $this->xham->getOption ( "DureeSession" ) ) ;
  	//eko(whereAmI(),true);
  	$param['cw'] = "WHERE last > '".$date->getDatetime()."' AND uid = '$login' AND idapplication=".IDAPPLICATION." order by last desc "  ;
   	$res = $this -> xham -> Execute ( "Fichier", "getSessionsActuelles", $param, "ResultQuery" ) ;
   	//si pas de données présentes, pas de comparaison possible, donc vrai.
	if($res['INDIC_SVC'][2] == 0)
		return true;
  	//sinon on prend la derniere
  	$lastIp = $res['ip'][0];
  	//eko($lastIp);
  	//eko($_SERVER['REMOTE_ADDR']);
  	return ($currentIp == $lastIp);
  }
  
}

?>