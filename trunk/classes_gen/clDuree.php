<?php

// Titre  : Classe Durée
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 04 Janvier 2005

// Description : 
// Principalement pour des calculs de différences entre deux date
// et des calculs d'âges.

class clDuree {

  // Déclaration des attributs.
  private $duree ;
  private $annees ;
  private $mois ;
  private $semaines ;
  private $jours ;
  private $heures ;
  private $minutes ;
  private $secondes ;

  // Constructeur.
  function __construct ( $duree='0' ) {
    $this -> duree = $duree ;
    $this -> setValues ( $duree ) ;
  }

  // Fonction d'accès aux attributs de la classe.
  function getYears    ( ) { return $this->annees ;   }
  function getMonths   ( ) { return $this->mois ;     }
  function getWeeks    ( ) { return $this->semaines ; }
  function getDays     ( ) { return $this->jours ;    }
  function getHours    ( ) { return $this->heures ;   }
  function getMinutes  ( ) { return $this->minutes ;  }
  function getSeconds  ( ) { return $this->secondes ; }


  // Retourne un dateur (ex:J+8)
  function getDateur ( $date='', $type='' ) {
  	//return 'J+8' ;
  	if ( $date ) {
      // Date du jour.
      $d1 = new clDate ( ) ;
      // Date de naissance de la personne.
      $d2 = new clDate ( $date ) ;
      // Calcul de la différence.
      $this->setValues ( $d1 -> getDifference ( $d2 ) ) ;
    }
  	switch ( $type ) {
  		case 'ddj':
  			if ( $this->getDays ( ) >= 0 ) return 'J+'.$this->getDays ( ) ;
  			else return 'J-'.$this->getDays ( ) ;
  		break;
  		case 'ddh':
  			if ( $this->getHours ( ) >= 0 ) return 'H+'.$this->getHours ( ) ;
  			else return 'H-'.$this->getHours ( ) ;
  		break;
  		case 'ddm':
  			if ( $this->getMinutes ( ) >= 0 ) return 'M+'.$this->getMinutes ( ) ;
  			else return 'M-'.$this->getMinutes ( ) ;
  		break;
  	}
  }

  // Calcule l'âge d'une personne.
  function getAge ( $date='' ) {
    // Si une date de naissance est transmise à la classe,
    // alors on calcul la différence avec la date d'aujourd'hui
    // et on récupère ainsi l'âge de la personne.
    if ( $date ) {
      // Date du jour.
      $d1 = new clDate ( ) ;
      // Date de naissance de la personne.
      $d2 = new clDate ( $date ) ;
      // Calcul de la différence.
      $this->setValues ( $d1 -> getDifference ( $d2 ) ) ;
    }
    // Unité de temps choisie automatiquement en fonction de l'âge.
    if ( $this->annees >= 3 ) return $this->annees." ans" ;
    elseif ( $this->mois >= 3 ) return $this->mois." mois" ;
    elseif ( $this->semaines >= 3 ) return $this->semaines." semaines" ;
    elseif ( $this->jours >= 3 ) return $this->jours." jours" ;
    elseif ( $this->heures >= 3 ) return $this->heures." heures" ;
    elseif ( $this->minutes > 1 ) return $this->minutes." minutes" ;
    else return $this->minutes." minute" ;
  }

  // Calcule l'âge précis (2 niveaux) d'une personne.
  function getAgePrecis ( $date='', $sep='<br/>' ) {
    // Si une date de naissance est transmise à la classe,
    // alors on calcul la différence avec la date d'aujourd'hui
    // et on récupère ainsi l'âge de la personne.
    if ( $date ) {
      // Date du jour.
      $d1 = new clDate ( ) ;
      // Date de naissance de la personne.
      $d2 = new clDate ( $date ) ;
      // Calcul de la différence.
      $this->setValues ( $d1 -> getDifference ( $d2 ) ) ;
    }
    // Calcul des durées.
    $res = $this->duree ;
    $annees   = sprintf ( "%d", $res / 31558464 ) ;
    $res      = $res - 31558464 * $annees ;
    $mois     = sprintf ( "%d", $res / 2592000 ) ;
    $res      = $res - 2592000 * $mois ;
    $semaines = sprintf ( "%d", $res / 604800 ) ;
    $res      = $res - 604800 * $semaines ;
    $jours    = sprintf ( "%d", $res / 86400 ) ;
    $res      = $res - 86400 * $jours ;
    $heures   = sprintf ( "%d", $res / 3600 ) ;
    $res      = $res - 3600 * $heures ;
    $minutes  = sprintf ( "%d", $res / 60 ) ;
    $res      = $res - 60 * $minutes ;
    $secondes = $res ;
    // Fabrication de l'affichage.
    if ( $annees   > 1 ) $sa = "s" ; else $sa = '' ; $da = "$annees an$sa"     ;
    if ( $mois     > 1 ) $sM = "s" ; else $sM = '' ; $dM = "$mois mois"           ;
    if ( $semaines > 1 ) $ss = "s" ; else $ss = '' ; $dS = "$semaines semaine$ss" ;
    if ( $jours    > 1 ) $sj = "s" ; else $sj = '' ; $dj = "$jours jour$sj"       ;
    if ( $heures   > 1 ) $sh = "s" ; else $sh = '' ; $dh = "$heures heure$sh"     ;
    if ( $minutes  > 1 ) $sm = "s" ; else $sm = '' ; $dm = "$minutes minute$sm"   ;
    if ( $secondes > 1 ) $se = "s" ; else $se = '' ; $ds = "$secondes seconde$se" ;
    if ( $annees   ) return $da.$sep.$dM ;
    if ( $mois     ) return $dM.$sep.$dS ;
    if ( $semaines ) return $dS.$sep.$dj ;
    if ( $jours    ) return $dj.$sep.$dh ;
    if ( $heures   ) return $dh.$sep.$dm ;
    if ( $minutes  ) return $dm.$sep.$ds ;
    return $ds ;
  }

  // Calcul d'une différence entre deux dates.
  function getDuree ( $date='' ) {
    // Si une date de naissance est transmise à la classe,
    // alors on calcul la différence avec la date d'aujourd'hui
    // et on récupère ainsi l'âge de la personne.
    if ( $date ) {
      // Date du jour.
      $d1 = new clDate ( ) ;
      // Date de naissance de la personne.
      $d2 = new clDate ( $date ) ;
      // Calcul de la différence.
      $this->setValues ( $d1 -> getDifference ( $d2 ) ) ;
    }
    // Calcul des durées.
    $res = $this->duree ;
    $annees   = sprintf ( "%d", $res / 31558464 ) ;
    $res      = $res - 31558464 * $annees ;
    $mois     = sprintf ( "%d", $res / 2592000 ) ;
    $res      = $res - 2592000 * $mois ;
    $semaines = sprintf ( "%d", $res / 604800 ) ;
    $res      = $res - 604800 * $semaines ;
    $jours    = sprintf ( "%d", $res / 86400 ) ;
    $res      = $res - 86400 * $jours ;
    $heures   = sprintf ( "%d", $res / 3600 ) ;
    $res      = $res - 3600 * $heures ;
    $minutes  = sprintf ( "%d", $res / 60 ) ;
    $res      = $res - 60 * $minutes ;
    $secondes = $res ;
    // Fabrication de l'affichage.
    if ( $annees   > 0 ) { if ( $annees   > 1 ) $sa = "s" ; $d .= "$annees année$sa, "     ; }
    if ( $mois     > 0 ) { if ( $mois     > 1 ) $sM = "s" ; $d .= "$mois mois, "           ; }
    if ( $semaines > 0 ) { if ( $semaines > 1 ) $ss = "s" ; $d .= "$semaines semaine$ss, " ; }
    if ( $jours    > 0 ) { if ( $jours    > 1 ) $sj = "s" ; $d .= "$jours jour$sj, "       ; }
    if ( $heures   > 0 ) { if ( $heures   > 1 ) $sh = "s" ; $d .= "$heures heure$sh, "     ; }
    if ( $minutes  > 0 ) { if ( $minutes  > 1 ) $sm = "s" ; $d .= "$minutes minute$sm et " ; }
    if ( $secondes > 0 ) { if ( $secondes > 1 ) $se = "s" ; $d .= "$secondes seconde$se"   ; }
    // On retourne finalement l'affichage.
    return $d ;
  }

  // Calcul d'une différence entre deux dates.
  function getDureeCourte ( $date='' ) {
    // Si une date de naissance est transmise à la classe,
    // alors on calcul la différence avec la date d'aujourd'hui
    // et on récupère ainsi l'âge de la personne.
    if ( $date ) {
      // Date du jour.
      $d1 = new clDate ( ) ;
      // Date de naissance de la personne.
      $d2 = new clDate ( $date ) ;
      // Calcul de la différence.
      $this->setValues ( $d1 -> getDifference ( $d2 ) ) ;
    }
    // Calcul des durées.
    $res = $this->duree ;
    $heures   = sprintf ( "%d", $res / 3600 ) ;
    $res      = $res - 3600 * $heures ;
    $minutes  = sprintf ( "%d", $res / 60 ) ;
    if ( $heures < 10 ) $heures = '0'.$heures ;
    if ( $minutes < 10 ) $minutes = '0'.$minutes ;
    // Fabrication de l'affichage.
    $d .= $heures."h" ; 
    $d .= $minutes."m" ;
    // On retourne finalement l'affichage.
    return $d ;
  }
  
  // Pour convertir des minutes en heures
  static function minToHours ( $min = 0 ) {
  	$h = sprintf ( "%d", $min / 60 ) ;
  	$m = sprintf ( "%2d", $min - $h*60 ) ;
  	if ( $m == 0 ) $min = '00' ; else $min = $m ;
  	return $h."h".$min ;
  }

  // inversion si négatif
  function invertNegatif ( ) {
  	if ( $this -> duree < 0 ) { 
  		
  		$this->setValues ( - $this->duree ) ;
  		return true ; 
  	} else return false ;
  }

  // Initialisation des attributs de la classe.
  function setValues ( $res ) {
    $this -> duree    = $res ;
    $this -> annees   = sprintf ( "%d", $res / 31558464 ) ;
    $this -> mois     = sprintf ( "%d", $res / 2592000 ) ;
    $this -> semaines = sprintf ( "%d", $res / 604800 ) ;
    $this -> jours    = sprintf ( "%d", $res / 86400 ) ;
    $this -> heures   = sprintf ( "%d", $res / 3600 ) ;
    $this -> minutes  = sprintf ( "%d", $res / 60 ) ;
    $this -> secondes = $res ;
  }
}



?>