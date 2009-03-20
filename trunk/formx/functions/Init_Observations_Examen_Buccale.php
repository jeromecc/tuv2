<?php
function Init_Observations_Examen_Buccale($formx) {

$item=utf8_decode($formx->getVar('L_Val_ECAVITE_comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ECAVITE_comm',"Champ Non Précisé.");
  }
 


return "O";
}
?>
