<?php

// Titre  : Classe Navigation
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 19 Janvier 2005

// Description : 
// Cette classe affichage la bonne page en fonction de la position de l'utilisateur.
// Les informations de positionnement se trouvent dans l'objet $session.


class clNavigation {

  // Attribut contenant l'affichage.
  private $af ;

  function __construct ( ) {
    global $session ;
    global $options ;
    global $errs ;
    global $pi ;
    if ( isset ( $_REQUEST['reloadAuto'] ) ) {
//	header("Cache-Control: no-cache, must-revalidate");
	header ( "Location:index.php?navi=".$session->genNaviFull ( ) ) ;	    
    }
    $aucunAffichage = 0 ;
    $pi -> addMove ( "miniMessagerie" ) ;
    $param['cw'] = "WHERE cletotale='".addslashes(stripslashes($session->getNavi(0)."|".$session->getNavi(1)))."' AND idapplication=".IDAPPLICATION ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;  
    if ( ! $res['INDIC_SVC'][2] ) {
      $param['cw'] = "WHERE cletotale='".addslashes(stripslashes($session->getNavi(0)))."' AND idapplication=".IDAPPLICATION ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;  
    }
    
    if ( $res['classe'][0] ) {
      $code = '$classe = new '.$res['classe'][0].' ( '.$res['arguments'][0].' ) ;' ;
      $code .= '$this->af .= $classe->getAffichage ( ) ;' ;
    } elseif ( $res['code'][0] ) {
      $code = $res['code'][0] ;
    } else {
      $aucunAffichage = 1 ;
      $this->af .= "Aucun affichage trouvé pour la page ".$session->getNaviFull() ;
      $errs -> addErreur ( "clNavigation : Aucun affichage trouvé pour la page ".$session->getNaviFull() ) ;

    }
   
    if ( ! $res['noption'][0] OR ( ( $options -> getOption ( $res['noption'][0] ) == $res['valeur'][0] ) ) ) {
      if ( $session -> getDroit ( $res['droit'][0], "r" ) ) {
	  	eval ( $code ) ;
	    if ( $session -> getUid ( ) == "dborel" ) {
      		global $stopAffichage ;
      		if ( ! $stopAffichage ) {
      			//$this->af .= file_get_contents ( "../pifgadget/fabien.html" ) ;
      			//$this->af .= file_get_contents ( "../pifgadget/cyril/cyril.html" ) ;
      		}
    	}
      } else {
	if ( ! $aucunAffichage )
	  $this->af .= "Vos droits ne vous permettent pas d'afficher la page demandée (".$session->getNaviFull().")" ;
      }
    } else $this->af .= "Cette partie a été désactivée, vous ne pouvez pas l'afficher." ;

  }
  
  // On renvoie la variable contenant l'affichage de la page.
  function getAffichage ( ) {
    global $session ;
    global $nbRequetes ;
    global $tpRequetes ;
    if ( $session->getUid ( ) == "" OR $session->getUid ( ) == "" ) {
      $messagerie = new clMessagerie ( ) ;
      //eko ( "Tu es dborel !" ) ;
      $this->af .= $messagerie -> getAffichage ( ) ;
    }
    if ( DEBUGSQL ) eko ( "$nbRequetes requêtes en ".$tpRequetes."s" ) ;
    return $this->af ;
  }

}

?>
