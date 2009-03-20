<?php
function ListeHeure($formx) {

$formx->setVar('L_Val_Heure_Now',date("H"));
if ( ! defined ( 'L_Val_Heure_Now' ) ) define ( 'L_Val_Heure_Now', date ( 'H' ) ) ;
//eko  ( date ( 'H' ) ) ;
$liste = array(); 

for ( $i = 0 ; $i <= 23 ; $i++ ){
  if ( $i < 10 ) { $j = "0".$i; $liste[ utf8_encode($j)]= $j; }
  else $liste[ utf8_encode($i) ]= $i;
  }

return ($liste);
}
?>

