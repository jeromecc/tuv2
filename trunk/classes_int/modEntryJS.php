<?php
/*
 * Created on 20 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
 // Description :
 // Cette classe gère l'affichage des formulaires de gestion des organisations
 // de l'annuaire LDAP.
 
 class modEntryJS {
 	
 	// Affichage généré par la classe.
 	private $af ;
 	
 	// Constructeur de la classe.
 	function __construct ( ) {
 		global $stopAffichage ;
 		$stopAffichage = 1 ;
		$this->genFormModificationJavascript ( ) ;
 	}
 	

 	
 	// Génération du javascript pour le status meters de modification.
 	function genFormModificationJavascript ( ) {

 		// Chargement du template ModeliXe.
    	$mod = new ModeliXe ( "modEntryJS.html" ) ;
    	$mod -> SetModeliXe ( ) ;    
    	$sup = '' ;	 		
 		switch ( $_SESSION['typeModJS'] ) {

 		}
 		
 		$mod -> MxText ( "ajax", $sup ) ;
    	$mod -> MxText ( "chargement", '' ) ;
    	//$mod -> MxText ( "chargement", "alert('coucou');" ) ;
    	print $mod -> MxWrite ( "1" ) ;
 	}
 	
 	function getAffichage ( ) { }
 	
 }
 
?>