<?php
function ListesBilans_choixbilan_formulaire_bio($formx) {

$listesbilans = new clListes ( "ListesBilans" ) ;

$liste = array(); 
foreach ( $listesbilans->getListeListes("ListesBilans", 1, 0, 0, 1 ) as $liste1 => $liste2 ){
  $liste[utf8_encode($liste2)]= $liste2;
  //$liste[ $liste1]= $liste2;
  }

//eko ( $liste );
return ($liste);

}
?>
