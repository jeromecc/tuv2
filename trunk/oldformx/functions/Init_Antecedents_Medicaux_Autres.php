<?php
function Init_Antecedents_Medicaux_Autres($formx) {

$item=utf8_decode($formx->getVar('L_Val_MEDICAUX_AUTRES_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_MEDICAUX_AUTRES_Comm',"Champ Non Précisé.");
  }

return "O";
}
?>
