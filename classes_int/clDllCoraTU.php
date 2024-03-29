<?php
// Titre  : Classe CORA
// Auteur : auteur Alain Falanga (CH Brignoles)
// Date   : 28 Juin 2007
// Classe           : clDllCoraTU     
// H�ritage classe  : cRPC            auteur Alain Falanga (CH Brignoles)

class clDllCoraTU {
    // Attributs de la classe.
  
  private $dll;
  
  // Contient l'affichage g�n�r� par la classe.
  private $af ;
  // Contient les messages d'informations
  private $infos ;
  // Contient les messages d'erreurs.
  private $erreurs ;
  
  // Constructeur en param�tre on indique l'idpatient.
  /****************************************************************************/
  function __construct ( $idpatient ) {
  /****************************************************************************/
    global $session;
    global $options;
    
    // R�cup�ration du patient.
    $this->patient   = new clPatient( $idpatient );
    $this->dll       = new cRPC($_SERVER[REMOTE_ADDR], $options->getOption('CCAMExterne_MRPCPORT'),$options->getOption('CCAMExterne_MRPCTIMEOUT'));
    }
    
  // ouverture de CORA.
  /****************************************************************************/
  function openCora ( ) {
  /****************************************************************************/

    global $session;
    global $options;
    global $patient;

    $idu         = $this->patient->getIDU ( );
    $idpass      = $this->patient->getNSej ( );
    $iduf        = $this->patient->getUF ( );
    $idpatient   = $this->patient->getID ( );
    //eko($idpatient);

    if ( $options->getOption('CCAMExterne') ) {
      // Version CHB Start
      $idmedecin   = $session->getUid( );

      // AF/DB 22-04-09 Support du mode E ou R en fonction de l'UF du patient
      // Start
      // Ligne � supprimer $mode   = 'E';
      if ($patient->getUF () == $options->getOption('numUFUHCD'))
       $mode = 'R';
      else
       $mode = 'E';
      // Stop

      $result = $this->dll->OpenCora($idpatient,$idmedecin,$mode);
      return $result; // Pour g�rer les erreurs (AF -10-01-08)
      // Version CHB Stop
    }
    else {
      if ( !$options->getOption('CCAMExterne_IDMEDECIN') )
        $idmedecin   = $session->getUid( );
      else
        $idmedecin   = $options->getOption('CCAMExterne_IDMEDECIN');
      // AF/DB 22-04-09 Support du mode E ou R en fonction de l'UF du patient
      // Start
      if ($patient->getUF () == $options->getOption('numUFUHCD'))
       $mode = 'R';
      else
       $mode = 'E';
      // Ligne � supprimer $mode   = $options->getOption('CCAMExterne_MODE');
      // Stop

      $result = $this->dll->OpenCora2($idu,$idpass,$iduf,$idmedecin,$mode);
      return $result;
    }
}    
  
  // Renvoie l'affichage g�n�r� par la classe.
  /****************************************************************************/
  function getAffichage ( ) {
  /****************************************************************************/
    return $this->af ;
  }
  
}

?>


