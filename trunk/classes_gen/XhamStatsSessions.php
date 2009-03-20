<?php

// Titre  : Classe StatsSession
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 02 Juin 2005

// Description : 
// Cette classe affiche les données sur les sessions en cours,
// les sessions passées, les statistiques...

class XhamStatsSessions {

  // Déclaration des attributs de la classe.
  // Contient les différents niveaux de la navigation en cours.
  private $af ;

  // Constructeur.
  function __construct ( $xham ) {
    $this->xham = $xham ;
    // Affichage des sessions en cours.
    $this->af .= $this->getSessions ( ) ;
    // Séparateur.
    $this->af .= "<hr/>" ;
    // Affichage des statistiques d'accès.
    $this->af .= $this->getStatsPages ( ) ;
  }  

  // Cette fonction calcule et affiche les statistiques d'accès au Terminal.
  function getStatsPages ( ) {

    // Date de lancement des statistiques.
    $datestats = new clDate ( DATESTATS ) ;
    
    // On récupère la liste des utilisateurs s'étant déjà connectés.
    $param['cw'] = "WHERE idapplication=".IDAPPLICATION ;
    $res = $this->xham -> Execute ( "Fichier", "getSessionsPersonnes", $param, "ResultQuery" ) ;
    // Fabrication du tableau attendu par ModeliXe.
    $tab['%'] = "globales" ;
    for ( $i = 0 ; isset ( $res['uid'][$i] ) ; $i++ ) $tab[$res['uid'][$i]] = "de ".$res['uid'][$i]." (".$res['somme'][$i].")" ;
    // Initialisation de l'utilisateur sélectionné et du filtre MySQL correspondant.
    if ( ! isset ( $_POST['choix'] ) ) {
      if ( isset ( $_GET['choix'] ) )
	$_POST['choix'] = $_GET['choix'] ;
      else
	$_POST['choix'] = "%" ;
    }
    // Application du filtre MySQL pour aller récupérer les statistiques de l'utilisateur.
    $param['cw'] = "WHERE uid LIKE '".$_POST['choix']."' AND idapplication=".IDAPPLICATION ;
    $param['cs'] = "" ;
    $res = $this->xham -> Execute ( "Fichier", "getSessionsStatistiques", $param, "ResultQuery" ) ;
    // Pour chaque statistique récupérées, on incrémente une case d'un tableau avec son nombre de clic. Il y a une case
    // par partie du Terminal.
    $stats["Total de clics"] = 0 ;
    for ( $i = 0 ; isset ( $res['idstats'][$i] ) ; $i++ ) {
      if ( $res['loc2'][$i] ) {
	if ( $res['loc1'][$i] ) {
	  if ( isset ( $stats[$res['loc1'][$i]."->".$res['loc2'][$i]] ) ) 
	    $stats[$res['loc1'][$i]."->".$res['loc2'][$i]] += $res['nombre'][$i] ;
	  else $stats[$res['loc1'][$i]."->".$res['loc2'][$i]] = $res['nombre'][$i] ;
	}
      } else {
	if ( $res['loc1'][$i] ) {
	  if ( isset ( $stats[$res['loc1'][$i]] ) )
	    $stats[$res['loc1'][$i]] += $res['nombre'][$i] ;
	  else $stats[$res['loc1'][$i]] = $res['nombre'][$i] ;
	}
      }
      // Calcul du nombre total de clic pour cet utilisateur.
      $stats["Total de clics"] += $res['nombre'][$i] ;
    }

    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "SessionsStatistiques.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Chargement du template ListMaker pour faire le tableau.
    $list = new ListMaker ( "template/SessionsStatistiques.html" ) ;
    // Passage des variables à transmettre et de leurs valeurs.
    $list -> addUserVar ( 'navi', $this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1) ) ) ;
    $list -> addUrlVar  ( 'navi', $this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1) ) ) ;
    $list -> addUserVar ( 'choix', $_POST['choix'] ) ;
    $list -> addUrlVar  ( 'choix', $_POST['choix'] ) ;
    // Nom des colonnes du tableau.
    $list -> setSortColumn ( 'col1', 'Page', 'page' ) ;
    $list -> setSortColumn ( 'col2', 'Clics', 'clics' ) ;
    // On alterne les couleurs en fonction de la parité de la ligne.
    $list -> setAlternateColor ( "pair", "impair" ) ;

    // On parcourt le tableau précédemment calculé contenant les statistiques,
    // et on les ajoute au template ListMaker.
    if ( is_array ( $stats ) ) {
      for ( $i = 0 ; list ( $key, $val ) = each ( $stats ) ; $i++ ) {
        $item['page'] = $key ;
        $item['clics'] = $val ;
        $list->addItem ( $item ) ;
      }  
    }
    // Affichage d'informations complémentaires si nous ne sommes pas dans le cas
    // de statistiques globales.
    if ( $_POST['choix'] != "%" ) {
      // Si l'utilisateur est connecté, on affiche un message le signalant.
      $param['cw'] = "WHERE uid LIKE '".$_POST['choix']."' AND idapplication=".IDAPPLICATION ;
      $res = $this->xham -> Execute ( "Fichier", "getSessionsActuelles", $param, "ResultQuery" ) ;
      if ( $res['INDIC_SVC'][2] ) $infos = "Cet utilisateur est actuellement connecté.<br />" ;
      // Sinon, on affiche les informations de sa dernière connexion.
      if ( ! isset ( $infos ) or ! $infos ) {
	// Récupération des informations.
	$param['cw'] = "WHERE uid LIKE '".$_POST['choix']."' AND idapplication=".IDAPPLICATION." ORDER BY dateslast DESC" ;
	$res = $this->xham -> Execute ( "Fichier", "getSessionsHistorique", $param, "ResultQuery" ) ;
	// Calcul de la durée de connexion et affichage des différentes dates.
	$last = new clDate ( (isset($res['dateslast'][0])?$res['dateslast'][0]:'') ) ;
	$date = new clDate ( (isset($res['dateshisto'][0])?$res['dateshisto'][0]:'') ) ;
	$duree = new clDuree ( $last -> getDifference ( $date ) ) ;
	if ( (isset($res['nombre'][0])?$res['nombre'][0]:'0') > 1 ) $sc = "s" ; else $sc = '' ;
	if ( $duree->getMinutes ( ) > 1 ) $sm = "s" ; else $sm = '' ;
	if ( $duree->getSeconds ( ) > 1 ) $ss = "s" ; else $ss = '' ;
	if ( $duree->getMinutes ( ) > 0 ) $temps = $duree->getMinutes()." minute$sm" ; else  $temps = $duree->getSeconds()." seconde$ss" ;
	$infos = "Dernière connexion le ".$date->getDateTextFull ( "à" )." (".(isset($res['nombre'][0])?$res['nombre'][0]:'0')." clic$sc, durée de $temps)<br />" ;
	$infos .= "Déconnexion enregistrée le ".$last->getDateTextFull ( "à" )."<br />" ;
      }
      $mod -> MxText ( "informations", $infos ) ;
    } else  $mod -> MxText ( "informations", '--' ) ;
    // On affiche la date à laquelle les stats ont commencé être enregistrées.
    $mod -> MxText ( "datestats", $datestats -> getDateText ( ) ) ;
    // Affichage du tableau de stats.
    $mod -> MxText ( "table",  $list->getList ( (isset($pagination)?$pagination:'') ) ) ;
    // Affichage de la liste des personnes à sélectionner.
    $mod -> MxSelect ( "choix", "choix", $_POST['choix'], $tab , '', '', "onChange=reload(this.form)") ; 
    // Variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1) ) ) ;
    // Option pour afficher ou non les graphiques.
    $mod -> MxCheckerField ( "graph", "checkbox", 'graph', 1, ((isset($_POST['graph']))?($_POST['graph']?true:false):false) ,"title='Graphiques' onChange=reload(this.form)") ;
    if ( isset ( $_POST['graph']) AND $_POST['graph'] ) {
    	$this->genGraphs ( $mod ) ;
    	$mod -> MxText ( "graphs.clicsHeure", "<img src=\"cache/image1.png\" alt=\"Clics par heure\"></img>" ) ;
    	$mod -> MxText ( "graphs.tempsHeure", "<img src=\"cache/image2.png\" alt=\"Temps par clics moyen par heure\"></img>" ) ;
    	$mod -> MxText ( "graphs.clicsJour", "<img src=\"cache/image3.png\" alt=\"Clics par jour\"></img>" ) ;
    } else $mod -> MxBloc ( "graphs", "delete" ) ;
    //$mod -> MxText ( "tempsJour", "<img src=cache/image4.png></img>" ) ;
    // Récupération du code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
  }

  // Cette fonction affiche les sessions en cours...
  function getSessions ( ) {
    // Préparation de deux objets clDate.
    $date = new clDate ( ) ;
    $last = new clDate ( ) ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "SessionsActuelles.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Chargement du template ListMaker.
    $list = new ListMaker ( "template/SessionsActuelles.html" ) ;
    // Transmission de la variable de navigation à ListMaker.
    $list -> addUserVar ( 'navi', $this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1) ) ) ;
    $list -> addUrlVar  ( 'navi', $this->xham->genNavi ( $this->xham->getNavi(0), $this->xham->getNavi(1) ) ) ;
    // Nom des colonnes.
    $list -> setSortColumn ( 'col1', 'Utilisateur', 'uid' ) ;
    $list -> setSortColumn ( 'col2', 'Type', 'type' ) ;
    $list -> setSortColumn ( 'col3', 'Adresse IP', 'ip' ) ;
    $list -> setSortColumn ( 'col4', 'Date de connexion', 'date' ) ;
    $list -> setSortColumn ( 'col5', 'Dernière action', 'last' ) ;
    $list -> setSortColumn ( 'col6', 'Page parcourue', 'localisation' ) ;
    // Tri automatique sur la colonne de la date de la dernière action effectuée.
    $list -> setdefaultSort ( 'col5' ) ;
    // Choix des couleurs à alterner d'une ligne sur l'autre.
    $list -> setAlternateColor ( "pair", "impair" ) ;
    // Récupération des sessions actuelles.
    $param['cw'] = "WHERE uid !='invité' AND idapplication=".IDAPPLICATION." ORDER BY last DESC" ;
    $res = $this->xham -> Execute ( "Fichier", "getSessionsActuelles", $param, "ResultQuery" ) ;
    // On parcourt les sessions récupérées.
    for ( $i = 0 ; isset ( $res['idsactu'][$i] ) ; $i++ ) {
      // Initialisation des dates.
      $date -> setDate ( $res['date'][$i] ) ;
      $last -> setDate ( $res['last'][$i] ) ;
      // Préparation du lien pour aller directement sur la page parcourue par l'utilisateur.
      if ( ENCODERURL ) $lien = '<a href="'.URLNAVI.$res['localisation'][$i].'">'.base64_decode($res['localisation'][$i]).'</a>' ;
      else $lien = '<a href="'.URLNAVI.$res['localisation'][$i].'">'.$res['localisation'][$i].'</a>' ;
      // Préparation des différentes colonnes : uid, type, ip, date, last et localisation.
      $item['uid'] = $res['uid'][$i] ;
      $item['type'] = $res['type'][$i] ;
      $item['ip'] = $res['ip'][$i] ;
      $item['date'] = $date->getDateTextFull ( ) ;
      $item['last'] = $last->getDateTextFull ( ) ;
      $item['localisation'] = $lien ;
      $list->addItem ( $item ) ;
    }
    // Récupération du résultat de ListMaker dans ModeliXe.
    $mod -> MxText ( "table",  $list->getList ( (isset($pagination)?$pagination:'') ) ) ;
    // Récupération du code généré par ModeliXe.
    return $mod -> MxWrite ( "1" ) ;
  }


  // Fonction vraiment atroce, j'ai la flegme de la commenter !
  function genGraphs ( $mod, $dateG='', $nbDays='7' ) {
    $param['cs'] = "*" ;
    $date = new clDate ( $dateG ) ;
    $dateT = new clDate ( $date->getDate ( "Y-m-d 00:00:00" ) ) ;
    $dateT -> addDays ( -1 ) ;
    $dateH = new clDate ( $dateT->getDate ( "Y-m-d" ) ) ;
    $dateT -> addDays ( 1 ) ;
    $dateT -> addDays ( -$nbDays ) ;
    $dateS = new clDate ( $dateT->getDate ( "Y-m-d" ) ) ;
    $dateT -> addDays ( $nbDays ) ;
    $j = 0 ;
    for ( $dateDeb = $dateH ; $dateDeb->getTimestamp ( ) < $dateT->getTimestamp ( ) ; ) {
      $heureA = $dateDeb -> getHours ( ) ;
      $dateA = $dateDeb -> getDatetime ( ) ;
      $dateDeb -> addHours ( 1 ) ;
      $param['cs'] = "*" ;
      $param['cw'] = "WHERE iduser LIKE '".$_POST['choix']."' AND ( date BETWEEN '".$dateA."' AND '".$dateDeb->getDatetime()."' ) AND idapplication=".IDAPPLICATION." AND type='navi'" ;
      $res = $this->xham -> Execute ( "Fichier", "getLogs", $param, "ResultQuery" ) ;
      $tab[0][0][] = $res['INDIC_SVC'][2] ;
      //eko ( $res['INDIC_SVC'] ) ;
      $tab[1][0][$j] = 0 ;
      $tab[1][1][$j] = 0 ;
      //      $tab[0][1][$j] = 0 ;
      $titres[1][] = $heureA."h - ".$dateDeb->getHours ( )."h" ;
      for ( $i = 0 ; isset ( $res['idlog'][$i] ) ; $i++ ) {
	$tab[1][0][$j] += $res['tempsPage'][$i] ;
	$tab[1][1][$j] += $res['tempsSQL'][$i] ;
	//$tab[0][1][$j] += $res['nombreSQL'][$i] ;
      }
      if ( $res['INDIC_SVC'][2] ) {
	$tab[1][0][$j] = $tab[1][0][$j] / $res['INDIC_SVC'][2] ;
	$tab[1][1][$j] = $tab[1][1][$j] / $res['INDIC_SVC'][2] ;
      }
      $j++ ;
    }
    $dateH->addDays ( -1 ) ;
    $jpG = new clJpGraph ( ) ;
    $jpG->arh_graph( "Nombre de clics et requetes par heure (".$_POST['choix'].") - ".$dateH->getDateText ( ),'','', $tab[0], array ( 'Clics', 'Requetes' ), array ( '#DDCC55', '#CC3355' ), 800, 500, "image1.png", $titres[1], "45", '#C8DE3D', "groupbar", '#FFFFFF', "%d" ) ;
    $jpG->arh_graph( "Temps moyen par heure (".$_POST['choix'].") - ".$dateH->getDateText ( ),'','', $tab[1], array ( 'Temps total', 'Temps SQL' ), array ( '#DDCC55', '#CC3355' ), 800, 400, "image2.png", $titres[1], "45", '#C8DE3D', "groupbar", '#FFFFFF', "%2.2f" ) ;

    $j = 0 ;
    for ( $dateDeb = $dateS ; $dateDeb->getTimestamp ( ) < $dateT->getTimestamp ( ) ; ) {
      $jourA = $dateDeb -> getDayWeek ( ) ;
      $dateA = $dateDeb -> getDatetime ( ) ;
      $dateDeb -> addDays ( 1 ) ;
      $param['cs'] = "*" ;
      $param['cw'] = "WHERE iduser LIKE '".$_POST['choix']."' AND ( date BETWEEN '".$dateA."' AND '".$dateDeb->getDatetime()."' ) AND idapplication=".IDAPPLICATION." AND type='navi'" ;
      $res = $this->xham -> Execute ( "Fichier", "getLogs", $param, "ResultQuery" ) ;
      $tab[2][0][] = $res['INDIC_SVC'][2] ;
      $titres[2][] = $jourA ;
      /*
      //eko ( $res['INDIC_SVC'] ) ;
      $tab[3][0][$j] = 0 ;
      $tab[3][1][$j] = 0 ;
      //$tab[2][1][$j] = 0 ;
      $titres[2][] = $jourA ;
      for ( $i = 0 ; isset ( $res['idlog'][$i] ) ; $i++ ) {
	$tab[3][0][$j] += $res['tempsPage'][$i] ;
	$tab[3][1][$j] += $res['tempsSQL'][$i] ;
	//$tab[2][1][$j] += $res['nombreSQL'][$i] ;
      }
      if ( $res['INDIC_SVC'][2] ) {
	$tab[3][0][$j] = $tab[3][0][$j] / $res['INDIC_SVC'][2] ;
	$tab[3][1][$j] = $tab[3][1][$j] / $res['INDIC_SVC'][2] ;
      }
      $j++ ;
      */
    }
    // eko ( $tab[3][1] ) ;
    $jpG->arh_graph( "Nombre de clics et requetes par jour (".$_POST['choix'].") ",'','', $tab[2], array ( 'Clics', 'Requetes' ), array ( '#DDCC55', '#CC3355' ), 800, 500, "image3.png", $titres[2], "45", '#C8DE3D', "groupbar", '#FFFFFF', "%d" ) ;
    //$jpG->arh_graph( "Temps moyen par jour - ".$date->getDateText ( ),'','', $tab[3], array ( 'Temps total', 'Temps SQL' ), array ( '#DDCC55', '#CC3355' ), 800, 400, "image4.png", $titres[2], "45", '#C8DE3D', "groupbar", '#FFFFFF', "%2.2f" ) ;
  }

  // Retourne l'affichage généré par cette classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}
