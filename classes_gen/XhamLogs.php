<?php
/*
 * Created on 7 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
// Description : 
// Gestion des logs stockés dans la table 'logs'
// du moteur XHAM.

class XhamLogs {

	private $nolog ;
	public $logSup ;

  	function __construct ( $xham ) {
  		$this->xham = $xham ;
		$this->noLog = 0 ;
  	}

	function noLog ( ) {
		$this->noLog = 1 ;
	}

	function IsNoLog ( ) {
		return $this->noLog ;
	}

	function setLogSup ( $logSup = '' ) {
		$this->logSup = $logSup ;
	}

  	function addLog ( $type, $description, $idcible='' ) {
    	if ( $description != "Configuration|cron" AND $description != "Importation" ) {
      		$date = new clDate ( ) ;
      		$data['idapplication'] = IDAPPLICATION ;
      		if ( isset ( $session ) ) $data['iduser'] = $this -> xham -> user -> getLogin ( ) ;
      		elseif (is_object($this -> xham) && is_object($this -> xham -> user) )
      			$data['iduser'] = $this -> xham -> user -> getLogin ( ) ;
      		else $data['iduser'] = ($_SESSION['informations']['iduser']?$_SESSION['informations']['iduser']:"Invité") ; 
      		$data['idcible'] = $idcible ;
      		// print $this->logSup ;
      		$data['type']         = $type ;
      		$data['ip']           = $_SERVER['REMOTE_ADDR'] ;
      		$data['date']         = $date -> getDatetime ( ) ;
      		$data['description']  = $description ;
      		$finTemps = XhamTools::temps ( ) ;
      		$tpPage = $finTemps - $this -> xham -> debTemps ;
      		$data['tempsPage']    = $tpPage ;
      		$data['tempsSQL']     = $this -> xham -> tpRequetes ;
      		$data['nombreSQL']    = $this -> xham -> nbRequetes + 1 ;
      		// Appel de la classe Requete.
      		$req = new XhamRequete ( BASEXHAM, TABLELOGS, $data ) ;
      		// Exécution de la requete.
      		$res = $req -> addRecord ( ) ;
      		// print affTab ( $res ) ;
      		// Limitation du nombre de lignes dans la table logs... Désactivé, mais fonctionne parfaitement.
      		//$mini = $res['cur_id'] - 150 ; 
      		//$res = $requete->delRecord ( "idlog<=$mini" ) ;
    	}
  	}
}
 
 
?>
