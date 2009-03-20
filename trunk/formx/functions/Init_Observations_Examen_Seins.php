<?php
function Init_Observations_Examen_Seins($formx) {

$item=utf8_decode($formx->getVar('L_Val_ESEINS_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ESEINS_Comm',"Champ Non Précisé.");
  }

 


return "O";
}
?>
