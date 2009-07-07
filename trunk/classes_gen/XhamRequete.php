<?php
/*
 * Created on 7 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */

// Description : 
// Cette classe permet de faire des INSERTs ou des UPDATEs
// dans une base MySQL tr�s facilement en lui passant un
// tableau format� contenant les valeurs � ins�rer.

class XhamRequete {
  // D�claration des attributs de la classe.
  // Base de donn�es.
  private $db ;
  // Table.
  private $table ;
  // Donn�es � ins�rer.
  private $data ;

  //tronquage par defaut d�sactiv� de la longueur de colonne
  public $isCutData ;

  function getData ( ) { return $this->data ; }
  function getTable ( ) { return $this->table ; }
  function getDb ( ) { return $this->db ; }
  
  // Constructeur.
  function __construct ( $db, $table='', $data='', $h='', $u='', $p='' ) {
    // Initialisation des attributs.
    if(! $data)
    	$data = array();
    $this->db = $db ;
    $this->data = $data ;
    $this->table = $table ;
    if ( $h AND $u ) $this->conn = @mysql_connect ( $h, $u, $p ) ;
    else $this->conn = @mysql_connect ( MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
    if ( $db ) mysql_select_db($db);
    $this->isCutData = false ;
  }

  // R�cup�ration de l'identifiant de connexion.
  function getConn ( ) {
    return $this->conn ;
  }
  
  // Lancement des TRUNCATEs.
  function truncateTable () {
    global $xham ;
    $debut = $this->temps();
    $truncate = "TRUNCATE TABLE ".$this->table;
    mysql_query ( $truncate ) ;
    $sql['errno'] = mysql_errno ( ) ;
    $sql['error'] = mysql_error ( ) ;
    $sql['cur_id'] = mysql_insert_id ( ) ;
    $sql['requete'] = $truncate ;
    if ( $sql['error'] AND isset ( $xham ) ) $xham -> addErreur ( "clRequete : ".$sql['error']."<br/>Requ�te : ".$sql['requete'] ) ;
    $fin = $this->temps();
    $total = $fin - $debut;
    $total = substr($total,0,8);
    if ( DEBUGSQL ) eko ( "Temps d'ex�cution de la requ�te : $total ($truncate)" ) ;
    $xham->nbRequetes++ ;
    $xham->tpRequetes += $total ;
    return $sql ;
  }
  
  // Lancement des DELETEs.
  function delRecord ( $cw='jambon' ) {
  	global $xham ;
    $debut = $this->temps();
    $delete = "DELETE FROM ".$this->table." WHERE $cw" ;
    mysql_query ( $delete ) ;
    $sql['errno'] = mysql_errno ( ) ;
    $sql['error'] = mysql_error ( ) ;
    $sql['cur_id'] = mysql_insert_id ( ) ;
    $sql['requete'] = $delete ;
    if ( $sql['error'] AND isset ( $xham ) ) $xham -> addErreur ( "clRequete : ".$sql['error']."<br/>Requ�te : ".$sql['requete'] ) ;
    $fin = $this->temps();
    $total = $fin - $debut;
    $total = substr($total,0,8);
    if ( DEBUGSQL ) eko ( "Temps d'ex�cution de la requ�te : $total ($delete)" ) ;
    $xham->nbRequetes++ ;
    $xham->tpRequetes += $total ;
    return $sql ;
  }

  // Lancement des INSERTs. 
  function addRecord ( ) {
  	global $xham ;
    $debut = $this->temps();
    $data = $this -> formatData ( ) ;
    reset ( $data ) ;
    while ( list ( $key, $val ) = each ( $data ) ) {
      if ( isset ( $cols ) ) $cols .= $key.",";
      else $cols = $key.",";
      if ( isset ( $values ) ) $values .= $val."," ;
      else $values = $val."," ;
    }
    $cols = substr ( $cols, 0, strlen ( $cols ) - 1 ) ;
    $values = substr ( $values, 0, strlen ( $values ) - 1 ) ;
    $insertion = "insert into ".$this->table."(".$cols.") values (".$values.")" ;
    mysql_query ( $insertion ) ;
    $sql['errno'] = mysql_errno ( ) ;
    $sql['error'] = mysql_error ( ) ;
    $sql['cur_id'] = mysql_insert_id ( ) ;
    $sql['requete'] = $insertion ;
    if ( $sql['error'] AND isset ( $xham ) ) $xham -> addErreur ( "clRequete : ".$sql['error']."<br/>Requ�te : ".$sql['requete'] ) ;
    //eko($sql[error]);
    $fin = $this->temps();
    $total = $fin - $debut;
    $total = substr($total,0,8);
    if ( DEBUGSQL ) eko ( "Temps d'ex�cution de la requ�te : $total ($requete)" ) ;
    $xham->nbRequetes++ ;
    $xham->tpRequetes += $total ;
    return $sql ;
  }

  // Mise � jour d'un enregistrement.
  function updRecord ( $cw='jambon' ) {
	global $xham ;
    $debut = $this->temps();
    $data = $this -> formatData ( ) ;
    if ( $cw != "" ) { $cw = " where $cw" ; }
    reset ( $data ) ;
    while ( list ( $key, $val ) = each ( $data ) ) { 
      if ( isset ( $set ) ) $set .= $key."=".$val." ," ; 
      else $set = $key."=".$val." ," ; 
    }
    $set = substr ( $set, 0, strlen ( $set ) - 1 ) ;
    $update = "update ".$this->table." set ".$set .$cw ;
    //print "<br/>".$update."<br/>" ;
    mysql_query ( $update ) ;
    $sql['errno'] = mysql_errno() ;
    $sql['error'] = mysql_error() ;
    $sql['affected_rows'] = mysql_affected_rows() ;
    $sql['requete'] = $update ;
    if ( $sql['error'] AND isset ( $xham ) ) $xham -> addErreur ( "clRequete : ".$sql['error']."<br/>Requ�te : ".$sql['requete'] ) ;
    $fin = $this->temps();
    $total = $fin - $debut;
    $total = substr($total,0,8);
    if ( DEBUGSQL ) eko ( "Temps d'ex�cution de la requ�te : $total ($update)" ) ;
    $xham->nbRequetes++ ;
    $xham->tpRequetes += $total ;
    return $sql ;
  }
  

	/*fait un select * from cw*/
	/*retourne au format resultquery ou tab*/
	/*a finir*/
	// Damien Borel - 12/11/2007 : Ajout du $select en Test
	function getGen($cw='',$format='',$select='') {
		global $xham ;
		if (! $cw ) $cw = ' 1=1 ';
    	$debut = $this->temps();
    	if ( $select ) $requete_test = "select $select from ".$this->table." where ".$cw;
		else $requete_test = "select * from ".$this->table." where ".$cw;
		
		//print $requete_test;
		//eko($requete_test);
		$res = mysql_query ( $requete_test) ;
		$nb_occ = mysql_num_rows($res);
		$sql['errno'] = mysql_errno() ;
		$sql['error'] = mysql_error() ;
    	$sql['affected_rows'] = mysql_affected_rows() ;
     	if ( $sql['error'] AND isset ( $xham ) ) $xham -> addErreur ( "clRequete : ".$sql['error']."<br/>Requ�te : ".$sql['requete'] ) ;
     	$fin = $this->temps();
     	$total = $fin - $debut;
    	//eko ( "$total = $fin - $debut" ) ;
    	//$total = substr($total,0,8);
    	if ( DEBUGSQL ) eko ( "Temps d'ex�cution de la requ�te : $total ($requete_test)" ) ;
    	$xham->nbRequetes++ ;
    	$xham->tpRequetes += $total ;
    	if($format=='')
			return $res ;
		else if($format=='tab') {
			$res2 = array();
			while ( $r = mysql_fetch_array ( $res,MYSQL_ASSOC) ) {
  				$res2[] = $r ;
			}
			return $res2;
		}else if ( $format = 'resultquery') {
			$INDIC_SVC = array();
			$INDIC_SVC[0] = mysql_errno ( ) ;
			$INDIC_SVC[1] = mysql_error ( ) ;
			$INDIC_SVC[2] = $nb_occ ;
			$INDIC_SVC[15] = $requete_test ;
			$i=0;
			$res2 = array();
			while ( $r = mysql_fetch_array ( $res,MYSQL_ASSOC) ) {
  				foreach($r as $key => $value) {
  					if ( ! isset ($res2[$key])) $res2[$key] = array();
  					$res2[$key][$i]=$value;
  				}
  				$i++;
			}
			$res2['INDIC_SVC']=$INDIC_SVC;
			return $res2 ;
		}
}


	//requete libre format peut �tre egal � 'sql' (ressource sql) "tab" tableau
	function exec_requete($req,$format = 'sql') {
		global $xham ;
		global $errs ;
		$debut = $this->temps();
		$res = mysql_query ( $req ) ;
		//print $req ;
		$sql = array();	
		$sql['errno'] = mysql_errno() ;
		$sql['error'] = mysql_error() ;
		if ( $sql['error'] AND is_object ( $xham ) AND ! defined('NOERROR')) $xham -> addErreur ( "clRequete : ".$sql['error']."<br/>Requ�te : ".$req ) ;
		//print affTab ( $sql ) ;
		$fin = $this->temps();
		$total = $fin - $debut;
		$total = substr($total,0,8);
		if ( DEBUGSQL ) eko ( "Temps d'ex�cution de la requ�te : $total ($req)" ) ;
		$xham->nbRequetes++ ;
		$xham->tpRequetes += $total ;
		if( $format == 'sql')
			return $res ;
		if( $format == 'resultquery') {
			$INDIC_SVC = array();
			$INDIC_SVC[0] = mysql_errno ( ) ;
			$INDIC_SVC[1] = mysql_error ( ) ;
			$INDIC_SVC[2] = @mysql_num_rows($res) ;
			$INDIC_SVC[15] = $req ;
			$i=0;
			$res2 = array();
			while ( $r = @mysql_fetch_array ( $res,MYSQL_ASSOC) ) {
				foreach($r as $key => $value) {
					if ( ! isset ($res2[$key])) $res2[$key] = array();
					$res2[$key][$i]=$value;
				}
				$i++;
			}
			$res2['INDIC_SVC']=$INDIC_SVC;
			return $res2 ;
		}
		$res2 = array();
		while ( $r = mysql_fetch_array ( $res,MYSQL_ASSOC) ) {
		  	$res2[] = $r ;
		}
		return $res2;	
	}

//teste si une ou plusieurs lignes existent d�ja avec les data existantes
function testData() {
 	global $xham ;
    $debut = $this->temps();
    $data = $this -> formatData ( ) ;
    if(! $data) return false;
    $tabtmp = array();
    	foreach($data as $key => $value) {
    		$tabtmp[] = " $key = $value ";
    	}
    $cw = implode(" AND ",$tabtmp);
    $requete_test = "select * from ".$this->table." where $cw";
    //eko($requete_test);
    //$result = @mysql_select_db ( $value_config[db] ) ;
    $res = mysql_query ( $requete_test) ;
    $nb_occ = mysql_num_rows($res);	
    $fin = $this->temps();
    $total = $fin - $debut;
    $total = substr($total,0,8);
    if ( DEBUGSQL ) eko ( "Temps d'ex�cution de la requ�te : $total ($requete)" ) ;
    $xham->nbRequetes++ ;
    $xham->tpRequetes += $total ;
    if( $nb_occ > 0 )
    	return $nb_occ;
    else
    	return false;
}


/*tente l'update et si �a marche pas, fait une insert*/
    function uoiRecord ( $cwi='jambon' ) {
    global $xham ;
    $debut = $this->temps();

    $tmp=explode('=',$cwi);
    $cle=trim($tmp[0]);
    $data = $this -> formatData ( ) ;
    $requete_test = "select $cle from ".$this->table." where ".$cwi;
    //eko($requete_test);
    //$result = @mysql_select_db ( $value_config[db] ) ;
    $res = mysql_query ( $requete_test) ;
    $nb_occ = mysql_num_rows($res);
    
    if ( $nb_occ > 0 ) {
    	if ( $cwi != "" ) { $cw = " where $cwi" ; }
    	reset ( $data ) ;
    	if(! isset($set)) $set='';
    	while ( list ( $key, $val ) = each ( $data ) ) { $set .= $key."=".$val." ," ; }
    	$set = substr ( $set, 0, strlen ( $set ) - 1 ) ;
    	$update = "update ".$this->table." set ".$set .$cw ;
    	//print "<br/>".$update."<br/>" ;
    	//mysql ( $this->db, $update ) ;
    	mysql_query ($update ) ;
    	$sql['errno'] = mysql_errno() ;
    	$sql['error'] = mysql_error() ;
    	$sql['affected_rows'] = mysql_affected_rows() ;
    	$sql['requete'] = $update ;
    	//print $update ;
    } else {
	$tmp=explode('=',$cwi);
	$tmp[0]=trim($tmp[0]);
	$tmp[1]=trim($tmp[1]);
	if(! isset($data[$tmp[0]]))
    	$data[$tmp[0]]=$tmp[1];
	reset ( $data ) ;
	//print afftab($data);
		$values = '';
		$cols = '';
    	while ( list ( $key, $val ) = each ( $data ) ) {
      		$cols .= $key.",";
      		$values .= $val."," ;
    	}
    	$cols = substr ( $cols, 0, strlen ( $cols ) - 1 ) ;
    	$values = substr ( $values, 0, strlen ( $values ) - 1 ) ;
    	$insertion = "insert into ".$this->table."(".$cols.") values (".$values.")" ;
		//print "<br/>".$insertion."<br/>" ;
		//mysql ( $this->db, $insertion ) ;
		mysql_query($insertion ) ;
		$sql['errno'] = mysql_errno() ;
		$sql['error'] = mysql_error() ;
    	$sql['affected_rows'] = mysql_affected_rows() ;
    	$sql['requete'] = $insertion ;
      if ( $sql['error'] AND isset ( $xham ) ) $xham -> addErreur ( "clRequete : ".$sql['error']."<br/>Requ�te : ".$sql['requete'] ) ;
     }
    $fin = $this->temps();
    $total = $fin - $debut;
    $total = substr($total,0,8);
    if ( DEBUGSQL ) eko ( "Temps d'ex�cution de la requ�te : $total ($requete)" ) ;
    $xham->nbRequetes++ ;
    $xham->tpRequetes += $total ;
    return $sql ;
  }

  // fonction permettant de r�cup�rer le temps �coul� depuis l'�poque UNIX ( 1 - 1 1970 )
  function temps() {
    $time = microtime();
    $tableau = explode(" ",$time);
    return ($tableau[1] + $tableau[0]);
  }
  
  // Formatage des donn�es.
  function formatData ( ) {
    global $xham ;
    $data = $this -> data ;
    $desc = $this -> descTable ( ) ;
    reset ( $data ) ;
    while ( list ( $key, $val ) = each ( $data ) ) {
      // On teste si la colonne existe
      if ( ! $desc[$key] AND isset ( $xham ) ) {
      $xham->addErreur ( "Base -> ".$this->db."<br/>Table -> ".$this->table."<br/>Erreur -> $key : La colonne n'existe pas<br>", "1" ) ;
      }
      // On teste si la longueur de la donn�e est coh�rente
      if (  $desc[$key][1] != 'blob' AND $desc[$key][0] < strlen ( $val ) AND isset ( $xham )  AND ! $this->isCutData AND $desc[$key][1] != "blob" ) { // Donn�e trop longue
	$erreur = "$key : <br/>Longueur attendue : ".$desc[$key][0]." <br/>Longueur re�ue : ".strlen($val)."<br/>Contenu : ".str_replace(' ','_',$val).'.' ;
	$xham->addErreur ( "Base -> ".$this->db."<br/>Table -> ".$this->table."<br/>Erreur -> Probl�me de longueur d'une cha�ne<br/>$erreur<br>" ) ;
      }
      	//tronquage �ventuel de la donn�e
      if ( $desc[$key][0] < strlen ( $val ) AND  $this->isCutData) {
      	$val = substr($val, 0,$desc[$key][0]);
      } 
                
      // On teste le type de la donn�e
      switch ( $desc[$key][1] ) {
      case "string":
      case "date":
	  case "time":
      case "datetime":
      case "blob":
		$data[$key] = "'".addslashes(stripslashes($val))."'" ;
		break ;
	  case "int":
	  case "real":
	  	if($val != 'NULL' && ereg('[a-zA-Z]+',$val) ) //si presence de lettres dans un champ numerique : on vide ( anti sql injection )
	  		$data[$key] = "''";
	  	if(! $val) $data[$key] = "''";
	  	if( $val === 0 or $val === '0' ) $data[$key] = "0" ;
	  		break ;
	  case "timestamp":
	  case "year":
	  case "date":
	  case "time":
	  	if($val != 'NULL' && ereg('[a-zA-Z]+',$val) ) //si presence de lettres dans un champ numerique : on vide ( anti sql injection )
	  		$data[$key] = "''";
	  	if(! $val) $data[$key] = "''";
			break;
	  	break ;
	default:
		if(! $val) $data[$key] = "''";
			break;
      }
    }
    return $data ;
  }
  

  
  
  // Description de la table...
  function descTable ( ) {
    global $errs ;
    if ( ( ! isset ( $this->db ) or ! isset ( $this->table ) ) AND isset ( $xham ) ) {
      $xham->addErreur ( "Base -> ".$this->db."<br/>Table -> ".$this->table."<br/>Erreur -> Nom de table ou base incorrecte<br>" ) ;
      $desc = "Param�tres manquants pour la description de table" ;
    } else {
      $infotable = @mysql_list_fields ( $this->db, $this->table ) ;
      if ( ! $infotable AND isset ( $xham ) ) $xham->addErreur ( "Base -> ".$this->db."<br/>Table -> ".$this->table."<br/>Erreur -> Probl�me d'acc�s aux informations de la table.<br>" ) ;
      $nbfield = @mysql_num_fields ( $infotable ) ;
      for ( $i = 0 ; $i < $nbfield ; $i++ ) {
	$desc[mysql_field_name ( $infotable, $i )][0] = mysql_field_len ( $infotable, $i ) ;
	$desc[mysql_field_name ( $infotable, $i )][1] = mysql_field_type ( $infotable, $i ) ;
      }
    }
    return $desc ;
  }

	function testAndMakeCol($col,$type='VARCHAR(64)'){
	global $errs;
	$infos=$this->descTable();
	if (array_key_exists ($col,$infos))
		return 1;
	$requete = "ALTER TABLE ".$this->table." ADD $col $type" ;
	mysql_query ($requete ) ;
	$sql['error'] = mysql_error() ;
	if ( $sql['error'] AND isset ( $xham ) )
		$xham -> addErreur ( "clRequete : ".$sql['error']."<br/>Requ�te : ".$requete ) ;	
    }

}

?>
