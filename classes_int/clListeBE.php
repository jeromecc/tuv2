<?php

// Titre  : Classe ListeBE
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 02 Août 2006

// Description : 
// Gestion de la liste destinée au BE

class clListeBE {

// Attributs de la classe.


// Constructeur.
    function __construct (  ) {
	global $session ;
	// Chargement du template ModeliXe.
	$mod = new ModeliXe ( "ListeBE.mxt" ) ;
	$mod -> SetModeliXe ( ) ;
	$fs = array ( 'Tous les messages à traiter', 'Les changements d\'UF', 'Les sorties', 'Les dernières actions effectuées' ) ;
	$mod -> MxSelect ( 'filtre', 'f', (isset($_REQUEST['f'])?stripslashes($_REQUEST['f']):''), $fs, '', '', "onChange=reload(this.form)" ) ;
	$mod -> MxText ( "listeBE", $this->genListe ( ) ) ;
	// Récupération du code HTML généré.
	$mod -> MxHidden ( "hidden", "navi=".$session -> genNavi ( $session->getNavi(0), $session->getNavi(1) ) ) ;
	$this->af .= $mod -> MxWrite ( "1" ) ;
    }

    // Génération de la liste des actions à réaliser.
    function genListe ( ) {
	global $session ;
	global $options ;

	if ( $session->getNavi ( 2 ) == 'Valider' ) {
	// On valide la ligne sur laquelle on vient de cliquer.
	    $id = $session->getNavi ( 3 ) ;
	    eko ( $id ) ;
	    $date = new clDate ( ) ;
	    $data['etat'] = $date->getDatetime ( ) ;
	    $requete = new clRequete ( BDD, 'bal', $data ) ;
	    $sql = $requete->updRecord ( 'id='.$id ) ;
	}

	// Préparation de la requête.
	$req = new clResultQuery ;
	$URL = 1 ;
	$item['urlimg'] = 'images/bt-valider.gif' ;
	if ( $options -> getOption ( "TriListeBE" ) == "nom" ) $order = " ORDER BY nom" ; else $order = '' ;
	switch ( $_REQUEST['f'] ) {
	    case '1':
		$param['cw'] = "WHERE etat='' AND type='UHCD' $order" ;
		break ;
	    case '2':
		$param['cw'] = "WHERE etat='' AND type='Sortie' $order" ;
		break ;
	    case '3':
		$URL = 0 ;
		$item['urlimg'] = 'images/valider.gif' ;
		$param['cw'] = "WHERE etat!='' ORDER BY etat DESC LIMIT 0, 200" ;
		break ;
	    default:
		$param['cw'] = "WHERE etat='' $order" ;
		break ;

	}

	// Exécution de la requête.
	$res = $req -> Execute ( "Fichier", "getBal", $param, "ResultQuery" ) ;
	$list = new ListMaker ( "template/ListeBE.html" ) ;
	for ( $i = 0 ; isset ( $res['id'][$i] ) ; $i++ ) {

	    // récupération date admission
	   /* $dt_adm = "Date inconnue";
	    $sql = "SELECT dt_admission FROM `patients_presents` WHERE nsej='".$res['nsej'][$i]."'";
	    $obRequete = new clRequete(BDD, 'patients_presents', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );
	    $tabResult = $obRequete->exec_requete($sql, 'tab');
	    if (isset($tabResult[0])) $dt_adm = $tabResult[0]['dt_admission'];
	    else {
		 $obRequete = new clRequete(BDD, 'patients_sortis', array() ,MYSQL_HOST, MYSQL_USER , MYSQL_PASS );
		$sql = "SELECT dt_admission FROM `patients_sortis` WHERE nsej='".$res['nsej'][$i]."'";
		$tabResult = $obRequete->exec_requete($sql, 'tab');
		if (isset($tabResult[0])) $dt_adm = $tabResult[0]['dt_admission'];
	    }*/

	    $item['dt_adm'] = $res['date_admission'][$i];
	    $item['ilp'] = $res['ilp'][$i] ;
	    $item['nsej'] = $res['nsej'][$i] ;
	    $item['patient'] = $res['nom'][$i].' '.ucfirst(strtolower($res['prenom'][$i])) ;
	    $item['uf'] = $res['uf'][$i] ;
	    if ( $res['dest_attendue'][$i] ) $item['destAttendue'] = $res['dest_attendue'][$i] ;
	    else $item['destAttendue'] = '--' ;
	    $item['action'] = $res['action'][$i] ;
	    $date = new clDate ( $res['date'][$i] ) ;
	    $item['date'] = $date -> getDate ( 'd-m-Y H:i:s') ;
	    if ( $URL ) $item['urlbe'] = URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), 'Valider', $res['id'][$i] ).'&f='.$_REQUEST['f'] ;
	    else $item['urlbe'] = URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1) ).'&f='.$_REQUEST['f'] ;
	    $list->addItem ( $item ) ;
	}
	// Récupération du code HTML généré.
	return $list->getList ( ) ;
    }


    // Renvoie l'affichage généré par la classe.
    function getAffichage ( ) {
	return $this->af ;
    }
}

?>