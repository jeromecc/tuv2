<?php
function ListeMinute($formx) {

$formx->setVar('L_Val_Minute_Now',date("i"));
if ( ! defined ( 'L_Val_Minute_Now' ) ) define ( 'L_Val_Minute_Now', date ( 'i' ) ) ;
$liste = array(); 

for ( $i = 0 ; $i <= 59 ; $i++ ){
  if ( $i < 10 ) { $j = "0".$i; $liste[ utf8_encode($j)]= $j; }
  else $liste[ utf8_encode($i) ]= $i;
  }

return ($liste);
}
?>
