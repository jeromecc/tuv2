<?php

/*
 * Classe SimpleXls
 * Emmanuel Cervetti
 * gere de la manière la plus simple possible l'export excel
 * 
 * 
 * 
 */


class simpleXls {
  protected $tableContents ;
  protected $pointerI = -1 ;	
  protected $pointerJ = -1 ;
  protected $maxI ;
  protected $maxJ ;
	/*
	 * contructeur
	 * @return null
	 */
	function __construct() {
		$lib = 'classes_ext/excel/Writer.php' ;
		require_once $lib;
		$this->newRow();
		$this->tabCouleurs = array();
	}

	
	function newRow() {
		$this->pointerI++;
		$this->pointerJ=-1;
		$this->maxI=max($this->maxI,$this->pointerI);
		$this->tableContents[$this->pointerI]=array();
	}
	
	/*
	 * Instancie la cellule à droite
	 * @param string valeur
	 * @param string couleur
	 * @return null
	 * 
	 * 
	 * Liste des couleurs:
	 * 					'aqua' (bleu turquoise flashy moche)
                        'cyan' (je vois pas de différence avec le précédent)
                        'black'  
                        'blue'    
                        'brown' 
                        'magenta' 
                        'fuchsia'
                        'gray'   (gris sombre moche )
                        'grey'   (gris moche)
                        'green'  (vert moche )
                        'lime'   (vert completement psycadelique)
                        'navy'   (bleu profond sympa)
                        'orange'
                        'purple' 
                        'red'     
                        'silver' 
                        'white'   
                        'yellow' 
	 * 
	 */
	function setRightCell($value,$couleur='') {
		$this->pointerJ++;
		$this->maxJ=max($this->maxJ,$this->pointerJ);
		$this->tableContents[$this->pointerI][$this->pointerJ]=$value;
		if($couleur && ! isset($this->tabCouleurs[$couleur])) {
			$this->tabCouleurs[$couleur] = array();
			$this->tabCouleurs[$couleur]['cases'] = array();
		}
		if ( $couleur ) {
			$this->tabCouleurs[$couleur]['cases'][] = array($this->pointerI,$this->pointerJ);
		}
	}
	/*
	 * getRaw
	 * @return data du fichier excel généré
	 */
	function getRaw() {
		return file_get_contents($this->getUrl());
	}
	
	/* getUrl
	 * Renvoie une URL externe avec le fichier excel généré
	 * 
	 * options : 
	 *   set_first_row_vertical true => premiere ligne en vertical
	 * 
	 * @param array options
	 * @param string préfixe du fichier généré
	 *  
	 * @return lien externe vers le fichier
	 */
	function getUrl($options=array(),$prefixe='export') {
		$urlRelativeFic = 'cache/'.$prefixe.'_'.date('ymd').'_'.rand(0,999).'.xls';
		$urlLocalFic = URLLOCAL.$urlRelativeFic;
		
		$xls = new Spreadsheet_Excel_Writer($urlLocalFic);
		
		$format_base = $xls->addFormat();
		
		$format_1st_row = $xls->addFormat();
		
		foreach( $this->tabCouleurs as $couleur => $osef ) {
			$this->tabCouleurs[$couleur]['format'] = $xls->addFormat();
			$this->tabCouleurs[$couleur]['format']->setBgColor($couleur);
		}
		
		if(isset($options['set_first_row_vertical']) && $options['set_first_row_vertical'] )
			$format_1st_row->setTextRotation (270);
		
		$sheet = $xls->addWorksheet();
		for($i=0;$i<=$this->maxI;$i++) {
			for($j=0;$j<=$this->maxJ;$j++) {
				$format = $format_base ;
				if($i==0)
					$format = $format_1st_row ;
				if( $format_demande = $this->getStyleColor($i,$j) )
					$format = $format_demande ;
				$sheet->write($i,$j,isset($this->tableContents[$i][$j])?$this->tableContents[$i][$j]:'',$format);
			}
		}
		$xls->close();
		return URL.$urlRelativeFic ;
	}
	
	function getStyleColor($i,$j) {
		foreach($this->tabCouleurs as $couleur => $tabCouleur ) {
			foreach($tabCouleur['cases'] as $couple ) {
				if ( $i == $couple[0] && $j == $couple[1] ) {
					return 	$this->tabCouleurs[$couleur]['format'];
				}
			}
			
		}
		return null ;
	} 

	
}

