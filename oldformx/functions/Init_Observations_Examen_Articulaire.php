<?php
function Init_Observations_Examen_Articulaire($formx) {

$item=utf8_decode($formx->getVar('L_Val_EOSTEO_Rachis'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EOSTEO_Rachis',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_EOSTEO_Arti'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EOSTEO_Arti',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_EOSTEO_Signes'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_EOSTEO_Signes',"Champ Non Précisé.");
  }



return "O";
}
?>
