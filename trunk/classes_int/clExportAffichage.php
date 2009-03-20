<?php

// Titre  : Classe ExportAffichage
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 30 Juin 2005

// Description : 
// Permet d'exporter des bouts d'affichage du terminal
// des urgences.

class clExportAffichage {

  // Attributs de la classe.
  // Contient l'affichage généré par la classe.
  private $af ;

  // Constructeur.
  function __construct ( ) {
    global $session ;
    $this->genAffichage ( ) ;
  }

  // Génération de l'affichage de cette classe.
  function genAffichage ( ) {
    global $session ;
    global $stopAffichage ;
    $stopAffichage = 1 ;
    $idpass = $session -> getNavi ( 2 ) ;
    $req = new clResultQuery ;
    // On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
    $param2['table'] = PPRESENTS ;
    $param2['cw'] = "WHERE nsej='".$idpass."'" ;
    $ras = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
    // On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
    $param3['table'] = PSORTIS ;
    $param3['cw'] = "WHERE nsej='".$idpass."'" ;
    $rus = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery" ) ;

    if ( $ras['INDIC_SVC'][2] ) {
      $type  = "Presents" ;
      $table = PPRESENTS ;
      $idpatient = $ras['idpatient'][0] ;
      $ok = 1 ;
    } elseif ( $rus['INDIC_SVC'][2] ) {
      $type  = "Sortis" ;
      $table = PSORTIS ;
      $idpatient = $rus['idpatient'][0] ;
      $ok = 1 ;
    }

    if ( $ok ) {
      $patient = new clFichePatient ( $type, $table, $idpatient, 1 ) ;
      switch ( $session -> getNavi ( 1 ) ) {
      case 'EtatCivil':
	$this -> af .= $patient -> EtatCivil ( ) ;
	break ;
      case 'HistoriquePassage':
	$this -> af .= $patient -> Historique ( ) ;
	break ;
      case 'HistoriqueDocuments':
	$this -> af .= $patient -> HistoriqueDocs ( ) ;
	break ;
      case 'Messages':
	$this -> af .= $patient -> viewMessages ( ) ;
	break ;
      case 'DocumentsEdites':
	$this -> af .= $patient -> Documents ( ) ;
	break ;
      case 'DiagnosticsActes':
	$this -> af .= $patient -> getActesDiagnostics ( ) ;
	break ;
      case 'Informations':
	$this -> af .= $patient -> Informations ( ) ;
	break ;
      default :
	$stopAffichage = 0 ;
	break ;
      }
    }
  }

  // Renvoie l'affichage généré par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}

?>