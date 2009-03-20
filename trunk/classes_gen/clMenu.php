<?php
// Titre  : Classe Menu
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 18 Janvier 2005

// Description : 
// Cette classe génère le menu de navigation du site.
// Elle génère de façon brute le code XHTML du menu.
// L'habillage se fait via le fichier CSS.



class clMenu {

  // Attribut contenant l'affichage.
  private $af ;

  // Constructeur du menu.
  function __construct ( ) {
    $this->menu = 0 ;
    $this->af .= $this->genMenu ( ) ;
  }

  // Génération du menu.
  function genMenu ( ) {
    global $options ;
    global $session ;
    $auth = new clAuthentification ( ) ;
    $this->menuAuth = $auth -> getFormulaire ( ) ;
    //$menu = "<center>\n" ;
    $menu = "" ;
    $param['cw'] = "WHERE type='menu' AND etat=1 AND idapplication=".IDAPPLICATION." ORDER BY rang" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ;  
    for ( $i = 0 ; isset ( $res['idmenu'][$i] ) ; $i++ ) {
      $faux = 0 ;
      if ( $res['noption'][$i] )
	if ( $options -> getOption ( $res['noption'][$i] ) != $res['valeur'][$i] ) { /*eko ( "Option fausse" ) ;*/ $faux = 1 ; }
      if ( ! $session -> getDroit ( $res['droit'][$i], "r" ) ) $faux = 1 ; 
      if ( $res['libelle'][$i] == "Accueil" ) $faux = 0 ;
      if ( ! $faux ) 
	$menu .= $this->genItem ( $res['libelle'][$i], $res['cle'][$i], $res['idunique'][$i] ) ;
    }
    
    //$menu .= "\t</center>\n" ;
    // On renvoie le menu ainsi construit.
    return $menu ;
  }

// Génération d'un onglet.
  function genItem ( $nom, $page, $idmenu ) {
    global $session ;
    global $options ;
    if ( $session->getNavi ( 0 ) == $page ) $active = "id=\"active\"" ;
    else $active = '' ;
    $param['cw'] = "WHERE type='item' AND etat=1 AND menuparent='$idmenu' AND idapplication=".IDAPPLICATION." ORDER BY rang" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getMenus", $param, "ResultQuery" ) ; 
    //eko ( $res[INDIC_SVC] ) ;
    if ( $res['INDIC_SVC'][2] ) {
      $this->menu++ ;
      if ( $this->menu == 10 ) $this->menu++ ;
      $af = "\t<dl id=\"menus".$idmenu."\"><dt $active onmouseover=\"javascript:montre('s".$this->menu."');\"><a>$nom</a></dt>\n" ;
      $af .= "\t<dd id=\"s".$this->menu."\"><ul>\n" ;
      for ( $i = 0 ; isset ( $res['idmenu'][$i] ) ; $i++ ) {
	if ( ! $res['noption'][$i] )
	  $af .= $this->genMiniItem ( $page, $res['libelle'][$i], $res['cle'][$i], $res['droit'][$i]  ) ;
	elseif ($options -> getOption ( $res['noption'][$i] ) == $res['valeur'][$i] )
	  $af .= $this->genMiniItem ( $page, $res['libelle'][$i], $res['cle'][$i], $res['droit'][$i]  ) ;
      }
      $af .= "\t</ul></dd></dl>\n" ;
      return $af ;
    } elseif ( $nom == "Accueil" ) {
      return "\t<dl id=\"menus".$idmenu."\"><dt $active onmouseover=\"javascript:montre('s10');\" onclick=\"document.location='".URLNAVI.$session->genNavi($page)."'\"><a href=\"".URLNAVI.$session->genNavi($page)."\">$nom</a></dt>".$this->menuAuth."</dl>\n" ;
    } else {
      return "\t<dl id=\"menus".$idmenu."\"><dt $active onmouseover=\"javascript:montre();\" onclick=\"document.location='".URLNAVI.$session->genNavi($page)."'\" ><a href=\"".URLNAVI.$session->genNavi($page)."\">$nom</a></dt><dd style=\"display:none;\">Vide</dd></dl>\n" ;
    }
  }

  // Génération d'un item dans sous-menu.
  function genMiniItem ( $pageup, $nom, $page, $droit ) {
    global $session ;
    if ( $session->getDroit ( $droit, "r" ) ) return "\t\t<li onclick=\"document.location='".URLNAVI.$session->genNavi($pageup, $page)."'\"><a href=\"".URLNAVI.$session->genNavi($pageup, $page)."\">$nom</a></li>\n" ;
  }

  // Retourne l'affichage généré par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}

?>