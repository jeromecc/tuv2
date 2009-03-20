<?php
function ListesBilans_casespredef_formulaire_bio_comp($formx) {

$listesbilans = new clListes ( "ListesBilansCmp" ) ;

$liste = array();
$liste_const = "";

$choixbilan = utf8_decode($formx->getFormVar('Val_F_BIO_Liste_Choix'));
//eko ( $casespredefinies );

  
foreach ( $listesbilans->getListeItems($choixbilan, 1, 0,"*AUCUNITEM*",1) as $liste1 => $liste2 ){
  $liste[$liste2]= $liste2;
  $liste_const.=$liste2."|";
  }

$liste_const       =   substr($liste_const,0,strlen($liste_const)-1);

//eko ( $liste );
//eko ( $liste_const );

if ( $liste_const=="null")
  $liste_const="";
  
return utf8_encode($liste_const);


}
?>
