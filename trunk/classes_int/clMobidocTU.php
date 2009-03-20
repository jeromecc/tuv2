<?php

// Classe       : clMobidocTU
// Version      : 1.0
// Date         : 10-01-08 (Version initiale)
// Auteur       : A.Falanga (CH Brignoles - a.falanga@ch-brignoles.fr)
// Utilisation  : Appel à Mobidoc à partir du termianl

class clMobidocTU {

  private $RPC;
  private $patTUid;
  
  function __construct ( $idpatient ) {
    global $options;

    // Récupération du patient.
    $this->patient   = new clPatient( $idpatient );
    $this->patTUid   = $this->patient->getID();
    $this->RPC       = new cRPC($_SERVER[REMOTE_ADDR], $options->getOption('CCAMExterne_MRPCPORT'),$options->getOption('CCAMExterne_MRPCTIMEOUT'));
    }
    
  public function __destruct() {
    $this->patient->free;
    $this->RPC->free;
    }
    
  function OpenMobidoc() {
    $result = $this->RPC->OpenMobidoc($this->patTUid);
    return $result;
    }  
}

?>


