<?php
/*
 * Created on 7 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
 // Description : 
// Cette classe r�cup�re les valeurs des options du terminal.
// Ces valeurs sont accessibles via une m�thode afin de savoir, 
// par exemple, si un module du terminal est activ�.

class XhamOptions {

  	// D�claration des attributs de la classe.
  	// Tableau associatif contenant toutes les options de l'application.
  	private $options ;

  	// Constructeur de la classe.
  	function __construct ( $xham ) {
    	$this->xham = $xham ;
    	$this->setOptions ( ) ;
  	}

  	// Retourne la valeur d'une option.
  	function getOption ( $nom ) { 
    	if ( DEBUGOPTION ) eko ( "<br />Option appel�e : \"$nom\", valeur : \"".$this->options["$nom"]."\"<br />" ) ;
    	return (isset($this->options["$nom"])?$this->options["$nom"]:'') ; 
  	}

  	// Initialisation des options.
  	function setOptions ( ) {
    	// Requ�te pour r�cup�rer les informations actuelles de l'option.
    	$param['cw'] = "WHERE idapplication=".IDAPPLICATION ;
    	$res = $this -> xham -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;  
    	// Fabrication du tableau contenant la valeur des options de l'application.
    	for ( $i = 0 ; isset ( $res['idoption'][$i] ) ; $i++ ) {
    		$this->options[$res['libelle'][$i]] = $res['valeur'][$i] ;
    	}
    	if ( DEBUGOPTION ) { affTab ( $this->options ) ; }
  	}
  	
  	//modifie la valeur d'une option qui existe d�j�
  	function modifOption($lib,$value) {
  		$data['valeur'] = $value ;
      	$requete = new XhamRequete ( BASEXHAM, "options", $data ) ;
      	$res = $requete->updRecord ( "libelle='$lib' AND idapplication=".IDAPPLICATION ) ;
      	//Rechargement des valeurs pour getOption
      	$param['cw'] = "WHERE idapplication=".IDAPPLICATION ;
    	$res = $this -> xham -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;  
      	$this->setOptions();
  	}
  	

	// V�rifie si une option d'une liste (classes clListes et clListesGenerales) existe ou non.
  	// Si elle n'existe pas, elle est cr��e au passage.
  	function checkOptionListe ( $option, $complexe='' ) {
    	$param['cw'] = "WHERE libelle='".addslashes(stripslashes($option))."' AND idapplication=".IDAPPLICATION ;
    	$res = $this->xham -> Execute ( "Fichier", "getOptions", $param, "ResultQuery" ) ;  
    	if ( ! $res['INDIC_SVC'][2] ) {
      		if ( $complexe ) {
				$data['libelle']     = $option ;
				$data['description'] = "Classement de la liste '$option'." ;
				$data['type']        = "combobox" ;
				$data['choix']       = "Manuel|Alphab�tique|Alphab�tique invers�" ;
				$data['valeur']      = "Manuel" ;
				$data['categorie']   = $option ;
				$data['idapplication'] = IDAPPLICATION ;
				$req = new XhamRequete ( BASEXHAM, TABLEOPTS, $data ) ;
				$ris = $req -> addRecord ( ) ;

				$data['libelle']     = "Cat�gories ".$option ;
				$data['description'] = "Classement de la liste des cat�gories de '$option'." ;
				$data['type']        = "combobox" ;
				$data['choix']       = "Manuel|Alphab�tique|Alphab�tique invers�" ;
				$data['valeur']      = "Manuel" ;
				$data['categorie']   = $option ;
				$data['idapplication'] = IDAPPLICATION ;
				$req = new XhamRequete ( BASEXHAM, TABLEOPTS, $data ) ;
				$ris = $req -> addRecord ( ) ;

				$data['libelle']     = "Lignes ".$option ;
				$data['description'] = "Nombre de lignes dans les listes de gestion de '$option'." ;
				$data['type']        = "combobox" ;
				$data['choix']       = "5|10|15|20|25|30" ;
				$data['valeur']      = "15" ;
				$data['categorie']   = $option ;
				$data['idapplication'] = IDAPPLICATION ;
				$req = new XhamRequete ( BASEXHAM, TABLEOPTS, $data ) ;
				$ris = $req -> addRecord ( ) ;
      		} else {
				switch ( $option ) {
					case 'LignesParListe':
	  					$data['libelle']     = "LignesParListe" ;
	  					$data['description'] = "Nombre de lignes par liste dans la partie administration." ;
	  					$data['type']        = "combobox" ;
	  					$data['choix']       = "3|4|5|6|7|8|9" ;
	  					$data['valeur']      = "8" ;
	  					$data['categorie']   = "Listes G�n�rales" ;
	  					$data['idapplication'] = IDAPPLICATION ;
	  					$req = new XhamRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	  					$ris = $req -> addRecord ( ) ;
	  					break;
					case 'ListesParLigne':
	  					$data['libelle']     = "ListesParLigne" ;
	  					$data['description'] = "Nombre de listes par ligne dans la partie administration." ;
	  					$data['type']        = "combobox" ;
	  					$data['choix']       = "3|4|5" ;
	  					$data['valeur']      = "4" ;
	  					$data['categorie']   = "Listes G�n�rales" ;
	  					$data['idapplication'] = IDAPPLICATION ;
	  					$req = new XhamRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	  					$ris = $req -> addRecord ( ) ;
	  					break;
					default:
	  					$data['libelle']     = $option ;
	  					$data['description'] = "Classement de la liste des '$option'." ;
	  					$data['type']        = "combobox" ;
	  					$data['choix']       = "Manuel|Alphab�tique|Alphab�tique invers�" ;
	  					$data['valeur']      = "Manuel" ;
	  					$data['categorie']   = "Listes G�n�rales" ;
	  					$data['idapplication'] = IDAPPLICATION ;
	  					$req = new XhamRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	  					$ris = $req -> addRecord ( ) ;
	  					$data['libelle']     = $option." Id" ;
	  					$data['description'] = "Gestion d'un code rattach� aux items de la liste '$option'." ;
	  					$data['type']        = "bool" ;
	  					$data['choix']       = "" ;
	  					$data['valeur']      = "0" ;
	  					$data['categorie']   = "Listes G�n�rales" ;
	  					$data['idapplication'] = IDAPPLICATION ;
	  					$req = new XhamRequete ( BASEXHAM, TABLEOPTS, $data ) ;
	  					$ris = $req -> addRecord ( ) ;
	  					break ;
				}
      		}
    	}
  	}
}
?>
