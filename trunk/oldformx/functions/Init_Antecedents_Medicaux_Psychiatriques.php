<?php
function Init_Antecedents_Medicaux_Psychiatriques($formx) {

$item=utf8_decode($formx->getVar('L_Val_PSYCHIATRIQUES_Autres'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PSYCHIATRIQUES_Autres',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PSYCHIATRIQUES_Psychose_Comm'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PSYCHIATRIQUES_Psychose_Comm',"Champ Non Précisé.");
  }

$item=utf8_decode($formx->getVar('L_Val_PSYCHIATRIQUES_Psychose'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PSYCHIATRIQUES_Psychose',"Non");
  }

$item=utf8_decode($formx->getVar('L_Val_PSYCHIATRIQUES_Pmd'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PSYCHIATRIQUES_Pmd',"Non");
  }
  
$item=utf8_decode($formx->getVar('L_Val_PSYCHIATRIQUES_Depressif'));
if ( $item == "" )
  {
  $formx->setVar('L_Val_PSYCHIATRIQUES_Depressif',"Non");
  }









return "O";
}
?>
