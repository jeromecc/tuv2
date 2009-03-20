<?php
function Init_Antecedents_Chirurgicaux_Thoraciques($formx) {

$item=utf8_decode($formx->getVar('L_Val_THORACIQUES_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_THORACIQUES_Autres',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_THORACIQUES_Pneumectomie_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_THORACIQUES_Pneumectomie_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_THORACIQUES_Pneumectomie'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_THORACIQUES_Pneumectomie',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_THORACIQUES_Pontage_C'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_THORACIQUES_Pontage_C',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_THORACIQUES_Pontage'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_THORACIQUES_Pontage',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_THORACIQUES_Space_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_THORACIQUES_Space_Comm',"Champ Non Précisé.");
  }
  
$item=utf8_decode($formx->getVar('L_Val_THORACIQUES_Space'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_THORACIQUES_Space',"Non");
  }










return "O";
}
?>
