<?php
/*XhamUser.php
 * Created on 7 juin 2006
 * Author : Emmanuel Cervetti ecervetti@ch-hyeres.fr
 * Version 0.1A
 */
 
abstract class XhamUserAbstract  {
 
 //tableau contenant les variables propres au user chargées au login
 public  $informations;
 //instance xham à laquelle le user est lié est qui doit être passée par le constructeur
 protected  $xham;
 //liste des groupes (au sens xham du terme) auquel appartient le user, séparés par une virgule
 protected $lg;
 
 function __construct($xhamInstance) {
 $this->xham = $xhamInstance;	
 }
 
 //connecte un user avec les données de connexion définies dans l'argument
 abstract function connect() ;
 //déconnecte l'user en cours
 abstract function disconnect() ;
//operations effectuées sur un user lors de chaque clic dans une même session
 abstract function reconnect() ;
 
 //--------------------------------
 //methodes non abstraites
 //--------------------------------

 // Retourne le tableau qui contient les informations de l'utilisateur.
 function getInformations ( ) {
 	return $this->informations ;
 }
 
 function get($opt) {
 	return $this->informations[$opt];
 }
 
	// Retourne vrai si l'utilisateur est un superAdmin (il a tout les droits).
	function getSuperAdmin ( ) {
		if ( isset ( $this->informations['superadmin'] ) AND $this->informations['superadmin'] )
			return true ;
		else return false ;
	}

	// Retourne la liste des identifiants des groupes XHAM auxquels l'utilisateur appartient.
	function getIdGroupes ( ) {
		return $this->informations['idgroupe'] ;
	}
	
  	function getLogin ( ) {
  		return $this->informations['iduser'];
  	}
 
 	function getNom ( ) {
 		return $this->informations['nom'];
 	} 
 
 	function getPrenom ( ) {
 		return $this->informations['prenom'];
	}
	
	function getIdentite ( ) {
		$s = $this->informations['prenom']." ".$this->informations['nom'];
		if ( $s == " " )
			$s = $this->informations['pseudo'];
		return $s ;
	}
	
	function getType ( ) {
		return $this->informations['type'] ;
	}
 
 	function getNavigateur ( ) {
 		return $this->informations['navigateur'] ;
 	}
 	
 	function getIP ( ) {
 		return $this->informations['ip'] ;
 	}
 	
 	function getFonctions ( ) {
 		return $this->informations['fonctions'] ;
 	}
 	
 	function getServices ( ) {
 		return $this->informations['service'] ;
 	}
 	
 	function getOrgs ( ) {
 		return $this->informations['org'] ;
 	}
 	
 	function getPassword ( ) {
 		return XhamTools::dechiffre ( $this->informations['password'] ) ;
 	}

  //Récupère les groupes définis dans la base xham
  function getGroupe ( $nom ) {
    $param['cw'] = "where nomgroupe='$nom'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getGroupes", $param, "ResultQuery" ) ;
    if ( $res['INDIC_SVC'][2] ) {
      if ( $this->lg ) $this->lg .= ",".$res['idgroupe'][0] ;
      else $this->lg = $res['idgroupe'][0] ;
    }
  } 
  
}
 
 
?>