<?php

// Titre  : Classe GestionNotes
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 28 Aout 2007

// Description :
// Gestion et affichage des notes.

class XhamNotes {
  
  	private $af ;
  
  	// Constructeur de la classe.
  	function __construct ( $xham, $ajax='', $droit='' ) {
    	global $session ;
    	$this->xham = $xham ;
    	$this->ajax = $ajax ;
    	if ( $droit ) {
    		if ( $xham -> getDroit ( $droit ) ) $this->droit = 1 ;
    		else $this->droit = 0 ; 
    	} else $this->droit = 1 ;
    	$this->genAffichage ( ) ;
  	} 
   
  	function genAffichage ( ) {
		
    	// En fonction du niveau 2 de la variable navi, on choisit l'action...
    	switch ( $this->xham->getNavi ( 1 ) ) {
	    	// Modification de la news.
	    	case 'majNote' :
	   			$this->majNote ( ) ;   
	      	break ;
	    	default :
	      		//$this->af .= $this->afficherNote ( ) ;
	      	break ;
    	}
  	}

	// 
	function majNote ( ) {
		if ( $this->droit ) {
			// $session->setLogSup ( 'Mise à jour du bloc-notes' ) ;
      		$data['note'] = utf8_decode( stripslashes ( $_REQUEST['note'] ) ) ;
      		$requete = new XhamRequete ( BASEXHAM, 'notes', $data ) ;
      		$requete -> uoiRecord ( "ids='".$this->xham->getNavi(2)."'" ) ;
		}
	}
	
	// 
	function afficherNote ( $ids, $div='note', $style='' ) {
		$req = new clResultQuery ;
    	$param['cw'] = "WHERE ids='$ids'" ;
    	$param['table'] = 'notes' ;
    	$res = $req -> Execute ( "Fichier", "getGenXHAM", $param, "ResultQuery" ) ;
    	//eko ( $res ) ;
    	if ( $res['INDIC_SVC'][2] > 0 )	$message = $res['note'][0] ;
    	$mod = new ModeliXe ( "note.html" ) ;
    	$mod -> SetModeliXe ( ) ;
    	$mod -> MxText ( "id", $div ) ;
    	if ( ! $this->droit ) { 
    		$mod -> MxBloc ( 'droit', 'replace', '<div id="text'.$div.'">' ) ;
    		$mod -> MxBloc ( 'droitJ', 'delete' ) ;
    	} else {
    		 $mod -> MxText ( "droit.id", $div ) ;
    		 $mod -> MxText ( "droitJ.id", $div ) ;
    		 $mod -> MxText ( "droitJ.navi", $this->xham->genNavi ( 'ajax', 'majNote', $ids ) ) ;
    	}
    	$mod -> MxText ( "contenu1", nl2br($message) ) ;
    	$mod -> MxText ( "contenu2", $message ) ;
    	//$mod -> MxText ( "navi", $this->xham->genNavi ( 'ajax', 'majNote', $ids ) ) ;
    	//$mod -> MxHidden ( "hidden1", "navi=".$session -> genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
    	return $mod -> MxWrite ( "1" ) ;  
	}

  	// Retourne l'affichage généré par la classe.
  	function getAffichage ( ) {
    	if ( $this->ajax ) {
			$this->xham->stopAffichage = 1 ;
			print $this->af ;
		} else return $this->af ;
  	}
}

?>
