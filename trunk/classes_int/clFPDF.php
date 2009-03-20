<?php

// Titre  : Classe FPDF
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 03 Mars 2005

// Description : 
// Cette classe est une extension de la classe fpdf.
// On définit des fonctions utiles...


class clFPDF extends FPDF {

public $footerOn ;

function Header ( ) {
    global $options ;
    global $tab ;
	
   
	$this -> Image ( URLIMGLOGO, 15, 10, 45, 45 ) ;
   	reset ( $tab ) ;
    	list ( $key, $service ) = each ( $tab ) ;
		// Génération de la partie Service.
    	$this -> SetLeftMargin ( $options->getOption ( "Documents MG" ) ) ;
    	$this -> SetTopMargin ( $options->getOption ( "Documents MH" ) ) ;
        $this -> setxy ( 65, 10 ) ;
    	$this -> SetFont ( 'times', 'B', 12 ) ;
    	list ( $key, $val ) = each ( $service ) ;
    	$this -> Cell ( 0, 5, $val, 1, 2, C ) ;
    	$this -> SetFont('times','',8);
    	while ( list ( $key, $val ) = each ( $service ) ) { 		
     		$this -> setx ( 65 ) ;
      		if ( eregi ( "affichercodebarre", $val ) ) { 
      			$tabcbarre = explode ( '|', $val ) ;
      			$this -> setx ( 100 ) ;
      			$this -> Cell ( 0, 15, "N° FINESS : ", 0, 0, L ) ;
      			
      			$this -> SetFont ( 'code39h48', '', 14 ) ;
      			$this -> setx ( 116 ) ;
      			$this -> Cell ( 0, 24, $tabcbarre[1], 0, 1, L ) ;
      			$this -> SetFont ( 'times', 'B', 12 ) ;
      		}
      		else $this -> Cell ( 0, 4, $val, 0, 1, C ) ;
      		
    	}
    	reset ( $service ) ;
    	// Décalage entête.
    	$this -> Cell ( 0, 35, "", 0, 1, 0 ) ;
    	// Colonne de gauche.
    	while ( list ( $key_sous_bloc, $val_sous_bloc ) = each ( $tab ) ) { 		
      		$this -> SetFont ( 'times', 'B', 10 ) ;
      		$this -> Cell ( 45, 5, $key_sous_bloc, R, 1, L ) ;
      		$this -> SetFont ( 'times', '', 8 ) ;
      		while ( list ( $key, $val ) = each ( $val_sous_bloc ) ) {
      				//eko ( $val ) ;
      		 		if ( eregi ( '<i>' , $val ) ) $this -> SetStyle ( "I", true ) ;
					if ( eregi ( '<b>' , $val ) ) $this -> SetStyle ( "B", true ) ;
					if ( eregi ( '<u>' , $val ) ) $this -> SetStyle ( "U", true ) ;
					if ( eregi ( '<br>', $val ) ) $val = '' ;
					$this -> Cell ( 45, 3, ereg_replace ( "<[uibUIB]>", "", $val ), R, 1, L ) ;
					if ( eregi ( '<i>' , $val ) ) $this -> SetStyle ( "I", false ) ;
					if ( eregi ( '<b>' , $val ) ) $this -> SetStyle ( "B", false ) ;
					if ( eregi ( '<u>' , $val ) ) $this -> SetStyle ( "U", false ) ;
      			}
      		reset ( $val_sous_bloc ) ;
      		$this -> Cell ( 45, 5, " ", R, 1, L ) ;
    		}
    	reset ( $tab ) ;
	$this -> SetLeftMargin ( 65 ) ;
	if(IDAPPLICATION != "2") {
    		$this -> setxy ( 150 , 50 ) ;
    		$now   = date ( $options->getOption ( "Documents Date" ) ) ;
    		$ville = $options->getOption ( "Location Documents" ) ;
    		$this -> SetFont ( 'times', '', 12 ) ;
    		$this -> Cell ( 0, 5, "$ville, le $now", 0, 1, L ) ;
    		$this -> setxy ( 65, 75 ) ;
	}else {
		$this -> setxy ( 65, 40 ) ;
		}
  	}

   //generation de l'entete pour tegeria, qui a des documents de plus d'une page
   function gen_entete(){
   	global $tab ;
	global $options ;
       	reset ( $tab ) ;
    	$this -> SetLeftMargin ( 65 ) ;
	$this -> SetTopMargin ( 0 ) ;
	
    	$this -> setxy ( 150 , 30 ) ;
    	$now   = date ( $options->getOption ( "Documents Date" ) ) ;
    	$ville = $options->getOption ( "Location Documents" ) ;
    	$this -> Cell ( 0, 5, "$ville, le $now", 0, 1, L ) ;
    	$this -> setxy ( 65, 40 ) ;
   	}
	
  	
	
  // Génération du code pour le fichier PDF.
  function WriteHTML ( $html ) {
     global $tab ;
     global $options ;
    if(IDAPPLICATION == "2") $this->gen_entete();
    // Parseur HTML
    $html = str_replace ( "\n", ' ', $html ) ;
    $a = preg_split ( '/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE ) ;
    foreach ( $a as $i => $e ) {
      if ( $i % 2 == 0 ) {
	// Texte.
	if ( $this->HREF )
	  $this -> PutLink ( $this->HREF, $e ) ;
	else
	  $this -> Write ( 5, $e ) ;
      } else {
	// Balise.
	if ( $e{0} == '/' )
	  $this -> CloseTag ( strtoupper ( substr ( $e, 1 ) ) ) ;
	else {
	  // Extraction des attributs.
	  $a2 = explode ( ' ', $e ) ;
	  $tag = strtoupper ( array_shift ( $a2 ) ) ;
	  $attr = array ( ) ;
	  foreach ( $a2 as $v )
	    if ( ereg ( '^([^=]*)=["\']?([^"\']*)["\']?$', $v, $a3 ) )
	      $attr[strtoupper ( $a3[1] )] = $a3[2] ;
	  $this -> OpenTag ( $tag, $attr ) ;
	}
      }
    }
  }

 function OpenTag ( $tag, $attr ) {
    global $actions;
 
    // Balise ouvrante
    if ( $tag == 'B' or $tag == 'I' or $tag == 'U' )
      $this -> SetStyle ( $tag, true ) ;
    if( $tag == 'A' )
      $this->HREF = $attr['HREF'] ;
    if( $tag == 'BR' )
      $this -> Ln ( 5 ) ;
      
    //ajout 16/08/2005 Emmanuel Cervetti
    //va chercher une variable globale dans la classe FoRmX
    
    if(($tag == 'FORMX') && isset($actions) )  {
    	$this->Write(5,$actions->getVar($attr['VAR']) );
	}
     }
	
  function CloseTag ( $tag ) {
    // Balise fermante 
    if ( $tag == 'B' or $tag == 'I' or $tag == 'U' )
      $this -> SetStyle ( $tag, false ) ;
    if ( $tag == 'A' )
      $this->HREF='' ;
  }
	
  function SetStyle ( $tag, $enable ) {
    // Modifie le style et sélectionne la police correspondante
    $this->$tag += ( $enable ? 1 : -1 ) ;
    $style = '' ;
    foreach ( array ( 'B', 'I', 'U' ) as $s )
      if ( $this->$s > 0 )
	$style .= $s ;
    $this -> SetFont ( '', $style ) ;
  }
	
  function PutLink ( $URL, $txt ) {
    // Place un hyperlien
    $this -> SetTextColor ( 0, 0, 255 ) ;
    $this -> SetStyle ( 'U', true ) ;
    $this -> Write ( 5, $txt, $URL ) ;
    $this -> SetStyle ( 'U', false ) ;
    $this -> SetTextColor ( 0 ) ;
  }
  
  
    function Footer ( ) {
    	if ( $this->footerOn ) {
    		global $options ;
    		$this -> SetLeftMargin ( 0 ) ;
    		$l1 = $options -> getOption ( "BonFooter1" ) ;
    		$l2 = $options -> getOption ( "BonFooter2" ) ;
    		$l3 = $options -> getOption ( "BonFooter3" ) ;
    		// Positionnement à 1,5 cm du bas
    		$this->SetY(-21);
    		//Police Arial italique 8
    		$this->SetFont('Arial','B',8);
    		//Numéro de page
    		$this->Cell(0,0,$l1,0,0,'C');
    		$this->SetY(-18);
    		$this->Cell(0,0,$l2,0,0,'C');
    		$this->SetY(-15);
    		$this->Cell(0,0,$l3,0,0,'C');
    	}
	}
   
  /*
  function Footer()
{
    if(IDAPPLICATION == "2") {
    //Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    //Police Arial italique 8
    $this->SetFont('Arial','I',8);
    //Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}
    */

  
  

}

?>