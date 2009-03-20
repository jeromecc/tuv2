<?php

// Titre  : Classe Messagerie
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 17 Janvier 2006

// Description : 
// Cette classe gère un echange de messages.

define ( 'TABLEMESS', 'messagerie' ) ;

class clMessagerie {

  private $af ;

  function __construct ( ) {
    $this->genSession ( ) ;
    $this->genAffichage ( ) ;
  }

  function genSession ( ) {
    if ( isset ( $_GET['showMessagerie'] ) AND $_GET['showMessagerie'] ) {
      $_SESSION['showMessagerie'] = 1 ;
    } elseif (  isset ( $_GET['showMessagerie'] ) AND ! $_GET['showMessagerie'] ) {
      $_SESSION['showMessagerie'] = 0 ;
    }
  }

  function genAffichage ( ) {
    global $session ;
    $mod = new ModeliXe ( "Messagerie.mxt" ) ;
    $mod -> SetModeliXe ( ) ;

    $req = new clResultQuery ;
    $param['cw'] = "WHERE pour='TOUS' AND statut NOT LIKE '%".$session->getUid()."%'" ;
    $res = $req -> Execute ( "Fichier", "getMessagerie", $param, "ResultQuery" ) ;
    $param['cw'] = "WHERE pour='".$session->getUid()."' AND statut NOT LIKE '%".$session->getUid()."%'" ;
    $resPerso = $req -> Execute ( "Fichier", "getMessagerie", $param, "ResultQuery" ) ;
    
    $mod -> MxText ( "messages", $res['INDIC_SVC'][2] ) ;
    $mod -> MxText ( "messagesPerso", $resPerso['INDIC_SVC'][2] ) ;
    if ( ! isset ( $_SESSION['showMessagerie'] ) OR ! $_SESSION['showMessagerie'] ) {
      $mod -> MxImage ( "imgAffichage", URLIMGAFF, "Afficher la messagerie XHAM" ) ;
      $mod -> MxUrl  ( "lienAffichage", URLNAVI.$session->genNaviFull ( )."&showMessagerie=1" ) ;
    } else {
      $mod -> MxImage ( "imgAffichage", URLIMGMAS, "Masquer la messagerie XHAM" ) ;
      $mod -> MxUrl  ( "lienAffichage", URLNAVI.$session->genNaviFull ( )."&showMessagerie=0" ) ;
      $this->genMessagerie ( ) ;
    }
    $mod -> MxImage ( "imgFermer", URLIMG."close2.png", "Masquer" ) ;
   
    $this->af .= $mod -> MxWrite ( "1" ) ;
  }

  function genMessagerie ( ) {

  }

  function getAffichage ( ) {
    return $this->af ;
  }

}

?>
