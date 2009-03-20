<?php

// Titre  : Classe Logs
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 08 Juillet 2005

// Description : 
// Gestion des logs stock�s dans la table 'logs'
// du moteur XHAM.


class clLogs {

  private $rep ;

  function __construct ( $rep='' ) {

  }

  function addLog ( $type, $description, $idcible='' ) {
    global $session ;
    global $debTemps ;
    global $nbRequetes ;
    global $tpRequetes ;
    if ( $description != "Configuration|cron" AND $description != "Importation" ) {
      $date = new clDate ( ) ;
      $data['idapplication'] = IDAPPLICATION ;
      if ( isset ( $session ) ) $data['iduser'] = $session->getUid ( ) ;
      else $data['iduser'] = ($_SESSION['informations']['iduser']?$_SESSION['informations']['iduser']:"Invit�") ; 
      if ( $idcible ) $data['idcible'] = $idcible ;
      $data['type']         = $type ;
      $data['ip']           = $_SERVER['REMOTE_ADDR'] ;
      $data['date']         = $date -> getDatetime ( ) ;
      $data['description']  = $description ;
      $finTemps = temps ( ) ;
      $tpPage = $finTemps - $debTemps ;
      $data['tempsPage']    = $tpPage ;
      $data['tempsSQL']     = $tpRequetes ;
      $data['nombreSQL']    = $nbRequetes + 1 ;
      // Appel de la classe Requete.
      $requete = new clRequete ( BASEXHAM, TABLELOGS, $data ) ;
      // Ex�cution de la requete.
      $res = $requete->addRecord ( ) ;
      // Limitation du nombre de lignes dans la table logs... D�sactiv�, mais fonctionne parfaitement.
      //$mini = $res['cur_id'] - 150 ; 
      //$res = $requete->delRecord ( "idlog<=$mini" ) ;
    }
    
  }

  

}

?>
