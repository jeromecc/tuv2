<?php

/*
*******************************************************************************************
*************                                                                 *************
*************               Classe Hopi                                       *************
*************               Damien Borel                                      *************
*************               02.12.2004                                        *************
*************                                                                 *************
*******************************************************************************************
*/

// Gestion des sessions Hopi + récupération des informations.

class clHopi {

  private $hopisession ;

  function __construct ( ) {
    // Mise à jour de l'idSession.
    
    if ( $_GET['idsession'] ) { $_SESSION['hopisession'] = $_GET['idsession'] ; }
    $this->hopisession = $_SESSION['hopisession'] ;
	//print affTab ( $_REQUEST ) ;   
    //$this->hopisession = $_GET['hopisession'] ;
  }

  // Renvoie 1 si la session est valide, rien sinon.
  function Valide ( ) {
    global $errs ;
    // Connexion à la base hopi.
    if ( function_exists( 'OCILogon' ) AND $this->hopisession ) {
      $connexion = @OCILogon ( "hopi", "hopi", "hopi" ) ;
      if ( ! $connexion ) { if ( isset ( $errs ) ) $errs -> addErreur ( "clHopi : Connexion impossible à HOPI." ) ; }
      else {
        $query = "select * from log where log_idsession=".$this->hopisession ;
        $stmt = OCIParse ( $connexion, $query ) ;
        @OCIExecute ( $stmt ) ;
        $nrows = @OCIFetchStatement ( $stmt, $result ) ;
        if ( $nrows != 0 ) {
	  $datedujour = date ( 'YmdHis' ) ;
	  $query = "update log set log_date=to_date('$datedujour','yyyymmddhh24miss') where log_idsession=".$this->hopisession ;
          $result = @OCIParse ( $connexion, $query ) ;
	  @OCIExecute ( $result ) ;
	  oci_close ( $connexion ) ;
	  return 1 ;
        } else oci_close ( $connexion ) ;
      }
    }
  }

  // Récupération des informations de la base hopi.
  function GetInformations ( ) {
    //    $query = "select log_nom,log_prenom,log_fonction,log_fonctions, log_equipes,log_uf,log_uid from hopi.log where log_idsession = ".$this->hopisession ;
    $query = "select * from hopi.log where log_idsession = ".$this->hopisession ;
    if ( function_exists( 'OCILogon' ) ) {
      $conn = @OCILogon ( "hopi", "hopi", "hopi" ) ;
      $stmt = @OCIParse ( $conn, $query ) ;
      @OCIExecute ( $stmt ) ;
      $nrows = @OCIFetchStatement ( $stmt, $results ) ;
      $ldap = new clAuthLdap ( ) ;
      if ( $nrows > 0 )  { 
	$_POST['login'] = $results["LOG_UID"][0] ;
	$ldap -> valide ( 'noBind' ) ;
  	$_SESSION['hopisession'] = '' ;
	return $ldap -> getInformations ( ) ;
      }

      /*
      if ( $nrows > 0 ) {
        $log[uid] = $results["LOG_UID"][0] ;

        $log[nom] = $results["LOG_NOM"][0] ;
        $log[prenom] = $results["LOG_PRENOM"][0] ;
        $log[fonction] = $results["LOG_FONCTION"][0] ;
        $log[fonctions] = explode ( ',', $results["LOG_FONCTIONS"][0] ) ;
        $log[equipes] = explode ( ',', $results["LOG_EQUIPES"][0] ) ;
        $log[uf] = $results["LOG_UF"][0] ;
	$log[org] = $results["LOG_ORGANISATION"][0] ;
      } else { $log = "false" ; }
      $infos[type]   = "Hopi" ;
      $infos[nom]    = $log[nom] ;
      $infos[prenom] = $log[prenom] ;
      $infos[iduser] = $log[uid] ;
      $infos[pseudo] = "Hopi (".$log[uid].")" ;
      $infos[mail]   = $log[uid]."@ch-hyeres.fr" ;
      $infos[uf]     = explode ( ",", str_ireplace ( "'", '', $results["LOG_UF"][0] ) ) ;
      $infos[org]    = $log[org] ;

      // Récupération de la liste des groupes.
      for ( $i = 0 ; isset ( $log[equipes][$i] ) ; $i++ ) $or_equipes .= " OR nomgroupe='".$log[equipes][$i]."'" ;
      for ( $i = 0 ; isset ( $log[fonctions][$i] ) ; $i++ ) $or_fonctions .= " OR nomgroupe='".$log[fonctions][$i]."'" ;
      $param[cw] = "where nomgroupe='HOPI' OR nomgroupe='".$log[uid]."' OR nomgroupe='".$log[fonction]."' $or_equipes $or_fonctions" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getGroupes", $param, "ResultQuery" ) ;
      $infos[idgroupe] = $res[idgroupe][0] ;
      for ( $j = 1 ; isset ( $res[idgroupe][$j] ) ; $j++ ) {
        $infos[idgroupe] .= ",".$res[idgroupe][$j] ;
      }
      //print "<br>Groupe(s) : ".$infos[idgroupe] ;
      */
      @oci_close ( $conn ) ;
      return $infos;
    }
  }
}

?>
