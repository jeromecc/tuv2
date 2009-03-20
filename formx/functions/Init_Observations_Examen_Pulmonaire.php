<?php
function Init_Observations_Examen_Pulmonaire($formx) {

$item=utf8_decode($formx->getVar('L_Val_EPULMO_Signes'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EPULMO_Signes',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_EPULMO_Freq'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EPULMO_Freq',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_EPULMO_Foy'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EPULMO_Foy',"Champ Non Précisé.");
  }
   

return "O";
}
?>
