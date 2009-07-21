<?php
//
// * Création : 3 Septembre juin 2007
// *   Auteur : Damien Borel <dborel@ch-hyeres.fr>
// *    Titre : Génération de fichiers RSS
//

 
class RSS {

	function __construct ( $nomSite='', $urlSite='', $descr='', $image='', $date='', $language='fr-FR' ) {
		$this->nomSite  = $nomSite ;
		$this->urlSite  = $urlSite ;
		$this->descr    = $descr ;
		$this->image    = $image ;
		$this->language = $language ;
		$this->lastPub  = $date ;
		$this->init ( ) ;
	}
	
	function setNomSite  ( $val='' ) { $this->nomSite  = $val ; }
	function setUrlSite  ( $val='' ) { $this->urlSite  = $val ; }
	function setDescr    ( $val='' ) { $this->descr    = $val ; }
	function setImage    ( $val='' ) { $this->image    = $val ; }
	function setLanguage ( $val='' ) { $this->language = $val ; }
	function setLastPub  ( $val='' ) { $this->lastPub  = $val ; }

	function init ( ) {
		$this -> mod = new ModeliXe ( "rss.html" ) ;
	    $this -> mod -> SetModeliXe ( ) ;
	    $date = new clDate ( $this->lastPub ) ;
	    $this -> mod -> MxText ( 'titre', $this->nomSite ) ;
	    $this -> mod -> MxText ( 'description', $this->descr ) ;
	    $this -> mod -> MxText ( 'dateL', $date -> getDateRSS ( ) ) ;
	    //$this -> mod -> MxText ( 'dateLdc', $date -> getDateRSSDC ( ) ) ;
	    $this -> mod -> MxText ( 'lien', $this->urlSite ) ;
	    $this -> mod -> MxText ( 'language', $this->language ) ;
	    if ( $this->image ) $this -> mod -> MxText ( 'image.image', $this->image ) ;
	    else $this -> mod -> MxBloc ( 'image', 'delete' ) ;
	}
	
	function addItem ( $titre='', $description='', $date='', $lien='', $auteur='', $categorie='', $comments='' ) {
		$this -> mod -> MxText ( 'item.titre', strip_tags($titre) ) ;
		$this -> mod -> MxText ( 'item.description', strip_tags($description) ) ;
		$dateR = new clDate ( $date ) ;
		$this -> mod -> MxText ( 'item.date', $dateR->getDateRSS ( ) ) ;
		//$this -> mod -> MxText ( 'item.datedc', $dateR->getDateRSSDC ( ) ) ;
		$this -> mod -> MxText ( 'item.lien', $lien ) ;
		$this -> mod -> MxText ( 'item.auteur', $auteur ) ;
		$this -> mod -> MxText ( 'item.categorie', $categorie ) ;
		if ( $comments ) $this -> mod -> MxText ( 'item.comments.comments', $comments ) ;
		else $this -> mod -> MxBloc ( 'item.comments', 'delete' ) ;
		$this -> mod -> MxBloc ( 'item', 'loop' ) ;
	}	
	
	// Récupération du contenu XML généré.
	function getRSS ( ) {
		return $this -> mod -> MxWrite ( "1" ) ;
	}
}
?>
