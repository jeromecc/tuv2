<?php
//
// Created on 08 Novembre 2007
// By Damien Borel <dborel@ch-hyeres.fr>
// 
//
 
class XhamTodo {

	// Constructeur de la classe.
  	function __construct ( $xham ) {
    	$this->xham = $xham ;
		if ( $xham->getr('ajax') ) $this->ajax = 1 ;
		else $this->ajax = 0 ;
		
		switch ( $xham->getr('ajax') ) {
			case 'refresh':
				$this->genListeTodo ( ) ;
			break ;
			case 'addTodo':
				$this->addTodo ( ) ;
				$this->genListeTodo ( ) ;
			break ;
			case 'changeStatus':
				$this->changeStatus ( ) ;
				$this->genListeTodo ( ) ;
			break ;
			default:
				$this->genAffichageTotal ( ) ;
			break ;
		}
  	}
  	
  	function changeStatus ( ) {
  		$req = new XhamRequete ( BASEXHAM, 'todo', array(), MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
		$res = $req -> getGen ( "idTodo=".$this->xham->getr('idTodo'), "resultQuery" ) ;
		if ( $res['idUser'][0] == $this->xham->user->getLogin ( ) OR $this->xham->getDroit ( 'Admin_Todo', 'a' ) ) {
			if ( $res['etat'][0] == 'afaire' ) {
				$data['etat'] = 'encours' ;
				$data['dateEnCours'] = date ( 'Y-m-d H:i:s' ) ;
			} elseif ( $res['etat'][0] == 'encours' ) {
				$data['etat'] = 'termines' ;
				$data['dateFin'] = date ( 'Y-m-d H:i:s' ) ;
			} else {
				$data['etat'] = 'encours' ;
				$data['dateFin'] = '0000-00-00 00:00:00' ;
			}
			$req = new XhamRequete ( BASEXHAM, 'todo', $data, MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
			$req->updRecord ( "idTodo=".$this->xham->getr('idTodo') ) ;
		} else $af = '<span class="erreur">Vous n\'avez pas le droit de modifier les tâches d\'un autre utilisateur.</span>' ;
		if ( $this->ajax ) $this->af .= $af ;
		else return $af ;
  	}

	function addTodo ( ) {
		if ( $this->xham->user->getLogin ( ) != 'Invité' ) {
			if ( $_POST['Nom'] ) {
			$data['idApplication'] = IDAPPLICATION ;
			$data['idUser'] = $this->xham->user->getLogin ( ) ;
			$data['date'] = date ( 'Y-m-d H:i:s') ;
			if ( $_POST['dateLimite'] ) {
				$date = new clDate ( $_POST['dateLimite'] ) ;
				$data['dateLimite'] = $date->getDatetime ( ) ;
			}
			$data['nom'] = utf8_decode($_POST['Nom']) ;
			$data['categorie'] = utf8_decode($_POST['CategorieA']) ;
			$data['importance'] = $_POST['Importance'] ;
			$data['etat'] = 'afaire' ;	
			$data['public'] = $_POST['Public'] ;
			$req = new XhamRequete ( BASEXHAM, 'todo', $data, MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
			$req->addRecord ( ) ;
			} else $af = '<span class="erreur">Vous devez choisir un nom pour la tâche.</span>' ;
		//$af = affTab ( $data ) ;
		} else $af = '<span class="erreur">Vous n\'avez pas le droit d\'utiliser ces fonctions.</span>' ;
		if ( $this->ajax ) $this->af .= $af ;
		else return $af ;
	}

	function genListeTodo ( ) {
		$req = new XhamRequete ( BASEXHAM, 'todo', array(), MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
		switch ( $this->xham->getr('Categorie') ) {
			case 'all':
				$filtreCates = "1=1" ;
			break ;
			case '#':
				$filtreCates = "categorie=''" ;
			break ;	
			default:
				$filtreCates = "categorie='".utf8_decode($this->xham->getr('Categorie'))."'" ;
			break ;
		}
		switch ( $this->xham->getr('idUser') ) {
			case 'all':
				$filtreUsers = "(idUser='".$this->xham->user->getLogin ()."' OR public=1)" ;
			break ;
			case '#':
				$filtreUsers = "idUser='".$this->xham->user->getLogin ()."'" ;
			break ;	
			default:
				$filtreUsers = "idUser='".$this->xham->getr('idUser')."'" ;
			break ;
		}
		switch ( $this->xham->getr('Mode') ) {
			default:
				$filtreModes = "etat like '".$this->xham->getr('Mode')."'" ;
			break ;
		}
		$filtre = " $filtreCates and $filtreUsers and $filtreModes " ;
		
		$res = $req -> getGen ( "$filtre order by etat, date", "resultQuery" ) ;
		$ras = $req -> getGen ( "$filtreCates and $filtreUsers and etat='afaire' order by etat", "resultQuery", 'sum(importance) nbHeures' ) ;
		$rus = $req -> getGen ( "$filtreCates and $filtreUsers and etat='encours' order by etat", "resultQuery", 'sum(importance) nbHeures' ) ;
		$ris = $req -> getGen ( "$filtreCates and $filtreUsers and etat='termines' order by etat", "resultQuery", 'sum(importance) nbHeures' ) ;
		
		//print affTab ( $ras['INDIC_SVC'] ) ;
		$mod = new ModeliXe ( "TodoListe.html" ) ; 
		$mod -> SetModeliXe ( ) ;
		
		$mod -> MxText ( 'recap', 'A faire : '.($ras['nbHeures'][0]?$ras['nbHeures'][0]:'0').'h - En cours : '.($rus['nbHeures'][0]?$rus['nbHeures'][0]:'0').'h - Terminés : '.($ris['nbHeures'][0]?$ris['nbHeures'][0]:'0').'h' ) ;
		
		$date = new clDate ( ) ;
		if ( $res['INDIC_SVC'][2] == 0 ) $mod -> MxBloc ( 'todo', 'replace', '<td colspan="6">Aucun todo.</td>' ) ;
		else for ( $i = 0 ; isset ( $res['idTodo'][$i] ) ; $i++ ) {
			$js  = XhamTools::genAjax ( 'onClick', 'refreshTodo', 'ajax=changeStatus&amp;idTodo='.$res['idTodo'][$i].'&amp;navi='.$this->xham->genNaviFull ( ) ) ;
			/*
			if ( $res['etat'][$i] != 'termines' ) $mod -> MxText ( 'todo.js', $js ) ;
			else $mod -> MxText ( 'todo.js', '' ) ;
			*/
			$mod -> MxText ( 'todo.js', $js ) ;
			$mod -> MxText ( 'todo.class', $res['etat'][$i] ) ;
			$mod -> MxText ( 'todo.idUser', $res['idUser'][$i] ) ;
			$mod -> MxText ( 'todo.categorie', $res['categorie'][$i] ) ;
			$mod -> MxText ( 'todo.nom', $res['nom'][$i] ) ;
			$date -> setDate ( $res['date'][$i] ) ;
			$mod -> MxText ( 'todo.date', $date->getDate('Y-m-d H:i:s') ) ;
			if ( $res['dateLimite'][$i] != '0000-00-00 00:00:00' ) {
				$date -> setDate ( $res['dateLimite'][$i] ) ;
				$mod -> MxText ( 'todo.dateLimite', $date->getDate('Y-m-d') ) ;
			} else $mod -> MxText ( 'todo.dateLimite', VIDEDEFAUT ) ;
			$mod -> MxText ( 'todo.importance', $res['importance'][$i] ) ;
			$mod -> MxBloc ( 'todo', 'loop' ) ;
		}
		$af = $mod -> MxWrite ( "1" ) ;
		if ( $this->ajax ) $this->af .= $af ;
		else return $af ;
	}

	function genAffichageTotal ( ) {
		
		$mod = new ModeliXe ( "TodoTotal.html" ) ; 
		$mod -> SetModeliXe ( ) ;
		//$note = new XhamNotes ( $this->xham, '' ) ;
		//$mod -> MxText ( 'texteLibre', $note->afficherNote ( 'Todo', 'Todo' ) ) ;
		// Utilisateurs
		$tabU1['#'] = 'vous appartenant' ;
		$tabU1['all'] = 'de tous les utilisateurs' ;
		$tabU2 = $this->getUsers ( ) ;
		$tabU = array_merge($tabU1,$tabU2) ;
		// Catégories
		$tabC0[''] = VIDEDEFAUT ;
		$tabC1['all'] = 'de toutes les catégories' ;
		$tabC1['#'] = 'sans catégorie' ;
		$tabC2 = $this -> xham -> getListeItems ( "Catégories Todo" ) ;
		$tabC = array_merge($tabC1,$tabC2) ;
		// Modes d'affichage
		$tabM['%'] = 'Afficher tous les todo' ;
		$tabM['afaire'] = 'Afficher les todo à faire' ;
		$tabM['encours'] = 'Afficher les todo en cours' ;
		$tabM['termines'] = 'Afficher les todo terminés' ;
		// Importances 
		for ( $i = 0 ; $i < 300 ; $i++ ) $tabI[$i] = $i ;
		//$tabI = array ( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 15, 20, 25, 30, 35, 40, 45, 50, 75, 100, 150, 200, 300 ) ;
		// Public
		$tabP['0'] = 'Non' ;
		$tabP['1'] = 'Oui' ;
		
		if ( ! $this->xham->getr ( 'Categorie' ) ) $this->xham->setr ( 'Categorie', 'all' ) ;
		if ( ! $this->xham->getr ( 'idUser' ) ) $this->xham->setr ( 'idUser', '#' ) ;
		if ( ! $this->xham->getr ( 'Mode' ) ) $this->xham->setr ( 'Mode', '%' ) ;
		
		
		$js  = XhamTools::genAjax ( 'onChange', 'refreshTodo', 'ajax=refresh&amp;navi='.$this->xham->genNaviFull ( ) ) ;
		
		$mod -> MxSelect( 'listeModes', 'listeModes', $this->xham->getr ( 'Mode' ), $tabM, '', '', 'id="listeModes" '.$js ) ;
		$mod -> MxSelect( 'listeUsers', 'listeUsers', $this->xham->getr ( 'idUser' ), $tabU, '', '', 'id="listeUsers" '.$js ) ;
		$mod -> MxSelect( 'listeCates', 'listeCates', $this->xham->getr ( 'Categorie' ), $tabC, '', '', 'id="listeCates" '.$js ) ; 
		
		$mod -> MxFormField ( 'nom', 'text', 'nom', '', 'class="inputTodo" id="Nom" maxlength=64' ) ;
		$mod -> MxFormField ( 'dateLimite', 'text', 'dateLimite', '', 'class="dateTodo" id="dateLimite"' ) ;
		$mod -> MxSelect( 'listeCates2', 'listeCates2', '0', $tabC0+$tabC2, '', '', 'id="Categorie" class="selectTodo"' ) ;
		$mod -> MxSelect( 'listeImportances', 'listeImportances', '0', $tabI, '', '', 'id="Importance" class="selectTodo"' ) ;
		$mod -> MxSelect( 'listePublic', 'listePublic', '1', $tabP, '', '', 'id="Public" class="selectTodo"' ) ;
		
		$js  = XhamTools::genAjax ( 'onClick', 'addTodo', 'ajax=addTodo&amp;navi='.$this->xham->genNaviFull ( ) ) ;
		$mod -> MxText ( 'valider', '<img src="images/valider.gif" alt="valider" class="validerTodo" '.$js.'/>' ) ;
		
		$mod -> MxText ( 'listeTodo', $this->genListeTodo ( ) ) ;
		$af = $mod -> MxWrite ( "1" ) ;
		$this->af = $af ;
	}
  	
  	function getUsers ( ) {
  		$config['type'] = "MySQL" ;
		$config['host'] = MYSQL_HOST ;
		$config['login'] = MYSQL_USER ;
		$config['password'] = MYSQL_PASS ;
		$config['db'] = BASEXHAM;
		$requete = new clResultQuery ;
		$req= "SELECT DISTINCT(idUser) FROM todo" ;
  		$res = $requete -> Execute ( "Query", $req, $config) ;
  		$tab = array ( ) ;
		for ( $i = 0 ; isset ( $res['idUser'][$i]) ; $i++ ) {
			$tab[$res['idUser'][$i]] = 'de '.$res['idUser'][$i] ;			
		}
		return $tab ;
  	}
  	
	// Retourne l'affichage généré par la classe.
  	function getAffichage ( ) {
    	if ( $this->ajax ) {
    		$this->xham->stopAffichage = 1 ;
    		print $this->af ;
    		//print utf8_decode($this->af) ;
    	} else return $this->af ;
  	}
}
?>
