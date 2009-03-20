<?php
/*
 * Created on 7 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
// Description : 
// Cette classe génère le menu de navigation du site.
// Elle génère de façon brute le code XHTML du menu.
// L'habillage se fait via le fichier CSS.

class XhamMenu {

  	// Attribut contenant l'affichage.
  	private $af ;
  	private $onclick ;
  	private $onclickfull ;

  	// Constructeur du menu.
  	function __construct ( ) {
    	$this->menu = 0 ;
    	if ( defined ( "ONUNLOAD" ) AND ONUNLOAD ) {
    		$this->onclick = "if ( ! ".ONUNLOAD." ) { return false ; } ;" ;
    		$this->onclickFull = 'onclick="if ( ! '.ONUNLOAD.' ) { return false ; } ;"' ;
    	}
    	$this->af .= $this->genMenu ( ) ;
  	}

	// Generation du tableau des menus
    function genTabMenu ( ) {
    	global $xham;
        $param['cw'] = "WHERE etat=1 AND idapplication=".IDAPPLICATION." ORDER BY rang" ;
        $res = $xham -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;
        $tab = array ( ) ;
        for ( $i = 0 ; isset ( $res['idmenu'][$i] ) ; $i++ ) {
                if ( $res['type'][$i] == 'menu' )
                        $tab['menu'][$res['cle'][$i]] = $i ;
                else $tab[$res['menuparent'][$i]][$res['cle'][$i]] = $i ;
        }
        $this->resMenu = $res ;
        $this->tabMenu = $tab ;
    }

  	// Génération du menu.
  	function genMenu ( ) {
  		global $xham ;
    	$this->menuAuth = $this -> getFormulaire ( ) ;
    	$menu = "" ;
    	$this->genTabMenu ( ) ;
        $tab = $this->tabMenu['menu'] ;
        while ( list ( $key, $val ) = each ( $tab ) ) {
                $faux = 0 ;
                if ( $this->resMenu['noption'][$val] )
                	if ( $xham -> getOption ( $this->resMenu['noption'][$val] ) != $this->resMenu['valeur'][$val] ) { eko ( "Option fausse" ) ; $faux = 1 ; }
                //Modif EC pour recherche du droit d'écriture de type lib_droit:droit
				$tabReg = array();
				if(ereg('^(.*):(.*)$',$this->resMenu['droit'][$val],$tabReg)) {
					$libdroit = $tabReg[1];
					$droit = $tabReg[2];
				} else {
					$libdroit = $this->resMenu['droit'][$val] ;
					$droit = 'r' ; 
				}
      			if ( ! $xham -> getDroit ($libdroit, $droit) ) $faux = 1 ;
                if ( $this->resMenu['libelle'][$val] == "Accueil" ) $faux = 0 ;
                if ( ! $faux ) $menu .= $this->genItem ( $this->resMenu['libelle'][$val], $this->resMenu['cle'][$val], $this->resMenu['idunique'][$val] ) ;
        }
    	// On renvoie le menu ainsi construit.
    	return $menu ;
  	}

	// Génération d'un onglet.
  	function genItem ( $nom, $page, $idmenu ) {
    	global $xham ;
    	if ( $xham->getNavi ( 0 ) == $page ) $active = "id=\"active\"" ;
    	else $active = '' ;
    	if ($active )
      		$lienactif = "class='actif'" ;	
      	else
      		$lienactif = "" ;
    	if ( isset ( $this->tabMenu[$idmenu] ) ) {
      		$this->menu++ ;
      		if ( $this->menu == 10 ) $this->menu++ ;
      		$af = "\t<dl id=\"menus".$idmenu."\"><dt $active onmouseover=\"javascript:montre('s".$this->menu."');\"><a id='lien_menu_$page' $lienactif  >$nom</a></dt>\n" ;
      		$af .= "\t<dd id=\"s".$this->menu."\"><ul>\n" ;
      		$tab = $this->tabMenu[$idmenu] ;
            while ( list ( $key, $val ) = each ( $tab ) ) {
            	if ( ! $this->resMenu['noption'][$val] )
            		$af .= $this->genMiniItem ( $page, $this->resMenu['libelle'][$val], $this->resMenu['cle'][$val], $this->resMenu['droit'][$val]  ) ;
            	elseif ($xham -> getOption ( $this->resMenu['noption'][$val] ) == $this->resMenu['valeur'][$val] )
            		$af .= $this->genMiniItem ( $page, $this->resMenu['libelle'][$val], $this->resMenu['cle'][$val], $this->resMenu['droit'][$val]  ) ;
            }
      		$af .= "\t</ul></dd></dl>\n" ;
      		return $af ;

    	} elseif ( $nom == "Accueil" ) {
      		return "\t<dl id=\"menus".$idmenu."\"><dt $active onmouseover=\"javascript:montre('s10');\" ><a id='lien_menu_$page' $lienactif ".$this->onclickfull." href=\"".URLNAVI.$xham->genNavi($page)."\">$nom</a></dt>".$this->menuAuth."</dl>\n" ;
    	} else {
      		return "\t<dl id=\"menus".$idmenu."\"><dt $active onmouseover=\"javascript:montre();\"  ><a id='lien_menu_$page' $lienactif ".$this->onclickfull." href=\"".URLNAVI.$xham->genNavi($page)."\">$nom</a></dt><dd style=\"display:none;\">Vide</dd></dl>\n" ;
    	}
  	}

  	// Génération d'un item dans sous-menu.
  	function genMiniItem ( $pageup, $nom, $page, $droit ) {
    	global $xham ;
    	//Modif EC pour recherche du droit d'écriture de type lib_droit:droit
			$tabReg = array();
			if(ereg('^(.*):(.*)$',$droit,$tabReg)) {
				$libdroit = $tabReg[1];
				$typedroit = $tabReg[2];
			} else {
				$libdroit = $droit;
				$typedroit = 'r' ; 
			}
		//eko("$nom: ".$libdroit.' '.$typedroit,true);
		//if ( $xham->getDroit ( $libdroit, $typedroit) ) eko("ok",true);
    	//if ( $xham->getDroit ( $libdroit, $typedroit) ) return "\t\t<li onclick=\"document.location='".URLNAVI.$xham->genNavi($pageup, $page)."'\"><a href=\"".URLNAVI.$xham->genNavi($pageup, $page)."\">$nom</a></li>\n" ;
    	if ( $xham->getDroit ( $libdroit, $typedroit) ) return "\t\t<li onclick=\"".$this->onclick."document.location='".URLNAVI.$xham->genNavi($pageup, $page)."'\"><a onclick=\"return false;\" href=\"".URLNAVI.$xham->genNavi($pageup, $page)."\">$nom</a></li>\n" ;
    	if ( $xham->getDroit ( $libdroit, $typedroit) ) return "\t\t<li ><a href=\"".URLNAVI.$xham->genNavi($pageup, $page)."\">$nom</a></li>\n" ;
    	//if ( $xham->getDroit ( $libdroit, $typedroit) ) return "\t\t<li onclick=\"document.location='".URLNAVI.$xham->genNavi($pageup, $page)."'\">$nom</li>\n" ;
  	}

  	// Retourne l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
  	
  	// Génération de la petite fenêtre de connexion.
  	function getFormulaire ( ) {
    	global $xham ;
    	$type = $xham -> getOption ( "TypeAuth" ) ;

    	// Chargement du template ModeliXe.
    	$mod = new ModeliXe ( "Authentification.mxt" ) ;
    	$mod -> SetModeliXe ( ) ;

    	switch ( $type ) {
    		default:
      			// Remplissage des champs.
      			$mod -> MxText ( "normal.uid", $xham -> user -> getIdentite ( ) ) ;
      			$mod -> MxFormField ( "normal.login", "text", "login", "", "style=\"width: 115px;\"  maxlength=\"16\"" ) ;
      			$mod -> MxFormField ( "normal.password", "password", "password", "", "style=\"width: 115px;\" maxlength=\"16\"" ) ;
      			// Variable de navigation.
	      		$urlnavi = $xham->genNaviFull ( );
	      		$urlnavi = strtr($urlnavi,array("&amp;" => "&"));
      			$mod -> MxHidden ( "normal.hidden", "navi=".$urlnavi ) ;
      			if ( $xham -> user -> getType ( ) == "MySQLInt" ) $lien = '<a href="'.URLNAVI.$xham->genNavi ( "ChangementPassword", "", "ChangementPassword" ).'">Changer mon mot de passe</a>' ;
      			else $lien = '' ;
      			$mod -> MxText ( "normal.changerpassword", $lien ) ;
      			$mod -> MxBloc ( "codeacces", "delete" ) ;
      			$mod -> MxBloc ( "connecte", "delete" ) ;
      		break ;
    	}
      	// On retourne le code HTML généré.
      	return $mod -> MxWrite ( "1" ) ;
  	}
}
?>
