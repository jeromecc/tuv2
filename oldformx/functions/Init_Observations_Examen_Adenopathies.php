<?php
function Init_Observations_Examen_Adenopathies($formx) {

$item=utf8_decode($formx->getVar('L_Val_EADENO_Cer'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EADENO_Cer',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_EADENO_Axi'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EADENO_Axi',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_EADENO_Ing'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EADENO_Ing',"Champ Non Précisé.");
  }
 


return "O";
}
?>
