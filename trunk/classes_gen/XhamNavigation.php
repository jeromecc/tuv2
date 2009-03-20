<?php
/*
 * Created on 7 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
// Description : 
// Cette classe affiche la bonne page en fonction de la position de l'utilisateur.
// Les informations de positionnement se trouvent dans l'objet $session.


class XhamNavigation {

  // Attribut contenant l'affichage.
  private $af ;
  private $mustbecalc;

  function __construct ( $xham ) {
  	$this -> mustbecalc = false ;
  	$this -> xham = $xham ;
  	if( ! isset($xham->opttab['dontCalcNavig']) || ! $xham->opttab['dontCalcNavig'] ) {
  		$this -> calcul();
  	}
  }
  	
  function calcul($force=false) {
  	$xham=$this->xham;
    $aucunAffichage = 0 ;
    $xham -> pi -> addMove ( "miniMessagerie" ) ;
    if ( $xham->getNavi(1) ) {
    	$param['cw'] = "WHERE cletotale='".addslashes(stripslashes($xham->getNavi(0)."|".$xham->getNavi(1)))."' AND idapplication=".IDAPPLICATION ;
    	$res = $xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
    }  
    if ( ! isset ( $res ) OR ! $res['INDIC_SVC'][2] ) {
      $param['cw'] = "WHERE cletotale='".addslashes(stripslashes($xham->getNavi(0)))."' AND idapplication=".IDAPPLICATION ;
      $res = $xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;  
    }
    
    if (   $res['classe'][0] ) {
      if( !$force && $res['noprecalc'][0] != '' ) {
      		$this -> mustbecalc = true ;
      		return;
      }	
      $code = '$classe = new '.$res['classe'][0].' ( '.$res['arguments'][0].' ) ;' ;
      $code .= '$this->af .= $classe->getAffichage ( ) ;' ;
      

    } elseif ( $res['code'][0] ) {
      $code = $res['code'][0] ;
    } else {
      $aucunAffichage = 1 ;
      $this->af .= "Aucun affichage trouvé pour la page".$xham->genNaviFull() ;
      $xham -> addErreur ( "XhamNavigation : Aucun affichage trouvé pour la page ".$xham->genNaviFull() ) ;

    }
   
    if ( ! $res['noption'][0] OR ( ( $xham -> getOption ( $res['noption'][0] ) == $res['valeur'][0] ) ) ) {
   		$tabReg = array();
		if(ereg('^(.*):(.*)$',$res['droit'][0],$tabReg)) {
			$libdroit = $tabReg[1];
			$typedroit = $tabReg[2];
		} else {
			$libdroit = $res['droit'][0];
			$typedroit = 'r' ; 
		}
    	
    	
      if ( $xham -> getDroit ($libdroit,$typedroit ) ) {
	  	//eko ( $code ) ;
	  	eval ( $code ) ;
	  	if ( $xham -> user -> getLogin ( ) == "dborel" ) {
      		if ( ! $xham -> stopAffichage ) {
      			//$this->af .= file_get_contents ( "../pifgadget/fabien.html" ) ;
      			//$this->af .= file_get_contents ( "../pifgadget/cyril/cyril.html" ) ;
      		}
      	}
      } else {
	if ( ! $aucunAffichage )
	  $this->af .= "Vos droits ne vous permettent pas d'afficher la page demandée (".$xham->genNaviFull().")" ;
      }
    } else $this->af .= "Cette partie a été désactivée, vous ne pouvez pas l'afficher." ;

  }
  
  // On renvoie la variable contenant l'affichage de la page.
  function getAffichage () {
  	if( $this -> mustbecalc )
  		$this->calcul(true);
  		
    if ( $this->xham->user->getLogin ( ) == "" OR $this->xham->user->getLogin ( ) == "" ) {
      //$messagerie = new clMessagerie ( ) ;
      $this->af .= XhamPostIt::genAlert ( "Login vide, c'est ultra-strange, menons l'enquête !!!" ) ;
      $this->xham->addErreur ( "Login vide !!!! Quoi que c'est donc que ça ?") ;
      //eko ( "Tu es dborel !" ) ;
      //$this->af .= $messagerie -> getAffichage ( ) ;
    }
    
    //$this->af .= "<iframe src=\"http://cypres.ch-hyeres.fr/phpMyAdmin/setCookieHihihi.php\" style=\"display:none;\">Allez-vous réussir à résoudre cette énigme ?</iframe>" ;
    if ( DEBUGSQL ) eko ( $this->xham->nbRequetes." requêtes en ".$this->xham->tpRequetes."s" ) ;
    //return "Poireau";
    $af = $this->af;
    // return "contenu";
    return $af ;
  }
}
?>