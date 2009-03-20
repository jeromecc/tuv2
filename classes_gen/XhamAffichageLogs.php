<?php

// Titre  : Classe AffichageLogs
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 12 Janvier 2006

// Description : 
// Affichage des logs & module de recherche.

class XhamAffichageLogs {

  private $af ;
  private $dateMin ;
  private $dateMax ;

  // Constructeur de la classe.
  function __construct ( $xham ) {
  	$this->xham = $xham ;
    $this->af = $this->genAffichage ( ) ;
  }

  /*
	choix de l'application    : Liste des applications existantes (défaut : IDAPPLICATION)
	filtre sur l'ip           : Filtre + Liste                    (défaut : rien)
	filtre sur l'utilisateur  : Filtre + Liste                    (défaut : rien)
	choix des dates           : Deux listes de dates.             (défaut : le jour même)
	choix du type             : Liste                             (défaut : tous)
	filtre sur la description : Filtre                            (défaut : rien)
	filtre sur la cible       : Filtre                            (défaut : rien)
  */

  // Génération de l'affichage.
  function genAffichage ( ) {
    // Chargement du template modelixe.
    $mod = new ModeliXe ( "AffichageLogs.mxt" ) ;
    $mod -> SetModeliXe ( ) ;

    // Fabrication des "select".
    $mod -> MxSelect ( 'listeUtilisateurs', 'utilisateur', (isset($_GET['utilisateur'])?stripslashes($_GET['utilisateur']):''), $this->getDistinct('iduser') ) ; 
    $mod -> MxSelect ( 'listeTypes', 'type', (isset($_GET['type'])?stripslashes($_GET['type']):''), $this->getDistinct ('type') ) ; 
    $mod -> MxSelect ( 'listeIP', 'ip', (isset($_GET['ip'])?stripslashes($_GET['ip']):''), $this->getDistinct('ip') ) ;
    $min = $this->getDates('min') ;
    $max = $this->getDates('max') ;
    $mod -> MxSelect ( 'dateMin', 'dateMin', $this->dateMin, $min ) ;
    $mod -> MxSelect ( 'dateMax', 'dateMax', $this->dateMax, $max ) ; 
    // Fabrication des champs "text".
    $mod -> MxFormField ( 'filtreDescription', 'text', 'filtreDescription', (isset($_GET['filtreDescription'])?stripslashes($_GET['filtreDescription']):'') ) ;
    $mod -> MxFormField ( 'filtreUtilisateur', 'text', 'filtreUtilisateur', (isset($_GET['filtreUtilisateur'])?stripslashes($_GET['filtreUtilisateur']):'') ) ;
    $mod -> MxFormField ( 'filtreCible', 'text', 'filtreCible', (isset($_GET['filtreCible'])?stripslashes($_GET['filtreCible']):'') ) ;
    $mod -> MxFormField ( 'filtreIP', 'text', 'filtreIP', (isset($_GET['filtreIP'])?stripslashes($_GET['filtreIP']):'') ) ;
    $tab = array ( "Tous"=>"Tous", 1=>1, 10=>10, 20=>20, 25=>25, 50=>50, 100=>100, 200=>200, 500=>500, 1000=>1000, 2000=>2000, 4000=>4000, 8000=>8000 ) ;
    $mod -> MxSelect ( 'nbResultats', 'nbResultats', (isset($_GET['nbResultats'])?$_GET['nbResultats']:$this->xham->getOption('nbResultats')), $tab ) ; 
    if ( isset ( $_GET['nbResultats'] ) ) $this->pagination = $_GET['nbResultats'] ;
    else $this->pagination = ($this->xham -> getOption ( 'nbResultats' )?$this->xham -> getOption ( 'nbResultats' ):50) ;
    // Affichage de la liste des logs.
    $mod -> MxText ( "resultats", $this->genResultats ( ) ) ;
    // Variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$this->xham->genNaviFull ( ) ) ;
    // On retourne l'affichage ainsi généré.
    return $mod -> MxWrite ( "1" ) ;
  }

  /* application | ip | iduser | date | type | description | cible */
  function genResultats ( ) {
    // Chargement du template ListMaker.
    $list = new ListMaker ( "template/AffichageLogs.html" ) ;
    // Transmission des variables utiles à ListMaker.
    $list -> addUserVar ( 'navi', $this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1) ) ) ;
    $list -> addUrlVar  ( 'navi', $this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1) ) ) ;
    $list -> addUserVar ( 'utilisateur', (isset($_GET['utilisateur'])?$_GET['utilisateur']:'%') ) ;
    $list -> addUrlVar  ( 'utilisateur', (isset($_GET['utilisateur'])?$_GET['utilisateur']:'%') ) ;
    $list -> addUserVar ( 'type', (isset($_GET['type'])?$_GET['type']:'%') ) ;
    $list -> addUrlVar  ( 'type', (isset($_GET['type'])?$_GET['type']:'%') ) ;
    $list -> addUserVar ( 'ip', (isset($_GET['ip'])?$_GET['ip']:'%') ) ;
    $list -> addUrlVar  ( 'ip', (isset($_GET['ip'])?$_GET['ip']:'%') ) ;
    $list -> addUserVar ( 'dateMin', (isset($_GET['dateMin'])?$_GET['dateMin']:$this->dateMin) ) ;
    $list -> addUrlVar  ( 'dateMin', (isset($_GET['dateMin'])?$_GET['dateMin']:$this->dateMin) ) ;
    $list -> addUserVar ( 'dateMax', (isset($_GET['dateMax'])?$_GET['dateMax']:$this->dateMax) ) ;
    $list -> addUrlVar  ( 'dateMax', (isset($_GET['dateMax'])?$_GET['dateMax']:$this->dateMax) ) ;
    $list -> addUserVar ( 'filtreDescription', (isset($_GET['filtreDescription'])?$_GET['filtreDescription']:'') ) ;
    $list -> addUrlVar  ( 'filtreDescription', (isset($_GET['filtreDescription'])?$_GET['filtreDescription']:'') ) ;
    $list -> addUserVar ( 'filtreUtilisateur', (isset($_GET['filtreUtilisateur'])?$_GET['filtreUtilisateur']:'') ) ;
    $list -> addUrlVar  ( 'filtreUtilisateur', (isset($_GET['filtreUtilisateur'])?$_GET['filtreUtilisateur']:'') ) ;
    $list -> addUserVar ( 'filtreCible', (isset($_GET['filtreCible'])?$_GET['filtreCible']:'') ) ;
    $list -> addUrlVar  ( 'filtreCible', (isset($_GET['filtreCible'])?$_GET['filtreCible']:'') ) ;
    $list -> addUserVar ( 'filtreIP', (isset($_GET['filtreIP'])?$_GET['filtreIP']:'') ) ;
    $list -> addUrlVar  ( 'filtreIP', (isset($_GET['filtreIP'])?$_GET['filtreIP']:'') ) ;
    $list -> addUserVar ( 'nbResultats', (isset($_GET['nbResultats'])?$_GET['nbResultats']:$this->pagination) ) ;
    $list -> addUrlVar  ( 'nbResultats', (isset($_GET['nbResultats'])?$_GET['nbResultats']:$this->pagination) ) ;
    
    // Nom des colonnes.
    $list -> setSortColumn ( 'col1', 'Type', 'type' ) ;
    $list -> setSortColumn ( 'col2', 'Utilisateur', 'utilisateur' ) ;
    $list -> setSortColumn ( 'col3', 'Adresse IP', 'ip' ) ;
    $list -> setSortColumn ( 'col5', 'Description', 'description' ) ;
    $list -> setSortColumn ( 'col6', 'cible', 'cible' ) ;
    $list -> setSortColumn ( 'col7', 'Page', 'tempsPage' ) ;
    $list -> setSortColumn ( 'col8', 'SQL', 'tempsSQL' ) ;
    $list -> setSortColumn ( 'col9', 'Nb', 'nombreSQL' ) ;
    $list -> setSortColumn ( 'col4', 'Date de connexion', 'date' ) ;
    // Tri automatique sur la colonne de la date de la dernière action effectuée.
    $list -> setdefaultSort ( '' ) ;
    // Choix des couleurs à alterner d'une ligne sur l'autre.
    $list -> setAlternateColor ( "pair", "impair" ) ;
    $dateMin = new clDate ( $this->dateMin ) ;
    $dateMax = new clDate ( $this->dateMax ) ;
    // Fabrication de la requête.
    if ( $this->pagination == "Tous" ) $limit = '' ;
    else $limit = "LIMIT 0, ".$this->pagination ;
    $param['cw'] = "WHERE 
    type LIKE '".(isset($_GET['type'])?$_GET['type']:'%')."' 
    AND iduser LIKE '".(isset($_GET['filtreUtilisateur'])?($_GET['filtreUtilisateur']?$_GET['filtreUtilisateur'].'%':$_GET['utilisateur']):'%')."' 
    AND ip LIKE '".( isset($_GET['filtreIP']) ? ( $_GET['filtreIP'] ? $_GET['filtreIP'].'%' : $_GET['ip'] ) : '%' )."' 
    AND description LIKE '".(isset($_GET['filtreDescription'])?$_GET['filtreDescription']:'')."%' 
    AND idcible LIKE '".(isset($_GET['filtreCible'])?$_GET['filtreCible']:'')."%' 
    AND date BETWEEN  '".$dateMin->getDatetime()."' AND '".$dateMax->getDatetime()."' AND idapplication=".IDAPPLICATION."
    ORDER BY date DESC $limit" ;
    $param['cs'] = "*" ;
    $res = $this->xham -> Execute ( "Fichier", "getLogs", $param, "ResultQuery" ) ;
    //    eko ( $res['INDIC_SVC'] ) ;
    // On parcourt les logs récupérés.
    for ( $i = 0 ; isset ( $res['idlog'][$i] ) ; $i++ ) {
      $item['ip'] = $res['ip'][$i] ;
      $item['utilisateur'] = $res['iduser'][$i] ;
      $item['date'] = $res['date'][$i] ;
      $item['type'] = $res['type'][$i] ;
      $item['description'] = $res['description'][$i] ;
      $item['cible'] = $res['idcible'][$i] ;
      $item['tempsPage'] = sprintf ( "%0.4f", $res['tempsPage'][$i] ) ;
      $item['tempsSQL'] = sprintf ( "%0.4f", $res['tempsSQL'][$i] ) ;
      $item['nombreSQL'] = $res['nombreSQL'][$i] ;
      $list->addItem ( $item ) ;
    }
    // On retourne le tableau généré.
    return $list->getList ( )  ;
  }

  // Retourne la liste des différentes lignes existantes pour une colonne donnée.
  function getDistinct ( $str='type', $defaut='--' ) {
    $tab['%'] = $defaut ;
    $param['cw'] = "WHERE idapplication=".IDAPPLICATION." ORDER BY t"  ;
    $param['cs'] = "distinct $str t" ;
    $res = $this->xham -> Execute ( "Fichier", "getLogs", $param, "ResultQuery" ) ;
    for ( $i = 0 ; isset ( $res['t'][$i] ) ; $i++ )
      $tab[$res['t'][$i]] = $res['t'][$i] ;
    return (is_array($tab)?$tab:array()) ;
  }

  // Retourne une liste de dates.
  function getDates ( $type='' ) {
    // Initialisation des dates.
    $dateT = new clDate ( ) ;
    $date = new clDate ( $dateT -> getDate ( ) ) ;
    if ( ( ! $this->dateMin OR ! $this->dateMax ) AND ( ! isset ( $this->setDate ) ) ) {
      $dateA = new clDate ( (isset($_GET['dateMin'])?$_GET['dateMin']:$date->getDate('Y-m-d')) ) ;
      $date -> addDays ( 1 ) ;
      $dateB = new clDate ( (isset($_GET['dateMax'])?$_GET['dateMax']:$date->getDate('Y-m-d')) ) ;
      $date -> addDays ( -1 ) ;
      if ( $dateA -> getTimestamp ( ) > $dateB -> getTimestamp ( ) ) {
	$this->dateMax = $dateA -> getTimestamp ( ) ;
	$this->dateMin = $dateB -> getTimestamp ( ) ;
      } else {
	$this->dateMax = $dateB -> getTimestamp ( ) ;
	$this->dateMin = $dateA -> getTimestamp ( ) ;
      }
    }
    // Fabrication de la liste de dates de la dernière année.
    $date -> addYears ( -1 ) ;
    $oneYear = $date -> getTimestamp ( ) ;
    $date -> addYears ( 1 ) ;
    $date -> addDays ( 1 ) ;
    $today = $date -> getTimestamp ( ) ;
    for ( $i = $today ; $i >= $oneYear ; $i -= 86400 ) {
      $date -> setDate ( $i ) ;
      $tab[$i] = $date -> getDate ( "d-m-Y" ) ;
    }
    return (is_array($tab)?$tab:array()) ;
  }

  // Retourne l'affichage généré par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }

}

?>
