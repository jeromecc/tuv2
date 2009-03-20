<?php
function ListeAnnee($formx) {

$liste = array(); 

for ( $i = date(Y) ; $i >= 1900 ; $i-- ){
  $liste[ utf8_encode($i)]= $i;
  }

return ($liste);
}
?>
