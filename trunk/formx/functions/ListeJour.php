<?php
function ListeJour($formx) {

$liste = array(); 

for ( $i = 1 ; $i <= 31 ; $i++ ){
  $liste[ utf8_encode($i)]= $i;
  }

return ($liste);
}
?>
