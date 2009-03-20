<?php
function Init_Evolutions_Evolutions($formx) {

$item=utf8_decode($formx->getVar('L_Val_FEvolution'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_FEvolution',"Champ Non Précisé.");
  }





return "O";
}
?>
