<?php
function Test_AEV_Prophylaxie_31($formx) {

$item_5  = utf8_decode($formx->getVar('L_AEV_Prophylaxie_Demande_31'));
$item_10 = utf8_decode($formx->getFormVar('AEV_Prophylaxie_Demande'));

if ( $item_10 == "" ) {
  $item = $item_5;
  }
else {
  $item = $item_10;
  }

if ( $item == "Oui" )
  return true;
else
  return false;

}
?>
