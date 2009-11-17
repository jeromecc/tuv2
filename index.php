<?php 
// Titre  : Fichier index du terminal
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 05 Janvier 2005
 
// Description : 
// Le squelette du site est fabriqué ici et les objets "globaux" sont
// instanciés à ce niveau.


// Appel du fichier de configuration.
include_once ( "config.php" ) ;

// On instancie les objets globaux.
// Appel de la classe Erreurs.
$errs    = new clErreurs ( ) ;
// Appel de la classe Options.
$options = new clOptions ( ) ;
// Appel de la classe Logs.
$logs    = new clLogs ( ) ;
// Appel de la classe PostIt.
$pi      = new clPostIt ( ) ;
// Appel de la classe session.
$session = new clSession ( ) ;
// Récupération de la date du jour.
$date    = new clDate ( ) ;
// Fabrication du menu.
$menu    = new clMenu ( ) ;
// Ajout des statistiques.
$session -> setStats  ( ) ;
// Fabrication de la page.
$navi    = new clNavigation ( ) ;


$pi -> addMove ( "messagenew", "handlem" ) ;
$pi -> addMove ( "ajouterattendu", "handlep" ) ;
$pi -> addMove ( "casemaincourante", "casemaincourante_handler" ) ;

if ( ! file_exists ( URLLOCAL."define.xml.php" ) ) $errs -> addErreur ( "La constante URLLOCAL est sûrement mal renseignée. Merci de vérifier votre fichier define.xml.php." ) ;

// Il ne reste plus qu'à afficher le code XHTML si nous ne sommes pas en mode
// "génération de pdf".$pi -> addPostIt ( "Test2", "Ceci est un test de ouf" ) ;
if ( ! $stopAffichage AND $session->getNavi ( 2 ) != "voirDoc" AND  $session->getNavi ( 3 ) != "genEdition" ) {
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">



<head>
  <title><?php print NOMAPPLICATION." ".VERSIONAPPLICATION; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link rel="stylesheet" type="text/css" href="css/style.css"></link>
  <?php if ( $session->getType() == 'Echec' ) print '<link rel="stylesheet" type="text/css" href="css/auth.css"></link>' ; ?>
  <link rel="shortcut icon" href="images/favicon.ico" type="image/ico" />
  <link rel="stylesheet" type="text/css" href="<?=URL?>css/FoRmX_terminurge.css"></link>
  <link rel="stylesheet" type="text/css" href="<?=URL?>css/FoRmX.css"></link>
  <link rel="stylesheet" type="text/css" media="all" href="modules/calendar/skins/aqua/theme.css" title="Aqua" />
  <script type="text/javascript" src="modules/calendar/calendar.js" ></script>
  <script type="text/javascript" src="modules/calendar/lang/calendar-fr.js" ></script>
  <script type="text/javascript" src="modules/calendar/calendar-setup.js" ></script>
  <script type="text/javascript" src="scripts.js"></script>
  <script type="text/javascript" src="ajax.js"></script>
  <script type="text/javascript" src="formx.js?v=<?php echo VERSIONAPPLICATION ?>"></script>
  <script type="text/javascript" src="modules/draganddrop/core.js"></script>
  <script type="text/javascript" src="modules/draganddrop/events.js"></script>
  <script type="text/javascript" src="modules/draganddrop/css.js"></script>
  <script type="text/javascript" src="modules/draganddrop/coordinates.js"></script>
  <script type="text/javascript" src="modules/draganddrop/drag.js"></script>
  <script type="text/javascript" src="modules/overlib/overlib.js"></script>
  <script type="text/javascript" src="modules/sorttable.js"></script>
  <script type="text/javascript" src="modules/wz_dragdrop.js"></script>
  <?php print $pi -> genJS ( ) ; ?>
</head>
<body>
  <div class="select-free"  id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
  
  <a name="acces" accesskey="0"></a>
    <div id="page">
      <div id="bandeauMenu">
      <div id="menu" class="logomenu">
        <?php if ( ! isset ( $noMenu ) OR ! $noMenu ) print $menu -> getAffichage ( ) ; ?>
      </div>
      </div>
      <div id="wait"></div>
      <div id="navigation" <?print $options->getOption('masquageMenu');?>="javascript:montre('s')">
        <?php print $navi -> getAffichage ( ) ; ?>
      </div>
      <div id="console_debug">
        <?php print $errs -> logPrint ( ) ; ?>
      </div>
      <?php print $pi -> getAffichage ( ) ; ?>
    </div>
    
    
    <?php
    
    
    //if ( $options->getOption('PetitBox_ShowBox') ) {
      //$petitBox = new clPetitBox ();
      //print $petitBox->getAffichage ();
    //}
    
    ?>
    
    
  <noscript>Votre navigateur ne lit pas le Javascript, veuillez l'activer pour utilisation complète du Terminal des urgences V2.</noscript>
 <script type="text/javascript">
 	loadScripts();
 </script>
 
 </body>
</html>
<?php
 } elseif ( $navi -> getAffichage ( ) ) {
   //print $navi -> getAffichage ( ) ;
 }
 $errs->destruct ( ) ;
 $session->destruct ( ) ;
?>
