<?php
// Titre  : Classe CORA
// Auteur : auteur Alain Falanga (CH Brignoles)
// Date   : 28 Juin 2007
// Classe           : clDllCoraTU     
// Héritage classe  : cRPC            auteur Alain Falanga (CH Brignoles)

class clDllCoraTU {
    // Attributs de la classe.
  
  private $dll;
  
  // Contient l'affichage généré par la classe.
  private $af ;
  // Contient les messages d'informations
  private $infos ;
  // Contient les messages d'erreurs.
  private $erreurs ;
  
  // Constructeur en paramètre on indique l'idpatient.
  /****************************************************************************/
  function __construct ( $idpatient ) {
  /****************************************************************************/
    global $session;
    global $options;
    
    // Récupération du patient.
    $this->patient   = new clPatient( $idpatient );
    $this->dll       = new cRPC($_SERVER[REMOTE_ADDR], $options->getOption('CCAMExterne_MRPCPORT'),$options->getOption('CCAMExterne_MRPCTIMEOUT'));
    }
    
  // ouverture de CORA.
  /****************************************************************************/
  function openCora ( ) {
  /****************************************************************************/
    
    global $session;
    global $options;
    
    $idu         = $this->patient->getIDU ( );
    $idpass      = $this->patient->getNSej ( );
    $iduf        = $this->patient->getUF ( );
    $idpatient   = $this->patient->getID ( );
    //eko($idpatient);
    
    if ( $options->getOption('CCAMExterne') ) {
      // Version CHB Start
      $idmedecin   = $session->getUid( );
      $mode        = "E";
      $result = $this->dll->OpenCora($idpatient,$idmedecin,$mode);
      return $result; // Pour gérer les erreurs (AF -10-01-08)
      // Version CHB Stop
    }
    else {

      if ( !$options->getOption('CCAMExterne_IDMEDECIN') )
        $idmedecin   = $session->getUid( );
      else
        $idmedecin   = $options->getOption('CCAMExterne_IDMEDECIN');

      $mode   = $options->getOption('CCAMExterne_MODE');
      $result = $this->dll->OpenCora2($idu,$idpass,$iduf,$idmedecin,$mode);
      return $result;

    }
}    
  
  // Renvoie l'affichage généré par la classe.
  /****************************************************************************/
  function getAffichage ( ) {
  /****************************************************************************/
    return $this->af ;
  }
  
}

?>


