<?php
function Init_Observations_Examen_Teguments($formx) {

$item=utf8_decode($formx->getVar('L_Val_ETEGU_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_ETEGU_Comm',"Champ Non Précisé.");
  }

 


return "O";
}
?>
