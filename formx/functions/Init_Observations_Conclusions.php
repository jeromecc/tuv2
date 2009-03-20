<?php
function Init_Observations_Conclusions($formx) {

$item=utf8_decode($formx->getVar('L_Val_OBSERVATIONS_Conlusion'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_OBSERVATIONS_Conlusion',"Champ Non Précisé.");
  }





return "O";
}
?>
