<?php

// Pour une meilleure gestion des timestamp.
if(! isset($relocate) ) $relocate = '' ;

if ( ! defined('ADODB_DATE_VERSION') && file_exists ( realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'adodb-time.inc.php'  ))
	include_once ( realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'adodb-time.inc.php'   ) ;
else if ( ! defined('ADODB_DATE_VERSION') && file_exists ( $relocate."../classes_ext/adodb-time.inc.php" ) )
  include_once ( $relocate."../classes_ext/adodb-time.inc.php" ) ;
elseif ( ! defined('ADODB_DATE_VERSION') && file_exists ( "classes_ext/adodb-time.inc.php" ) )
  include_once ( ""."classes_ext/adodb-time.inc.php" ) ;

// Titre  : Classe Date
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 14 Décembre 2004

// Description : 
// Gestion des dates et Opérations sur les différents formats de date
// existants.

//emmanuel cervetti 11/07/2005
//ajout getSimpleDate ex : 08/02/79

//Emmanuel Cervetti 10/09/09 : compatibilité avec syfony & gestion du numéro de semaine dans l'année

class clDate {

  private $timestamp ; // Valeur de la date
  private $months ;    // Noms des mois 
  private $days ;      // Noms des jours de la semaine
  private $words ;     // Vocabulaire utilisé
  private $minidays ;
  private $minimonths ;

  // Constructeur de la classe Date
  // Arg 1 "$string" : Valeur de la date
  // Arg 1 "$lang"   : Langue utilisée par la classe (anglais ou français)
  function __construct ( $string='', $lang ='français' ) {
  	if( ! defined('DEBUGDATE'))
  		define('DEBUGDATE',false);
    $this->erreur = 0 ;
    // Si $string est égal à "today", alors on initialise l'objet avec la date du jour.
    if ( $string == "today" ) {
      $this->timestamp = time ( ) ;
      if ( DEBUGDATE ) print "Date.php : __construct : Demande de fabrication de la date du jour : ".$this->timestamp."<br>" ;
    // Détection du format Datetime.
    } else if ( eregi ( '[0-2][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (substr($string,11,2),substr($string,14,2),substr($string,17,2),substr($string,5,2),
                         substr($string,8,2),substr($string,0,4));
      if ( DEBUGDATE ) print "Date.php : __construct : Date complète détectée : $string -> ".$this->timestamp."<br>" ;
    // Détection du format Date.
    } else if ( eregi ( '[0-2][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (0,0,0,substr($string,5,2),substr($string,8,2),substr($string,0,4));
      if ( DEBUGDATE ) print "Date.php : __construct : Date détectée : $string -> ".$this->timestamp."<br>" ;
  // Détection du format Date.
    } else if ( eregi ( '[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (0,0,0,substr($string,3,2),substr($string,0,2),substr($string,6,4));
      if ( DEBUGDATE ) print "Date.php : __construct : Date détectée : $string -> ".$this->timestamp."<br>" ;
     // Détection d'un autre format Date.
    } else if ( eregi ( '[0-2][0-9][0-9][0-9]/[0-1][0-9]/[0-3][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (0,0,0,substr($string,5,2),substr($string,8,2),substr($string,0,4));
      if ( DEBUGDATE ) print "Date.php : __construct : Date détectée : $string -> ".$this->timestamp."<br>" ;
   //dates format xml rpu
    } else if ( eregi ( '[0-3][0-9]/[0-1][0-9]/[0-9][0-9][0-9][0-9] [0-9][0-9]:[0-9][0-9]', $string ) ) {
    //print "\n-".substr($string,11,2)."-".substr($string,14,2)."-".substr($string,3,2)."-".substr($string,0,2)."-".substr($string,6,4);
    	$this->timestamp = adodb_mktime (substr($string,11,2),substr($string,14,2),0,substr($string,3,2),substr($string,0,2),substr($string,6,4));
   // Détection d'un autre format Date.
    } else if ( eregi ( '[0-3][0-9]/[0-1][0-9]/[0-2][0-9][0-9][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (0,0,0,substr($string,3,2),substr($string,0,2),substr($string,6,4));
      if ( ! checkdate ( substr($string,3,2), substr($string,0,2), substr($string,6,4) ) ) $this->erreur = 1 ;
      if ( DEBUGDATE ) print "Date.php : __construct : Date détectée : $string -> ".$this->timestamp."<br>" ;
      // Détection d'un autre format Date.
    } else if ( eregi ( '[0-3][0-9]/[0-1][0-9]/[0-9][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (0,0,0,substr($string,3,2),substr($string,0,2),'20'.substr($string,6,2));
      if ( ! checkdate ( substr($string,3,2), substr($string,0,2), '20'.substr($string,6,2) ) ) $this->erreur = 1 ;
      if ( DEBUGDATE ) print "Date.php : __construct : Date détectée : $string -> ".$this->timestamp."<br>" ;
    // Si on est dans un format timestamp, aucune conversion n'est nécessaire.
    } else if ( is_numeric ( $string ) ) {
      $this->timestamp = $string ;
       if ( DEBUGDATE ) print "Date.php : __construct : Date au format timestamp détectée : $string -> ".$this->timestamp."<br>" ;
    // Par défaut, on initialise avec la date du jour.
    } else {
      $this->erreur = 1 ;
      $this->timestamp = time ( ) ;
      if ( DEBUGDATE ) print "Date.php : __construct : Fabrication par défaut de la date du jour : ".$this->timestamp."<br>" ;
    }

    // Choix de la langue en fonction de l'argument $lang
    $this->minidays = Array ( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ) ;
    $this->minimonths = Array ( '', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ) ;

    if ( $lang == "anglais" ) {
      $this->months = Array ( '', 'January', 'February', 'March', 'April', 'May', 'Jun', 'July', 'August', 'September', 'October', 'November', 'December' ) ;
      $this->days   = Array ( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ) ;
      $this->words  = Array ( 'year', 'month', 'week', 'day', 'hour', 'minute', 'second' ) ;
    } else {
      $this->months  = Array ( '', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre' ) ;
      $this->days    = Array ( 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche' ) ;
      $this->words  = Array ( 'année', 'mois', 'semaine', 'jour', 'heure', 'minute', 'seconde' ) ;
    }
  }



  function setDate ( $string='', $lang ='français' ) {
      $this->erreur = 0 ;
    // Si $string est égal à "today", alors on initialise l'objet avec la date du jour.
    if ( $string == "today" ) {
      $this->timestamp = time ( ) ;
      if ( DEBUGDATE ) print "Date.php : __construct : Demande de fabrication de la date du jour : ".$this->timestamp."<br>" ;
    // Détection du format Datetime.
    } else if ( eregi ( '[0-2][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (substr($string,11,2),substr($string,14,2),substr($string,17,2),substr($string,5,2),
                         substr($string,8,2),substr($string,0,4));
      if ( DEBUGDATE ) print "Date.php : __construct : Date complète détectée : $string -> ".$this->timestamp."<br>" ;
    // Détection du format Date.
    } else if ( eregi ( '[0-2][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (0,0,0,substr($string,5,2),substr($string,8,2),substr($string,0,4));
      if ( DEBUGDATE ) print "Date.php : __construct : Date détectée : $string -> ".$this->timestamp."<br>" ;
  // Détection du format Date.
    } else if ( eregi ( '[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (0,0,0,substr($string,3,2),substr($string,0,2),substr($string,6,4));
      if ( DEBUGDATE ) print "Date.php : __construct : Date détectée : $string -> ".$this->timestamp."<br>" ;
     // Détection d'un autre format Date.
    } else if ( eregi ( '[0-2][0-9][0-9][0-9]/[0-1][0-9]/[0-3][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (0,0,0,substr($string,5,2),substr($string,8,2),substr($string,0,4));
      if ( DEBUGDATE ) print "Date.php : __construct : Date détectée : $string -> ".$this->timestamp."<br>" ;
   // Détection d'un autre format Date.
    } else if ( eregi ( '[0-3][0-9]/[0-1][0-9]/[0-2][0-9][0-9][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (0,0,0,substr($string,3,2),substr($string,0,2),substr($string,6,4));
      if ( ! checkdate ( substr($string,3,2), substr($string,0,2), substr($string,6,4) ) ) $this->erreur = 1 ;
      if ( DEBUGDATE ) print "Date.php : __construct : Date détectée : $string -> ".$this->timestamp."<br>" ;
      // Détection d'un autre format Date.
    } else if ( eregi ( '[0-3][0-9]/[0-1][0-9]/[0-9][0-9]', $string ) ) {
      $this->timestamp = adodb_mktime (0,0,0,substr($string,3,2),substr($string,0,2),'20'.substr($string,6,2));
      if ( ! checkdate ( substr($string,3,2), substr($string,0,2), '20'.substr($string,6,2) ) ) $this->erreur = 1 ;
      if ( DEBUGDATE ) print "Date.php : __construct : Date détectée : $string -> ".$this->timestamp."<br>" ;
    // Si on est dans un format timestamp, aucune conversion n'est nécessaire.
    } else if ( is_numeric ( $string ) ) {
      $this->timestamp = $string ;
      if ( DEBUGDATE ) print "Date.php : __construct : Date au format timestamp détectée : $string -> ".$this->timestamp."<br>" ;
    // Par défaut, on initialise avec la date du jour.
    } else {
      $this->erreur = 1 ;
      $this->timestamp = time ( ) ;
      if ( DEBUGDATE ) print "Date.php : __construct : Fabrication par défaut de la date du jour : ".$this->timestamp."<br>" ;
    }

    // Choix de la langue en fonction de l'argument $lang
    $this->minidays = Array ( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ) ;
    $this->minimonths = Array ( '', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ) ;

    if ( $lang == "anglais" ) {
      $this->months = Array ( '', 'January', 'February', 'March', 'April', 'May', 'Jun', 'July', 'August', 'September', 'October', 'November', 'December' ) ;
      $this->days   = Array ( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ) ;
      $this->words  = Array ( 'year', 'month', 'week', 'day', 'hour', 'minute', 'second' ) ;
    } else {
      $this->months  = Array ( '', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre' ) ;
      $this->days    = Array ( 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche' ) ;
      $this->words  = Array ( 'année', 'mois', 'semaine', 'jour', 'heure', 'minute', 'seconde' ) ;
    }
  }

  // Retourne vrai si la date entrée a été reconnue.
  function isValid ( ) {
    return ($this->erreur?0:1) ;
  }

  
   //getSimpleDate : Retourne la date au format dd/mm/yyyy
    function getSimpleDate ($sep='/') {
  if ( DEBUGDATE ) print "Date.php : getSimpleDate : Retourne la date au format dd/mm/yyyy : ".$this->timestamp."<br>" ;
  
  $mois = $this->getMonthNumber ( );
  if ( strlen($mois)<2 ) { $mois = "0".$mois ;}
  return $this->getDay ( ).$sep.$mois.$sep. $this->getYear ( ); 
   }
  
  
  // Retourne la date au format timestamp.
  function getTimestamp ( ) {
    if ( DEBUGDATE ) print "Date.php : getTimestamp : Retourne la date au format timestamp : ".$this->timestamp."<br>" ;
    return $this->timestamp ;
  }

  // Retourne la date au format date.
  function getDate ( $arg='' ) 
  {
    if ( $arg ) {
      if ( DEBUGDATE ) print "Date.php : getDate : Retourne la date au format Date : ".adodb_date ( "$arg", $this->timestamp )."<br>" ;
      return adodb_date ( "$arg", $this->timestamp ) ;
    } else {
      if ( DEBUGDATE ) print "Date.php : getDate : Retourne la date au format Date : ".adodb_date ( "Y-m-d", $this->timestamp )."<br>" ;
      return adodb_date ( "Y-m-d", $this->timestamp ) ;
    }
  }
  
  //retourne un objet date avec le premier lundi trouvé précédent ( = même jour si c'est un lundi )
  function getLastMonday() 
  {
  	for($nouvelleDate = clone $this ; $nouvelleDate->getDayWeekNumber() != 1 ; $nouvelleDate->addDays(-1) ) true ;
  	return $nouvelleDate ;
  }
  
  /**
   * test if it's the same day than the argument
   * @param clDate $obDate
   * @return bool
   */
  function isSameDay(clDate $obDate)
  {
	  if( ( $this->getYear() == $obDate->getYear() ) &&  ( $this->getDayYear() ==  $obDate->getDayYear()  ) )
	  {
		  return true ;
	  }
	  return false ;
  }


  // Retourne 1 si c'est un jour férié.
  function isHoliday ( ) {


    // Donner un timestamp unix en paramètre
    // Retourne si jour_férié ou week-end
    $jour = date("d", $this->timestamp);
    $mois = date("m", $this->timestamp);
    $annee = date("Y", $this->timestamp);
    
    if($jour == 1 && $mois == 1) return 1; // 1er janvier
    if($jour == 1 && $mois == 5) return 1; // 1er mai
    if($jour == 8 && $mois == 5) return 1; // 5 mai
    if($jour == 14 && $mois == 7) return 1; // 14 juillet
    if($jour == 15 && $mois == 8) return 1; // 15 aout
    if($jour == 1 && $mois == 11) return 1; // 1 novembre
    if($jour == 11 && $mois == 11) return 1; // 11 novembre
    if($jour == 25 && $mois == 12) return 1; // 25 décembre

    $date_paques = easter_date($annee);
    $jour_paques = date("d", $date_paques);
    $mois_paques = date("m", $date_paques);
    if($jour_paques == $jour && $mois_paques == $mois) return 1; // Pâques

	$obDatePaques = clDate::getInstance($date_paques) ;
	$obDatLundiPaques = $obDatePaques->addDaysClone(1);

	if( $obDatLundiPaques->isSameDay($this) ) // lundi de pacques
	{
		return true ;
	}

	$datesAscension = array(
		'21/05/2009',
		'13/05/2010',
		'02/06/2011',
		'17/05/2012',
		'09/05/2013',
		'29/05/2014',
		'14/05/2015'
	);
	foreach( $datesAscension as $dateAscension )
	{
		if( clDate::getInstance($dateAscension)->isSameDay($this) ) // jeudi de ascension
			return true ;
	}
	$datesPentecote = array(
		'31/05/2009',
		'23/05/2010',
		'12/06/2011',
		'27/05/2012',
		'19/05/2013',
		'08/06/2014',
		'24/05/2015'
	);
	foreach( $datesPentecote as $pentecote )
	{
		if(  clDate::getInstance($pentecote)->isSameDay($this) ) //pentecote
			return true ;
		if(  clDate::getInstance($pentecote)->addDays(1)->isSameDay($this) ) //lundi de pentecote
			return true ;
	}
    
  }

  // Retourne la date au format datetime.
  function getDatetime ( ) {
    if ( DEBUGDATE ) print "Date.php : getDatetime : Retourne la date au format Datetime : ".adodb_date ( "Y-m-d H:i:s", $this->timestamp )."<br>" ;
    return adodb_date ( "Y-m-d H:i:s", $this->timestamp ) ;
  }

  // Retourne l'affichage de la date dans un format texte.
  function getDateText ( ) {
    $date = $this->getDayWeek()." ".$this->getDay()." ".$this->getMonth()." ".$this->getYear() ;
    if ( DEBUGDATE ) print "Date.php : getDateText : Retourne la date au format texte : ".$date."<br>" ;
    return $date ;
  }

  // Retourne l'affichage de la date avec l'heure dans un format texte.
  function getDateTextFull ( $sep='-' ) {
    $date = $this->getDayWeek()." ".$this->getDay()." ".$this->getMonth()." ".$this->getYear()." $sep ".date ( "H:i:s",$this->timestamp) ;
    if ( DEBUGDATE ) print "Date.php : getDateTextFull : Retourne la date au format texte avec l'heure : ".$date."<br>" ;
    return $date ;
  }

  function getDateRSS ( ) {
    $nomday = $this->minidays[$this->getDayWeekNumber()] ;
    $nommonth = $this->minimonths[$this->getMonthNumber()] ;
    $date = $nomday.", ".$this->getDay()." ".$nommonth." ".$this->getYear()." ".date ( "H:i:s",$this->timestamp)." +0100" ;
    if ( DEBUGDATE ) print "Date.php : getDateRSS : Retourne la date au format attendu dans un flux RSS : ".$date."<br>" ;
    return $date ;
  }
  
  function getDateRSSDC ( ) {
    
    $date = $this->getDate ( 'Y-m-d' ).'T'.$this->getDate('H:i:s+0100') ;
    if ( DEBUGDATE ) print "Date.php : getDateRSS : Retourne la date au format attendu dans un flux RSS : ".$date."<br>" ;
    return $date ;
  }

  // Retourne les heures (0-23)
  function getHours() {
    $array = adodb_getdate ( $this->timestamp ) ;
    return ( $array['hours'] ) ;
  }

  // Retourne les minutes (0-59)
  function getMinutes ( ) {
    $array = adodb_getdate ( $this->timestamp ) ;
    return ( $array['minutes'] ) ;
  }

  // Retourne les secondes (0-59)
  function getSeconds ( ) {
    $array = adodb_getdate ( $this->timestamp ) ;
    return ( $array['seconds'] ) ;
  }

  // Retourne le numéro de jour dans le mois (1-31)
  function getDay ( $no0='' ) {
    $array = adodb_getdate ( $this->timestamp ) ;
    if ( $array['mday'] < 10 AND ! $no0 ) $s = "0" ; else $s = '' ;
    return ( $s.$array['mday'] ) ;
  }

  // Retourne le nom du jour (Lundi...Dimanche ou Monday...Sunday)
  function getDayWeek ( ) {
    $array = adodb_getdate ( $this->timestamp ) ;
    return ( $this->days[$array['wday']] ) ;
  }

  // Retourne le numéro du jour dans la semaine (0-6) dimanche = 0
  function getDayWeekNumber ( ) {
    $array = adodb_getdate ( $this->timestamp ) ;
    return ( $array['wday'] ) ;
  }

  // Retourne le numéro du jour dans l'année (0-365)
  function getDayYear ( ) {
    $array = adodb_getdate ( $this->timestamp ) ;
    return ( $array['yday'] ) ;
  }
  
  // Retourne le numéro de semaine dans l'année 
  function getWeekNumber() {
  	return $this->getDate('W');
  }
  


  // Retourne le nom du mois (Janvier...Décembre ou January...December)
  function getMonth ( ) {
    $array = adodb_getdate ( $this->timestamp ) ;
    return ( $this->months[$array['mon']] ) ;
  }


  // Retourne le numéro du trimestre
  function getTrimestre() {
	if( in_array( $this->getMonthNumber() , array(1,2,3) ))
		return 1 ;
	if( in_array( $this->getMonthNumber() , array(4,5,6) ))
		return 2 ;
	if( in_array( $this->getMonthNumber() , array(7,8,9) ))
		return 3 ;
	if( in_array( $this->getMonthNumber() , array(10,11,12) ))
		return 4 ;
  }

  // Retourne le numéro du mois (1-12)
  function getMonthNumber ( ) {
    $array = adodb_getdate ( $this->timestamp ) ;
    return ( $array['mon'] ) ;
  }

  // Retourne l'année
  function getYear ( ) {
    $array = adodb_getdate ( $this->timestamp ) ;
    return ( $array['year'] ) ;
  }

  // Ajoute $seconds secondes à la date
  function addSeconds ( $seconds ) {
    $this->timestamp += $seconds ;
    return $this ;
  }

  // Ajoute $minutes minutes à la date
  function addMinutes ( $minutes ) {
    $this->timestamp += 60 * $minutes ;
    return $this ;
  }

  // Ajoute $hours heures à la date
  function addHours ( $hours ) {
    $this->timestamp += 3600 * $hours ;
    return $this ;
  }

  // Ajoute $days jours à la date
  function addDays ( $days ) {
    	if ( $days > 0 ) $sign = '+' ; else $sign = '' ;
    $this->timestamp = strtotime ( "$sign$days days", $this->getTimestamp ( ) ) ;
    return $this ;
  }
  
  // Ajoute $days jours à la date sans modifier this, renvoie la nouvelle date changée
  /**
   *
   * @param clDate $days
   * @return clDate
   */
  function addDaysClone( $days )
  {
  	$newdate = clone $this;
    	if ( $days > 0 ) $sign = '+' ; else $sign = '' ;
    $newdate->timestamp = strtotime ( "$sign$days days", $newdate->getTimestamp ( ) ) ;
    return $newdate ;
  }

  // Ajoute $weeks semaines à la date
  function addWeeks ( $weeks ) {
    $this->timestamp += 604800 * $weeks ;
   	return $this ;
  }
  
  function addYears($years) {
  	$this->timestamp += 31557600* $years ;
  	return $this ;
  }

  // Calcul de la différence avec une date passsée en paramètre
  function getDifference ( $d ) {
    $res = $this->timestamp - $d->getTimestamp ( ) ;
    if ( DEBUGDATE ) print "Date.php : getDifference : Différence de $res secondes<br>" ;
    //eko($this->timestamp);
    //eko($d->getTimestamp ( ));
    return $res ;
  }
  
  function laterOrEqualThan($d='') {
  	if(! is_object($d)) $d= new clDate($d);
  	$res = $this->getDifference ( $d );
  	if($res>=0) {
  	//eko("vrai: ".$this->getSimpleDate()." est plus tard que".$d->getSimpleDate());
  		return true;
  	}
  	//eko("faux: ".$this->getSimpleDate()." n'est pas plus tard que".$d->getSimpleDate());
  	return false;
  }
  
function earlierOrEqualThan($d='') {
  	if(! is_object($d)) $d= new clDate($d);
  	$res = $this->getDifference ( $d );
  	if($res<=0) {
  		//eko("vrai: ".$this->getSimpleDate()." est plus tot que".$d->getSimpleDate());
  		return true;
  	}
  	//eko("faux: ".$this->getSimpleDate()." n'est pas plus tot que".$d->getSimpleDate());
  	return false;
  }
 
  function laterThan($d='') {
  	if(! is_object($d)) $d= new clDate($d);
  	$res = $this->getDifference ( $d );
  	if($res>0) {
  	//eko("vrai: ".$this->getSimpleDate()." est plus tard que".$d->getSimpleDate());
  		return true;
  	}
  	//eko("faux: ".$this->getSimpleDate()." n'est pas plus tard que".$d->getSimpleDate());
  	return false;
  }
  
function earlierThan($d='') {
  	if(! is_object($d)) $d= new clDate($d);
  	$res = $this->getDifference ( $d );
  	if($res<0) {
  		return true;
  	}
  	return false;
  }
  
 //teste si la date est dans la periode  
 function in($d1,$d2){
 if($this->earlierOrEqualThan($d2) && $this->laterOrEqualThan($d1))
 	return true;
  return false;		
 }
 
  //Crée une instance de date 
 static function makeDate($arg='') {
 	$obDateTmp = new clDate($arg);
 	return $obDateTmp;
 }

 /**
  *
  * @param <type> $arg
  * @return clDate
  */
 static function getInstance($arg='') {
 	return clDate::makeDate($arg);
 }
 
 //Crée une instance de date aujourd'hui mais à 00h00
 static function makeDateToday() {
 	$obDateTmp = new clDate();
 	$obDateTmp = new clDate($obDateTmp->getDate());
 	return $obDateTmp ;
 }
 
   //Retourne le premier lundi , suivant une année et un numéro de semaine (EC)
  static function getMondayFromWeekNumber($year,$weekNumber) {
  	for( $date = new clDate ('01/01/'.$year) ; $date->getDayWeekNumber() != 1 ; $date->addDays(-1) ) true ;
  	$date->addDays(7*($weekNumber-1));
  	return $date ;
  }

}


?>