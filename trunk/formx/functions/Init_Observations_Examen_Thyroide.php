<?php
function Init_Observations_Examen_Thyroide($formx) {

$item=utf8_decode($formx->getVar('L_Val_ETHYRO_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ETHYRO_Comm',"Champ Non Précisé.");
  }

 


return "O";
}
?>
