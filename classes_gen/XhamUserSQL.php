<?php
/*XhamUser.php
 * Created on 7 juin 2006
 * Author : Emmanuel Cervetti ecervetti@ch-hyeres.fr
 * Author : Damien Borel <dborel@ch-hyeres.fr>
 * Version 0.1A
 */
 
class XhamUserSQL extends XhamUserAbstract  {
 	// Connexion ldap
	protected $conne;

	//---------------------------------------------
	//    METHODE D'ACCES AUX ATTRIBUTS PRIVES
	//---------------------------------------------
 
 	// Diverses fonctions de recupération d'informations
 	function getName ( ) {
		return $this->informations['nom'] ;
	}
	function getPrenom ( ) {
		return $this->informations['prenom'] ;
	}
	
 	function getTel ( ) {
 		return $this->informations['tel'] ;
	}
	
	function getId () {
		return $this->informations['id'];
	}

	//---------------------------------------------
	//              RECUPERATIONS LDAP
	//---------------------------------------------
 	// Connecte un user avec les données de connexion définies dans l'argument
	function connect ( $valid='' ) {
		
    global $errs ;
    if ( $valid  || $this->xham->getMode() == 'manuel' ) {
      $param['cw'] = "WHERE idutilisateur=".$_POST['iduser'] ;
      $res = $this -> xham -> Execute ( "Fichier", "getUtilisateurs", $param, "ResultQuery" ) ;
    } else {
    	
      $param['password'] = $_POST['password'] ;
      $param['uid'] = $_POST['login'] ;
      $req = new clResultQuery ;
      $res = $this -> xham -> Execute ( "Fichier", "authUtilisateur", $param, "ResultQuery" ) ;
    }
    if ( $res['uid'][0] == $_POST['login'] OR ( $res['idutilisateur'][0] == $_POST['iduser'] AND $_POST['iduser'] ) ) {
      $this->informations['password']   = XhamTools::chiffre($_POST['password']) ;
      $this->informations['type']       = "MySQLInt" ;
      $this->informations['nom']        = $res['nom'][0] ;
      $this->informations['prenom']     = $res['prenom'][0] ;
      $this->informations['pseudo']     = $res['uid'][0] ;
      $this->informations['mail']       = $res['mail'][0] ;
      $this->informations['iduser']     = $res['uid'][0] ;
      $this->informations['fonctions']  = array ( ) ;
      $this->informations['tel']        = '' ;
      $this->informations['mob']        = '' ;
      $this->informations['org']        = '' ;
	  $this->informations['equipes']    = array ( ) ;
	  $this->informations['service']    = array ( ) ;
	  $this->informations['ip']         = $_SERVER['REMOTE_ADDR'] ;
      $this->informations['navigateur'] = $_SERVER["HTTP_USER_AGENT"] ;
      $this->informations['id'] = $res['idutilisateur'][0] ;
      
      $param['cw'] = "WHERE idutilisateur='".$res['idutilisateur'][0]."'" ;
      $res = $this -> xham -> Execute ( "Fichier", "getGroupesUtilisateur", $param, "ResultQuery" ) ;
      $groupes = $res['idgroupe'][0] ;
      for ( $i = 1 ; isset ( $res['idutilisateur'][$i] ) ; $i++ ) {
		$groupes .= ",".$res['idgroupe'][$i] ;
      }
      $this->informations['idgroupe'] = $groupes ;
      return 1 ;
    }
  }
  	
  	
  	
  	

 
 
	//--------------------------------------------------  
	//-----------pour compatibilité avec classe virtuelle
	//--------------------------------------------------
 
 	// Operations effectuées sur un user lors de chaque clic dans une même session
 	function reconnect ( ) { return true ; }
 
 	// Déconnecte l'user en cours
 	// rien à faire
 	function disconnect ( ) { return true ; }
 
  
}
 
 
?>