<?php
function Init_Antecedents_Chirurgicaux_Neurologiques($formx) {

$item=utf8_decode($formx->getVar('L_Val_CHIRURGICAUX_NEUROLOGIQUES_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_CHIRURGICAUX_NEUROLOGIQUES_Comm',"Champ Non Précisé.");
  }


return "O";
}
?>
